# 🧭 Exam Module — Frontend & Backend Logic Flow

This document describes the **complete logic flow** of the Exam Module in text-only form, outlining what happens step-by-step on both the **Frontend (mobile/web)** and **Backend (Laravel)** sides.

It helps developers, testers, and integrators understand the end-to-end process from starting an exam to viewing the final review.

---

## 🧩 1️⃣ Exam Details Page — Before Exam Starts

### 🎨 Frontend (App)
1. Student selects an exam.
2. Calls `GET /api/v1/exams/{exam_id}`.
3. Displays title, description, total questions, time limit, and remaining attempts.
4. Shows “Start Exam” button.

### ⚙️ Backend (Laravel)
1. Fetches exam details from `exams` table.
2. Validates if exam is active and user has remaining attempts.
3. Returns data in standard API response format.

**Output:** Exam info screen is shown.

---

## 🚀 2️⃣ Start Exam Attempt

### 🎨 Frontend
1. Student clicks **Start Exam**.
2. Calls `POST /api/v1/exams/{exam_id}/start`.
3. Redirects to question view screen.

### ⚙️ Backend
1. Creates new record in `exam_attempts` table.
2. Fetches all `exam_questions` IDs.
3. Randomizes order → stores in `Memcached` as `exam_attempt:{id}:order`.
4. Returns attempt details and first question ID.

**Output:** Frontend loads first question using new `attempt_id`.

---

## ❓ 3️⃣ Fetch Question (Dynamic Navigation)

### 🎨 Frontend
1. When user moves between questions → calls `GET /api/v1/exams/{exam_id}/attempts/{attempt_id}/questions/{question_id}`.
2. Displays question text, options, and flags.

### ⚙️ Backend
1. Validates attempt ownership and question existence.
2. Fetches from cache → fallback to DB if missing.
3. Returns question + options.

**Output:** Question loaded on screen.

---

## 💾 4️⃣ Save Progress (Auto / Manual)

### 🎨 Frontend
1. Triggered automatically every 1–2 minutes or on navigation.
2. Sends current answers to: `POST /api/v1/exams/{exam_id}/attempts/{attempt_id}/save-progress`.

### ⚙️ Backend
1. Writes instantly to **Memcached**.
2. Dispatches a queue job to write answers to DB asynchronously.
3. Updates `progress_saved_at` timestamp.

**Output:** Progress safely stored (cache + DB).

---

## 🔁 5️⃣ Resume / Reconnect Scenario

### 🎨 Frontend
1. If app restarts → reload attempt from local storage or call API.
2. Restores question states from previous save.

### ⚙️ Backend
1. Checks cache for answers.
2. If missing → reloads from `exam_answers`.
3. Returns restored progress.

**Output:** Student resumes exactly where they left off.

---

## 📤 6️⃣ Submit Exam

### 🎨 Frontend
1. On clicking **Submit Exam**, shows confirmation dialog.
2. Calls `POST /api/v1/exams/{exam_id}/attempts/{attempt_id}/submit`.

### ⚙️ Backend
1. Fetches all cached answers → merges with latest request.
2. Evaluates responses:
   - Compares selected options with `exam_options.is_correct`.
   - Calculates score, correct/wrong counts.
3. Updates `exam_attempts` with `score`, `status='submitted'`, `submitted_at`.
4. Clears cache.

**Output:** Result summary returned to frontend.

---

## 🧾 7️⃣ View Result Summary

### 🎨 Frontend
1. Calls `GET /api/v1/exams/{exam_id}/attempts/{attempt_id}/result`.
2. Displays total score, correct/wrong counts, and pass/fail status.

### ⚙️ Backend
1. Fetches data from `exam_attempts` table.
2. Returns formatted result summary.

**Output:** Result screen displayed.

---

## 🔍 8️⃣ Review Attempt (Post-Exam Analysis)

### 🎨 Frontend
1. Calls `GET /api/v1/exams/{exam_id}/attempts/{attempt_id}/review?page=1&per_page=20`.
2. Displays each question with student’s answer, correct answer, and explanation.

### ⚙️ Backend
1. Validates access (review_mode).
2. Joins `exam_questions`, `exam_answers`, and `exam_options`.
3. Returns paginated results with meta info.

**Output:** Review page loaded with explanations.

---

## ⏰ 9️⃣ Auto-Submit (Time Expiry)

### 🎨 Frontend
1. Timer monitors duration (based on `time_limit`).
2. Auto-calls `/submit` API when time ends.

### ⚙️ Backend
1. Same as manual submission process.
2. Marks `exam_attempts.status = submitted`.

**Output:** Exam automatically submitted and evaluated.

---

## 🧠 🔟 Background Jobs & Maintenance

### ⚙️ Backend (Scheduler)
- Every 2–3 minutes:
  - Sync cached answers to DB.
  - Auto-submit expired attempts.
- Nightly cleanup clears old cache keys.

**Logs:** Optional tracking of answer changes and user events for auditing.

---

## 🧱 Summary Table

| Step | Action | Frontend | Backend | Cache | DB Tables |
|------|---------|-----------|----------|--------|------------|
| 1 | View Exam | Display details | Fetch exam info | ❌ | `exams` |
| 2 | Start Exam | Begin session | Create attempt | ✅ | `exam_attempts` |
| 3 | Fetch Question | Load question | Retrieve question | ✅ | `exam_questions`, `exam_options` |
| 4 | Save Progress | Auto/manual save | Cache + async DB write | ✅ | `exam_answers`, `exam_attempts` |
| 5 | Resume | Restore state | Load cache or DB | ✅ | `exam_answers` |
| 6 | Submit | Finalize exam | Evaluate + store result | ❌ | `exam_attempts`, `exam_answers` |
| 7 | Result | Show summary | Fetch result | ❌ | `exam_attempts` |
| 8 | Review | Load paginated review | Join and return | ❌ | `exam_questions`, `exam_answers`, `exam_options` |
| 9 | Auto-Submit | Timer triggers | Auto-evaluate | ❌ | `exam_attempts` |
| 10 | Maintenance | — | Sync + cleanup | ✅ | All |

---

## ✅ Key Insights

- **Frontend** manages navigation, timer, and API calls.
- **Backend** handles caching, persistence, and evaluation.
- **Memcached** is the live layer; **MySQL** is durable.
- **Progress never lost** thanks to hybrid cache-DB design.
- **Paginated review** ensures performance for large exams.
- **Background jobs** handle recovery, auto-submit, and cleanup.

---

**Maintained by:** Trogon LMS Development Team  
**Last Updated:** October 2025

