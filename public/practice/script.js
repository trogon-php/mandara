// Practice Exam Application
class PracticeExamApp {
    constructor() {
        this.currentPage = 'practice-selection-page';
        this.currentQuestion = 0;
        this.timeRemaining = 30 * 60; // 30 minutes for practice
        this.timerInterval = null;
        this.userAnswers = {};
        this.markedQuestions = new Set();
        this.selectedSubjects = new Set();
        this.selectedLessons = new Set();
        this.questionCount = 10;
        this.practiceQuestions = [];
        this.init();
    }

    init() {
        this.showPage('practice-selection-page');
        this.setupEventListeners();
        this.updateSelectedSummary();
    }

    setupEventListeners() {
        // Subject checkbox listeners
        document.querySelectorAll('.subject-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', (e) => {
                const subject = e.target.id.replace('-checkbox', '');
                if (e.target.checked) {
                    this.selectedSubjects.add(subject);
                    // Auto-select all lessons for this subject
                    this.selectAllLessonsForSubject(subject);
                } else {
                    this.selectedSubjects.delete(subject);
                    // Unselect all lessons for this subject
                    this.unselectAllLessonsForSubject(subject);
                }
                this.updateSelectedSummary();
                this.updateStartButton();
            });
        });

        // Lesson checkbox listeners
        document.querySelectorAll('.lesson-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', (e) => {
                const subject = e.target.dataset.subject;
                if (e.target.checked) {
                    this.selectedLessons.add(e.target.id);
                } else {
                    this.selectedLessons.delete(e.target.id);
                    // If no lessons selected for this subject, uncheck subject
                    if (!this.hasSelectedLessonsForSubject(subject)) {
                        document.getElementById(`${subject}-checkbox`).checked = false;
                        this.selectedSubjects.delete(subject);
                    }
                }
                this.updateSelectedSummary();
                this.updateStartButton();
            });
        });

        // Question count listeners
        document.querySelectorAll('input[name="question-count"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                this.questionCount = parseInt(e.target.value);
            });
        });

        // Question navigation
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('question-number')) {
                const questionNum = parseInt(e.target.textContent);
                this.goToQuestion(questionNum - 1);
            }
            
            if (e.target.classList.contains('grid-question')) {
                const questionNum = parseInt(e.target.textContent);
                this.goToQuestion(questionNum - 1);
                this.toggleQuestionPalette();
            }
        });
    }

    selectAllLessonsForSubject(subject) {
        document.querySelectorAll(`.lesson-checkbox[data-subject="${subject}"]`).forEach(checkbox => {
            checkbox.checked = true;
            this.selectedLessons.add(checkbox.id);
        });
    }

    unselectAllLessonsForSubject(subject) {
        document.querySelectorAll(`.lesson-checkbox[data-subject="${subject}"]`).forEach(checkbox => {
            checkbox.checked = false;
            this.selectedLessons.delete(checkbox.id);
        });
    }

    hasSelectedLessonsForSubject(subject) {
        return Array.from(this.selectedLessons).some(lessonId => 
            document.getElementById(lessonId)?.dataset.subject === subject
        );
    }

    updateSelectedSummary() {
        const container = document.getElementById('selected-topics');
        container.innerHTML = '';

        if (this.selectedLessons.size === 0) {
            container.innerHTML = '<p class="no-selection">No topics selected</p>';
            return;
        }

        this.selectedLessons.forEach(lessonId => {
            const checkbox = document.getElementById(lessonId);
            if (checkbox) {
                const label = checkbox.nextElementSibling.textContent;
                const subject = checkbox.dataset.subject;
                const topicElement = document.createElement('span');
                topicElement.className = 'selected-topic';
                topicElement.textContent = `${subject}: ${label}`;
                container.appendChild(topicElement);
            }
        });
    }

    updateStartButton() {
        const button = document.querySelector('.start-practice-btn');
        const hasSelection = this.selectedLessons.size > 0;
        button.disabled = !hasSelection;
    }

    showPage(pageId) {
        document.querySelectorAll('.page').forEach(page => {
            page.classList.remove('active');
        });
        
        document.getElementById(pageId).classList.add('active');
        this.currentPage = pageId;
        
        if (pageId === 'practice-exam-page') {
            this.renderQuestion();
            this.renderQuestionNumbers();
            this.startTimer();
        } else if (pageId === 'practice-results-page') {
            this.renderResults();
        } else if (pageId === 'answer-review-page') {
            this.renderAnswerReview();
        }
    }

    startTimer() {
        if (this.timerInterval) {
            clearInterval(this.timerInterval);
        }
        
        this.timerInterval = setInterval(() => {
            this.timeRemaining--;
            this.updateTimerDisplay();
            
            if (this.timeRemaining <= 0) {
                this.submitPracticeExam();
            }
        }, 1000);
    }

    updateTimerDisplay() {
        const minutes = Math.floor(this.timeRemaining / 60);
        const seconds = this.timeRemaining % 60;
        const timeString = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        
        const timerElement = document.getElementById('time-remaining');
        if (timerElement) {
            timerElement.textContent = timeString;
        }
    }

    generatePracticeQuestions() {
        // Generate questions based on selected lessons
        const questions = [];
        const questionTemplates = this.getQuestionTemplates();
        
        for (let i = 0; i < this.questionCount; i++) {
            const randomTemplate = questionTemplates[Math.floor(Math.random() * questionTemplates.length)];
            questions.push({
                id: i + 1,
                ...randomTemplate,
                subject: this.getRandomSubject(),
                lesson: this.getRandomLesson()
            });
        }
        
        return questions;
    }

    getQuestionTemplates() {
        return [
            {
                question: "Which of the following is the primary function of the cardiovascular system?",
                options: [
                    { label: "A", text: "Oxygen transport and circulation" },
                    { label: "B", text: "Digestion and nutrient absorption" },
                    { label: "C", text: "Waste elimination" },
                    { label: "D", text: "Hormone production" }
                ],
                correctAnswer: "A",
                explanation: "The cardiovascular system's primary function is to transport oxygen, nutrients, and waste products throughout the body via blood circulation."
            },
            {
                question: "What is the normal range for adult heart rate at rest?",
                options: [
                    { label: "A", text: "40-60 bpm" },
                    { label: "B", text: "60-100 bpm" },
                    { label: "C", text: "100-140 bpm" },
                    { label: "D", text: "140-180 bpm" }
                ],
                correctAnswer: "B",
                explanation: "Normal adult resting heart rate ranges from 60-100 beats per minute."
            },
            {
                question: "Which organ is responsible for filtering blood and removing waste products?",
                options: [
                    { label: "A", text: "Liver" },
                    { label: "B", text: "Kidneys" },
                    { label: "C", text: "Lungs" },
                    { label: "D", text: "Pancreas" }
                ],
                correctAnswer: "B",
                explanation: "The kidneys are responsible for filtering blood and removing waste products through the formation of urine."
            },
            {
                question: "What is the primary function of insulin?",
                options: [
                    { label: "A", text: "Increase blood glucose levels" },
                    { label: "B", text: "Decrease blood glucose levels" },
                    { label: "C", text: "Regulate blood pressure" },
                    { label: "D", text: "Control heart rate" }
                ],
                correctAnswer: "B",
                explanation: "Insulin's primary function is to decrease blood glucose levels by promoting glucose uptake into cells."
            },
            {
                question: "Which type of blood cell is responsible for carrying oxygen?",
                options: [
                    { label: "A", text: "White blood cells" },
                    { label: "B", text: "Red blood cells" },
                    { label: "C", text: "Platelets" },
                    { label: "D", text: "Plasma cells" }
                ],
                correctAnswer: "B",
                explanation: "Red blood cells contain hemoglobin and are responsible for carrying oxygen from the lungs to tissues."
            }
        ];
    }

    getRandomSubject() {
        const subjects = Array.from(this.selectedSubjects);
        return subjects[Math.floor(Math.random() * subjects.length)];
    }

    getRandomLesson() {
        const lessons = Array.from(this.selectedLessons);
        return lessons[Math.floor(Math.random() * lessons.length)];
    }

    renderQuestion() {
        const question = this.practiceQuestions[this.currentQuestion];
        if (!question) return;

        document.getElementById('question-text').textContent = question.question;

        const optionsContainer = document.getElementById('options-container');
        optionsContainer.innerHTML = '';

        question.options.forEach(option => {
            const optionElement = document.createElement('div');
            optionElement.className = 'option';
            optionElement.innerHTML = `
                <span class="option-label">${option.label}.</span>
                <span class="option-text">${option.text}</span>
            `;
            
            if (this.userAnswers[this.currentQuestion] === option.label) {
                optionElement.classList.add('selected');
            }
            
            optionElement.addEventListener('click', () => {
                this.selectOption(option.label);
            });
            
            optionsContainer.appendChild(optionElement);
        });
    }

    selectOption(optionLabel) {
        this.userAnswers[this.currentQuestion] = optionLabel;
        this.renderQuestion();
        this.updateQuestionNumbers();
    }

    renderQuestionNumbers() {
        const container = document.getElementById('question-numbers');
        container.innerHTML = '';

        this.practiceQuestions.forEach((_, index) => {
            const numberElement = document.createElement('div');
            numberElement.className = 'question-number';
            numberElement.textContent = index + 1;
            
            if (index === this.currentQuestion) {
                numberElement.classList.add('active');
            }
            
            if (this.userAnswers[index]) {
                numberElement.classList.add('answered');
            }
            
            if (this.markedQuestions.has(index)) {
                numberElement.classList.add('marked');
            }
            
            container.appendChild(numberElement);
        });
    }

    updateQuestionNumbers() {
        this.renderQuestionNumbers();
    }

    goToQuestion(questionIndex) {
        if (questionIndex >= 0 && questionIndex < this.practiceQuestions.length) {
            this.currentQuestion = questionIndex;
            this.renderQuestion();
            this.updateQuestionNumbers();
        }
    }

    nextQuestion() {
        if (this.currentQuestion < this.practiceQuestions.length - 1) {
            this.goToQuestion(this.currentQuestion + 1);
        } else {
            this.submitPracticeExam();
        }
    }

    previousQuestion() {
        if (this.currentQuestion > 0) {
            this.goToQuestion(this.currentQuestion - 1);
        }
    }

    toggleQuestionPalette() {
        const palette = document.getElementById('question-palette');
        const isVisible = palette.style.display !== 'none';
        
        if (isVisible) {
            palette.style.display = 'none';
        } else {
            this.renderQuestionGrid();
            palette.style.display = 'block';
        }
    }

    renderQuestionGrid() {
        const grid = document.getElementById('question-grid');
        grid.innerHTML = '';

        this.practiceQuestions.forEach((_, index) => {
            const gridElement = document.createElement('div');
            gridElement.className = 'grid-question';
            gridElement.textContent = index + 1;
            
            if (index === this.currentQuestion) {
                gridElement.classList.add('current');
            }
            
            if (this.userAnswers[index]) {
                gridElement.classList.add('answered');
            }
            
            if (this.markedQuestions.has(index)) {
                gridElement.classList.add('marked');
            }
            
            grid.appendChild(gridElement);
        });
    }

    submitPracticeExam() {
        this.calculateResults();
        this.showPage('practice-results-page');
        
        if (this.timerInterval) {
            clearInterval(this.timerInterval);
        }
    }

    calculateResults() {
        let correct = 0;
        let wrong = 0;
        
        this.practiceQuestions.forEach((question, index) => {
            const userAnswer = this.userAnswers[index];
            if (userAnswer) {
                if (userAnswer === question.correctAnswer) {
                    correct++;
                } else {
                    wrong++;
                }
            }
        });
        
        this.results = {
            total: this.practiceQuestions.length,
            correct,
            wrong,
            attempted: correct + wrong,
            unattempted: this.practiceQuestions.length - (correct + wrong),
            accuracy: Math.round((correct / (correct + wrong)) * 100) || 0,
            marksObtained: correct * 2 // 2 marks per correct answer for practice
        };
    }

    renderResults() {
        if (!this.results) return;

        document.getElementById('final-score').textContent = 
            `${this.results.correct} out of ${this.results.total}`;
        document.getElementById('total-questions').textContent = this.results.total;
        document.getElementById('marks-obtained').textContent = this.results.marksObtained;
        document.getElementById('accuracy-percentage').textContent = `${this.results.accuracy}%`;
        document.getElementById('accuracy-detail').textContent = 
            `${this.results.correct} Correct out of ${this.results.total} Questions`;
        document.getElementById('correct-count').textContent = this.results.correct;
        document.getElementById('wrong-count').textContent = this.results.wrong;
    }

    goBackToSelection() {
        this.currentQuestion = 0;
        this.timeRemaining = 30 * 60;
        this.userAnswers = {};
        this.markedQuestions.clear();
        this.results = null;
        
        if (this.timerInterval) {
            clearInterval(this.timerInterval);
        }
        
        this.showPage('practice-selection-page');
    }

    goBack() {
        if (this.currentPage === 'practice-exam-page' || this.currentPage === 'practice-results-page') {
            this.goBackToSelection();
        } else if (this.currentPage === 'answer-review-page') {
            this.showPage('practice-results-page');
        }
    }

    viewAnswers() {
        this.currentReviewQuestion = 0;
        this.showPage('answer-review-page');
        this.renderAnswerReview();
    }

    renderAnswerReview() {
        const question = this.practiceQuestions[this.currentReviewQuestion];
        if (!question) return;

        document.getElementById('review-question-text').textContent = question.question;
        document.getElementById('answer-progress').textContent = 
            `${this.currentReviewQuestion + 1}/${this.practiceQuestions.length}`;

        const optionsContainer = document.getElementById('review-options-container');
        optionsContainer.innerHTML = '';

        question.options.forEach(option => {
            const optionElement = document.createElement('div');
            optionElement.className = 'option';
            
            // Add classes based on answer status
            if (option.label === question.correctAnswer) {
                optionElement.classList.add('correct');
            } else if (this.userAnswers[this.currentReviewQuestion] === option.label) {
                optionElement.classList.add('incorrect');
            }
            
            optionElement.innerHTML = `
                <span class="option-label">${option.label}.</span>
                <span class="option-text">${option.text}</span>
            `;
            
            optionsContainer.appendChild(optionElement);
        });

        // Update solutions section
        const correctOption = question.options.find(opt => opt.label === question.correctAnswer);
        document.querySelector('.answer-text').textContent = correctOption ? correctOption.text : 'N/A';
        document.querySelector('.explanation p').textContent = question.explanation;
    }

    nextAnswer() {
        if (this.currentReviewQuestion < this.practiceQuestions.length - 1) {
            this.currentReviewQuestion++;
            this.renderAnswerReview();
        }
    }

    previousAnswer() {
        if (this.currentReviewQuestion > 0) {
            this.currentReviewQuestion--;
            this.renderAnswerReview();
        }
    }
}

