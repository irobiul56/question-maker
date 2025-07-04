<script setup>
import LatexRenderer from '@/Components/LatexRenderer.vue';
import UserDashboardLayout from '@/Layouts/UserDashboardLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, computed, nextTick } from 'vue';
import html2canvas from 'html2canvas';
import jsPDF from 'jspdf';

const props = defineProps({
    savedquestion: {
        type: Array,
        required: true,
        default: () => []
    }
});

// Utility functions
const getBanglaLetter = (index) => {
    const banglaLetters = ['ক', 'খ', 'গ', 'ঘ', 'ঙ', 'চ', 'ছ', 'জ', 'ঝ', 'ঞ'];
    return banglaLetters[index] || String.fromCharCode(2433 + index);
};

const toBengaliInteger = (num) => {
    const numerals = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
    return num.toString().replace(/[0-9]/g, digit => numerals[digit]);
};

const toBengaliNumerals = (num) => {
    return toBengaliInteger(num);
};

// Component state
const optionStyle = ref('circle');
const showAnswers = ref(false);
const address = ref(true);
const instruction = ref(true);
const subject = ref(true);
const chapter = ref(true);
const setcode = ref(true);
const currentSet = ref('ক');
const sets = ['ক', 'খ', 'গ', 'ঘ'];
const selectedFont = ref('bangla');
const fontSize = ref(14);
const minFontSize = 10;
const maxFontSize = 24;
const columnCount = ref(1);
const showColumnDivider = ref(true);
const isPrinting = ref(false);
const printError = ref(false);

// Font classes
const fontClasses = {
    'bangla': 'bangla-font',
    'SolaimanLipi': 'font-solaiman-lipi',
    'Kalpurush': 'font-kalpurush',
    'Times New Roman': 'font-times-new-roman'
};

// Create a local reactive copy of questions
const localQuestions = ref(JSON.parse(JSON.stringify(props.savedquestion)));

// Group questions by format
const groupedQuestions = computed(() => {
    const groups = {
        mcq: [],
        cq: [],
        mix: {}
    };
    
    localQuestions.value[0]?.questions?.forEach(question => {
        if (question.format === 'mcq') {
            groups.mcq.push(question);
        } else if (question.format === 'cq') {
            groups.cq.push(question);
        } else if (question.format === 'mix') {
            // Get level name or use default if null
            const levelName = question.lavel?.name || 'সাধারণ প্রশ্ন';
            
            // Initialize array for this level if it doesn't exist
            if (!groups.mix[levelName]) {
                groups.mix[levelName] = [];
            }
            
            groups.mix[levelName].push(question);
        }
    });
    
    return groups;
});

// Check if questions exist for each format
const hasMCQ = computed(() => groupedQuestions.value.mcq.length > 0);
const hasCQ = computed(() => groupedQuestions.value.cq.length > 0);
const hasMix = computed(() => groupedQuestions.value.mix.length > 0);

// Calculate marks
const fullMarks = computed(() => {
    return toBengaliInteger(groupedQuestions.value.mcq.length);
});

const fullMarkscq = computed(() => {
    const total = groupedQuestions.value.cq.reduce((sum, question) => {
        return sum + question.cqoptions.reduce((qSum, cq) => qSum + parseFloat(cq.mark), 0);
    }, 0);
    return toBengaliInteger(total);
});

const totalTime = computed(() => {
    const mcqTime = groupedQuestions.value.mcq.length;
    const cqTime = groupedQuestions.value.cq.length * 10; // Assuming 10 minutes per CQ
    return `${toBengaliInteger(mcqTime + cqTime)} মিনিট`;
});

// Methods
function setOptionStyle(style) {
    optionStyle.value = style;
}

function shuffleQuestions() {
    currentSet.value = sets[Math.floor(Math.random() * sets.length)];
    const questionsCopy = JSON.parse(JSON.stringify(localQuestions.value[0].questions));
    
    for (let i = questionsCopy.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [questionsCopy[i], questionsCopy[j]] = [questionsCopy[j], questionsCopy[i]];
    }
    
    questionsCopy.forEach(question => {
        if (question.options) {
            for (let i = question.options.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [question.options[i], question.options[j]] = [question.options[j], question.options[i]];
            }
        }
    });
    
    localQuestions.value = [{ questions: questionsCopy }];
}

function increaseFontSize() {
    if (fontSize.value < maxFontSize) fontSize.value++;
}

function decreaseFontSize() {
    if (fontSize.value > minFontSize) fontSize.value--;
}

