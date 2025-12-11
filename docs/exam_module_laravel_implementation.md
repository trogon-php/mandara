# 🧱 Exam Module - Laravel Implementation Guide

This document provides a **complete, detailed technical guide** for implementing the **Exam Module** in Laravel, following the structure, standards, and coding conventions of the Trogon LMS Base Architecture.

It explains:
- Folder structure & class responsibilities
- Models, relationships, and migrations
- Caching (Memcached) integration
- Services and Controllers
- API routing and response format
- Example code for each key endpoint

---

## ⚙️ Architecture Overview

### 📁 Core Module Structure (Inside `modules/Exams`)

```
modules/
 └── Exams/
     ├── Controllers/
     │   ├── ExamApiController.php
     │   └── ExamController.php (for admin)
     ├── Models/
     │   ├── Exam.php
     │   ├── ExamAttempt.php
     │   ├── ExamAnswer.php
     │   ├── ExamQuestion.php
     │   └── ExamOption.php
     ├── Services/
     │   ├── ExamService.php
     │   ├── ExamAttemptService.php
     │   ├── ExamCacheService.php
     │   └── ExamEvaluationService.php
     ├── routes/
     │   ├── api.php
     │   └── web.php
     └── database/
         ├── migrations/
         └── seeders/
```

---

## 🧩 Database Structure

All tables must include:
- `created_by`, `updated_by`, `deleted_by`
- `created_at`, `updated_at`, `deleted_at`

### 1️⃣ `exams`
| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK) | Exam ID |
| title | VARCHAR(255) | Exam title |
| description | TEXT | Exam details |
| time_limit | INT | Duration in minutes |
| total_marks | INT | Total score |
| pass_marks | INT | Passing score |
| review_mode | ENUM('none','summary','full') | Review visibility |
| status | ENUM('active','inactive') | Active flag |
| created_by / timestamps | INT + DATETIME | Audit fields |

### 2️⃣ `exam_questions`
| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK) | Question ID |
| exam_id | INT (FK) | Reference to `exams.id` |
| question_text | TEXT | Question content |
| question_type | ENUM('mcq_single','mcq_multiple','true_false','short_answer') | Type |
| marks | INT | Marks per question |
| explanation | TEXT | Optional explanation |
| sort_order | INT | Question order |
| created_by / timestamps | - | - |

### 3️⃣ `exam_options`
| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK) | Option ID |
| question_id | INT (FK) | Reference to `exam_questions.id` |
| option_text | VARCHAR(255) | Option text |
| is_correct | TINYINT(1) | 1 = Correct |
| created_by / timestamps | - | - |

### 4️⃣ `exam_attempts`
| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK) | Attempt ID |
| exam_id | INT | FK → exams.id |
| user_id | INT | FK → users.id |
| score | INT | Final score |
| correct_count | INT | Count of correct answers |
| wrong_count | INT | Wrong answers |
| status | ENUM('in_progress','submitted','evaluated') | Attempt state |
| started_at | DATETIME | Exam start time |
| submitted_at | DATETIME | End time |
| progress_saved_at | DATETIME | Last auto-save time |
| created_by / timestamps | - | - |

### 5️⃣ `exam_answers`
| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK) | Answer ID |
| attempt_id | INT | FK → exam_attempts.id |
| question_id | INT | FK → exam_questions.id |
| selected_option_ids | JSON | For MCQs |
| answer_text | TEXT | For descriptive answers |
| is_correct | TINYINT(1) | Result evaluation |
| marks | INT | Marks awarded |
| time_spent | INT | Seconds spent |
| created_by / timestamps | - | - |

---

## 🧠 Model Relationships (Eloquent)

### `Exam.php`
```php
class Exam extends BaseModel {
    protected $table = 'exams';

    public function questions() {
        return $this->hasMany(ExamQuestion::class, 'exam_id');
    }

    public function attempts() {
        return $this->hasMany(ExamAttempt::class, 'exam_id');
    }
}
```

### `ExamQuestion.php`
```php
class ExamQuestion extends BaseModel {
    protected $table = 'exam_questions';

    public function options() {
        return $this->hasMany(ExamOption::class, 'question_id');
    }
}
```

### `ExamAttempt.php`
```php
class ExamAttempt extends BaseModel {
    protected $table = 'exam_attempts';

    public function exam() {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function answers() {
        return $this->hasMany(ExamAnswer::class, 'attempt_id');
    }
}
```

---

## ⚡ Services Implementation

### 1️⃣ `ExamCacheService`
Handles temporary caching of exam data and answers.

