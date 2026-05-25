<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Head, Link } from '@inertiajs/vue3';
import LatexRenderer from '@/Components/LatexRenderer.vue';
import { ref, computed, watch, nextTick } from 'vue';
const imageUrl = ref('/storage/img/logo.png');
const settingUrl = ref('/storage/img/setting.png');
import { usePage } from '@inertiajs/vue3';

const { props } = usePage()
const data = ref(props.bloglist)

// Get Bengali character for option index
const bengaliChars = ['ক', 'খ', 'গ', 'ঘ', 'ঙ', 'চ', 'ছ', 'জ', 'ঝ', 'ঞ', 'ট', 'ঠ', 'ড', 'ঢ', 'ণ', 'ত', 'থ', 'দ', 'ধ', 'ন', 'প', 'ফ', 'ব', 'ভ', 'ম', 'য', 'র', 'ল', 'শ', 'ষ', 'স', 'হ', 'ড়', 'ঢ়', 'য়'];

// Track the active category for filtering
const selectedCategory = ref('all');

// Pagination
const itemsPerPage = 9;
const currentPage = ref(1);

// Modal visibility state
const showModal = ref(false);
const showResultModal = ref(false);
const selectedExam = ref(null);
const examResults = ref(null);

// Track selected answers and their correctness
const selectedAnswers = ref({});
const answerStatus = ref({});

// Function to open the modal with selected exam data
const openExamModal = (exam) => {
  selectedExam.value = exam;
  showModal.value = true;
  // Prevent body scroll when modal is open
  document.body.style.overflow = 'hidden';
  
  // Reset selections
  selectedAnswers.value = {};
  answerStatus.value = {};
  isExamSubmitted.value = false;
  
  // Reset and start timer after modal is shown
  nextTick(() => {
    resetTimer();
    startTimer();
  });
};

// Function to close the modal
const closeExamModal = () => {
  showModal.value = false;
  selectedExam.value = null;
  // Re-enable body scroll
  document.body.style.overflow = '';
  
  // Stop timer
  if (timerInterval.value) {
    clearInterval(timerInterval.value);
    timerInterval.value = null;
  }
};

// Function to close result modal
const closeResultModal = () => {
  showResultModal.value = false;
  examResults.value = null;
  document.body.style.overflow = '';
};

// Handle answer selection - radio button behavior (only one option per question)
const selectAnswer = (questionIndex, optionId, isCorrect) => {
  const answerKey = `q${questionIndex}`;
  
  // Store the selected answer (this automatically deselects any previously selected option)
  selectedAnswers.value[answerKey] = optionId;
  
  // Store answer status
  answerStatus.value[answerKey] = {
    isCorrect: isCorrect,
    selectedOptionId: optionId
  };
};

// Check if an option is selected
const isOptionSelected = (questionIndex, optionId) => {
  return selectedAnswers.value[`q${questionIndex}`] === optionId;
};

// Check if any option is selected for a question (to disable others)
const isAnyOptionSelected = (questionIndex) => {
  return selectedAnswers.value[`q${questionIndex}`] !== undefined;
};

// Get option style class
// Get option style class - NO pre-highlighting of correct answers
const getOptionClass = (questionIndex, optionId, isCorrect, isSelected) => {
  const answerKey = `q${questionIndex}`;
  const status = answerStatus.value[answerKey];
  
  // If this option is selected
  if (isSelected) {
    return status?.isCorrect ? 'option-correct-selected' : 'option-wrong-selected';
  }
  
  // AFTER wrong answer selected, show the correct answer in light green
  if (status && !status.isCorrect && isCorrect) {
    return 'option-correct-highlight';
  }
  
  return 'option-default';
};

// Compute exam details for the modal
const modalExamDetails = computed(() => {
  if (!selectedExam.value) return null;
  
  const exam = selectedExam.value;
  const totalQuestions = exam.questions?.length || 0;
  const totalMarks = totalQuestions;
  const durationMinutes = Math.ceil(totalQuestions / 2);
  
  return {
    title: exam.title,
    totalQuestions: totalQuestions,
    totalMarks: totalMarks,
    durationMinutes: durationMinutes > 60 ? `${Math.floor(durationMinutes / 60)} Hours` : `${durationMinutes} Minutes`,
    durationSeconds: durationMinutes > 60 ? Math.floor(durationMinutes / 60) * 60 * 60 : durationMinutes * 60,
    questions: exam.questions || []
  };
});

// Timer functionality
const timeLeft = ref(1800);
const timerInterval = ref(null);
const isExamSubmitted = ref(false);

// Format time as MM:SS
const formattedTime = computed(() => {
  const minutes = Math.floor(timeLeft.value / 60);
  const seconds = timeLeft.value % 60;
  return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
});

// Timer progress percentage
const timerProgress = computed(() => {
  const totalSeconds = modalExamDetails.value?.durationSeconds || 1800;
  if (totalSeconds <= 0) return 0;
  const progress = (timeLeft.value / totalSeconds) * 100;
  return Math.max(0, Math.min(100, progress));
});

// Start the timer
const startTimer = () => {
  if (timerInterval.value) {
    clearInterval(timerInterval.value);
    timerInterval.value = null;
  }
  
  timerInterval.value = setInterval(() => {
    if (timeLeft.value > 0 && !isExamSubmitted.value && showModal.value) {
      timeLeft.value--;
    } else if (timeLeft.value <= 0 && !isExamSubmitted.value && showModal.value) {
      submitExam();
    }
  }, 1000);
};