// Global functions for HTML onclick handlers
function toggleSubject(subjectId) {
    const lessonsContainer = document.getElementById(`${subjectId}-lessons`);
    const arrow = document.getElementById(`${subjectId}-arrow`);
    
    lessonsContainer.classList.toggle('expanded');
    arrow.classList.toggle('expanded');
}

function startPracticeExam() {
    if (practiceApp.selectedLessons.size === 0) {
        alert('Please select at least one lesson to start the practice exam.');
        return;
    }
    
    practiceApp.practiceQuestions = practiceApp.generatePracticeQuestions();
    practiceApp.showPage('practice-exam-page');
}

function nextQuestion() {
    practiceApp.nextQuestion();
}

function previousQuestion() {
    practiceApp.previousQuestion();
}

function toggleQuestionPalette() {
    practiceApp.toggleQuestionPalette();
}

function viewAnswers() {
    practiceApp.viewAnswers();
}

function goBackToSelection() {
    practiceApp.goBackToSelection();
}

function goBack() {
    practiceApp.goBack();
}

function nextAnswer() {
    practiceApp.nextAnswer();
}

function previousAnswer() {
    practiceApp.previousAnswer();
}

// Initialize the practice exam application
let practiceApp;

document.addEventListener('DOMContentLoaded', () => {
    practiceApp = new PracticeExamApp();
});