```php
namespace App\Services\Exams;

use Illuminate\Support\Facades\Cache;

class ExamCacheService {
    protected string $prefix = 'exam_attempt:';
    protected int $ttl = 120; // minutes

    public function cacheAttemptAnswers(int $attemptId, array $answers): void {
        Cache::put($this->prefix.$attemptId.':answers', $answers, now()->addMinutes($this->ttl));
    }

    public function getCachedAnswers(int $attemptId): ?array {
        return Cache::get($this->prefix.$attemptId.':answers');
    }

    public function clearAttemptCache(int $attemptId): void {
        Cache::forget($this->prefix.$attemptId.':answers');
    }
}
```

### 2️⃣ `ExamAttemptService`
Handles DB persistence and partial progress save.

```php
class ExamAttemptService {
    public function savePartialProgress(int $attemptId, array $answers): void {
        foreach ($answers as $a) {
            ExamAnswer::updateOrCreate(
                ['attempt_id' => $attemptId, 'question_id' => $a['question_id']],
                [
                    'selected_option_ids' => json_encode($a['selected_option_ids'] ?? []),
                    'answer_text' => $a['answer_text'] ?? null,
                    'time_spent' => $a['time_spent'] ?? 0,
                ]
            );
        }
        ExamAttempt::where('id', $attemptId)->update(['progress_saved_at' => now()]);
    }
}
```

### 3️⃣ `ExamEvaluationService`
Handles marking and scoring.

```php
class ExamEvaluationService {
    public function evaluate(int $examId, int $attemptId, array $answers): array {
        $examQuestions = ExamQuestion::with('options')->where('exam_id', $examId)->get()->keyBy('id');

        $score = $correct = $wrong = 0;

        foreach ($answers as $ans) {
            $q = $examQuestions[$ans['question_id']] ?? null;
            if (!$q) continue;

            $isCorrect = false;
            $selected = $ans['selected_option_ids'][0] ?? null;
            $correctOpt = $q->options->where('is_correct', 1)->pluck('id')->first();
            $isCorrect = ($selected == $correctOpt);

            ExamAnswer::updateOrCreate(
                ['attempt_id' => $attemptId, 'question_id' => $q->id],
                [
                    'selected_option_ids' => json_encode($ans['selected_option_ids'] ?? []),
                    'answer_text' => $ans['answer_text'] ?? null,
                    'is_correct' => $isCorrect,
                    'marks' => $isCorrect ? $q->marks : 0,
                ]
            );

            $isCorrect ? $correct++ : $wrong++;
            $score += $isCorrect ? $q->marks : 0;
        }

        ExamAttempt::where('id', $attemptId)->update([
            'score' => $score,
            'correct_count' => $correct,
            'wrong_count' => $wrong,
            'status' => 'submitted',
            'submitted_at' => now()
        ]);

        return compact('score','correct','wrong');
    }
}
```

---

## 🧭 Controller Implementation

### `ExamApiController.php`
Extends `BaseApiController` and uses service dependency injection.