// Reset timer
const resetTimer = () => {
  if (timerInterval.value) {
    clearInterval(timerInterval.value);
    timerInterval.value = null;
  }
  
  const totalSeconds = modalExamDetails.value?.durationSeconds || 1800;
  timeLeft.value = totalSeconds;
  isExamSubmitted.value = false;
};

// Calculate detailed results
const calculateResults = () => {
  const questions = modalExamDetails.value?.questions || [];
  let correctCount = 0;
  let wrongCount = 0;
  let unattemptedCount = 0;
  const questionResults = [];
  
  questions.forEach((question, index) => {
    const answerKey = `q${index}`;
    const selectedOptionId = selectedAnswers.value[answerKey];
    const status = answerStatus.value[answerKey];
    
    // Find the selected option text
    let selectedOptionText = 'Not attempted';
    if (selectedOptionId && question.options) {
      const selectedOpt = question.options.find(opt => opt.id === selectedOptionId);
      if (selectedOpt) {
        selectedOptionText = selectedOpt.option_text;
      }
    }
    
    // Find correct option text
    const correctOption = question.options?.find(opt => opt.is_correct);
    const correctOptionText = correctOption ? correctOption.option_text : 'N/A';
    
    if (!selectedOptionId) {
      unattemptedCount++;
      questionResults.push({
        questionText: question.question_text,
        selectedAnswer: 'Not attempted',
        correctAnswer: correctOptionText,
        isCorrect: false,
        isAttempted: false
      });
    } else if (status?.isCorrect) {
      correctCount++;
      questionResults.push({
        questionText: question.question_text,
        selectedAnswer: selectedOptionText,
        correctAnswer: correctOptionText,
        isCorrect: true,
        isAttempted: true
      });
    } else {
      wrongCount++;
      questionResults.push({
        questionText: question.question_text,
        selectedAnswer: selectedOptionText,
        correctAnswer: correctOptionText,
        isCorrect: false,
        isAttempted: true
      });
    }
  });
  
  const totalQuestions = questions.length;
  const marksObtained = correctCount;
  const totalMarks = totalQuestions;
  const percentage = totalMarks > 0 ? (marksObtained / totalMarks) * 100 : 0;
  let grade = '';
  let status = '';
  
  if (percentage >= 80) {
    grade = 'A+';
    status = 'Excellent!';
  } else if (percentage >= 70) {
    grade = 'A';
    status = 'Very Good!';
  } else if (percentage >= 60) {
    grade = 'A-';
    status = 'Good!';
  } else if (percentage >= 50) {
    grade = 'B';
    status = 'Satisfactory';
  } else if (percentage >= 40) {
    grade = 'C';
    status = 'Need Improvement';
  } else {
    grade = 'F';
    status = 'Better Luck Next Time';
  }
  
  return {
    correctCount,
    wrongCount,
    unattemptedCount,
    totalQuestions,
    marksObtained,
    totalMarks,
    percentage: percentage.toFixed(2),
    grade,
    status,
    questionResults
  };
};

// Submit exam function
const submitExam = () => {
  if (isExamSubmitted.value) return;
  
  isExamSubmitted.value = true;
  
  if (timerInterval.value) {
    clearInterval(timerInterval.value);
    timerInterval.value = null;
  }
  
  // Calculate results
  const results = calculateResults();
  examResults.value = results;
  
  // Close exam modal and open result modal
  showModal.value = false;
  showResultModal.value = true;
  
  // Keep body scroll prevented
  document.body.style.overflow = 'hidden';
};

// Get unique categories from the data
const categories = computed(() => {
  const uniqueCategories = new Set();
  uniqueCategories.add('all');
  data.value.forEach(item => {
    if (item.category && item.category.name) {
      uniqueCategories.add(item.category.name);
    }
  });
  return Array.from(uniqueCategories);
});

// Filtered exams based on selected category
const filteredExams = computed(() => {
  if (selectedCategory.value === 'all') {
    return data.value;
  }
  return data.value.filter(item =>
    item.category && item.category.name === selectedCategory.value
  );
});

// Paginated exams
const paginatedExams = computed(() => {
  const start = 0;
  const end = currentPage.value * itemsPerPage;
  return filteredExams.value.slice(start, end);
});

// Check if there are more items to load
const hasMoreItems = computed(() => {
  return paginatedExams.value.length < filteredExams.value.length;
});

// Load more items
const loadMore = () => {
  currentPage.value++;
};

// Reset pagination when category changes
const setCategory = (category) => {
  selectedCategory.value = category;
  currentPage.value = 1;
};

// Format date function
const formatDate = (dateString) => {
  if (!dateString) return 'N/A';

  const date = new Date(dateString);
  if (isNaN(date.getTime())) return dateString;

  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
};
</script>

