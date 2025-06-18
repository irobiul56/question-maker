<script setup>
import UserDashboardLayout from '@/Layouts/UserDashboardLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    savedquestion: {
        type: Array,
        required: true,
        default: () => []
    }
});

const getBanglaLetter = (index) => {
    const banglaLetters = ['ক', 'খ', 'গ', 'ঘ', 'ঙ', 'চ', 'ছ', 'জ', 'ঝ', 'ঞ'];
    return banglaLetters[index] || String.fromCharCode(2433 + index);
};

</script>

<template>

    <Head title="User Dashboard" />

    <UserDashboardLayout>
        <div class="bangla flex flex-col lg:flex-row justify-center gap-5 print:gap-0 mx-4 print:mx-0">
        <!-- Main content -->
        <div class="w-full max-w-3xl p-4">
            <!-- Mobile buttons -->
        <div class="flex md:hidden items-center justify-between mt-4">
            <button class="flex justify-center gap-1 items-center border py-1 px-2 bg-white rounded" tabindex="0">
                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" class="text-gray-600 text-xs" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                    <path d="M495.9 166.6c3.2 8.7 .5 18.4-6.4 24.6l-43.3 39.4c1.1 8.3 1.7 16.8 1.7 25.4s-.6 17.1-1.7 25.4l43.3 39.4c6.9 6.2 9.6 15.9 6.4 24.6c-4.4 11.9-9.7 23.3-15.8 34.3l-4.7 8.1c-6.6 11-14 21.4-22.1 31.2c-5.9 7.2-15.7 9.6-24.5 6.8l-55.7-17.7c-13.4 10.3-28.2 18.9-44 25.4l-12.5 57.1c-2 9.1-9 16.3-18.2 17.8c-13.8 2.3-28 3.5-42.5 3.5s-28.7-1.2-42.5-3.5c-9.2-1.5-16.2-8.7-18.2-17.8l-12.5-57.1c-15.8-6.5-30.6-15.1-44-25.4L83.1 425.9c-8.8 2.8-18.6 .3-24.5-6.8c-8.1-9.8-15.5-20.2-22.1-31.2l-4.7-8.1c-6.1-11-11.4-22.4-15.8-34.3c-3.2-8.7-.5-18.4 6.4-24.6l43.3-39.4C64.6 273.1 64 264.6 64 256s.6-17.1 1.7-25.4L22.4 191.2c-6.9-6.2-9.6-15.9-6.4-24.6c4.4-11.9 9.7-23.3 15.8-34.3l4.7-8.1c6.6-11 14-21.4 22.1-31.2c5.9-7.2 15.7-9.6 24.5-6.8l55.7 17.7c13.4-10.3 28.2-18.9 44-25.4l12.5-57.1c2-9.1 9-16.3 18.2-17.8C227.3 1.2 241.5 0 256 0s28.7 1.2 42.5 3.5c9.2 1.5 16.2 8.7 18.2 17.8l12.5 57.1c15.8 6.5 30.6 15.1 44 25.4l55.7-17.7c8.8-2.8 18.6-.3 24.5 6.8c8.1 9.8 15.5 20.2 22.1 31.2l4.7 8.1c6.1 11 11.4 22.4 15.8 34.3zM256 336a80 80 0 1 0 0-160 80 80 0 1 0 0 160z"></path>
                </svg>
                <span>সেটিংস</span>
            </button>
            <button class="flex justify-center gap-1 items-center border py-1 px-2 bg-white rounded" tabindex="0">
                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 640 512" class="text-gray-600 text-xs" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                    <path d="M320 400c-75.85 0-137.25-58.71-142.9-133.11L72.2 185.82c-13.79 17.3-26.48 35.59-36.72 55.59a32.35 32.35 0 0 0 0 29.19C89.71 376.41 197.07 448 320 448c26.91 0 52.87-4 77.89-10.46L346 397.39a144.13 144.13 0 0 1-26 2.61zm313.82 58.1l-110.55-85.44a331.25 331.25 0 0 0 81.25-102.07 32.35 32.35 0 0 0 0-29.19C550.29 135.59 442.93 64 320 64a308.15 308.15 0 0 0-147.32 37.7L45.46 3.37A16 16 0 0 0 23 6.18L3.37 31.45A16 16 0 0 0 6.18 53.9l588.36 454.73a16 16 0 0 0 22.46-2.81l19.64-25.27a16 16 0 0 0-2.82-22.45zm-183.72-142l-39.3-30.38A94.75 94.75 0 0 0 416 256a94.76 94.76 0 0 0-121.31-92.21A47.65 47.65 0 0 1 304 192a46.64 46.64 0 0 1-1.54 10l-73.61-56.89A142.31 142.31 0 0 1 320 112a143.92 143.92 0 0 1 144 144c0 21.63-5.29 41.79-13.9 60.11z"></path>
                </svg>
                <span>উত্তরমালা</span>
            </button>
            <button class="flex justify-center gap-1 items-center border py-1 px-2 bg-white rounded" tabindex="0">
                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" class="text-gray-600 text-xs" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                    <path d="M288 32c0-17.7-14.3-32-32-32s-32 14.3-32 32V274.7l-73.4-73.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l128 128c12.5 12.5 32.8 12.5 45.3 0l128-128c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L288 274.7V32zM64 352c-35.3 0-64 28.7-64 64v32c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V416c0-35.3-28.7-64-64-64H346.5l-45.3 45.3c-25 25-65.5 25-90.5 0L165.5 352H64zm368 56a24 24 0 1 1 0 48 24 24 0 1 1 0-48z"></path>
                </svg>
                <span>ডাউনলোড</span>
            </button>
        </div>
            <div class="w-full p-[5mm] md:p-[10mm] print:p-0 print:w-full print:shadow-none bg-white">
                <!-- Header section -->
                <div class="py-2 print:py-0">
                    <h1 class="text-xl font-bold text-center">Jamalpur Kaliakair M E H Arif Institute</h1>
                    <h3 class="text-xl font-bold text-center">Jamalpur, Kaliakair, Gazipur</h3>
                    <div class="relative">
                        {{savedquestion}}
                        <p class="text-center text-lg">{{ savedquestion[0]?.exam_name }}</p>
                        <p class="text-center text-lg">নবম/দশম</p>
                        <p class="text-center truncate">বাংলা ১ম পত্র</p>
                        <p class="text-center truncate">গদ্য ১ - সুভা</p>
                        <div class="absolute top-0 right-0 flex">
                            <p class="border px-2 border-gray-500">সেট</p>
                            <p class="border-y border-r px-2 border-gray-500">ক</p>
                        </div>
                    </div>
                    <div class="flex justify-between relative">
                        <div class="flex items-center">সময়—<p class="mx-1">৪০ মিনিট</p></div>
                        <div>পূর্ণমান—<span class="mx-1">৩০</span></div>
                    </div>
                    <hr>
                    <div class="text-center text-sm my-1">
                        <span><i><span class="bangla-bold">দ্রষ্টব্যঃ</span> সরবরাহকৃত নৈর্ব্যত্তিক অভীক্ষার উত্তরপত্রে প্রশ্নের ক্রমিক নম্বরের বিপরীতে প্রদত্ত বর্ণসম্বলিত বৃত্ত সমুহ হতে সঠিক উত্তরের বৃত্তটি</i> 
                        (<svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" class="inline-block" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                            <path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512z"></path>
                        </svg>) <i>বল পয়েন্ট কলম দ্বারা সম্পুর্ণ ভরাট করো । প্রতিটি প্রশ্নের মান ১ ।</i></span>
                    </div>
                    <p class="bangla-bold text-center text-sm mt-1 font-bold">প্রশ্নপত্রে কোনো প্রকার দাগ/চিহ্ন দেয়া যাবেনা ।</p>
                </div>

                <!-- Questions section -->
                 <div style="font-size: 14px;">
                    <div v-if="savedquestion.length > 0" class="relative flex-1 columns-2 lg:columns-2 print:columns-2" style="column-rule: 1px solid rgba(0, 0, 0, 0.2);">
                        <!-- Single exam (since we only fetch latest) -->
                        <div v-for="(question, questionIndex) in savedquestion[0].questions" :key="questionIndex" 
                            class="bg-white relative p-0.5 hover:bg-gray-50 rounded group">
                            <div class="flex items-baseline gap-x-2">
                                <span class="bangla-bold text-gray-900">{{ questionIndex + 1 }}.</span>
                                <div class="flex flex-wrap justify-between items-center w-full">
                                    <div class="false bangla-bold">{{ question.question_text }}</div>
                                </div>
                            </div>
                            <div class="relative grid grid-cols-2 ml-7 group">
                                <div v-for="(option, optionIndex) in question.options" :key="optionIndex" 
                                :class="{ 'bg-green-100 border-green-300': option.is_correct }" class="option flex flex-1 items-baseline">
                                    <div :class="{ 'bg-green-600 text-white': option.is_correct }" class="h-4 w-4 border border-gray-500 shrink-0 mr-1 rounded-full flex justify-center items-center">
                                        {{ getBanglaLetter(optionIndex) }}
                                    </div>
                                    <div>{{ option.option_text }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="p-4 text-center text-gray-500">
                        No saved questions found
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar settings -->
        <div class="hidden lg:block fixed inset-y-0 right-0 w-72 bg-white overflow-y-auto border-l z-10 font-10">
            <div class="relative overflow-hidden">
                <div class="bg-white backdrop-blur-lg p-4">
                    <h1 class="py-2 flex items-center gap-2 justify-center rounded text-center bg-gray-50 mb-2 shadow">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" class="text-gray-500 text-sm" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                            <path d="M495.9 166.6c3.2 8.7 .5 18.4-6.4 24.6l-43.3 39.4c1.1 8.3 1.7 16.8 1.7 25.4s-.6 17.1-1.7 25.4l43.3 39.4c6.9 6.2 9.6 15.9 6.4 24.6c-4.4 11.9-9.7 23.3-15.8 34.3l-4.7 8.1c-6.6 11-14 21.4-22.1 31.2c-5.9 7.2-15.7 9.6-24.5 6.8l-55.7-17.7c-13.4 10.3-28.2 18.9-44 25.4l-12.5 57.1c-2 9.1-9 16.3-18.2 17.8c-13.8 2.3-28 3.5-42.5 3.5s-28.7-1.2-42.5-3.5c-9.2-1.5-16.2-8.7-18.2-17.8l-12.5-57.1c-15.8-6.5-30.6-15.1-44-25.4L83.1 425.9c-8.8 2.8-18.6 .3-24.5-6.8c-8.1-9.8-15.5-20.2-22.1-31.2l-4.7-8.1c-6.1-11-11.4-22.4-15.8-34.3c-3.2-8.7-.5-18.4 6.4-24.6l43.3-39.4C64.6 273.1 64 264.6 64 256s.6-17.1 1.7-25.4L22.4 191.2c-6.9-6.2-9.6-15.9-6.4-24.6c4.4-11.9 9.7-23.3 15.8-34.3l4.7-8.1c6.6-11 14-21.4 22.1-31.2c5.9-7.2 15.7-9.6 24.5-6.8l55.7 17.7c13.4-10.3 28.2-18.9 44-25.4l12.5-57.1c2-9.1 9-16.3 18.2-17.8C227.3 1.2 241.5 0 256 0s28.7 1.2 42.5 3.5c9.2 1.5 16.2 8.7 18.2 17.8l12.5 57.1c15.8 6.5 30.6 15.1 44 25.4l55.7-17.7c8.8-2.8 18.6-.3 24.5 6.8c8.1 9.8 15.5 20.2 22.1 31.2l4.7 8.1c6.1 11 11.4 22.4 15.8 34.3zM256 336a80 80 0 1 0 0-160 80 80 0 1 0 0 160z"></path>
                        </svg> 
                        <span class="text-lg">সেটিংস</span>
                    </h1>
                    
                    <button class="hover:bg-green-400 bg-green-500 transition-all py-2 rounded w-full text-center text-white flex items-center gap-2 justify-center">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                            <path d="M288 32c0-17.7-14.3-32-32-32s-32 14.3-32 32V274.7l-73.4-73.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l128 128c12.5 12.5 32.8 12.5 45.3 0l128-128c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L288 274.7V32zM64 352c-35.3 0-64 28.7-64 64v32c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V416c0-35.3-28.7-64-64-64H346.5l-45.3 45.3c-25 25-65.5 25-90.5 0L165.5 352H64zm368 56a24 24 0 1 1 0 48 24 24 0 1 1 0-48z"></path>
                        </svg>
                        ডাউনলোড
                    </button>
                    
                    <!-- Answer sheet toggle -->
                    <div class="bg-gray-100 p-2 rounded flex justify-between items-center my-2">
                        <span class="bangla">উত্তরপত্র</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        </label>
                    </div>
                    
                    <!-- Editing mode toggle -->
                    <div class="bg-gray-100 p-2 rounded my-2">
                        <div class="flex justify-between items-center">
                            <span class="bangla">এডিটিং মোড</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Sheet toggle -->
                    <div>
                        <div class="relative bg-gray-100 p-2 rounded flex justify-between items-center my-2">
                            <span class="bangla">শীট</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Option style selector -->
                    <div class="relative bg-gray-100 p-2 rounded my-2">
                        <p class="bangla mb-2">অপশন স্টাইল</p>
                        <div class="flex gap-2">
                            <div class="p-1 flex-1 flex justify-center items-center cursor-pointer bg-emerald-600">
                                <div class="h-5 w-5 border border-gray-500 rounded-full bg-white"></div>
                            </div>
                            <div class="p-1 flex-1 flex justify-center items-center cursor-pointer bg-white hover:bg-emerald-100">.</div>
                            <div class="p-1 flex-1 flex justify-center items-center cursor-pointer bg-white hover:bg-emerald-100">( )</div>
                            <div class="p-1 flex-1 flex justify-center items-center cursor-pointer bg-white hover:bg-emerald-100">)</div>
                        </div>
                    </div>
                    
                    <!-- Address toggle -->
                    <div>
                        <div class="bg-gray-100 p-2 rounded flex justify-between items-center my-2">
                            <span class="bangla">ঠিকানা</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Watermark toggle -->
                    <div class="bg-gray-100 my-2 p-2">
                        <div class="rounded flex justify-between items-center">
                            <span class="bangla">জলছাপ</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Shuffle button -->
                    <div>
                        <div class="cursor-pointer bg-gray-100 hover:bg-emerald-600 hover:text-white flex justify-between items-center p-2 rounded my-2" tabindex="0">
                            <p>শাফল করুন</p>
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                                <path d="M403.8 34.4c12-5 25.7-2.2 34.9 6.9l64 64c6 6 9.4 14.1 9.4 22.6s-3.4 16.6-9.4 22.6l-64 64c-9.2 9.2-22.9 11.9-34.9 6.9s-19.8-16.6-19.8-29.6V160H352c-10.1 0-19.6 4.7-25.6 12.8L284 229.3 244 176l31.2-41.6C293.3 110.2 321.8 96 352 96h32V64c0-12.9 7.8-24.6 19.8-29.6zM164 282.7L204 336l-31.2 41.6C154.7 401.8 126.2 416 96 416H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H96c10.1 0 19.6-4.7 25.6-12.8L164 282.7zm274.6 188c-9.2 9.2-22.9 11.9-34.9 6.9s-19.8-16.6-19.8-29.6V416H352c-30.2 0-58.7-14.2-76.8-38.4L121.6 172.8c-6-8.1-15.5-12.8-25.6-12.8H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H96c30.2 0 58.7 14.2 76.8 38.4L326.4 339.2c6 8.1 15.5 12.8 25.6 12.8h32V320c0-12.9 7.8-24.6 19.8-29.6s25.7-2.2 34.9 6.9l64 64c6 6 9.4 14.1 9.4 22.6s-3.4 16.6-9.4 22.6l-64 64z"></path>
                            </svg>
                        </div>
                    </div>
                    
                    <!-- OMR toggle -->
                    <div>
                        <div class="bg-gray-100 p-2 rounded flex justify-between items-center my-2">
                            <span class="bangla">OMR সংযুক্ত</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Instructions toggle -->
                    <div class="bg-gray-100 p-2 rounded flex justify-between items-center my-2">
                        <span class="bangla">নির্দেশনা</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        </label>
                    </div>
                    
                    <!-- Subject name toggle -->
                    <div class="bg-gray-100 p-2 rounded flex justify-between items-center my-2">
                        <span class="bangla">বিষয়ের নাম</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        </label>
                    </div>
                    
                    <!-- Chapter name toggle -->
                    <div class="bg-gray-100 p-2 rounded flex justify-between items-center my-2">
                        <span class="bangla">অধ্যায়ের নাম</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        </label>
                    </div>
                    
                    <!-- Important questions toggle -->
                    <div class="bg-gray-100 p-2 rounded flex justify-between items-center my-2">
                        <span class="bangla">গুরুত্বপূর্ণ প্রশ্ন</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        </label>
                    </div>
                    
                    <!-- Set code toggle -->
                    <div class="bg-gray-100 p-2 rounded flex justify-between items-center my-2">
                        <span class="bangla">সেট কোড</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        </label>
                    </div>
                    
                    <!-- Title toggle -->
                    <div class="bg-gray-100 p-2 rounded flex justify-between items-center my-2">
                        <span class="bangla">টাইটেল</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        </label>
                    </div>
                    
                    <!-- Question info toggle -->
                    <div class="bg-gray-100 p-2 rounded flex justify-between items-center my-2">
                        <span class="bangla">প্রশ্নের তথ্য</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        </label>
                    </div>
                    
                    <!-- Font selector -->
                    <div class="bg-gray-100 my-2 p-2">
                        <div class="rounded justify-between items-center">
                            <p class="bangla mb-1 text-center">ফন্ট পরিবর্তন</p>
                            <select id="font-selector" class="w-full p-1 border rounded">
                                <option value="Bangla">বাংলা</option>
                                <option value="SolaimanLipi">সোলাইমান লিপি</option>
                                <option value="Kalpurush">কালপুরুষ</option>
                                <option value="roman">Times New Roman</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Font size -->
                    <div class="bg-gray-100 p-2 rounded flex justify-between items-center my-2">
                        <span class="bangla">ফন্ট সাইজ</span>
                        <div class="flex items-center gap-1">
                            <button class="hover:bg-white px-2 rounded text-lg">-</button>
                            <p class="border rounded p-0.5 px-1.5 bg-white">14</p>
                            <button class="hover:bg-white px-2 rounded text-lg">+</button>
                        </div>
                    </div>
                    
                    <!-- Columns selector -->
                    <div class="my-2">
                        <p>কলাম</p>
                        <div class="flex gap-2 justify-center">
                            <div class="border hover:border-gray-500 rounded py-2 px-3 flex-1 cursor-pointer">
                                <div class="flex justify-center gap-0.5 items-center">
                                    <div class="w-4 h-6 bg-gray-300"></div>
                                </div>
                            </div>
                            <div class="border border-black rounded py-2 px-3 flex-1 cursor-pointer">
                                <div class="flex justify-center gap-0.5 items-center">
                                    <div class="w-4 h-6 bg-gray-300"></div>
                                    <div class="w-4 h-6 bg-gray-300"></div>
                                </div>
                            </div>
                            <div class="border hover:border-gray-500 rounded py-2 px-3 flex-1 cursor-pointer">
                                <div class="flex justify-center gap-0.5 items-center">
                                    <div class="w-4 h-6 bg-gray-300"></div>
                                    <div class="w-4 h-6 bg-gray-300"></div>
                                    <div class="w-4 h-6 bg-gray-300"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Column divider toggle -->
                    <div class="bg-gray-100 p-2 rounded flex justify-between items-center my-2">
                        <span class="bangla">কলাম ডিভাইডার</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        </label>
                    </div>
                    
                    <!-- Chart (simplified) -->
                    <div class="bg-gray-100 p-2 rounded my-2">
                        <div class="flex justify-center">
                            <div class="w-32 h-32 rounded-full bg-emerald-500 flex items-center justify-center text-white">
                                100%
                            </div>
                        </div>
                        <div class="text-center mt-2">
                            <p class="text-sm">বোর্ড প্রশ্ন</p>
                        </div>
                    </div>
                </div>
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
