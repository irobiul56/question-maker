<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { usePage, useForm } from '@inertiajs/vue3';
import { ElMessage, ElLoading } from "element-plus";
import { Plus, Edit, Delete, Check } from '@element-plus/icons-vue';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import LatexRenderer from '@/Components/LatexRenderer.vue';

const { props } = usePage();
const isLoading = ref(false);
const isResolvingHierarchy = ref(false);
const isTableLoading = ref(true); // Add table loading state

// Reactive data
const questions = ref(props.questions ?? []);
const topics = ref(props.topics ?? []);
const chapters = ref(props.chapters ?? []);
const education = ref(props.education ?? []);
const classes = ref(props.classes ?? []);
const subjects = ref(props.subjects ?? []);
const levels = ref(props.levels ?? []);
const types = ref(props.types ?? []);
const boards = ref(props.boards ?? []);

// Use:
const topicId = questions.topic?.id;

// UI state
const isQuestionModalVisible = ref(false);
const isTopicModalVisible = ref(false);
const itemsPerPage = ref(10);
const currentPage = ref(1);
const searchQuery = ref('');
const selectedEducation = ref('');
const selectedClass = ref('');
const selectedSubject = ref('');
const selectedChapter = ref('');
const selectedTopic = ref('');

// Forms
const questionForm = useForm({
    id: null,
    topic_id: '',
    type_id: [],
    level_id: '',
    board_id: '',
    academic_classes_id: '',
    subject_id: '',
    chapter_id: '',
    format: 'mcq',
    question_text: '',
    explanation: '',
    mark: '',
    options: [
        { option_text: '', is_correct: false },
        { option_text: '', is_correct: false },
        { option_text: '', is_correct: false },
        { option_text: '', is_correct: false }
    ],
    cq: [
        { cq_text: '', mark: '' },
        { cq_text: '', mark: '' },
        { cq_text: '', mark: '' },
        { cq_text: '', mark: '' },
    ],
});

const topicForm = useForm({
    id: null,
    name: '',
    chapter_id: selectedChapter.value || ''
});

// Computed properties
const filteredClasses = computed(() => {
    if (!selectedEducation.value) return [];
    return classes.value.filter(cls => cls.education_id == selectedEducation.value);
});

const filteredSubjects = computed(() => {
    if (!selectedClass.value) return [];
    return subjects.value.filter(subject => subject.academic_classes_id == selectedClass.value);
});

const filteredChapters = computed(() => {
    if (!selectedSubject.value) return [];
    return chapters.value.filter(chapter => chapter.subject_id == selectedSubject.value);
});

const filteredTopics = computed(() => {
    if (!selectedChapter.value) return [];
    return topics.value.filter(topic => topic.chapter_id == selectedChapter.value);
});

const filteredQuestions = computed(() => {
    return questions.value.filter(question => {
        const matchesTopic = !selectedTopic.value || question.topic_id == selectedTopic.value;
        const matchesSearch = !searchQuery.value ||
            question.question_text.toLowerCase().includes(searchQuery.value.toLowerCase());
        return matchesTopic && matchesSearch;
    });
});

const paginatedQuestions = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    const end = start + itemsPerPage.value;
    return filteredQuestions.value.slice(start, end);
});

const totalQuestionPages = computed(() => {
    return Math.ceil(filteredQuestions.value.length / itemsPerPage.value);
});

// Watchers
watch(() => props, (newProps) => {
    questions.value = newProps.questions ?? [];
    topics.value = newProps.topics ?? [];
    chapters.value = newProps.chapters ?? [];
    education.value = newProps.education ?? [];
    classes.value = newProps.classes ?? [];
    subjects.value = newProps.subjects ?? [];
    levels.value = newProps.levels ?? [];
    types.value = newProps.types ?? [];
    boards.value = newProps.boards ?? [];
    
    // Hide table loading when data is loaded
    if (newProps.questions) {
        isTableLoading.value = false;
    }
}, { deep: true });

// On mounted - set a timeout to handle loading state
onMounted(() => {
    // If data is already loaded, hide loading
    if (props.questions && props.questions.length > 0) {
        isTableLoading.value = false;
    } else {
        // Set a timeout to handle potential delays
        setTimeout(() => {
            if (questions.value.length === 0) {
                isTableLoading.value = false;
            }
        }, 5000); // 5 second timeout
    }
});