<template>
  <AppLayout>
  <Head title="Welcome" />
  <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50 hind-siliguri-light">
    <section class="relative py-16 bg-gray-50 overflow-hidden">
      <!-- Gradient Background Elements -->
      <div aria-hidden="true" class="absolute inset-0 grid grid-cols-2 -space-x-52 opacity-40 dark:opacity-20">
        <div class="blur-[106px] h-56 bg-gradient-to-br from-blue-600 to-purple-400 dark:from-blue-700"></div>
        <div class="blur-[106px] h-32 bg-gradient-to-r from-blue-600 to-sky-300 dark:to-indigo-600"></div>
      </div>

      <!-- Main Content -->
      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="text-center mb-16">
          <div
            class="gap-2 inline-flex items-center justify-center px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 mb-4">
            <img :src="imageUrl" class="h-5 w-5 md:h-5 md:w-5 object-contain" alt="">
            <span class="text-sm font-medium">learnandteach.app</span>
          </div>

          <div>
            <h2 class="text-4xl font-bold text-gray-900 mb-4 relative inline-block">
              <span class="relative">
                <svg viewBox="0 0 52 24" fill="currentColor"
                  class="absolute top-0 left-0 z-0 hidden w-32 -mt-8 -ml-20 text-gray-400 lg:w-32 lg:-ml-28 lg:-mt-10 sm:block">
                  <defs>
                    <pattern id="dc223fcc-6d72-4ebc-b4ef-abe121034d6e" x="0" y="0" width=".135" height=".30">
                      <circle cx="1" cy="1" r=".7"></circle>
                    </pattern>
                  </defs>
                  <rect fill="url(#dc223fcc-6d72-4ebc-b4ef-abe121034d6e)" width="52" height="24"></rect>
                </svg>
                <span class="relative">ফ্রিতে ক্লিকেই</span>
              </span> প্রশ্ন রেডি
            </h2>
          </div>

          <p class="text-lg text-gray-600 max-w-2xl mx-auto">
            স্কুল, কলেজ ও মাদ্ররাসার শিশু থেকে দ্বাদশ শ্রেণির যেকোন প্রশ্ন তৈরি করুন ক্লিকেই | শিক্ষক ও শিক্ষার্থীদের
            জন্য অসাধারণ একটি সফটওয়্যার।
          </p>
        </div>

        <!-- Call to Action Buttons -->
        <div class="flex flex-col space-y-4 sm:flex-row sm:space-y-0 sm:space-x-6 justify-center">
          <Link :href="route('login')">
            <button
              class="relative px-6 py-3 bg-blue-600 text-white font-semibold rounded-full transition-all duration-300 hover:scale-105 active:scale-95">
              <span class="relative">প্রশ্ন তৈরি করুন</span>
            </button>
          </Link>

          <button
            class="relative px-6 py-3 bg-white text-blue-600 font-semibold border border-gray-200 rounded-full transition-all duration-300 hover:scale-105 active:scale-95">
            <span class="relative">অনলাইন পরীক্ষা</span>
          </button>
        </div>

        <!-- Statistics Cards -->
        <div class="mt-20 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 max-w-5xl mx-auto">
          <!-- Card 1 -->
          <div class="bg-white p-8 rounded-lg shadow-md text-center">
            <div class="w-12 h-12 mx-auto mb-4 rounded-full bg-indigo-50 flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="text-indigo-600">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                <path d="M12 17h.01"></path>
              </svg>
            </div>
            <p class="font-bold text-gray-800">১০০০০+ শিক্ষা প্রতিষ্ঠান যুক্ত</p>
          </div>

          <!-- Card 2 -->
          <div class="bg-white p-8 rounded-lg shadow-md text-center">
            <div class="w-12 h-12 mx-auto mb-4 rounded-full bg-indigo-50 flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="text-indigo-600">
                <path d="M12 7v14"></path>
                <path d="M16 12h2"></path>
                <path d="M16 8h2"></path>
                <path
                  d="M3 18a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h5a4 4 0 0 1 4 4 4 4 0 0 1 4-4h5a1 1 0 0 1 1 1v13a1 1 0 0 1 1 1h-6a3 3 0 0 0-3 3 3 3 0 0 0-3-3z">
                </path>
                <path d="M6 12h2"></path>
                <path d="M6 8h2"></path>
              </svg>
            </div>
            <p class="font-bold text-gray-800">২১৩+ বিষয়</p>
          </div>

          <!-- Card 3 -->
          <div class="bg-white p-8 rounded-lg shadow-md text-center">
            <div class="w-12 h-12 mx-auto mb-4 rounded-full bg-indigo-50 flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="text-indigo-600">
                <path d="M12 21V7"></path>
                <path d="m16 12 2 2 4-4"></path>
                <path
                  d="M22 6V4a1 1 0 0 0-1-1h-5a4 4 0 0 0-4 4 4 4 0 0 0-4-4H3a1 1 0 0 0-1 1v13a1 1 0 0 0 1 1h6a3 3 0 0 1 3 3 3 3 0 0 1 3-3h6a1 1 0 0 0 1-1v-1.3">
                </path>
              </svg>
            </div>
            <p class="font-bold text-gray-800">৭০০+ প্রশ্ন তৈরী করছে প্রতিদিন </p>
          </div>

          <!-- Card 4 -->
          <div class="bg-white p-8 rounded-lg shadow-md text-center">
            <div class="w-12 h-12 mx-auto mb-4 rounded-full bg-indigo-50 flex items-center justify-center">
              <svg stroke="currentColor" fill="currentColor" stroke-width="2" viewBox="0 0 256 256"
                class="text-indigo-600" height="24" width="24" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M226.53,56.41l-96-32a8,8,0,0,0-5.06,0l-96,32A8,8,0,0,0,24,64v80a8,8,0,0,0,16,0V75.1L73.59,86.29a64,64,0,0,0,20.65,88.05c-18,7.06-33.56,19.83-44.94,37.29a8,8,0,1,0,13.4,8.74C77.77,197.25,101.57,184,128,184s50.23,13.25,65.3,36.37a8,8,0,0,0,13.4-8.74c-11.38-17.46-27-30.23-44.94-37.29a64,64,0,0,0,20.65-88l44.12-14.7a8,8,0,0,0,0-15.18ZM176,120A48,48,0,1,1,89.35,91.55l36.12,12a8,8,0,0,0,5.06,0l36.12-12A47.89,47.89,0,0,1,176,120ZM128,87.57,57.3,64,128,40.43,198.7,64Z">
                </path>
              </svg>
            </div>
            <p class="font-bold text-gray-800">৩০k+ শিক্ষক যুক্ত</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Premium Features Section - White Background Version -->
    <section class="relative py-20 bg-white overflow-hidden">
      <!-- Animated Background Elements -->
      <div class="absolute inset-0 overflow-hidden">
        <div
          class="absolute top-1/4 -left-20 w-96 h-96 bg-indigo-100 rounded-full filter blur-3xl opacity-70 animate-float">
        </div>
        <div
          class="absolute bottom-1/3 -right-20 w-80 h-80 bg-blue-100 rounded-full filter blur-3xl opacity-70 animate-float-delay">
        </div>
        <div
          class="absolute top-1/2 left-1/2 w-64 h-64 bg-purple-100 rounded-full filter blur-3xl opacity-50 animate-pulse">
        </div>
      </div>

      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header with Colorful Accents -->
        <div class="text-center mb-20">
          <span
            class="inline-block px-6 py-2 bg-gradient-to-r from-blue-100 to-indigo-100 text-indigo-800 rounded-full text-sm font-medium mb-6 border border-indigo-200 shadow-sm">
            শিক্ষকদের ডিজিটাল অস্ত্র
          </span>
          <h2 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-6">
            <span
              class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600 animate-text-shine">এক
              ক্লিকেই</span> তৈরি করুন প্রশ্নপত্র
          </h2>
          <p class="text-lg text-gray-600 max-w-3xl mx-auto font-medium">
            সকল শ্রেণির সকল বিষয়ের <span class="text-indigo-600 font-bold">লাখ লাখ প্রশ্নের ডাটাবেজ</span>। টাইপিং বা
            প্রুফ রিডিংয়ের ঝামেলা নেই!
          </p>
        </div>

        <div class="flex flex-col lg:flex-row gap-12 items-center">
          <!-- Features List -->
          <div class="lg:w-1/2 space-y-8">
            <!-- Feature 1 - Colorful Card -->
            <div
              class="feature-card group relative bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl border border-gray-100 transition-all duration-300 hover:-translate-y-2">
              <div
                class="absolute -top-4 -left-4 w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center text-3xl font-bold text-white shadow-md">
                ১
              </div>
              <div class="pl-12">
                <h3 class="text-2xl font-bold text-gray-800 mb-2"> ৩০k+ শিক্ষক</h3>
                <p class="text-gray-600">নিয়মিত ব্যবহার করছেন আমাদের প্ল্যাটফর্ম</p>
              </div>
              <div
                class="absolute bottom-4 right-4 w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center group-hover:bg-blue-200 transition-colors duration-300">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7">
                  </path>
                </svg>
              </div>
            </div>

            <!-- Feature 2 - Colorful Card -->
            <div
              class="feature-card group relative bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl border border-gray-100 transition-all duration-300 hover:-translate-y-2">
              <div
                class="absolute -top-4 -left-4 w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center text-3xl font-bold text-white shadow-md">
                ২
              </div>
              <div class="pl-12">
                <h3 class="text-2xl font-bold text-gray-800 mb-2">এক ক্লিকে তৈরি</h3>
                <p class="text-gray-600">প্রশ্নপত্র, ওয়ার্কশীট, সাজেশন এবং অনলাইন পরীক্ষা</p>
              </div>
              <div
                class="absolute bottom-4 right-4 w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center group-hover:bg-purple-200 transition-colors duration-300">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7">
                  </path>
                </svg>
              </div>
            </div>

            <!-- Feature 3 - Colorful Card -->
            <div
              class="feature-card group relative bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl border border-gray-100 transition-all duration-300 hover:-translate-y-2">
              <div
                class="absolute -top-4 -left-4 w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center text-3xl font-bold text-white shadow-md">
                ৩
              </div>
              <div class="pl-12">
                <h3 class="text-2xl font-bold text-gray-800 mb-2">১০ লক্ষাধিক প্রশ্ন</h3>
                <p class="text-gray-600">নির্ভুল ও ইউনিক প্রশ্নের বিশাল ডাটাবেজ</p>
              </div>
              <div
                class="absolute bottom-4 right-4 w-10 h-10 bg-green-100 rounded-full flex items-center justify-center group-hover:bg-green-200 transition-colors duration-300">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7">
                  </path>
                </svg>
              </div>
            </div>

            <!-- Feature 4 - Colorful Card -->
            <div
              class="feature-card group relative bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl border border-gray-100 transition-all duration-300 hover:-translate-y-2">
              <div
                class="absolute -top-4 -left-4 w-16 h-16 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-2xl flex items-center justify-center text-3xl font-bold text-white shadow-md">
                ৪
              </div>
              <div class="pl-12">
                <h3 class="text-2xl font-bold text-gray-800 mb-2">ঝামেলামুক্ত</h3>
                <p class="text-gray-600">টাইপিং ও প্রুফ রিডিংয়ের কোনো ঝামেলা নেই</p>
              </div>
              <div
                class="absolute bottom-4 right-4 w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center group-hover:bg-yellow-200 transition-colors duration-300">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7">
                  </path>
                </svg>
              </div>
            </div>
          </div>

          <!-- Image/Video Section -->
          <div class="lg:w-1/2 relative z-10">
            <div
              class="relative rounded-3xl overflow-hidden shadow-xl border border-blue-600 transform hover:-translate-y-2 transition-transform duration-500">
              <div class="absolute inset-0 bg-gradient-to-br from-blue-100/50 to-indigo-100/50 rounded-3xl"></div>
              <img :src="settingUrl" alt="Question Creation Interface"
                class="w-full h-auto rounded-3xl transform hover:scale-102 transition-transform duration-700">
              <div
                class="absolute -bottom-4 -right-4 w-20 h-20 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition-transform duration-300">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z">
                  </path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Call to Action Buttons -->
        <div class="flex flex-col mt-10 space-y-4 sm:flex-row sm:space-y-0 sm:space-x-6 justify-center">
          <Link :href="route('login')">
            <button
              class="relative px-6 py-3 bg-blue-600 text-white font-semibold rounded-full transition-all duration-300 hover:scale-105 active:scale-95">
              <span class="relative">প্রশ্ন তৈরি করা শুরু করুন</span>
            </button>
          </Link>
        </div>

        <!-- Video Demo Section - Increased Height -->
        <div class="mt-24 max-w-4xl mx-auto relative">
          <div class="relative bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-200">
            <div class="aspect-w-16 aspect-h-9 h-[500px]">
              <iframe class="w-full h-full" src="https://www.youtube-nocookie.com/embed/xDzQTYPSVXU?si=CVZPW8qj5h-5xRtT"
                title="YouTube video player"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"></iframe>
            </div>
            <div class="p-6 bg-gradient-to-r from-blue-50 to-indigo-50">
              <h3 class="text-2xl font-bold text-gray-800 mb-2 flex items-center">
                <svg class="w-6 h-6 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z">
                  </path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                কিভাবে ব্যবহার করবেন?
              </h3>
              <p class="text-gray-600">১ মিনিটের ভিডিও টিউটোরিয়ালে দেখে নিন কিভাবে সহজেই প্রশ্ন তৈরি করবেন</p>
            </div>
          </div>
        </div>

        <!-- Exam Question Section -->
        <div class="max-w-5xl mx-auto mt-10">
          <div class="text-center mb-5">
            <span
              class="inline-block px-6 py-2 bg-gradient-to-r from-blue-100 to-indigo-100 text-indigo-800 rounded-full text-sm font-medium mb-6 border border-indigo-200 shadow-sm">শিক্ষার্থীদের
              ডিজিটাল অস্ত্র</span>
            <h2 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-6">
              <span
                class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600 animate-text-shine">মেধা
                যাচাই</span> এখানেই
            </h2>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto font-medium">শিক্ষার্থী ও চাকুরী প্রত্যাশীদের জন্য <span
                class="text-indigo-600 font-bold">জনপ্রিয় ও পছন্দের প্লাটফর্ম</span>। পরীক্ষা দিয়ে মেধা যাচাই করুন।</p>
          </div>

          <!-- Category Filter Buttons -->
          <div class="flex flex-wrap justify-center gap-3 mb-8">
            <button v-for="category in categories" :key="category" @click="setCategory(category)" :class="[
              'px-4 py-2 rounded-lg font-semibold text-sm transition-all duration-300',
              selectedCategory === category
                ? 'bg-indigo-600 text-white shadow-md transform scale-105'
                : 'bg-white text-slate-600 border border-slate-200 hover:bg-indigo-50 hover:text-indigo-600'
            ]">
              {{ category === 'all' ? 'সবগুলো' : category }}
            </button>
          </div>

          <!-- Showing counter -->
          <div class="text-center mb-4 text-sm text-gray-500">
            Showing {{ paginatedExams.length }} of {{ filteredExams.length }} exams          </div>

          <!-- Exam Cards Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="(item, index) in paginatedExams" :key="index"
              class="bg-white rounded-2xl border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-5 hover:shadow-md transition-shadow">
              <div class="flex justify-between items-start">
                <div class="flex-1">
                  <h3 class="text-lg font-bold text-slate-800 leading-tight">{{ item.title }}</h3>
                  <p class="text-[10px] text-slate-400 font-medium mt-1 uppercase tracking-wide">
                    {{ item.questions[0]?.academic_class?.name || 'N/A' }} | MCQ
                  </p>
                </div>
                <div class="flex flex-col items-end">
                  <span class="text-[9px] text-slate-400 font-bold uppercase mb-1.5">Instructions</span>
                  <div class="flex gap-1.5">
                    <div
                      class="w-6 h-6 rounded-full bg-orange-50 border border-orange-100 flex items-center justify-center text-[10px]">
                      🖥️</div>
                    <div
                      class="w-6 h-6 rounded-full bg-orange-50 border border-orange-100 flex items-center justify-center text-[10px]">
                      🔒</div>
                  </div>
                </div>
              </div>

              <div class="mt-4">
                <span
                  class="bg-emerald-50 text-emerald-600 text-[10px] font-bold px-2.5 py-1 rounded-md border border-emerald-100">
                  {{ item.questions[0]?.subject?.name || 'N/A' }}
                </span>
              </div>

              <hr class="mt-2">

              <div class="grid grid-cols-3 gap-0 mt-6 border-b border-slate-50 pb-5 mb-5">
                <div class="border-r border-slate-300">
                  <p class="text-[9px] text-slate-400 font-bold uppercase">Publish Date</p>
                  <p class="text-xs font-bold text-slate-700 mt-0.5">{{ formatDate(item.created_at) }}</p>
                </div>
                <div class="border-r border-slate-300 px-3">
                  <p class="text-[9px] text-slate-400 font-bold uppercase">Duration</p>
                  <p class="text-xs font-bold text-slate-700 mt-0.5">
                    {{ Math.ceil(item.questions.length / 2) }}
                    <span v-if="Math.ceil(item.questions.length / 2) <= 60">Min</span>
                    <span v-else>Hours</span>
                  </p>
                </div>
                <div class="pl-3">
                  <p class="text-[9px] text-slate-400 font-bold uppercase">Marks</p>
                  <p class="text-xs font-bold text-slate-700 mt-0.5">{{ item.questions.length }}</p>
                </div>
              </div>

              <div class="flex justify-between items-center">
                <div>
                  <p class="text-[10px] text-orange-500 font-bold">Number of Questions</p>
                  <p class="text-sm font-black text-slate-800 tracking-tight">{{ item.questions.length }} MCQ</p>
                </div>
                <div class="flex gap-2">
                  <Link :href="route('onlineexamshow', item.slug )" type="buttom"
                    class="px-5 py-1.5 text-[11px] font-bold bg-emerald-100 text-black rounded-lg shadow-sm shadow-indigo-200 hover:bg-emerald-300 active:scale-95 transition-all"> View
                  </Link>
                  <button @click="openExamModal(item)" type="button"
                    class="px-5 py-1.5 text-[11px] font-bold text-white bg-indigo-600 rounded-lg shadow-sm shadow-indigo-200 hover:bg-indigo-700 active:scale-95 transition-all">
                    Start
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Load More Button -->
          <div v-if="hasMoreItems" class="text-center mt-10">
            <button @click="loadMore"
              class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-blue-600 text-white font-semibold rounded-full shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
              Load More Exams
              <svg class="inline-block w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3">
                </path>
              </svg>
            </button>
          </div>

          <!-- No Results Message -->
          <div v-if="filteredExams.length === 0" class="text-center py-12">
            <p class="text-gray-500 text-lg">এই ক্যাটাগরিতে কোনো পরীক্ষা পাওয়া যায়নি।</p>
          </div>
        </div>
      </div>
    </section>
  </div>

  <!-- FULLSCREEN EXAM MODAL -->
  <div v-if="showModal" id="examModal" class="fixed inset-0 w-screen h-screen z-[9999] bg-gradient-to-br from-slate-100 via-blue-50 to-indigo-100 overflow-hidden">
    <!-- HEADER -->
    <div class="sticky top-0 z-50 bg-white/80 backdrop-blur-sm border-b border-white/50">
      <div class="px-6 py-4">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
          <!-- LEFT -->
          <div>
            <div class="flex flex-wrap items-center gap-3">
              <h1 class="text-2xl font-bold text-slate-800">{{ modalExamDetails?.title || 'Online Exam' }}</h1>
              <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">{{ modalExamDetails?.totalQuestions || 0 }} Questions</span>
              <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Full Marks: {{ modalExamDetails?.totalMarks || 0 }}</span>
              <span class="px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-700">{{ modalExamDetails?.durationMinutes || '30 Minutes' }}</span>
            </div>
          </div>

          <!-- RIGHT -->
          <div class="flex items-center gap-5">
            <div class="text-right">
              <p class="text-[11px] text-slate-500 font-medium">Time Remaining</p>
              <div class="text-3xl font-bold text-red-500 tracking-widest font-mono">{{ formattedTime }}</div>
            </div>
            <button @click="closeExamModal" type="button" class="w-11 h-11 rounded-full bg-red-100 hover:bg-red-200 text-red-600 text-lg font-bold transition-colors">✕</button>
          </div>
        </div>

        <!-- TIMER BAR -->
        <div class="w-full h-2 bg-slate-200 rounded-full mt-4 overflow-hidden">
          <div class="timer-progress h-full rounded-full bg-gradient-to-r from-red-500 to-orange-400 transition-all duration-300" :style="{ width: timerProgress + '%' }"></div>
        </div>
      </div>
    </div>

    <!-- BODY -->
    <div class="h-[calc(100vh-105px)] overflow-y-auto">
      <div class="p-6 space-y-6 max-w-7xl mx-auto">
        <!-- QUESTION CARDS -->
        <div v-for="(question, qIndex) in modalExamDetails?.questions || []" :key="qIndex" class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-shadow">
          <div class="p-6">
            <!-- TOP -->
            <div class="flex items-start justify-between gap-4">
              <div class="flex gap-3 flex-1">
                <input type="checkbox" class="mt-1 w-4 h-4 rounded border-gray-300">
                <div class="flex-1">
                  <!-- TAGS -->
                  <div class="flex flex-wrap items-center gap-2 mb-3">
                    <span class="bg-yellow-100 text-yellow-800 text-[10px] px-2 py-1 rounded-md font-medium">{{ question.academic_class?.name || 'Unknown Class' }}</span>
                    <span class="bg-blue-100 text-blue-700 text-[10px] px-2 py-1 rounded-md font-medium">{{ question.chapter?.name || 'Chapter' }}</span>
                  </div>

                  <!-- QUESTION TITLE -->
                  <div class="flex items-baseline">
                    <span class="mr-2 font-semibold">{{ qIndex + 1 }}.</span>
                    <div class="flex-1">
                      <div class="mb-2">
                        <LatexRenderer :content="question.question_text" />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <button class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-xs font-medium transition-colors shrink-0">💬 মন্তব্য</button>
            </div>

            
            <!-- OPTIONS with Radio Buttons - No pre-highlighting of correct answers -->
          <div v-if="question.format === 'mcq' && question.options" class="lg:grid grid-cols-2 gap-3 mt-4">
            <div v-for="option in question.options" :key="option.id" class="my-2 lg:m-0">
              <label :class="{ 'cursor-not-allowed opacity-60': isAnyOptionSelected(qIndex) && !isOptionSelected(qIndex, option.id) }"
                    class="flex items-center gap-3 p-3 rounded-lg border-2 cursor-pointer transition-all duration-200">
                <input type="radio" 
                      :name="`question_${qIndex}`" 
                      :value="option.id"
                      :checked="isOptionSelected(qIndex, option.id)"
                      @change="selectAnswer(qIndex, option.id, option.is_correct)"
                      :disabled="isAnyOptionSelected(qIndex) && !isOptionSelected(qIndex, option.id)"
                      class="w-4 h-4 cursor-pointer"
                      :class="{ 'cursor-not-allowed': isAnyOptionSelected(qIndex) && !isOptionSelected(qIndex, option.id) }">
                <div :class="{ 'font-bold': isOptionSelected(qIndex, option.id) }" class="flex items-center gap-2 flex-1">
                  <div class="flex items-center justify-center h-6 w-6 rounded-full border-2 border-gray-400 bg-white text-gray-700">
                    {{ bengaliChars[question.options.indexOf(option)] }}
                  </div>
                  <LatexRenderer :content="option.option_text" />
                </div>
              </label>
            </div>
          </div>
            
            <!-- Show message after selection -->
            <div v-if="isAnyOptionSelected(qIndex)" class="mt-3 text-xs text-gray-500 italic">
              ✓ আপনি এই প্রশ্নের উত্তর দিয়েছেন
            </div>
          </div>
        </div>

        <!-- EMPTY STATE -->
        <div v-if="!modalExamDetails?.questions?.length" class="text-center py-12">
          <p class="text-gray-500">No questions available for this exam.</p>
        </div>

        <!-- SUBMIT BUTTON -->
        <div class="text-center py-8">
          <button @click="submitExam" :disabled="isExamSubmitted" class="px-12 py-4 rounded-2xl text-lg font-semibold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:scale-105 shadow-2xl transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100">
            {{ isExamSubmitted ? 'Submitting...' : 'Submit Exam' }}
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- RESULT MODAL -->
  <div v-if="showResultModal" class="fixed inset-0 w-screen h-screen z-[10000] bg-black/50 backdrop-blur-sm overflow-y-auto" @click.self="closeResultModal">
    <div class="min-h-screen flex items-center justify-center p-4">
      <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full mx-4 animate-fadeInUp">
        <!-- Result Header -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-t-2xl p-6 text-white">
          <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold">পরীক্ষার ফলাফল</h2>
            <button @click="closeResultModal" class="w-10 h-10 rounded-full bg-white/20 hover:bg-white/30 text-white text-xl font-bold transition-colors">✕</button>
          </div>
          <p class="text-white/80 mt-2">{{ selectedExam?.title }}</p>
        </div>

        <!-- Score Cards -->
        <div class="p-6">
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-green-50 rounded-xl p-4 text-center border border-green-200">
              <div class="text-3xl font-bold text-green-600">{{ examResults?.correctCount || 0 }}</div>
              <div class="text-sm text-green-700 font-medium mt-1">সঠিক</div>
            </div>
            <div class="bg-red-50 rounded-xl p-4 text-center border border-red-200">
              <div class="text-3xl font-bold text-red-600">{{ examResults?.wrongCount || 0 }}</div>
              <div class="text-sm text-red-700 font-medium mt-1">ভুল</div>
            </div>
            <div class="bg-yellow-50 rounded-xl p-4 text-center border border-yellow-200">
              <div class="text-3xl font-bold text-yellow-600">{{ examResults?.unattemptedCount || 0 }}</div>
              <div class="text-sm text-yellow-700 font-medium mt-1">উত্তর দেননি</div>
            </div>
            <div class="bg-purple-50 rounded-xl p-4 text-center border border-purple-200">
              <div class="text-3xl font-bold text-purple-600">{{ examResults?.marksObtained || 0 }}/{{ examResults?.totalMarks || 0 }}</div>
              <div class="text-sm text-purple-700 font-medium mt-1">মোট নম্বর</div>
            </div>
          </div>

          <!-- Grade and Percentage -->
          <div class="bg-gray-50 rounded-xl p-6 mb-8 text-center">
            <div class="inline-block px-6 py-3 rounded-full text-2xl font-bold mb-2" :class="{
              'bg-green-100 text-green-700': examResults?.percentage >= 60,
              'bg-yellow-100 text-yellow-700': examResults?.percentage >= 40 && examResults?.percentage < 60,
              'bg-red-100 text-red-700': examResults?.percentage < 40
            }">
              গ্রেড: {{ examResults?.grade || 'N/A' }}
            </div>
            <div class="text-4xl font-bold text-gray-800 mt-2">{{ examResults?.percentage || 0 }}%</div>
            <div class="text-lg text-gray-600 mt-2">{{ examResults?.status || '' }}</div>
          </div>

          <!-- Detailed Results -->
          <div class="mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
              <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
              </svg>
              বিস্তারিত উত্তরপত্র
            </h3>
            <div class="space-y-4 max-h-96 overflow-y-auto">
              <div v-for="(result, idx) in examResults?.questionResults || []" :key="idx" class="border rounded-lg p-4" :class="{
                'border-green-200 bg-green-50': result.isCorrect,
                'border-red-200 bg-red-50': !result.isCorrect && result.isAttempted,
                'border-yellow-200 bg-yellow-50': !result.isAttempted
              }">
                <div class="flex items-start gap-3">
                  <div class="flex-shrink-0">
                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-sm font-bold" :class="{
                      'bg-green-500 text-white': result.isCorrect,
                      'bg-red-500 text-white': !result.isCorrect && result.isAttempted,
                      'bg-yellow-500 text-white': !result.isAttempted
                    }">
                      {{ idx + 1 }}
                    </div>
                  </div>
                  <div class="flex-1">
                    <LatexRenderer :content="result.questionText" class="font-medium text-gray-800 mb-2" />
                    <div class="space-y-1 text-sm">
                      <p><span class="font-semibold">আপনার উত্তর:</span> 
                        <span :class="{ 'text-green-700': result.isCorrect, 'text-red-700': !result.isCorrect && result.isAttempted, 'text-yellow-700': !result.isAttempted }">
                          {{ result.selectedAnswer }}
                        </span>
                      </p>
                      <p v-if="!result.isCorrect"><span class="font-semibold">সঠিক উত্তর:</span> 
                        <span class="text-green-700">{{ result.correctAnswer }}</span>
                      </p>
                    </div>
                  </div>
                  <div class="flex-shrink-0">
                    <svg v-if="result.isCorrect" class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <svg v-else-if="result.isAttempted" class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <svg v-else class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex gap-4 justify-center pt-4 border-t">
            <button @click="closeResultModal" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
              বন্ধ করুন
            </button>
            <button @click="closeResultModal; openExamModal(selectedExam)" class="px-6 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:scale-105 transition-all font-medium">
              আবার চেষ্টা করুন
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
  </AppLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&display=swap');

