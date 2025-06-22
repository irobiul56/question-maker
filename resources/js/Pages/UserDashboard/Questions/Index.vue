<script setup>
import UserDashboardLayout from '@/Layouts/UserDashboardLayout.vue';
import { Head } from '@inertiajs/vue3';
import { usePage, useForm, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const { props } = usePage();
const education = ref(props.education || []);
const classes = ref(props.classes || []);
const subjects = ref(props.subjects || []);
const chapters = ref(props.chapters || []);

// Selected values
const examName = ref('');
const selectedEducation = ref('');
const selectedClass = ref('');
const selectedSubject = ref('');
const selectedChapter = ref('');
const selectedType = ref('');
const questionCount = ref(30);

// Computed properties for dependent dropdowns
const filteredClasses = computed(() => {
    if (!selectedEducation.value) return [];
    return classes.value.filter(cls => cls.education_id == selectedEducation.value);
});

const filteredSubjects = computed(() => {
    if (!selectedClass.value) return [];
    return subjects.value.filter(subject =>
        subject.academic_classes_id == selectedClass.value
    );
});

const filteredChapters = computed(() => {
    if (!selectedSubject.value) return [];
    return chapters.value.filter(chapter =>
        chapter.subject_id == selectedSubject.value
    );
});

const generateQuestions = () => {
    if (!selectedChapter.value || !examName.value) {
        alert('Please fill all required fields');
        return;
    }

    router.get(route('sltquestion'), {
        exam_name: examName.value,
        chapter_id: selectedChapter.value,
        type: selectedType.value,
        count: questionCount.value,
        
    });
};
</script>

<template>
    <Head title="User Dashboard" />

    <UserDashboardLayout>
<div class=" bangla w-full min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 p-4 md:p-8 flex items-center justify-center">
  <div class="w-full max-w-4xl bg-white rounded-xl shadow-2xl overflow-hidden transition-all duration-300 hover:shadow-3xl">
    <!-- Header with version -->
    <div class="flex justify-between items-center px-6 py-3 bg-gradient-to-r from-indigo-800 to-purple-800">
      <div class="flex space-x-2">
        <div class="w-3 h-3 rounded-full bg-red-400"></div>
        <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
        <div class="w-3 h-3 rounded-full bg-green-400"></div>
      </div>
      <p class="text-xs text-white opacity-80">সফটওয়্যার ভার্শন ৪.১.১</p>
    </div>
    
    <!-- Hero section -->
    <div class="bg-gradient-to-r from-indigo-600 to-blue-800 p-6 md:p-8 text-center">
      <h1 class="text-2xl md:text-3xl lg:text-4xl font-extrabold text-white mb-2 animate-pulse">
       ফ্রিতে ক্লিকেই প্রশ্ন রেডি
      </h1>
      <p class="text-lg md:text-xl text-indigo-100 font-medium">
        প্রশ্ন তৈরি করা এখন আরো সহজ🏆
      </p>
      
      <div class="mt-6 mb-2">
        <a href="https://eproshnobank.com/pricing" target="_blank" class="inline-block bg-white text-indigo-600 font-bold px-6 py-3 rounded-lg shadow-md hover:bg-yellow-200 hover:text-indigo-800 transition-all transform hover:scale-105">
          Subscribe Now!
        </a>
      </div>
    </div>
    
    <!-- Form section -->
    <div class="p-6 md:p-8 relative">
      <div class="absolute bottom-4 right-4 opacity-10">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
        </svg>
      </div>
      
      <div class="mb-6 border-b border-gray-200 pb-4 text-center">
        <p class="text-gray-600 mb-2">নিচের ইনপুট ফিল্ড গুলো সিলেক্ট করে সাবমিট করুন</p>
        <div class="flex items-center justify-center space-x-2 text-sm text-green-600 font-medium">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
          </svg>
          <span>সর্বশেষ প্রশ্ন যুক্ত হয়েছে an hour ago</span>
        </div>
      </div>
      
       <form @submit.prevent="generateQuestions" class="space-y-4">
                <div>
                    <input v-model="examName" type="text" placeholder="প্রোগ্রাম/পরীক্ষার নাম লিখুন *" 
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <select v-model="selectedEducation" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white appearance-none">
                            <option value="">ইডুকেশন লেভেল</option>
                            <option v-for="item in education" :key="item.id" :value="item.id">
                                {{ item.name }}
                            </option>
                        </select>
                    </div>
                    
                    <div>
                        <select v-model="selectedClass" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white appearance-none">
                            <option value="">শ্রেণি</option>
                            <option v-for="item in filteredClasses" :key="item.id" :value="item.id">
                                {{ item.name }}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <select v-model="selectedSubject" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white appearance-none">
                            <option value="">বিষয়</option>
                            <option v-for="item in filteredSubjects" :key="item.id" :value="item.id">
                                {{ item.name }}
                            </option>
                        </select>
                    </div>
                    
                    <div>
                        <select v-model="selectedChapter" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white appearance-none">
                            <option value="">অধ্যায়</option>
                            <option v-for="item in filteredChapters" :key="item.id" :value="item.id">
                                {{ item.name }}
                            </option>
                        </select>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <select v-model="selectedType" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white appearance-none">
                            <option value="">টাইপ</option>
                            <option value="mcq">বহুনির্বাচনী</option>
                            <option value="cq">সৃজনশীল</option>
                            <option value="mix">সমন্বিত প্রশ্ন</option>
                        </select>
                    </div>
                    
                    <div>
                        <input v-model.number="questionCount" type="number" placeholder="প্রশ্নের পরিমাণ" 
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>
                </div>
        
        <button type="submit" class="w-full bg-gradient-to-r from-green-500 to-emerald-600 text-white font-bold py-3 px-4 rounded-lg shadow-md hover:shadow-lg transform hover:scale-[1.01] transition-all duration-200">
          প্রশ্ন তৈরী করুন
        </button>
      </form>
    </div>
  </div>
</div>
    </UserDashboardLayout>
</template>
<style scoped>
  @import url('https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;700&display=swap');
        .bangla {
            font-family: 'Hind Siliguri', sans-serif;
        }
        .bangla-bold {
            font-family: 'Hind Siliguri', sans-serif;
            font-weight: 700;
        }
</style>
