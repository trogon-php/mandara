// Exam Data and State Management
class ExamApp {
    constructor() {
        this.currentPage = 'exam-details-page';
        this.currentQuestion = 0;
        this.timeRemaining = 35 * 60; // 35 minutes in seconds
        this.timerInterval = null;
        this.userAnswers = {};
        this.markedQuestions = new Set();
        this.examData = this.generateExamData();
        this.init();
    }

    generateExamData() {
        const questions = [
            {
                id: 1,
                question: "Who is the current Chief Election Commissioner of India as of 2025?",
                options: [
                    { label: "A", text: "Rajiv Kumar" },
                    { label: "B", text: "Sushil Chandra" },
                    { label: "C", text: "Arun Goel" },
                    { label: "D", text: "Sunil Arora" }
                ],
                correctAnswer: "A",
                explanation: "Rajiv Kumar is the current Chief Election Commissioner of India as of 2025. He was appointed to this position and has been serving in this capacity."
            },
            {
                id: 2,
                question: "What is the capital of Australia?",
                options: [
                    { label: "A", text: "Sydney" },
                    { label: "B", text: "Melbourne" },
                    { label: "C", text: "Canberra" },
                    { label: "D", text: "Perth" }
                ],
                correctAnswer: "C",
                explanation: "Canberra is the capital city of Australia. While Sydney and Melbourne are larger cities, Canberra was specifically designed to be the capital."
            },
            {
                id: 3,
                question: "Which programming language is known for its use in web development?",
                options: [
                    { label: "A", text: "Python" },
                    { label: "B", text: "JavaScript" },
                    { label: "C", text: "C++" },
                    { label: "D", text: "Java" }
                ],
                correctAnswer: "B",
                explanation: "JavaScript is widely used in web development for both frontend and backend development, making it essential for modern web applications."
            },
            {
                id: 4,
                question: "What is the largest planet in our solar system?",
                options: [
                    { label: "A", text: "Earth" },
                    { label: "B", text: "Jupiter" },
                    { label: "C", text: "Saturn" },
                    { label: "D", text: "Neptune" }
                ],
                correctAnswer: "B",
                explanation: "Jupiter is the largest planet in our solar system, with a mass greater than all other planets combined."
            },
            {
                id: 5,
                question: "Which company developed the iPhone?",
                options: [
                    { label: "A", text: "Samsung" },
                    { label: "B", text: "Google" },
                    { label: "C", text: "Apple" },
                    { label: "D", text: "Microsoft" }
                ],
                correctAnswer: "C",
                explanation: "Apple Inc. developed and manufactures the iPhone, which was first introduced in 2007 by Steve Jobs."
            }
        ];

        // Generate more questions to reach 20 total
        const additionalQuestions = [];
        for (let i = 6; i <= 20; i++) {
            additionalQuestions.push({
                id: i,
                question: `This is question number ${i}. What is the correct answer?`,
                options: [
                    { label: "A", text: "Option A" },
                    { label: "B", text: "Option B" },
                    { label: "C", text: "Option C" },
                    { label: "D", text: "Option D" }
                ],
                correctAnswer: "A",
                explanation: `This is the explanation for question ${i}. The correct answer is A because it makes the most sense in this context.`
            });
        }

        return [...questions, ...additionalQuestions];
    }

