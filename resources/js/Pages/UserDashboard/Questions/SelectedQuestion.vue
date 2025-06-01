<template>
    <Head title="User Dashboard" />

    <UserDashboardLayout>
        <!-- Loading Overlay -->
        <div v-if="loading" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
            <div class="bg-white p-4 rounded-lg shadow-lg">
                Loading questions...
            </div>
        </div>

        <div class="bg-white min-h-screen">
            <!-- Main Content -->
            <div class="w-full max-w-3xl p-4">
                <!-- Title Section -->
                <div class="mb-2 sticky top-0 text-sm md:text-md flex gap-1 items-center bg-gray-50 rounded p-2 border justify-between z-10">
                    <div class="lg:hidden">
                        <button class="bg-slate-600 text-white rounded hover:bg-amber-400 px-3 py-1.5 round flex items-center justify-center">
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" class="mr-2 text-sm text-white" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                                <path d="M487.976 0H24.028C2.71 0-8.047 25.866 7.058 40.971L192 225.941V432c0 7.831 3.821 15.17 10.237 19.662l80 55.98C298.02 518.69 320 507.493 320 487.98V225.941l184.947-184.97C520.021 25.896 509.338 0 487.976 0z"></path>
                            </svg>
                            <span>Filter</span>
                        </button>
                    </div>
                    <p class="bg-gray-200 px-2 py-1.5 rounded">
                        <span class="hidden md:inline-block">Selected:</span> {{ questions.length }} questions
                    </p>
                    
                    <Link :href="route('qstIndex')" class="flex gap-1 items-center bg-gray-200 rounded hover:bg-gray-300 px-3 py-1.5">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 576 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                            <path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z"></path>
                        </svg>
                        Back
                    </Link>
                    <button class="bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded">
                        <span class="flex items-center gap-1">
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 448 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                                <path d="M433.941 129.941l-83.882-83.882A48 48 0 0 0 316.118 32H48C21.49 32 0 53.49 0 80v352c0 26.51 21.49 48 48 48h352c26.51 0 48-21.49 48-48V163.882a48 48 0 0 0-14.059-33.941zM224 416c-35.346 0-64-28.654-64-64 0-35.346 28.654-64 64-64s64 28.654 64 64c0 35.346-28.654 64-64 64zm96-304.52V212c0 6.627-5.373 12-12 12H76c-6.627 0-12-5.373-12-12V108c0-6.627 5.373-12 12-12h228.52c3.183 0 6.235 1.264 8.485 3.515l3.48 3.48A11.996 11.996 0 0 1 320 111.48z"></path>
                            </svg>
                            Save
                        </span>
                    </button>
                </div>
                
                <!-- Notice Banner -->
                <div class="text-center bg-yellow-50 p-2 mb-4 flex gap-2 items-center justify-center">
                    <p>এই লিস্টে আপনার জন্য বাছাই করা <span class="text-green-600 font-bold">{{ filters.count }}টি ইউনিক প্রশ্ন</span>  তৈরি করে দেওয়া হয়েছে। আপনি চাইল এই প্রশ্নগুলো সিলেক্ট করে সেভ করতে পারেন। অথবা অ্যাডভান্স ফিল্টার করেও সেফ করতে পারেন।</p>
                </div>

                <!-- Questions Container - Grouped by Board -->
                <div class="space-y-6">
                    <div v-for="(boardQuestions, boardName) in groupedQuestions" :key="boardName" class="bg-white rounded-lg border">
                        <!-- Board Header -->
                        <div class="bg-gray-100 p-3 border-b flex justify-between items-center">
                            <h3 class="font-bold">{{ boardName }}</h3>
                            <span class="text-sm bg-gray-200 px-2 py-1 rounded">
                                {{ boardQuestions.length }} questions
                            </span>
                        </div>
                        
                        <!-- Questions for this board -->
                        <div class="divide-y">
                            <div v-for="(question, index) in boardQuestions" :key="question.id" class="p-5">
                                <div class="mb-2 flex justify-between items-center">
                                    <div class="flex items-center gap-2">
                                        <div class="text-xs">
                                            <mark><i>
                                                <span class="mr-1">
                                                    <span class="ml-1">{{ question.board?.name || 'Unknown' }}</span>
                                                    <span>'{{ question.board?.year || '----' }}</span>
                                                </span>
                                            </i></mark>
                                        </div>
                                        <button 
                                            @click="toggleExplanation(question.id)"
                                            class="shrink-0 bg-green-600 hover:bg-green-500 text-white rounded-full text-xs px-1.5 py-0.5">
                                            ব্যাখা দেখুন
                                        </button>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div title="প্রশ্নে ভুল পেলে রিপোর্ট করুন" class="border p-1 rounded text-gray-500 hover:border-green-500 hover:text-green-500">
                                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M448 0H64C28.7 0 0 28.7 0 64v288c0 35.3 28.7 64 64 64h96v84c0 9.8 11.2 15.5 19.1 9.7L304 416h144c35.3 0 64-28.7 64-64V64c0-35.3-28.7-64-64-64z"></path>
                                            </svg>
                                        </div>
                                        <div class="flex flex-wrap gap-1">
                                            <span class="text-xs p-1 border rounded-full px-1.5 py-0.5">
                                                {{ question.topic.name }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-baseline">
                                    <span class="bg-white hover:border-green-600 mr-3 h-4 w-4 border"></span>
                                    <span class="mr-1">{{ index + 1 }}.</span>
                                    <div class="flex-1">
                                        <div class="mb-2" v-html="question.question_text"></div>
                                    </div>
                                </div>

                                <!-- MCQ Options -->
                                <div v-if="question.format === 'mcq' && question.options" class="lg:grid grid-cols-2 gap-2">
                                    <div 
                                        v-for="(option, optIndex) in question.options" 
                                        :key="option.id"
                                        :class="{'bg-green-100 border-green-300': option.is_correct}"
                                        class="bg-gray-100 my-2 lg:m-0 rounded-lg p-2 flex gap-1 items-center">
                                        <div 
                                            :class="{'bg-green-600 text-white': option.is_correct}"
                                            class="flex items-center justify-center h-5 w-5 border border-gray-400 rounded-full p-0.5">
                                            {{ bengaliChars[question.options.indexOf(option)] }}
                                        </div>
                                        <div v-html="option.option_text"></div>
                                    </div>
                                </div>

                                <!-- Explanation -->
                                <div 
                                    v-if="question.explanation && question.showExplanation"
                                    class="mt-1.5 bg-green-50 py-2 px-3 text-green-700 rounded transition-all duration-300">
                                    <div v-html="question.explanation"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Action Buttons -->
                <div class="flex items-center justify-between mt-4">
                    <div class="py-1 flex gap-1 items-center">
                        <p class="bg-gray-100 px-2 py-1 rounded">Selected: 0/{{ questions.length }}</p>
                        <button class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded">
                            <span class="flex items-center gap-1">
                                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 448 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M433.941 129.941l-83.882-83.882A48 48 0 0 0 316.118 32H48C21.49 32 0 53.49 0 80v352c0 26.51 21.49 48 48 48h352c26.51 0 48-21.49 48-48V163.882a48 48 0 0 0-14.059-33.941zM224 416c-35.346 0-64-28.654-64-64 0-35.346 28.654-64 64-64s64 28.654 64 64c0 35.346-28.654 64-64 64zm96-304.52V212c0 6.627-5.373 12-12 12H76c-6.627 0-12-5.373-12-12V108c0-6.627 5.373-12 12-12h228.52c3.183 0 6.235 1.264 8.485 3.515l3.48 3.48A11.996 11.996 0 0 1 320 111.48z"></path>
                                </svg>
                                Save
                            </span>
                        </button>
                    </div>
                    <button class="bg-gray-200 hover:bg-gray-300 px-2.5 py-1 rounded flex gap-1 items-center">
                        Top 
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 384 512" class="text-gray-600" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                            <path d="M214.6 41.4c-12.5-12.5-32.8-12.5-45.3 0l-160 160c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L160 141.2V448c0 17.7 14.3 32 32 32s32-14.3 32-32V141.2L329.4 246.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3l-160-160z"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Sidebar (hidden on mobile by default) -->
            <div class="hidden lg:block fixed top-20 inset-y-0 right-0 w-80 bg-white overflow-y-auto border-l z-10 font-9">
                <div class="p-4">
                    <div class="border rounded-t text-lg font-bold py-2 text-center flex justify-center items-center gap-2">
                        এডভান্সড ফিল্টার মেনু
                    </div>
                    
                    <!-- Search Box -->
                    <div class="my-4">
                        <div class="flex gap-2">
                            <input type="text" placeholder="কিওয়ার্ড সার্চ করুন" class="flex-1 border rounded px-1 py-1">
                            <button class="rounded bg-gray-200 hover:bg-gray-300 px-1 py-1">সার্চ করুন</button>
                        </div>
                    </div>
                    
                    <!-- Unique Question Section -->
                    <div class="my-4 rounded-lg border border-green-400 bg-gradient-to-r from-green-50 to-white p-4">
                        <div class="mb-2 flex flex-col items-center justify-center">
                            <p class="text-center font-bold mt-3 text-lg text-green-700">ইউনিক প্রশ্ন তৈরী</p>
                            <p class="text-center my-2 text-gray-600">পূর্বের তৈরী কোনো একটি প্রশ্নের সকল প্রশ্ন বাদ দিয়ে নতুন প্রশ্ন তৈরী করতে পারবেন।</p>
                            <p>একাধিক ব্যাচের জন্য গুরুত্বপূর্ণ</p>
                        </div>
                        <div class="bg-white max-h-40 overflow-auto">
                            <div class="p-1.5 border rounded mb-0.5 flex items-center gap-2 justify-between cursor-pointer hover:border-red-500">
                                <span class="truncate text-gray-800">xxsdd</span>
                                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" class="shrink-0 text-gray-500" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zM124 296c-6.6 0-12-5.4-12-12v-56c0-6.6 5.4-12 12-12h264c6.6 0 12 5.4 12 12v56c0 6.6-5.4 12-12 12H124z"></path>
                                </svg>
                            </div>
                            <div class="p-1.5 border rounded mb-0.5 flex items-center gap-2 justify-between cursor-pointer hover:border-red-500">
                                <span class="truncate text-gray-800">jhhkh</span>
                                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" class="shrink-0 text-gray-500" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zM124 296c-6.6 0-12-5.4-12-12v-56c0-6.6 5.4-12 12-12h264c6.6 0 12 5.4 12 12v56c0 6.6-5.4 12-12 12H124z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Question Type Filter -->
                    <div class="bg-white border rounded-lg my-4">
                        <div class="p-2 bg-gray-100 flex justify-between items-center">
                            <p><span class="font-bold">প্রশ্নের ধরণ</span></p>
                        </div>
                        <div class="border-t text-gray-700">
                            <div class="p-2">
                                <div 
                                    v-for="(quetypes, index) in quetype" 
                                    :key="index" 
                                    class="mb-1"
                                >
                                    <div
                                        class="px-2 py-1.5 flex items-center cursor-pointer rounded border transition-colors"
                                    >
                                        <input 
                                            type="checkbox" 
                                            class="mr-2 rounded cursor-pointer">

                                        <span class="w-full">{{ quetypes.name }}</span>
                                        <span 
                                            class="ml-2 text-blue-600"
                                        >
                                            ✓
                                        </span>
                                    </div>
                                </div>
                              
                            </div>
                        </div>
                    </div>
                    
                    <!-- Topic Filter -->
                    <div class="bg-white border rounded-lg my-4">
                        <div class="p-2 bg-gray-100 flex justify-between items-center">
                            <p><span class="font-bold">অধ্যায়:</span> {{ chapter?.name || 'Not Found' }}</p>
                            <button 
                                v-if="selectedTopics.length > 0" 
                                @click="clearTopicFilters"
                                class="text-xs bg-gray-200 hover:bg-gray-300 px-2 py-1 rounded"
                            >
                                Clear All
                            </button>
                        </div>
                        <div class="border-t text-gray-700">
                            <div class="p-2">
                                <p class="font-bold mb-2">Topics ({{ selectedTopics.length }} selected):</p>
                                <button 
                                    v-if="chapter?.topic?.length > 0 && selectedTopics.length < chapter.topic.length"
                                    @click="selectAllTopics"
                                    class="text-xs bg-gray-200 hover:bg-gray-300 px-2 py-1 rounded mb-2"
                                >
                                    Select All Topics
                                </button>
                                <div 
                                    v-for="topic in chapter?.topic" 
                                    :key="topic.id" 
                                    class="mb-1"
                                >
                                    <div 
                                        @click="toggleTopic(topic.id)"
                                        :class="{
                                            'bg-blue-100 border-blue-300': selectedTopics.includes(topic.id),
                                            'hover:bg-gray-100': !selectedTopics.includes(topic.id)
                                        }"
                                        class="px-2 py-1.5 flex items-center cursor-pointer rounded border transition-colors"
                                    >
                                        <input 
                                            type="checkbox" 
                                            :checked="selectedTopics.includes(topic.id)"
                                            class="mr-2 rounded cursor-pointer"
                                            @click.stop
                                        >
                                        <span class="w-full">{{ topic.name }}</span>
                                        <span 
                                            v-if="selectedTopics.includes(topic.id)"
                                            class="ml-2 text-blue-600"
                                        >
                                            ✓
                                        </span>
                                    </div>
                                </div>
                                <div v-if="!chapter?.topic?.length" class="text-gray-500 italic">
                                    No topics found for this chapter
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Board and Year Filter Section -->
                    <div class="bg-white border rounded-lg my-4">
                        <div class="bg-gray-100 p-2 flex justify-between items-center">
                            <p class="font-bold">বোর্ড</p>
                            <span>
                                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 448 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M201.4 137.4c12.5-12.5 32.8-12.5 45.3 0l160 160c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L224 205.3 86.6 342.6c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3l160-160z"></path>
                                </svg>
                            </span>
                        </div>
                        <div class="border-t text-gray-700">
                            <div class="p-2">
                                <!-- Year Filter -->
                                <div class="p-2">
                                    <label class="block mb-1 text-sm font-medium">Year</label>
                                    <div class="flex flex-wrap gap-2">
                                        <button 
                                            v-for="year in availableYears" 
                                            :key="year"
                                            @click="toggleYear(year)"
                                            :class="{
                                                'bg-blue-500 text-white': selectedYears.includes(year),
                                                'bg-gray-200': !selectedYears.includes(year)
                                            }"
                                            class="px-3 py-1 rounded text-sm"
                                        >
                                            {{ year }}
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Board Filter -->
                                <div class="mt-2">
                                    <div v-for="board in availableBoards" :key="board.id" class="mb-1">
                                        <label 
                                            @click="toggleBoard(board.id)"
                                            :class="{
                                                'bg-blue-100 border-blue-300': selectedBoards.includes(board.id),
                                                'hover:bg-gray-100': !selectedBoards.includes(board.id)
                                            }"
                                            class="px-2 py-1.5 flex items-center cursor-pointer rounded border transition-colors"
                                        >
                                            <input 
                                                type="checkbox" 
                                                :checked="selectedBoards.includes(board.id)"
                                                class="mr-2 rounded cursor-pointer"
                                                @click.stop
                                            >
                                            <span class="w-full">{{ board.name }} ({{ board.year }})</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </UserDashboardLayout>