function setColumns(count) {
    columnCount.value = count;
}

const printQuestions = () => {
    isPrinting.value = true;
    printError.value = false;
    const originalHTML = document.body.innerHTML;
    const originalScrollPos = window.scrollY;

    try {
        const element = document.getElementById('questions-container');
        const clone = element.cloneNode(true);
        const contentDiv = clone.querySelector('.question-content > div');
        
        if (contentDiv) {
            contentDiv.style.columnCount = columnCount.value;
            contentDiv.style.columnGap = showColumnDivider.value ? '1.5rem' : '0.5rem';
            contentDiv.style.columnRule = showColumnDivider.value ? '1px solid rgba(0, 0, 0, 0.2)' : 'none';
        }

        const printContainer = document.createElement('div');
        printContainer.id = 'print-container';
        printContainer.appendChild(clone);

        const printStyles = document.createElement('style');
        printStyles.innerHTML = `
            @page { size: A4; margin: 10mm; }
            body { background: white !important; }
            #print-container {
                width: 210mm !important;
                margin: 0 auto !important;
                padding: 10mm !important;
            }
            .break-inside-avoid { 
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
        `;

        document.body.innerHTML = '';
        document.body.appendChild(printStyles);
        document.body.appendChild(printContainer);

        const reloadTimeout = setTimeout(() => {
            window.location.reload();
        }, 3000);

        window.onafterprint = () => {
            clearTimeout(reloadTimeout);
            window.location.reload();
        };

        window.print();

    } catch (error) {
        console.error('Print failed:', error);
        printError.value = true;
        document.body.innerHTML = originalHTML;
        window.scrollTo(0, originalScrollPos);
        isPrinting.value = false;
        window.location.reload();
    }
};

// Load fonts
onMounted(() => {
    const fontLinks = [
        'https://fonts.maateen.me/siyam-rupali/font.css',
        'https://fonts.maateen.me/kalpurush/font.css',
        'https://fonts.maateen.me/solaiman-lipi/font.css'
    ];
    
    fontLinks.forEach(link => {
        const existing = document.querySelector(`link[href="${link}"]`);
        if (!existing) {
            const fontLink = document.createElement('link');
            fontLink.href = link;
            fontLink.rel = 'stylesheet';
            document.head.appendChild(fontLink);
        }
    });
});

</script>