    init() {
        this.showPage('exam-details-page');
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Timer setup
        this.updateTimerDisplay();
        
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

    showPage(pageId) {
        // Hide all pages
        document.querySelectorAll('.page').forEach(page => {
            page.classList.remove('active');
        });
        
        // Show target page
        document.getElementById(pageId).classList.add('active');
        this.currentPage = pageId;
        
        // Initialize page-specific content
        if (pageId === 'exam-page') {
            this.renderQuestion();
            this.renderQuestionNumbers();
            this.startTimer();
        } else if (pageId === 'results-page') {
            this.renderResults();
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
                this.submitExam();
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

    renderQuestion() {
        const question = this.examData[this.currentQuestion];
        if (!question) return;

        // Update question text
        document.getElementById('question-text').textContent = question.question;

        // Render options
        const optionsContainer = document.getElementById('options-container');
        optionsContainer.innerHTML = '';

        question.options.forEach(option => {
            const optionElement = document.createElement('div');
            optionElement.className = 'option';
            optionElement.innerHTML = `
                <span class="option-label">${option.label}.</span>
                <span class="option-text">${option.text}</span>
            `;
            
            // Check if this option is selected
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
        this.renderQuestion(); // Re-render to show selection
        this.updateQuestionNumbers();
    }

    renderQuestionNumbers() {
        const container = document.getElementById('question-numbers');
        container.innerHTML = '';

        this.examData.forEach((_, index) => {
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
        if (questionIndex >= 0 && questionIndex < this.examData.length) {
            this.currentQuestion = questionIndex;
            this.renderQuestion();
            this.updateQuestionNumbers();
        }
    }

    nextQuestion() {
        if (this.currentQuestion < this.examData.length - 1) {
            this.goToQuestion(this.currentQuestion + 1);
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

        this.examData.forEach((_, index) => {
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

    showSubmitDialog() {
        const attempted = Object.keys(this.userAnswers).length;
        const unattempted = this.examData.length - attempted;
        
        document.getElementById('attempted-count').textContent = attempted;
        document.getElementById('unattempted-count').textContent = unattempted;
        document.getElementById('submit-dialog').style.display = 'block';
    }

    closeSubmitDialog() {
        document.getElementById('submit-dialog').style.display = 'none';
    }

    submitExam() {
        this.closeSubmitDialog();
        this.calculateResults();
        this.showPage('results-page');
        
        if (this.timerInterval) {
            clearInterval(this.timerInterval);
        }
    }

    calculateResults() {
        let correct = 0;
        let wrong = 0;
        
        this.examData.forEach((question, index) => {
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
            total: this.examData.length,
            correct,
            wrong,
            attempted: correct + wrong,
            unattempted: this.examData.length - (correct + wrong),
            accuracy: Math.round((correct / (correct + wrong)) * 100) || 0,
            marksObtained: correct * 3 // 3 marks per correct answer
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

    viewAnswers() {
        this.currentReviewQuestion = 0;
        this.showPage('answers-page');
        this.renderAnswerReview();
    }

    renderAnswerReview() {
        const question = this.examData[this.currentReviewQuestion];
        if (!question) return;

        document.getElementById('review-question-text').textContent = question.question;
        document.getElementById('answer-progress').textContent = 
            `${this.currentReviewQuestion + 1}/${this.examData.length}`;

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
    }

    nextAnswer() {
        if (this.currentReviewQuestion < this.examData.length - 1) {
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

    retakeExam() {
        // Reset exam state
        this.currentQuestion = 0;
        this.timeRemaining = 35 * 60;
        this.userAnswers = {};
        this.markedQuestions.clear();
        this.results = null;
        
        if (this.timerInterval) {
            clearInterval(this.timerInterval);
        }
        
        this.showPage('exam-details-page');
    }

    goBack() {
        if (this.currentPage === 'exam-page') {
            this.showPage('exam-details-page');
        } else if (this.currentPage === 'results-page' || this.currentPage === 'answers-page') {
            this.showPage('exam-details-page');
        }
    }
}

// Global functions for HTML onclick handlers
function startExam() {
    examApp.showPage('exam-page');
}

function nextQuestion() {
    examApp.nextQuestion();
}

function previousQuestion() {
    examApp.previousQuestion();
}

function toggleQuestionPalette() {
    examApp.toggleQuestionPalette();
}

function showSubmitDialog() {
    examApp.showSubmitDialog();
}

function closeSubmitDialog() {
    examApp.closeSubmitDialog();
}

function submitExam() {
    examApp.submitExam();
}

function viewAnswers() {
    examApp.viewAnswers();
}

function nextAnswer() {
    examApp.nextAnswer();
}

function previousAnswer() {
    examApp.previousAnswer();
}

function retakeExam() {
    examApp.retakeExam();
}

function goBack() {
    examApp.goBack();
}

// Initialize the exam application
let examApp;

document.addEventListener('DOMContentLoaded', () => {
    examApp = new ExamApp();
    
    // Add submit button functionality
    const nextBtn = document.querySelector('.next-btn');
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            if (examApp.currentQuestion === examApp.examData.length - 1) {
                showSubmitDialog();
            } else {
                nextQuestion();
            }
        });
    }
});

// Add keyboard navigation
document.addEventListener('keydown', (e) => {
    if (examApp.currentPage === 'exam-page') {
        switch(e.key) {
            case 'ArrowLeft':
                previousQuestion();
                break;
            case 'ArrowRight':
                if (examApp.currentQuestion === examApp.examData.length - 1) {
                    showSubmitDialog();
                } else {
                    nextQuestion();
                }
                break;
            case '1':
            case '2':
            case '3':
            case '4':
                examApp.selectOption(e.key);
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
            if (examApp.currentPage === 'exam-page') {
                if (examApp.currentQuestion === examApp.examData.length - 1) {
                    showSubmitDialog();
                } else {
                    nextQuestion();
                }
            }
        } else {
            // Swipe right - previous question
            if (examApp.currentPage === 'exam-page') {
                previousQuestion();
            }
        }
    }
}