</template>

<script setup>
import UserDashboardLayout from '@/Layouts/UserDashboardLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';

const props = defineProps({
    questions: Array,
    groupedQuestions: Object,
    availableYears: Array,
    chapter: Object,
    filters: Object,
    boards: Array,
    quetype: Array,
});

const questions = ref((props.questions || []).map(q => ({
    ...q,
    showExplanation: false
})));
const groupedQuestions = ref(props.groupedQuestions || {});
const chapter = ref(props.chapter || null);
const filters = ref(props.filters || {});
const selectedTopics = ref(props.filters?.topic_id || []);
const loading = ref(false);
const selectedBoards = ref([]);
const selectedYears = ref([]);

// Extract available boards from props or from questions
const availableYears = computed(() => {
    const years = new Set();
    props.boards?.forEach(board => {
        if (board.year) years.add(board.year);
    });
    return Array.from(years).sort((a, b) => b - a); // Newest first
});

const availableBoards = computed(() => {
    if (!selectedYears.value.length) {
        return props.boards || [];
    }
    return (props.boards || []).filter(board => 
        selectedYears.value.includes(board.year)
    );
});

// Watch for changes in filters
watch([selectedTopics, selectedBoards, selectedYears], ([newTopics, newBoards, newYears]) => {
    loading.value = true;
    
    router.get(route('sltquestion'), {
        chapter_id: filters.value.chapter_id,
        topic_id: newTopics,
        type: filters.value.type,
        count: filters.value.count,
        board_ids: newBoards,
        years: newYears
    }, {
        preserveState: true,
        replace: true,
        only: ['questions', 'groupedQuestions', 'chapter', 'filters'],
        onSuccess: () => {
            loading.value = false;
        },
        onError: () => {
            loading.value = false;
        }
    });
}, { deep: true });