.hind-siliguri-light {
  font-family: "Hind Siliguri", sans-serif;
  font-weight: 300;
  font-style: normal;
}

/* Animation Keyframes */
@keyframes float {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-15px); }
}

@keyframes float-delay {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(10px); }
}

@keyframes pulse {
  0%, 100% { opacity: 0.5; transform: scale(1); }
  50% { opacity: 0.7; transform: scale(1.05); }
}

@keyframes textShine {
  0% { background-position: 0% 50%; }
  100% { background-position: 100% 50%; }
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Custom Animations */
.animate-float { animation: float 8s ease-in-out infinite; }
.animate-float-delay { animation: float-delay 8s ease-in-out infinite 2s; }
.animate-pulse { animation: pulse 6s ease-in-out infinite; }
.animate-text-shine { background-size: 200% auto; animation: textShine 3s linear infinite; }
.animate-fadeInUp { animation: fadeInUp 0.5s ease-out; }

/* Option Styles */
/* Option Styles - No pre-highlighting */
.option-default {
  background-color: white;
  border-color: #e2e8f0;
}
.option-default:hover {
  background-color: #f9fafb;
}

/* Only shown AFTER user selects correct answer */
.option-correct-selected {
  background-color: #dcfce7;
  border-color: #22c55e;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

/* Only shown AFTER user selects wrong answer */
.option-wrong-selected {
  background-color: #fee2e2;
  border-color: #ef4444;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

/* Only shown AFTER user selects wrong answer - highlights the correct answer */
.option-correct-highlight {
  background-color: #dcfce7;
  border-color: #86efac;
}

/* Custom Effects */
.feature-card:hover {
  border-color: rgba(99, 102, 241, 0.5) !important;
}

.hover\:scale-102:hover {
  transform: scale(1.02);
}

/* Video Container Styles */
.aspect-w-16 {
  position: relative;
  padding-bottom: 0;
}

.aspect-h-9 {
  height: 500px;
}

.aspect-w-16 iframe {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}

/* Prevent body scroll when modal is open */
body.modal-open {
  overflow: hidden;
}

/* Radio button styling */
input[type="radio"] {
  accent-color: #4f46e5;
}

/* Scrollbar styling */
::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

::-webkit-scrollbar-thumb {
  background: #c7d2fe;
  border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
  background: #818cf8;
}

/* Disabled cursor */
.cursor-not-allowed {
  cursor: not-allowed;
}
</style>