watch(() => questionForm.topic_id, async (newTopicId) => {
    if (!newTopicId) {
        questionForm.academic_classes_id = '';
        questionForm.subject_id = '';
        questionForm.chapter_id = '';
        return;
    }

    isResolvingHierarchy.value = true;
    
    try {
        const selectedTopic = topics.value.find(t => t.id == newTopicId);
        if (!selectedTopic) {
            console.error('Topic not found:', newTopicId);
            return;
        }

        const chapter = chapters.value.find(c => c.id == selectedTopic.chapter_id);
        if (!chapter) {
            console.error('Chapter not found for topic:', selectedTopic.chapter_id);
            return;
        }

        const subject = subjects.value.find(s => s.id == chapter.subject_id);
        if (!subject) {
            console.error('Subject not found for chapter:', chapter.subject_id);
            return;
        }

        const cls = classes.value.find(c => c.id == subject.academic_classes_id);
        if (!cls) {
            console.error('Class not found for subject:', subject.academic_classes_id);
            return;
        }

        // Update form fields
        questionForm.chapter_id = chapter.id;
        questionForm.subject_id = subject.id;
        questionForm.academic_classes_id = cls.id;
        
        // Update filters to match
        selectedEducation.value = cls.education_id;
        selectedClass.value = cls.id;
        selectedSubject.value = subject.id;
        selectedChapter.value = chapter.id;
        
    } catch (error) {
        console.error('Error resolving hierarchy:', error);
        ElMessage.error('Failed to resolve topic hierarchy');
    } finally {
        isResolvingHierarchy.value = false;
    }
}, { immediate: true });

// Methods
const submitQuestion = async () => {
    isLoading.value = true;
    
    try {
        // Final verification before submission
        if (questionForm.topic_id) {
            const selectedTopic = topics.value.find(t => t.id == questionForm.topic_id);
            if (selectedTopic) {
                if (!questionForm.chapter_id) questionForm.chapter_id = selectedTopic.chapter_id;
                
                const chapter = chapters.value.find(c => c.id == questionForm.chapter_id);
                if (chapter && !questionForm.subject_id) questionForm.subject_id = chapter.subject_id;
                
                const subject = subjects.value.find(s => s.id == questionForm.subject_id);
                if (subject && !questionForm.academic_classes_id) questionForm.academic_classes_id = subject.academic_classes_id;
            }
        }

        const isUpdate = Boolean(questionForm.id);
        const method = isUpdate ? 'put' : 'post';
        const url = isUpdate ? route('question.update', questionForm.id) : route('question.store');

        await questionForm[method](url, {
            preserveScroll: true,
            onSuccess: (page) => {
                const updatedQuestion = page.props.flash?.updatedQuestion || 
                                     page.props.question || 
                                     (page.props.questions?.length ? page.props.questions[0] : null);

                if (!updatedQuestion) {
                    throw new Error("Invalid response from server");
                }

                if (isUpdate) {
                    const index = questions.value.findIndex(q => q.id === updatedQuestion.id);
                    if (index !== -1) {
                        questions.value.splice(index, 1, updatedQuestion);
                    } else {
                        questions.value.unshift(updatedQuestion);
                    }
                } else {
                    questions.value.unshift(updatedQuestion);
                }

                questionForm.reset();
                isQuestionModalVisible.value = false;
                ElMessage.success(`Question ${isUpdate ? 'updated' : 'created'} successfully!`);
            },
            onError: (errors) => {
                const errorMsg = errors?.message || Object.values(errors).join('\n');
                ElMessage.error(`Operation failed: ${errorMsg}`);
            }
        });
    } catch (error) {
        console.error('Submission error:', error);
        ElMessage.error(error.message || 'Failed to submit question');
    } finally {
        isLoading.value = false;
    }
};

const deleteQuestion = (questionId) => {
    if (confirm("Are you sure you want to delete this question?")) {
        const loading = ElLoading.service({
            lock: true,
            text: 'Deleting question...',
            background: 'rgba(0, 0, 0, 0.7)'
        });

        const questionIndex = questions.value.findIndex(q => q.id === questionId);
        questions.value = questions.value.filter(q => q.id !== questionId);

        questionForm.delete(route('question.destroy', questionId), {
            onSuccess: () => {
                loading.close();
                ElMessage.success("Question deleted successfully!");
            },
            onError: () => {
                loading.close();
                ElMessage.error("Failed to delete the question. Please try again.");
                if (questionIndex !== -1) {
                    const deletedQuestion = props.questions.find(q => q.id === questionId);
                    if (deletedQuestion) {
                        questions.value.splice(questionIndex, 0, deletedQuestion);
                    }
                }
            }
        });
    }
};