const toggleTopic = (topicId) => {
    const index = selectedTopics.value.indexOf(topicId);
    if (index > -1) {
        selectedTopics.value.splice(index, 1);
    } else {
        selectedTopics.value.push(topicId);
    }
};

// Toggle functions
const toggleBoard = (boardId) => {
    const index = selectedBoards.value.indexOf(boardId);
    if (index > -1) {
        selectedBoards.value.splice(index, 1);
    } else {
        selectedBoards.value.push(boardId);
    }
};


const toggleYear = (year) => {
    const index = selectedYears.value.indexOf(year);
    if (index > -1) {
        selectedYears.value.splice(index, 1);
    } else {
        selectedYears.value.push(year);
    }
    
    // Clear board selections when changing years
    selectedBoards.value = [];
};

const selectAllTopics = () => {
    selectedTopics.value = chapter.value.topic.map(topic => topic.id);
};

const clearTopicFilters = () => {
    selectedTopics.value = [];
};

const toggleExplanation = (questionId) => {
    const question = questions.value.find(q => q.id === questionId);
    if (question) {
        question.showExplanation = !question.showExplanation;
    }
};

// Get Bengali character for option index
const bengaliChars = ['ক', 'খ', 'গ', 'ঘ', 'ঙ', 'চ', 'ছ', 'জ', 'ঝ', 'ঞ', 'ট', 'ঠ', 'ড', 'ঢ', 'ণ', 'ত', 'থ', 'দ', 'ধ', 'ন', 'প', 'ফ', 'ব', 'ভ', 'ম', 'য', 'র', 'ল', 'শ', 'ষ', 'স', 'হ', 'ড়', 'ঢ়', 'য়'];
// Watcher for filters

// Watch for props changes to update local state
watch(() => props.questions, (newQuestions) => {
    questions.value = (newQuestions || []).map(q => ({
        ...q,
        showExplanation: false
    }));
}, { immediate: true });

watch(() => props.groupedQuestions, (newGrouped) => {
    groupedQuestions.value = newGrouped || {};
}, { immediate: true });

watch(() => props.filters, (newFilters) => {
    filters.value = newFilters || {};
}, { immediate: true });

watch(() => props.chapter, (newChapter) => {
    chapter.value = newChapter || null;
}, { immediate: true });



</script>