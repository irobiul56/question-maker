<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
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
const selectedChapters = ref([]); // Changed to array for multiple selection
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

// Method to toggle chapter selection
const toggleChapterSelection = (chapterId) => {
    const index = selectedChapters.value.indexOf(chapterId);
    if (index > -1) {
        selectedChapters.value.splice(index, 1);
    } else {
        selectedChapters.value.push(chapterId);
    }
};

// Method to select all chapters
const selectAllChapters = () => {
    if (filteredChapters.value.length > 0) {
        selectedChapters.value = filteredChapters.value.map(chapter => chapter.id);
    }
};

// Method to clear chapter selection
const clearChapterSelection = () => {
    selectedChapters.value = [];
};

const generateQuestions = () => {
    if (selectedChapters.value.length === 0 || !examName.value) {
        alert('Please fill all required fields');
        return;
    }

    router.get(route('sltquestionblog'), {
        exam_name: examName.value,
        chapter_id: selectedChapters.value, // Now passing array
        type: selectedType.value,
        count: questionCount.value,
    });
};
</script>

<template>
    <Head title="Admin Dashboard" />

    <AuthenticatedLayout>
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
       অনলাইন এক্সাম তৈরি করুন
      </h1>
      <p class="text-lg md:text-xl text-indigo-100 font-medium">
        প্রশ্ন তৈরি করা এখন আরো সহজ🏆
      </p>
      
    </div>
    
    <!-- Form section -->
    <div class="p-6 md:p-8 relative">
      <div class="absolute bottom-4 right-4 opacity-10">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
        </svg>
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
                            <div class="relative">
                                <div class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-white min-h-[60px]">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-gray-500">অধ্যায় ({{ selectedChapters.length }} selected)</span>
                                        <div class="flex gap-1">
                                            <button type="button" @click="selectAllChapters" 
                                                    class="text-xs bg-gray-200 hover:bg-gray-300 px-2 py-1 rounded">
                                                Select All
                                            </button>
                                            <button type="button" @click="clearChapterSelection"
                                                    class="text-xs bg-gray-200 hover:bg-gray-300 px-2 py-1 rounded">
                                                Clear
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="max-h-40 overflow-y-auto">
                                        <div v-for="chapter in filteredChapters" :key="chapter.id" 
                                             @click="toggleChapterSelection(chapter.id)"
                                             :class="{
                                                 'bg-blue-50 border-blue-200': selectedChapters.includes(chapter.id),
                                                 'hover:bg-gray-50': !selectedChapters.includes(chapter.id)
                                             }"
                                             class="mb-1 px-3 py-2 border rounded cursor-pointer flex items-center transition-colors">
                                            <input type="checkbox" 
                                                   :checked="selectedChapters.includes(chapter.id)"
                                                   @click.stop
                                                   class="mr-2 h-4 w-4 text-blue-600 rounded">
                                            <span>{{ chapter.name }}</span>
                                        </div>
                                        <div v-if="filteredChapters.length === 0" class="text-gray-400 text-center py-2">
                                            Select a subject first
                                        </div>
                                    </div>
                                </div>
                            </div>
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
          পরীক্ষা তৈরী করুন
        </button>
      </form>
    </div>
  </div>
</div>
    </AuthenticatedLayout>
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
