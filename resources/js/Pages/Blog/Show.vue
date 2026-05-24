<script setup>
import { Head, Link } from '@inertiajs/vue3';
import LatexRenderer from '@/Components/LatexRenderer.vue';
import { ref, computed, watch, nextTick } from 'vue';
import { usePage } from '@inertiajs/vue3';

const { props } = usePage();
const blog = ref(props.blog);

// Bengali characters for options
const bengaliChars = ['ক', 'খ', 'গ', 'ঘ', 'ঙ', 'চ', 'ছ', 'জ', 'ঝ', 'ঞ', 'ট', 'ঠ', 'ড', 'ঢ', 'ণ', 'ত', 'থ', 'দ', 'ধ', 'ন', 'প', 'ফ', 'ব', 'ভ', 'ম', 'য', 'র', 'ল', 'শ', 'ষ', 'স', 'হ', 'ড়', 'ঢ়', 'য়'];

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
  document.body.style.overflow = 'hidden';
  
  selectedAnswers.value = {};
  answerStatus.value = {};
  isExamSubmitted.value = false;
  
  nextTick(() => {
    resetTimer();
    startTimer();
  });
};

// Function to close the modal
const closeExamModal = () => {
  showModal.value = false;
  selectedExam.value = null;
  document.body.style.overflow = '';
  
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

// Handle answer selection
const selectAnswer = (questionIndex, optionId, isCorrect) => {
  const answerKey = `q${questionIndex}`;
  selectedAnswers.value[answerKey] = optionId;
  answerStatus.value[answerKey] = {
    isCorrect: isCorrect,
    selectedOptionId: optionId
  };
};

// Check if an option is selected
const isOptionSelected = (questionIndex, optionId) => {
  return selectedAnswers.value[`q${questionIndex}`] === optionId;
};

// Check if any option is selected for a question
const isAnyOptionSelected = (questionIndex) => {
  return selectedAnswers.value[`q${questionIndex}`] !== undefined;
};

// Get option style class
const getOptionClass = (questionIndex, optionId, isCorrect, isSelected) => {
  const answerKey = `q${questionIndex}`;
  const status = answerStatus.value[answerKey];
  
  if (isSelected) {
    return status?.isCorrect ? 'option-correct-selected' : 'option-wrong-selected';
  }
  
  if (status && !status.isCorrect && isCorrect) {
    return 'option-correct-highlight';
  }
  
  return 'option-default';
};

// Compute exam details
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

const formattedTime = computed(() => {
  const minutes = Math.floor(timeLeft.value / 60);
  const seconds = timeLeft.value % 60;
  return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
});

const timerProgress = computed(() => {
  const totalSeconds = modalExamDetails.value?.durationSeconds || 1800;
  if (totalSeconds <= 0) return 0;
  const progress = (timeLeft.value / totalSeconds) * 100;
  return Math.max(0, Math.min(100, progress));
});

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

const resetTimer = () => {
  if (timerInterval.value) {
    clearInterval(timerInterval.value);
    timerInterval.value = null;
  }
  
  const totalSeconds = modalExamDetails.value?.durationSeconds || 1800;
  timeLeft.value = totalSeconds;
  isExamSubmitted.value = false;
};

// Calculate results
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
    
    let selectedOptionText = 'Not attempted';
    if (selectedOptionId && question.options) {
      const selectedOpt = question.options.find(opt => opt.id === selectedOptionId);
      if (selectedOpt) {
        selectedOptionText = selectedOpt.option_text;
      }
    }
    
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

// Submit exam
const submitExam = () => {
  if (isExamSubmitted.value) return;
  
  isExamSubmitted.value = true;
  
  if (timerInterval.value) {
    clearInterval(timerInterval.value);
    timerInterval.value = null;
  }
  
  const results = calculateResults();
  examResults.value = results;
  
  showModal.value = false;
  showResultModal.value = true;
  document.body.style.overflow = 'hidden';
};

// Format date
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
  <Head :title="blog?.title || 'Exam Details'" />
  
  <div class="bg-gray-50 min-h-screen hind-siliguri-light">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Breadcrumb -->
      <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
          <li class="inline-flex items-center">
            <Link href="/" class="text-gray-600 hover:text-indigo-600 transition-colors">হোম</Link>
          </li>
          <li>
            <div class="flex items-center">
              <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
              </svg>
              <span class="text-gray-500 ml-1 md:ml-2">{{ blog?.title || 'Exam' }}</span>
            </div>
          </li>
        </ol>
      </nav>

      <!-- Main Exam Card -->
      <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <!-- Header Section -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-8 text-white">
          <div class="flex flex-wrap justify-between items-start gap-4">
            <div>
              <h1 class="text-3xl md:text-4xl font-bold mb-3">{{ blog?.title }}</h1>
              <div class="flex flex-wrap gap-3">
                <span class="px-3 py-1 bg-white/20 rounded-full text-sm">
                  {{ blog?.questions?.[0]?.academic_class?.name || 'N/A' }}
                </span>
                <span class="px-3 py-1 bg-white/20 rounded-full text-sm">
                  {{ blog?.questions?.[0]?.subject?.name || 'N/A' }}
                </span>
                <span class="px-3 py-1 bg-white/20 rounded-full text-sm">
                  {{ blog?.category?.name || 'General' }}
                </span>
              </div>
            </div>
            <div class="text-right">
              <div class="text-sm opacity-90">প্রকাশিত</div>
              <div class="font-medium">{{ formatDate(blog?.created_at) }}</div>
            </div>
          </div>
        </div>

        <!-- Content Body -->
        <div class="p-8">
          <!-- Description -->
          <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-3 flex items-center gap-2">
              <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              পরীক্ষার বিবরণ
            </h2>
            <p class="text-gray-600 leading-relaxed">{{ blog?.description || 'No description available.' }}</p>
          </div>

          <!-- Exam Info Cards -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-blue-50 rounded-xl p-5 text-center border border-blue-100">
              <div class="text-3xl font-bold text-blue-600">{{ blog?.questions?.length || 0 }}</div>
              <div class="text-sm text-blue-700 mt-1">মোট প্রশ্ন</div>
            </div>
            <div class="bg-green-50 rounded-xl p-5 text-center border border-green-100">
              <div class="text-3xl font-bold text-green-600">{{ blog?.questions?.length || 0 }}</div>
              <div class="text-sm text-green-700 mt-1">মোট নম্বর</div>
            </div>
            <div class="bg-orange-50 rounded-xl p-5 text-center border border-orange-100">
              <div class="text-3xl font-bold text-orange-600">{{ Math.ceil((blog?.questions?.length || 0) / 2) }} <span class="text-base">মিনিট</span></div>
              <div class="text-sm text-orange-700 mt-1">সময়কাল</div>
            </div>
          </div>

          <!-- Question Preview Section -->
          <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
              <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              নমুনা প্রশ্ন
            </h2>
            <div class="space-y-4">
              <div v-for="(question, idx) in blog?.questions?.slice(0, 10)" :key="idx" class="border border-gray-200 rounded-xl p-5 hover:shadow-md transition-shadow">
                <div class="flex items-start gap-3">
                  <span class="font-bold text-indigo-600 bg-indigo-50 w-7 h-7 rounded-full flex items-center justify-center text-sm flex-shrink-0">{{ idx + 1 }}</span>
                  <div class="flex-1">
                    <LatexRenderer :content="question.question_text" class="text-gray-800 font-medium" />
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-3">
                      <div v-for="(option, optIdx) in question.options?.slice(0, 4)" :key="option.id" class="flex items-center gap-2 text-sm text-gray-600">
                        <span class="font-semibold text-gray-500">{{ bengaliChars[optIdx] }}.</span>
                        <LatexRenderer :content="option.option_text" />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div v-if="blog?.questions?.length > 10" class="text-center text-gray-500 text-sm">
                এবং আরও {{ blog.questions.length - 10 }}টি প্রশ্ন...
              </div>
            </div>
          </div>

          <!-- Start Exam Button -->
          <div class="text-center pt-4 border-t border-gray-200">
            <button @click="openExamModal(blog)" class="px-10 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-lg font-bold rounded-full shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
              <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              পরীক্ষা শুরু করুন
            </button>
            <p class="text-xs text-gray-500 mt-3">প্রশ্নের উত্তর দিয়ে আপনার মেধা যাচাই করুন</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- FULLSCREEN EXAM MODAL -->
  <div v-if="showModal" class="fixed inset-0 w-screen h-screen z-[9999] bg-gradient-to-br from-slate-100 via-blue-50 to-indigo-100 overflow-hidden">
    <div class="sticky top-0 z-50 bg-white/80 backdrop-blur-sm border-b border-white/50">
      <div class="px-6 py-4">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
          <div>
            <div class="flex flex-wrap items-center gap-3">
              <h1 class="text-2xl font-bold text-slate-800">{{ modalExamDetails?.title || 'Online Exam' }}</h1>
              <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">{{ modalExamDetails?.totalQuestions || 0 }} Questions</span>
              <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Full Marks: {{ modalExamDetails?.totalMarks || 0 }}</span>
              <span class="px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-700">{{ modalExamDetails?.durationMinutes || '30 Minutes' }}</span>
            </div>
          </div>

          <div class="flex items-center gap-5">
            <div class="text-right">
              <p class="text-[11px] text-slate-500 font-medium">Time Remaining</p>
              <div class="text-3xl font-bold text-red-500 tracking-widest font-mono">{{ formattedTime }}</div>
            </div>
            <button @click="closeExamModal" type="button" class="w-11 h-11 rounded-full bg-red-100 hover:bg-red-200 text-red-600 text-lg font-bold transition-colors">✕</button>
          </div>
        </div>

        <div class="w-full h-2 bg-slate-200 rounded-full mt-4 overflow-hidden">
          <div class="timer-progress h-full rounded-full bg-gradient-to-r from-red-500 to-orange-400 transition-all duration-300" :style="{ width: timerProgress + '%' }"></div>
        </div>
      </div>
    </div>

    <div class="h-[calc(100vh-105px)] overflow-y-auto">
      <div class="p-6 space-y-6 max-w-7xl mx-auto">
        <div v-for="(question, qIndex) in modalExamDetails?.questions || []" :key="qIndex" class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-shadow">
          <div class="p-6">
            <div class="flex items-start justify-between gap-4">
              <div class="flex gap-3 flex-1">
                <div class="flex-1">
                  <div class="flex flex-wrap items-center gap-2 mb-3">
                    <span class="bg-yellow-100 text-yellow-800 text-[10px] px-2 py-1 rounded-md font-medium">{{ question.academic_class?.name || 'Unknown Class' }}</span>
                    <span class="bg-blue-100 text-blue-700 text-[10px] px-2 py-1 rounded-md font-medium">{{ question.chapter?.name || 'Chapter' }}</span>
                  </div>

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
            </div>

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
            
            <div v-if="isAnyOptionSelected(qIndex)" class="mt-3 text-xs text-gray-500 italic">
              ✓ আপনি এই প্রশ্নের উত্তর দিয়েছেন
            </div>
          </div>
        </div>

        <div v-if="!modalExamDetails?.questions?.length" class="text-center py-12">
          <p class="text-gray-500">No questions available for this exam.</p>
        </div>

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
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-t-2xl p-6 text-white">
          <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold">পরীক্ষার ফলাফল</h2>
            <button @click="closeResultModal" class="w-10 h-10 rounded-full bg-white/20 hover:bg-white/30 text-white text-xl font-bold transition-colors">✕</button>
          </div>
          <p class="text-white/80 mt-2">{{ selectedExam?.title }}</p>
        </div>

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
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&display=swap');

.hind-siliguri-light {
  font-family: "Hind Siliguri", sans-serif;
  font-weight: 300;
  font-style: normal;
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

.animate-fadeInUp {
  animation: fadeInUp 0.5s ease-out;
}

.option-default {
  background-color: white;
  border-color: #e2e8f0;
}
.option-default:hover {
  background-color: #f9fafb;
}

.option-correct-selected {
  background-color: #dcfce7;
  border-color: #22c55e;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.option-wrong-selected {
  background-color: #fee2e2;
  border-color: #ef4444;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.option-correct-highlight {
  background-color: #dcfce7;
  border-color: #86efac;
}

input[type="radio"] {
  accent-color: #4f46e5;
}

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

.cursor-not-allowed {
  cursor: not-allowed;
}
</style>