<template>

    <Head title="User Dashboard" />

    <UserDashboardLayout>
        <!-- Error Alert -->
        <div v-if="printError" class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50">
            <div class="bg-white p-6 rounded-lg shadow-lg max-w-md">
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 bangla mt-2">প্রিন্টে সমস্যা হয়েছে</h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500 bangla">আবার চেষ্টা করুন</p>
                    </div>
                    <div class="mt-4">
                        <button @click="printError = false" type="button" class="bangla inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:text-sm">
                            ঠিক আছে
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
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
                
                <!-- Questions Container -->
                <div id="questions-container" :class="[fontClasses[selectedFont], 'question-text']" class="break-inside-avoid first-letter:w-full p-[5mm] md:p-[10mm] print:p-0 print:w-full print:shadow-none scroll-m-0 bg-white">
                    <!-- Header section -->
                    <div class="py-2 print:py-0">
                        <h1 class="text-xl font-bold text-center" contenteditable="true">Jamalpur Kaliakair M E H Arif Institute</h1>
                        <h3 v-if="address" class="text-xl font-bold text-center" contenteditable="true">Jamalpur, Kaliakair, Gazipur</h3>
                        <div class="relative" contenteditable="true">
                            <p class="text-center text-lg">{{ localQuestions[0]?.exam_name }}</p>
                            <p class="text-center text-lg">{{ localQuestions[0]?.questions[0]?.academic_class?.name }}</p>
                            <p v-if="subject" class="text-center truncate">{{ localQuestions[0]?.questions[0]?.subject?.name }}</p>
                            <p v-if="chapter" class="text-center truncate">{{ localQuestions[0]?.questions[0]?.chapter?.name || '' }}</p>
                            <!-- Set indicator -->
                            <div v-if="setcode" class="absolute top-0 right-0 flex">
                                <p class="border px-2 border-gray-500">সেট</p>
                                <p class="border-y border-r px-2 border-gray-500">{{ currentSet }}</p>
                            </div>
                        </div>
                        <div class="flex justify-between relative">
                        <!-- সময় সেকশন -->
                        <div class="flex items-center">
                            সময়—
                            <p class="mx-1" contenteditable="true">
                                <!-- শর্তসাপেক্ষে সময় দেখানো -->
                                <template v-if="hasMCQ && !hasCQ && !hasMix">{{ totalTime }}</template>
                                <template v-else-if="hasCQ && !hasMCQ && !hasMix">২ ঘন্টা ৩০ মিনিট</template>
                                <template v-else>২ ঘন্টা ৩০ মিনিট</template>
                            </p>
                        </div>

                        <!-- পূর্ণমান সেকশন -->
                        <div class="flex items-center">
                            পূর্ণমান—
                            <span class="mx-1" contenteditable="true">
                                <!-- শর্তসাপেক্ষে পূর্ণমান দেখানো -->
                                <template v-if="hasMCQ && !hasCQ && !hasMix">{{ fullMarks }}</template>
                                <template v-else-if="hasCQ && !hasMCQ && !hasMix">{{ fullMarkscq }}</template>
                                <template v-else>১০০</template>
                            </span>
                        </div>
                    </div>
                        <hr>
                        <div v-if="instruction" class="text-center text-sm my-1">
                            <template v-if="hasMCQ && !hasCQ">
                                <span contenteditable="true"><i><span class="bangla-bold">দ্রষ্টব্যঃ</span> সরবরাহকৃত নৈর্ব্যত্তিক অভীক্ষার উত্তরপত্রে প্রশ্নের ক্রমিক নম্বরের বিপরীতে প্রদত্ত বর্ণসম্বলিত বৃত্ত সমুহ হতে সঠিক উত্তরের বৃত্তটি</i> 
                                (<svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" class="inline-block" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512z"></path>
                                </svg>) <i>বল পয়েন্ট কলম দ্বারা সম্পুর্ণ ভরাট করো । প্রতিটি প্রশ্নের মান ১ ।</i></span>
                                <p class="bangla-bold text-center text-sm mt-1 font-bold">প্রশ্নপত্রে কোনো প্রকার দাগ/চিহ্ন দেয়া যাবেনা ।</p>
                            </template>
                            <template v-if="hasCQ && !hasMCQ">
                                <span contenteditable="true">
                                    <i>
                                    <span class="bangla-bold">দ্রষ্টব্যঃ</span> 
                                    ডান পাশের সংখ্যা প্রশ্নের পূর্ণমান জ্ঞাপক। প্রদত্ত উদ্দীপকগুলো মনোযোগসহকারে পর এবং সংশ্লিষ্ট প্রশ্নগুলোর উত্তর দাও। যেকোনো 
                                    <span 
                                        class="border-none px-0 py-0 inline-block min-w-[1ch] text-center" 
                                        contenteditable="true"
                                    >৫</span> 
                                    টি প্রশ্নের উত্তর দিতে হবে।
                                    </i>
                                </span>
                                </template>
                        </div>
                    </div>

                    <!-- Questions section -->
                    <div :style="{ fontSize: `${fontSize}px` }" class="question-content">
                        <div v-if="localQuestions[0]?.questions?.length > 0" class="relative flex-1"
                            :class="{
                                'columns-1': columnCount === 1,
                                'columns-2': columnCount === 2,
                                'columns-3': columnCount === 3
                            }"
                            :style="{
                                'column-count': columnCount,
                                'column-rule': showColumnDivider ? '1px solid rgba(0, 0, 0, 0.2)' : 'none',
                                'column-gap': showColumnDivider ? '1.5rem' : '0.5rem'
                            }">

                            <!-- MCQ Questions Section -->
                            <div v-if="hasMCQ" class="mb-6">
                                <h3 contenteditable="true" v-if="hasMCQ && hasCQ" class="font-bold bangla-bold mb-2">বহুনির্বাচনী প্রশ্ন</h3>
                                
                                <div v-for="(question, questionIndex) in groupedQuestions.mcq" :key="`mcq-${questionIndex}`" 
                                    class="bg-white relative p-0.5 hover:bg-gray-50 rounded group break-inside-avoid">
                                    <div class="flex items-baseline gap-x-2">
                                        <span class="bangla-bold text-gray-900">{{ toBengaliNumerals(questionIndex + 1) }}.</span>
                                        <div class="flex flex-wrap justify-between items-center w-full">
                                            <div class="false bangla-bold">
                                                <LatexRenderer :content="question.question_text" />
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- MCQ Options -->
                                    <div class="relative grid grid-cols-2 ml-7 group">
                                        <div v-for="(option, optionIndex) in question.options" :key="`mcq-opt-${optionIndex}`" 
                                            class="option flex flex-1 items-baseline">
                                            <div class="shrink-0 mr-1 flex justify-center items-center"
                                                :class="{
                                                    'h-4 w-4 border border-gray-500 rounded-full': optionStyle === 'circle',
                                                    'text-black': optionStyle === 'parens',
                                                    'text-black': optionStyle === 'dot',
                                                    'text-black': optionStyle === 'paren',
                                                    'bg-black text-white': showAnswers && option.is_correct && optionStyle === 'circle',
                                                    'font-bold underline': showAnswers && option.is_correct && optionStyle !== 'circle'
                                                }">
                                                <span v-if="optionStyle === 'circle'" class="text-xs">
                                                    {{ getBanglaLetter(optionIndex) }}
                                                </span>
                                                <span v-else-if="optionStyle === 'parens'">
                                                    ({{ getBanglaLetter(optionIndex) }})
                                                </span>
                                                <span v-else-if="optionStyle === 'dot'">
                                                    {{ getBanglaLetter(optionIndex) }}.
                                                </span>
                                                <span v-else-if="optionStyle === 'paren'">
                                                    {{ getBanglaLetter(optionIndex) }})
                                                </span>
                                            </div>
                                            <div>
                                                <LatexRenderer :content="option.option_text" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- CQ Questions Section -->
                            <div v-if="hasCQ" class="mb-6">
                                <h3 contenteditable="true" v-if="hasMCQ && hasCQ" class="font-bold bangla-bold mb-2">সৃজনশীল প্রশ্ন</h3>
                                
                                <div v-for="(question, questionIndex) in groupedQuestions.cq" :key="`cq-${questionIndex}`" 
                                    class="bg-white relative p-0.5 hover:bg-gray-50 rounded group break-inside-avoid">
                                    <div class="flex items-baseline gap-x-2">
                                        <span class="bangla-bold text-gray-900">{{ toBengaliNumerals(questionIndex + 1 + (hasMCQ ? groupedQuestions.mcq.length : 0)) }}.</span>
                                        <div class="flex flex-wrap justify-between items-center w-full">
                                            <div class="false bangla-bold">
                                                <LatexRenderer :content="question.question_text" />
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- CQ Sub-questions -->
                                    <div class="relative grid grid-cols-1 ml-7 group">
                                        <div v-for="(cqoption, optionIndex) in question.cqoptions" :key="`cq-opt-${optionIndex}`" 
                                            class="option flex items-center justify-between w-full">
                                            <div class="flex items-baseline flex-grow">
                                                <div class="shrink-0 mr-1 flex justify-center items-center"
                                                    :class="{
                                                        'h-4 w-4 border border-gray-500 rounded-full': optionStyle === 'circle',
                                                        'text-black': optionStyle === 'parens',
                                                        'text-black': optionStyle === 'dot',
                                                        'text-black': optionStyle === 'paren',
                                                    }">
                                                    <span v-if="optionStyle === 'circle'" class="text-xs">
                                                        {{ getBanglaLetter(optionIndex) }}
                                                    </span>
                                                    <span v-else-if="optionStyle === 'parens'">
                                                        ({{ getBanglaLetter(optionIndex) }})
                                                    </span>
                                                    <span v-else-if="optionStyle === 'dot'">
                                                        {{ getBanglaLetter(optionIndex) }}.
                                                    </span>
                                                    <span v-else-if="optionStyle === 'paren'">
                                                        {{ getBanglaLetter(optionIndex) }})
                                                    </span>
                                                </div>
                                                <div class="flex-grow">
                                                    <LatexRenderer :content="cqoption.cq_text" />
                                                </div>
                                            </div>
                                            
                                            <!-- Mark value aligned to the right -->
                                            <div class="shrink-0 ml-4">
                                                {{ toBengaliInteger(cqoption.mark) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Mix Questions Section -->
                            <div v-if="groupedQuestions.mix && Object.keys(groupedQuestions.mix).length > 0" class="mb-6">
                                <template v-for="(questions, levelName) in groupedQuestions.mix" :key="levelName">
                                    <div contenteditable="true" class="flex flex-wrap justify-between items-center w-full">
                                                <div class="false bangla-bold"><h3 class="font-bold bangla-bold mb-2">{{ levelName }}</h3></div>
                                                <span class="text-sm text-gray-500 ml-2">
                                                    {{toBengaliInteger(questions.length * questions[0]?.mark) }}
                                                </span>
                                            </div>
                                    
                                    
                                    <div v-for="(question, questionIndex) in questions" :key="`mix-${questionIndex}`" 
                                        class="bg-white relative p-0.5 hover:bg-gray-50 rounded group break-inside-avoid">
                                        <div class="flex items-baseline gap-x-2">
                                            <span class="bangla-bold text-gray-900">{{ toBengaliNumerals(questionIndex + 1) }}.</span>
                                            <div class="flex flex-wrap justify-between items-center w-full">
                                                <div class="false">
                                                    <LatexRenderer :content="question.question_text" />
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div v-else class="p-4 text-center text-gray-500">
                            No saved questions found
                        </div>
                    </div>
                </div>
            </div>

        <!-- Sidebar settings -->
        <div class="hidden lg:block fixed mt-20 inset-y-0 right-0 w-72 bg-white overflow-y-auto border-l z-10 font-10">
            <div class="relative overflow-hidden">
                <div class="bg-white backdrop-blur-lg p-4">
                    <h1 class="py-2 flex items-center gap-2 justify-center rounded text-center bg-gray-50 mb-2 shadow">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" class="text-gray-500 text-sm" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                            <path d="M495.9 166.6c3.2 8.7 .5 18.4-6.4 24.6l-43.3 39.4c1.1 8.3 1.7 16.8 1.7 25.4s-.6 17.1-1.7 25.4l43.3 39.4c6.9 6.2 9.6 15.9 6.4 24.6c-4.4 11.9-9.7 23.3-15.8 34.3l-4.7 8.1c-6.6 11-14 21.4-22.1 31.2c-5.9 7.2-15.7 9.6-24.5 6.8l-55.7-17.7c-13.4 10.3-28.2 18.9-44 25.4l-12.5 57.1c-2 9.1-9 16.3-18.2 17.8c-13.8 2.3-28 3.5-42.5 3.5s-28.7-1.2-42.5-3.5c-9.2-1.5-16.2-8.7-18.2-17.8l-12.5-57.1c-15.8-6.5-30.6-15.1-44-25.4L83.1 425.9c-8.8 2.8-18.6 .3-24.5-6.8c-8.1-9.8-15.5-20.2-22.1-31.2l-4.7-8.1c-6.1-11-11.4-22.4-15.8-34.3c-3.2-8.7-.5-18.4 6.4-24.6l43.3-39.4C64.6 273.1 64 264.6 64 256s.6-17.1 1.7-25.4L22.4 191.2c-6.9-6.2-9.6-15.9-6.4-24.6c4.4-11.9 9.7-23.3 15.8-34.3l4.7-8.1c6.6-11 14-21.4 22.1-31.2c5.9-7.2 15.7-9.6 24.5-6.8l55.7 17.7c13.4-10.3 28.2-18.9 44-25.4l12.5-57.1c2-9.1 9-16.3 18.2-17.8C227.3 1.2 241.5 0 256 0s28.7 1.2 42.5 3.5c9.2 1.5 16.2 8.7 18.2 17.8l12.5 57.1c15.8 6.5 30.6 15.1 44 25.4l55.7-17.7c8.8-2.8 18.6-.3 24.5 6.8c8.1 9.8 15.5 20.2 22.1 31.2l4.7 8.1c6.1 11 11.4 22.4 15.8 34.3zM256 336a80 80 0 1 0 0-160 80 80 0 1 0 0 160z"></path>
                        </svg> 
                        <span class="text-lg">সেটিংস</span>
                    </h1>
                    
                    <!-- Print Button -->
                    <button 
                      @click="printQuestions"
                      :disabled="isPrinting"
                      class="hover:bg-blue-400 bg-blue-500 transition-all py-2 rounded w-full text-center text-white flex items-center gap-2 justify-center"
                    >
                      <svg v-if="!isPrinting" stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                        <path d="M400 96V56c0-13.3-10.7-24-24-24H136c-13.3 0-24 10.7-24 24v40H24c-13.3 0-24 10.7-24 24v104c0 13.3 10.7 24 24 24h8v96c0 13.3 10.7 24 24 24h400c13.3 0 24-10.7 24-24v-96h8c13.3 0 24-10.7 24-24V120c0-13.3-10.7-24-24-24h-88zm-8 0H120V56h272v40zM104 392H56v-80h48v80zm344 0h-48v-80h48v80zm40-128H24V120h432v144zm-40 64c13.3 0 24-10.7 24-24s-10.7-24-24-24-24 10.7-24 24 10.7 24 24 24z"></path>
                      </svg>
                      <span v-if="!isPrinting">প্রিন্ট করুন</span>
                      <span v-else class="bangla">প্রিন্ট প্রস্তুত হচ্ছে...</span>
                    </button>
                    
                    <!-- Answer sheet toggle -->
                    <div class="bg-gray-100 p-2 rounded flex justify-between items-center my-2">
                        <span class="bangla">উত্তরপত্র</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" v-model="showAnswers" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        </label>
                    </div>
                    
                    <!-- Editing mode toggle -->
                    <!-- <div class="bg-gray-100 p-2 rounded my-2">
                        <div class="flex justify-between items-center">
                            <span class="bangla">এডিটিং মোড</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                            </label>
                        </div>
                    </div> -->
                    
                    <!-- Sheet toggle -->
                    <!-- <div>
                        <div class="relative bg-gray-100 p-2 rounded flex justify-between items-center my-2">
                            <span class="bangla">শীট</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                            </label>
                        </div>
                    </div> -->
                    
                    <!-- Option style selector -->
                    <div class="relative bg-gray-100 p-2 rounded my-2">
                        <p class="bangla mb-2">অপশন স্টাইল</p>
                        <div class="flex gap-2">
                            <!-- Circle -->
                            <div @click="setOptionStyle('circle')" 
                                class="p-1 flex-1 flex justify-center items-center cursor-pointer rounded"
                                :class="{ 'bg-emerald-600 text-white': optionStyle === 'circle', 'bg-white hover:bg-gray-100': optionStyle !== 'circle' }">
                                <div class="h-5 w-5 border border-gray-500 rounded-full flex items-center justify-center">
                                    ○
                                </div>
                            </div>
                            
                            <!-- Parentheses () -->
                            <div @click="setOptionStyle('parens')" 
                                class="p-1 flex-1 flex justify-center items-center cursor-pointer rounded"
                                :class="{ 'bg-emerald-600 text-white': optionStyle === 'parens', 'bg-white hover:bg-gray-100': optionStyle !== 'parens' }">
                                ( )
                            </div>
                            
                            <!-- Dot . -->
                            <div @click="setOptionStyle('dot')" 
                                class="p-1 flex-1 flex justify-center items-center cursor-pointer rounded"
                                :class="{ 'bg-emerald-600 text-white': optionStyle === 'dot', 'bg-white hover:bg-gray-100': optionStyle !== 'dot' }">
                                <span class="text-2xl leading-none">.</span>
                            </div>
                            
                            <!-- Single Parenthesis ) -->
                            <div @click="setOptionStyle('paren')" 
                                class="p-1 flex-1 flex justify-center items-center cursor-pointer rounded"
                                :class="{ 'bg-emerald-600 text-white': optionStyle === 'paren', 'bg-white hover:bg-gray-100': optionStyle !== 'paren' }">
                                )
                            </div>
                        </div>
                    </div>
                    
                    <!-- Address toggle -->
                    <div>
                        <div class="bg-gray-100 p-2 rounded flex justify-between items-center my-2">
                            <span class="bangla">ঠিকানা</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input v-model="address" type="checkbox" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Watermark toggle -->
                    <!-- <div class="bg-gray-100 my-2 p-2">
                        <div class="rounded flex justify-between items-center">
                            <span class="bangla">জলছাপ</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                            </label>
                        </div>
                    </div> -->
                    
                    <!-- Shuffle button -->
                    <div>
                        <div class="cursor-pointer bg-gray-100 hover:bg-emerald-600 hover:text-white flex justify-between items-center p-2 rounded my-2" 
                            @click="shuffleQuestions" tabindex="0">
                        <p>শাফল করুন</p>
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                            <path d="M403.8 34.4c12-5 25.7-2.2 34.9 6.9l64 64c6 6 9.4 14.1 9.4 22.6s-3.4 16.6-9.4 22.6l-64 64c-9.2 9.2-22.9 11.9-34.9 6.9s-19.8-16.6-19.8-29.6V160H352c-10.1 0-19.6 4.7-25.6 12.8L284 229.3 244 176l31.2-41.6C293.3 110.2 321.8 96 352 96h32V64c0-12.9 7.8-24.6 19.8-29.6zM164 282.7L204 336l-31.2 41.6C154.7 401.8 126.2 416 96 416H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H96c10.1 0 19.6-4.7 25.6-12.8L164 282.7zm274.6 188c-9.2 9.2-22.9 11.9-34.9 6.9s-19.8-16.6-19.8-29.6V416H352c-30.2 0-58.7-14.2-76.8-38.4L121.6 172.8c-6-8.1-15.5-12.8-25.6-12.8H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H96c30.2 0 58.7 14.2 76.8 38.4L326.4 339.2c6 8.1 15.5 12.8 25.6 12.8h32V320c0-12.9 7.8-24.6 19.8-29.6s25.7-2.2 34.9 6.9l64 64c6 6 9.4 14.1 9.4 22.6s-3.4 16.6-9.4 22.6l-64 64z"></path>
                        </svg>
                        </div>
                    </div>
                    
                    <!-- OMR toggle -->
                    <!-- <div>
                        <div class="bg-gray-100 p-2 rounded flex justify-between items-center my-2">
                            <span class="bangla">OMR সংযুক্ত</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                            </label>
                        </div>
                    </div>
                     -->
                    <!-- Instructions toggle -->
                    <div class="bg-gray-100 p-2 rounded flex justify-between items-center my-2">
                        <span class="bangla">নির্দেশনা</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input v-model="instruction" type="checkbox" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        </label>
                    </div>
                    
                    <!-- Subject name toggle -->
                    <div class="bg-gray-100 p-2 rounded flex justify-between items-center my-2">
                        <span class="bangla">বিষয়ের নাম</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input v-model="subject" type="checkbox" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        </label>
                    </div>
                    
                    <!-- Chapter name toggle -->
                    <div class="bg-gray-100 p-2 rounded flex justify-between items-center my-2">
                        <span class="bangla">অধ্যায়ের নাম</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input v-model="chapter" type="checkbox" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        </label>
                    </div>
                    
                    <!-- Important questions toggle -->
                    <!-- <div class="bg-gray-100 p-2 rounded flex justify-between items-center my-2">
                        <span class="bangla">গুরুত্বপূর্ণ প্রশ্ন</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        </label>
                    </div> -->
                    
                    <!-- Set code toggle -->
                    <div class="bg-gray-100 p-2 rounded flex justify-between items-center my-2">
                        <span class="bangla">সেট কোড</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input v-model="setcode" type="checkbox" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        </label>
                    </div>
                    
                    <!-- Title toggle -->
                    <!-- <div class="bg-gray-100 p-2 rounded flex justify-between items-center my-2">
                        <span class="bangla">টাইটেল</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        </label>
                    </div> -->
                    
                    <!-- Question info toggle -->
                    <!-- <div class="bg-gray-100 p-2 rounded flex justify-between items-center my-2">
                        <span class="bangla">প্রশ্নের তথ্য</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        </label>
                    </div> -->
                    
                   <!-- Font Selector -->
                    <div class="bg-gray-100 my-2 p-2">
                        <div class="rounded justify-between items-center">
                        <p class="bangla mb-1 text-center">ফন্ট পরিবর্তন</p>
                        <select 
                            v-model="selectedFont"
                            class="w-full p-1 border rounded bangla"
                        >
                            <option value="bangla">বাংলা</option>
                            <option value="SolaimanLipi">সোলাইমান লিপি</option>
                            <option value="Kalpurush">কালপুরুষ</option>
                            <option value="Times New Roman">টাইমস নিউ রোমান</option>
                        </select>
                        </div>
                    </div>
                    
                    <!-- Font size control -->
                    <div class="bg-gray-100 p-2 rounded flex justify-between items-center my-2">
                        <span class="bangla">ফন্ট সাইজ</span>
                        <div class="flex items-center gap-1">
                            <button 
                                @click="decreaseFontSize"
                                class="hover:bg-white px-2 rounded text-lg"
                                :disabled="fontSize <= minFontSize"
                                :class="{ 'opacity-50 cursor-not-allowed': fontSize <= minFontSize }"
                            >-</button>
                            <p class="border rounded p-0.5 px-1.5 bg-white">{{ fontSize }}</p>
                            <button 
                                @click="increaseFontSize"
                                class="hover:bg-white px-2 rounded text-lg"
                                :disabled="fontSize >= maxFontSize"
                                :class="{ 'opacity-50 cursor-not-allowed': fontSize >= maxFontSize }"
                            >+</button>
                        </div>
                    </div>
                    
                    <!-- Columns selector -->
                    <div class="my-2">
                    <p class="bangla mb-1">কলাম</p>
                    <div class="flex gap-2 justify-center">
                        <!-- 1 Column -->
                        <div 
                        @click="setColumns(1)"
                        class="border rounded py-2 px-3 flex-1 cursor-pointer transition-colors"
                        :class="{
                            'border-black': columnCount === 1,
                            'hover:border-gray-500': columnCount !== 1
                        }"
                        >
                        <div class="flex justify-center gap-0.5 items-center">
                            <div class="w-4 h-6 bg-gray-300"></div>
                        </div>
                        </div>
                        
                        <!-- 2 Columns -->
                        <div 
                        @click="setColumns(2)"
                        class="border rounded py-2 px-3 flex-1 cursor-pointer transition-colors"
                        :class="{
                            'border-black': columnCount === 2,
                            'hover:border-gray-500': columnCount !== 2
                        }"
                        >
                        <div class="flex justify-center gap-0.5 items-center">
                            <div class="w-4 h-6 bg-gray-300"></div>
                            <div class="w-4 h-6 bg-gray-300"></div>
                        </div>
                        </div>
                        
                        <!-- 3 Columns -->
                        <div 
                        @click="setColumns(3)"
                        class="border rounded py-2 px-3 flex-1 cursor-pointer transition-colors"
                        :class="{
                            'border-black': columnCount === 3,
                            'hover:border-gray-500': columnCount !== 3
                        }"
                        >
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
                            <input v-model="showColumnDivider" type="checkbox" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        </label>
                    </div>
                    
                    <!-- Chart (simplified) -->
                    <!-- <div class="bg-gray-100 p-2 rounded my-2">
                        <div class="flex justify-center">
                            <div class="w-32 h-32 rounded-full bg-emerald-500 flex items-center justify-center text-white">
                                100%
                            </div>
                        </div>
                        <div class="text-center mt-2">
                            <p class="text-sm">বোর্ড প্রশ্ন</p>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
    </div>
    </UserDashboardLayout>