```php
class ExamApiController extends BaseApiController {
    public function __construct(
        protected ExamService $examService,
        protected ExamCacheService $cacheService,
        protected ExamAttemptService $attemptService,
        protected ExamEvaluationService $evaluationService
    ) {}

    public function getExam($examId) {
        $exam = Exam::findOrFail($examId);
        return $this->respondSuccess($exam, 'Exam details fetched successfully');
    }

    public function start($examId) {
        $attempt = ExamAttempt::create([
            'exam_id' => $examId,
            'user_id' => auth()->id(),
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        $questions = ExamQuestion::where('exam_id', $examId)->pluck('id')->shuffle()->toArray();

        Cache::put('exam_attempt:'.$attempt->id.':order', $questions, now()->addMinutes(120));

        return $this->respondSuccess([
            'attempt_id' => $attempt->id,
            'question_order' => $questions,
            'time_limit' => Exam::find($examId)->time_limit,
            'started_at' => now()
        ], 'Exam started successfully');
    }

    public function getQuestion($examId, $attemptId, $questionId) {
        $question = ExamQuestion::with('options')->findOrFail($questionId);
        return $this->respondSuccess($question, 'Question fetched successfully');
    }

    public function saveProgress(Request $req, $examId, $attemptId) {
        $answers = $req->get('answers', []);
        $this->cacheService->cacheAttemptAnswers($attemptId, $answers);
        dispatch(fn() => $this->attemptService->savePartialProgress($attemptId, $answers));
        return $this->respondSuccess([], 'Progress saved successfully');
    }

    public function submit(Request $req, $examId, $attemptId) {
        $cached = $this->cacheService->getCachedAnswers($attemptId) ?? [];
        $final = $req->get('answers', []);
        $merged = array_merge($cached, $final);

        $result = $this->evaluationService->evaluate($examId, $attemptId, $merged);

        $this->cacheService->clearAttemptCache($attemptId);

        return $this->respondSuccess($result, 'Exam submitted successfully');
    }

    public function result($examId, $attemptId) {
        $attempt = ExamAttempt::findOrFail($attemptId);
        return $this->respondSuccess($attempt, 'Exam result fetched successfully');
    }

    public function review(Request $req, $examId, $attemptId) {
        $perPage = $req->get('per_page', 20);
        $attempt = ExamAttempt::findOrFail($attemptId);
        $exam = Exam::select('id','title')->findOrFail($examId);

        $paginator = ExamQuestion::with(['options','answers'=>fn($q)=>$q->where('attempt_id',$attemptId)])
            ->where('exam_id',$examId)
            ->paginate($perPage);

        $data = $paginator->map(fn($q,$i)=>[
            'id'=>$q->id,
            'question_number'=>$i+1,
            'question_text'=>$q->question_text,
            'question_type'=>$q->question_type,
            'marks'=>$q->marks,
            'student_answer'=>[
                'selected_option_ids'=>json_decode(optional($q->answers->first())->selected_option_ids ?? '[]'),
                'answer_text'=>optional($q->answers->first())->answer_text
            ],
            'correct_answer'=>[
                'selected_option_ids'=>$q->options->where('is_correct',1)->pluck('id')->values()
            ],
            'is_correct'=>optional($q->answers->first())->is_correct,
            'explanation'=>$q->explanation,
            'options'=>$q->options->map(fn($o)=>[
                'id'=>$o->id,
                'text'=>$o->option_text,
                'is_correct'=>(bool)$o->is_correct
            ])
        ]);

        return response()->json([
            'status'=>true,
            'http_code'=>200,
            'message'=>'Exam review fetched successfully',
            'data'=>[
                'exam'=>$exam,
                'attempt'=>$attempt,
                'questions'=>$data
            ],
            'errors'=>(object)[],
            'meta'=>[
                'current_page'=>$paginator->currentPage(),
                'last_page'=>$paginator->lastPage(),
                'per_page'=>$paginator->perPage(),
                'total'=>$paginator->total(),
                'from'=>$paginator->firstItem(),
                'to'=>$paginator->lastItem()
            ]
        ]);
    }
}
```

---

## 🧾 Routing (`modules/Exams/routes/api.php`)

```php
use Illuminate\Support\Facades\Route;
use Modules\Exams\Controllers\ExamApiController;

Route::prefix('exams')->group(function () {
    Route::get('{exam}', [ExamApiController::class, 'getExam']);
    Route::post('{exam}/start', [ExamApiController::class, 'start']);
    Route::get('{exam}/attempts/{attempt}/questions/{question}', [ExamApiController::class, 'getQuestion']);
    Route::post('{exam}/attempts/{attempt}/save-progress', [ExamApiController::class, 'saveProgress']);
    Route::post('{exam}/attempts/{attempt}/submit', [ExamApiController::class, 'submit']);
    Route::get('{exam}/attempts/{attempt}/result', [ExamApiController::class, 'result']);
    Route::get('{exam}/attempts/{attempt}/review', [ExamApiController::class, 'review']);
});
```

---

## 💾 Caching Notes (Memcached)

- `CACHE_STORE=memcached`
- `CACHE_PREFIX=laravel_trogon`
- `MEMCACHED_HOST=127.0.0.1`
- Use write-through pattern → cache first, queue DB writes.
- TTL recommended: `exam_duration + 15 minutes`.

**Key Format Examples:**
```
exam_attempt:{id}:answers
exam_attempt:{id}:order
exam_attempt:{id}:status
```

---

## 🧠 Best Practices

| Area | Recommendation |
|------|----------------|
| **Caching** | Always use write-through to prevent data loss on crash |
| **Queue** | Dispatch async DB sync jobs for save-progress |
| **Evaluation** | Keep auto & manual evaluation separate (MCQ vs Descriptive) |
| **Pagination** | Default `per_page = 20` for reviews |
| **Security** | Verify user ownership of attempt before access |
| **Error Handling** | Use `respondError()` in BaseApiController for uniform format |
| **Testing** | Use PHPUnit for auto-submit & resume scenarios |

---

**Maintained By:** Trogon LMS Development Team  
**Last Updated:** October 2025