const bengaliChars = ['ক', 'খ', 'গ', 'ঘ', 'ঙ', 'চ', 'ছ', 'জ', 'ঝ', 'ঞ', 'ট', 'ঠ', 'ড', 'ঢ', 'ণ', 'ত', 'থ', 'দ', 'ধ', 'ন', 'প', 'ফ', 'ব', 'ভ', 'ম', 'য', 'র', 'ল', 'শ', 'ষ', 'স', 'হ', 'ড়', 'ঢ়', 'য়'];
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Questions Management" />

        <!-- Flash Message -->
        <div v-if="$page.props.flash?.message"
            class="p-4 mb-6 text-sm text-white rounded-lg bg-gradient-to-r from-green-400 to-blue-500 shadow-lg">
            <div class="flex items-center justify-between">
                <span>{{ $page.props.flash?.message }}</span>
                <button @click="$page.props.flash.message = null" class="text-white hover:text-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
        </div>

        <div class="container mx-auto px-4 sm:px-6 lg:px-8 mt-5">
            <!-- Search and Filter Section -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6 border border-gray-100">
                <div class="flex flex-col lg:flex-row items-center justify-between gap-4">
                    <!-- Education Level Dropdown -->
                    <el-select v-model="selectedEducation" placeholder="All Education Levels" clearable
                        class="w-full lg:w-48" size="large">
                        <el-option v-for="edu in education" :key="edu.id" :label="edu.name" :value="edu.id" />
                    </el-select>

                    <!-- Class Dropdown -->
                    <el-select v-model="selectedClass" placeholder="All Classes" clearable
                        :disabled="!selectedEducation" class="w-full lg:w-48" size="large">
                        <el-option v-for="cls in filteredClasses" :key="cls.id" :label="cls.name" :value="cls.id" />
                    </el-select>

                    <!-- Subject Dropdown -->
                    <el-select v-model="selectedSubject" placeholder="All Subjects" clearable :disabled="!selectedClass"
                        class="w-full lg:w-48" size="large">
                        <el-option v-for="subject in filteredSubjects" :key="subject.id" :label="subject.name"
                            :value="subject.id" />
                    </el-select>

                    <!-- Chapter Dropdown -->
                    <el-select v-model="selectedChapter" placeholder="All Chapters" clearable
                        :disabled="!selectedSubject" class="w-full lg:w-48" size="large">
                        <el-option v-for="chapter in filteredChapters" :key="chapter.id" :label="chapter.name"
                            :value="chapter.id" />
                    </el-select>

                    <!-- Topic Dropdown -->
                    <el-select v-model="selectedTopic" placeholder="All Topics" clearable :disabled="!selectedChapter"
                        class="w-full lg:w-48" size="large">
                        <el-option v-for="topic in filteredTopics" :key="topic.id" :label="topic.name"
                            :value="topic.id" />
                    </el-select>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto mt-4 lg:mt-0">
                        <Link :href="route('question.create')">
                            <el-button type="primary" size="large" class="w-full lg:w-auto">
                                <el-icon class="mr-2">
                                    <Plus />
                                </el-icon>
                                Add Question
                            </el-button>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Questions Table -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Questions</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Showing {{ filteredQuestions.length }} questions for selected filters
                        </p>
                    </div>
                    
                    <!-- Search Bar -->
                    <div class="w-full sm:w-64">
                        <el-input
                            v-model="searchQuery"
                            placeholder="Search questions..."
                            clearable
                            size="large"
                        >
                            <template #prefix>
                                <el-icon class="el-input__icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="11" cy="11" r="8"></circle>
                                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                    </svg>
                                </el-icon>
                            </template>
                        </el-input>
                    </div>

                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-500">
                            Page {{ currentPage }} of {{ totalQuestionPages }}
                        </span>
                        <div class="flex space-x-2">
                            <el-button @click="currentPage--" :disabled="currentPage <= 1" size="small">
                                Previous
                            </el-button>
                            <el-button @click="currentPage++" :disabled="currentPage >= totalQuestionPages" size="small">
                                Next
                            </el-button>
                        </div>
                    </div>
                </div>

                <!-- Table Loading State -->
                <div v-if="isTableLoading" class="p-8">
                    <div class="flex flex-col items-center justify-center space-y-4">
                        <!-- Loading Spinner -->
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                        <div class="text-center">
                            <p class="text-gray-600 font-medium">Loading questions...</p>
                            <p class="text-sm text-gray-500 mt-1">Please wait while we fetch your data</p>
                        </div>
                    </div>
                </div>

                <!-- Table Content -->
                <div v-else class="overflow-y-auto" style="max-height: calc(100vh - 300px)">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    #
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Question
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Type
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template v-if="paginatedQuestions.length === 0">
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center">
                                        <div class="flex flex-col items-center justify-center text-gray-500">
                                            <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <p class="text-lg font-medium mb-1">No questions found</p>
                                            <p class="text-sm">Try adjusting your filters or search terms</p>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <template v-else>
                                <tr v-for="(question, index) in paginatedQuestions" :key="question.id"
                                    class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ (currentPage - 1) * itemsPerPage + index + 1 }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <!-- Question Text -->
                                        <div class="font-medium">
                                            <LatexRenderer :content="question.question_text" />
                                        </div>

                                        <!-- Explanation -->
                                        <div v-if="question.explanation" class="text-xs text-gray-500 mt-1">
                                            <span class="font-semibold">Explanation:</span> <LatexRenderer :content="question.explanation" />
                                        </div>

                                        <!-- MCQ Options -->
                                        <div v-if="question.options?.length" class="mt-2 space-y-1">
                                            <div v-for="option in question.options" :key="option.id"
                                                class="text-xs flex items-center">
                                                <span class="mr-2">{{ bengaliChars[question.options.indexOf(option)] }}.</span>
                                                <span
                                                    :class="{ 'font-semibold text-green-600': option.is_correct }">
                                                    <LatexRenderer :content="option.option_text" />
                                                </span>
                                                <span v-if="option.is_correct" class="ml-1 text-green-500">✓</span>
                                            </div>
                                        </div>

                                        <!-- CQ Options -->
                                        <div v-if="question.cqoptions?.length" class="mt-2 space-y-1">
                                            <div v-for="option in question.cqoptions" :key="option.id"
                                                class="text-xs flex items-center">
                                                <span class="mr-2">{{ bengaliChars[question.cqoptions.indexOf(option)] }}.</span>
                                                <span class="font-medium">
                                                    <LatexRenderer :content="option.cq_text" />
                                                </span>
                                                <span class="ml-2 text-gray-500">({{ option.mark }} pts)</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span :class="{
                                            'px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800': question.format === 'cq',
                                            'px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800': question.format === 'mcq',
                                            'px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800': question.format === 'mix'
                                        }">
                                            {{ question.format.toUpperCase() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <Link :href="route('question.edit', question.id)">
                                                <button
                                                    class="text-blue-600 hover:text-blue-900 p-1 rounded-full hover:bg-blue-50 transition duration-200"
                                                    title="Edit">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </Link>
                                            <button @click="deleteQuestion(question.id)"
                                                class="text-red-600 hover:text-red-900 p-1 rounded-full hover:bg-red-50 transition duration-200"
                                                title="Delete">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.el-select {
    --el-select-input-focus-border-color: theme('colors.indigo.500');
    --el-select-border-color-hover: theme('colors.indigo.400');
}

.el-button--primary {
    --el-button-bg-color: theme('colors.blue.600');
    --el-button-hover-bg-color: theme('colors.blue.700');
    --el-button-active-bg-color: theme('colors.blue.800');
}

.el-button--success {
    --el-button-bg-color: theme('colors.green.600');
    --el-button-hover-bg-color: theme('colors.green.700');
    --el-button-active-bg-color: theme('colors.green.800');
}

/* Loading animation */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

/* Responsive adjustments */
@media (max-width: 1024px) {
    .el-select,
    .el-button {
        width: 100%;
    }
}

.error-message {
    @apply text-sm text-red-500 font-medium;
}

.is-invalid {
    @apply border-red-500;
}
</style>