</template>

<style scoped>
    @import url('https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;700&display=swap');
    @import url('https://fonts.maateen.me/siyam-rupali/font.css');
    @import url('https://fonts.maateen.me/kalpurush/font.css');
    @import url('https://fonts.maateen.me/solaiman-lipi/font.css');
     .bangla {
            font-family: 'Hind Siliguri', sans-serif;
        }
        .bangla-bold {
            font-family: 'Hind Siliguri', sans-serif;
            font-weight: 700;
        }


.font-siyam-rupali {
  font-family: 'Siyam Rupali', Arial, sans-serif;
}

.font-solaiman-lipi {
  font-family: 'SolaimanLipi', Arial, sans-serif;
}

.font-kalpurush {
  font-family: 'Kalpurush', Arial, sans-serif;
}

.font-times-new-roman {
  font-family: 'Times New Roman', serif;
}

.question-text {
  font-size: 14px;
  line-height: 1.5;
}

/* Scoped selectors for questions */
.question-text :deep(.question-content) {
  font-family: inherit; /* Inherits from parent */
}

/* Scoped but allows font to penetrate child components */
.question-text :deep(*) {
  font-family: inherit;
}

.question-content {
  line-height: 1.5; /* Maintain proper spacing */
}

/* Ensure all text elements inherit the size */
.question-content :deep(*) {
  font-size: inherit;
}

/* Option circles should maintain their size */
.question-content :deep(.option-circle) {
  font-size: 12px; /* Fixed size for circles */
  width: 1rem;
  height: 1rem;
}

/* Disabled button styling */
button[disabled] {
  opacity: 0.5;
  cursor: not-allowed;
}

.break-inside-avoid {
  break-inside: avoid;
  page-break-inside: avoid;
}

/* Smooth transitions for column changes */
.columns-1, .columns-2, .columns-3 {
  transition: column-count 0.3s ease;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .columns-3 {
    columns: 2 !important;
  }
}

@media (max-width: 640px) {
  .columns-2, .columns-3 {
    columns: 1 !important;
  }
}

/* For screen view */
.columns-1 {
  column-count: 1 !important;
}
.columns-2 {
  column-count: 2 !important;
}
.columns-3 {
  column-count: 3 !important;
}

/* For print view */
@media print {
  .columns-1 {
    column-count: 1 !important;
  }
  .columns-2 {
    column-count: 2 !important;
  }
  .columns-3 {
    column-count: 3 !important;
  }
}


</style>