// Add keyboard navigation
document.addEventListener('keydown', (e) => {
    if (practiceApp.currentPage === 'practice-exam-page') {
        switch(e.key) {
            case 'ArrowLeft':
                previousQuestion();
                break;
            case 'ArrowRight':
                nextQuestion();
                break;
            case '1':
            case '2':
            case '3':
            case '4':
                practiceApp.selectOption(e.key);
                break;
        }
    } else if (practiceApp.currentPage === 'answer-review-page') {
        switch(e.key) {
            case 'ArrowLeft':
                previousAnswer();
                break;
            case 'ArrowRight':
                nextAnswer();
                break;
        }
    }
});

// Add touch/swipe gestures for mobile
let touchStartX = 0;
let touchEndX = 0;

document.addEventListener('touchstart', (e) => {
    touchStartX = e.changedTouches[0].screenX;
});

document.addEventListener('touchend', (e) => {
    touchEndX = e.changedTouches[0].screenX;
    handleSwipe();
});

function handleSwipe() {
    const swipeThreshold = 50;
    const diff = touchStartX - touchEndX;
    
    if (Math.abs(diff) > swipeThreshold) {
        if (diff > 0) {
            // Swipe left - next question
            if (practiceApp.currentPage === 'practice-exam-page') {
                nextQuestion();
            }
        } else {
            // Swipe right - previous question
            if (practiceApp.currentPage === 'practice-exam-page') {
                previousQuestion();
            }
        }
    }
}
