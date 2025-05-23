<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { Link } from "@inertiajs/vue3";
import { ref, computed, watch } from 'vue';
import { usePage, useForm } from '@inertiajs/vue3';
import { ElMessage, ElLoading } from "element-plus";
import { Plus, Edit, Delete, Check } from '@element-plus/icons-vue';


const { props } = usePage();
const questions = ref(props.questions);
const topics = ref(props.topics);
const chapters = ref(props.chapters);
const education = ref(props.education);
const classes = ref(props.classes);
const subjects = ref(props.subjects);
const levels = ref(props.levels);
const types = ref(props.types);
const boards = ref(props.boards);

// Get errors from page props
const errors = computed(() => props.errors);

// Pagination and filtering
const itemsPerPage = ref(10);
const currentPage = ref(1);
const searchQuery = ref('');
const selectedEducation = ref('');
const selectedClass = ref('');
const selectedSubject = ref('');
const selectedChapter = ref('');
const selectedTopic = ref('');

// Expanded state for tree structure
const expandedClasses = ref({});
const expandedSubjects = ref({});
const expandedChapters = ref({});

// Modal visibility
const isQuestionModalVisible = ref(false);
const isTopicModalVisible = ref(false);

// Forms
const questionForm = useForm({
    id: null,
    topic_id: '',
    type_id: '',
    level_id: '',
    board_id: '',
    format: 'mcq',
    question_text: '',
    explanation: '',
    options: [
        { option_text: '', is_correct: false },
        { option_text: '', is_correct: false },
        { option_text: '', is_correct: false },
        { option_text: '', is_correct: false }
    ]
});

const topicForm = useForm({
    id: null,
    name: '',
    chapter_id: selectedChapter.value || ''
});

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

const filteredTopics = computed(() => {
    if (!selectedChapter.value) return [];
    return topics.value.filter(topic => 
        topic.chapter_id == selectedChapter.value
    );
});

// Filter questions based on selections
const filteredQuestions = computed(() => {
    return questions.value.filter(question => {
        const matchesTopic = !selectedTopic.value || question.topic_id == selectedTopic.value;
        const matchesSearch = !searchQuery.value || 
            question.question_text.toLowerCase().includes(searchQuery.value.toLowerCase());
        return matchesTopic && matchesSearch;
    });
});

// Pagination for questions
const paginatedQuestions = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    const end = start + itemsPerPage.value;
    return filteredQuestions.value.slice(start, end);
});

const totalQuestionPages = computed(() => {
    return Math.ceil(filteredQuestions.value.length / itemsPerPage.value);
});

// Group data hierarchically
const groupedData = computed(() => {
    const groups = {};
    
    filteredClasses.value.forEach(cls => {
        if (!groups[cls.id]) {
            groups[cls.id] = {
                class: cls,
                subjects: {}
            }
        }
        
        filteredSubjects.value.forEach(subject => {
            if (subject.academic_classes_id === cls.id) {
                if (!groups[cls.id].subjects[subject.id]) {
                    groups[cls.id].subjects[subject.id] = {
                        subject: subject,
                        chapters: {}
                    }
                }
                
                filteredChapters.value.forEach(chapter => {
                    if (chapter.subject_id === subject.id) {
                        if (!groups[cls.id].subjects[subject.id].chapters[chapter.id]) {
                            groups[cls.id].subjects[subject.id].chapters[chapter.id] = {
                                chapter: chapter,
                                topics: []
                            }
                        }
                        
                        filteredTopics.value.forEach(topic => {
                            if (topic.chapter_id === chapter.id) {
                                groups[cls.id].subjects[subject.id].chapters[chapter.id].topics.push(topic);
                            }
                        });
                    }
                });
            }
        });
    });
    
    return groups;
});

// Flatten the grouped structure for display
const flattenedData = computed(() => {
    const result = [];
    
    Object.values(groupedData.value).forEach(classGroup => {
        const classId = classGroup.class.id;
        const isClassExpanded = expandedClasses.value[classId] !== false;
        
        // Add class row
        result.push({
            type: 'class',
            id: classId,
            name: classGroup.class.name,
            education_id: classGroup.class.education_id,
            isExpanded: isClassExpanded,
            indentLevel: 0
        });
        
        if (isClassExpanded) {
            Object.values(classGroup.subjects).forEach(subjectGroup => {
                const subjectId = subjectGroup.subject.id;
                const isSubjectExpanded = expandedSubjects.value[subjectId] !== false;
                
                // Add subject row
                result.push({
                    type: 'subject',
                    id: subjectId,
                    name: subjectGroup.subject.name,
                    class_id: classId,
                    isExpanded: isSubjectExpanded,
                    indentLevel: 1
                });
                
                if (isSubjectExpanded) {
                    Object.values(subjectGroup.chapters).forEach(chapterGroup => {
                        const chapterId = chapterGroup.chapter.id;
                        const isChapterExpanded = expandedChapters.value[chapterId] !== false;
                        
                        // Add chapter row
                        result.push({
                            type: 'chapter',
                            id: chapterId,
                            name: chapterGroup.chapter.name,
                            subject_id: subjectId,
                            isExpanded: isChapterExpanded,
                            indentLevel: 2
                        });
                        
                        if (isChapterExpanded) {
                            // Add topics
                            chapterGroup.topics.forEach(topic => {
                                result.push({
                                    type: 'topic',
                                    ...topic,
                                    indentLevel: 3
                                });
                            });
                        }
                    });
                }
            });
        }
    });
    
    return result;
});

// Navigation
const nextPage = () => {
    if (currentPage.value < totalQuestionPages.value) {
        currentPage.value++;
    }
};

const prevPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--;
    }
};

// Watch for changes in filters and reset dependent filters
watch(selectedEducation, () => {
    selectedClass.value = '';
    selectedSubject.value = '';
    selectedChapter.value = '';
    selectedTopic.value = '';
    currentPage.value = 1;
    expandedClasses.value = {};
    expandedSubjects.value = {};
    expandedChapters.value = {};
});

watch(selectedClass, () => {
    selectedSubject.value = '';
    selectedChapter.value = '';
    selectedTopic.value = '';
    currentPage.value = 1;
    expandedSubjects.value = {};
    expandedChapters.value = {};
});

watch(selectedSubject, () => {
    selectedChapter.value = '';
    selectedTopic.value = '';
    currentPage.value = 1;
    expandedChapters.value = {};
});

watch(selectedChapter, () => {
    selectedTopic.value = '';
    currentPage.value = 1;
});

// Toggle expand/collapse
const toggleExpand = (item) => {
    if (item.type === 'class') {
        expandedClasses.value = {
            ...expandedClasses.value,
            [item.id]: !expandedClasses.value[item.id]
        };
    } else if (item.type === 'subject') {
        expandedSubjects.value = {
            ...expandedSubjects.value,
            [item.id]: !expandedSubjects.value[item.id]
        };
    } else if (item.type === 'chapter') {
        expandedChapters.value = {
            ...expandedChapters.value,
            [item.id]: !expandedChapters.value[item.id]
        };
    }
};

// Expand/collapse all
const toggleExpandAll = (expand) => {
    if (expand) {
        // Expand all classes, subjects, and chapters
        Object.keys(groupedData.value).forEach(classId => {
            expandedClasses.value[classId] = true;
            Object.keys(groupedData.value[classId].subjects).forEach(subjectId => {
                expandedSubjects.value[subjectId] = true;
                Object.keys(groupedData.value[classId].subjects[subjectId].chapters).forEach(chapterId => {
                    expandedChapters.value[chapterId] = true;
                });
            });
        });
    } else {
        // Collapse all
        expandedClasses.value = {};
        expandedSubjects.value = {};
        expandedChapters.value = {};
    }
};

// Question CRUD operations
const openQuestionModal = (topicId = null) => {
    questionForm.reset();
    questionForm.topic_id = topicId || selectedTopic.value || '';
    isQuestionModalVisible.value = true;
};

const editQuestion = (question) => {
    questionForm.id = question.id;
    questionForm.topic_id = question.topic_id;
    questionForm.type_id = question.type_id;
    questionForm.level_id = question.level_id;
    questionForm.board_id = question.board_id;
    questionForm.format = question.format;
    questionForm.question_text = question.question_text;
    questionForm.explanation = question.explanation;
    
    // Load options if they exist
    if (question.options && question.options.length > 0) {
        questionForm.options = question.options.map(opt => ({
            option_text: opt.option_text,
            is_correct: Boolean(opt.is_correct)
        }));
    } else {
        questionForm.options = [
            { option_text: '', is_correct: false },
            { option_text: '', is_correct: false },
            { option_text: '', is_correct: false },
            { option_text: '', is_correct: false }
        ];
    }
    
    isQuestionModalVisible.value = true;
};

const addOption = () => {
    questionForm.options.push({ option_text: '', is_correct: false });
};

const removeOption = (index) => {
    if (questionForm.options.length > 2) {
        questionForm.options.splice(index, 1);
    }
};

const submitQuestion = () => {
    const isUpdate = Boolean(questionForm.id);
    const routeName = isUpdate ? 'question.update' : 'question.store';
    const routeParams = isUpdate ? [questionForm.id] : [];
    
    const loading = ElLoading.service({
        lock: true,
        text: isUpdate ? 'Updating question...' : 'Creating question...',
        background: 'rgba(0, 0, 0, 0.7)'
    });
    
    questionForm[isUpdate ? 'put' : 'post'](route(routeName, ...routeParams), {
        onSuccess: () => {
            loading.close();
            isQuestionModalVisible.value = false;
            ElMessage.success(`Question ${isUpdate ? 'updated' : 'created'} successfully!`);
            
            // Refresh questions data instantly
            if (isUpdate) {
                // Update existing question
                const index = questions.value.findIndex(q => q.id === questionForm.id);
                if (index !== -1) {
                    questions.value[index] = {
                        ...questions.value[index],
                        ...questionForm.data()
                    };
                }
            } else {
                // Add new question at the beginning of the list
                questions.value.unshift({
                    ...questionForm.data(),
                    id: Math.max(...questions.value.map(q => q.id)) + 1 // Temporary ID until real response comes
                });
                
                // When the real response comes from the server, replace the temporary question
                // This assumes your backend returns the created question in the response
                // You would need to implement this in your controller
            }
        },
        onError: () => {
            loading.close();
            ElMessage.error("Failed to submit the question. Please try again.");
        }
    });
};

const deleteQuestion = (questionId) => {
    if (confirm("Are you sure you want to delete this question?")) {
        const loading = ElLoading.service({
            lock: true,
            text: 'Deleting question...',
            background: 'rgba(0, 0, 0, 0.7)'
        });
        
        // Store the index before deletion for rollback
        const questionIndex = questions.value.findIndex(q => q.id === questionId);
        
        // Optimistically remove from UI
        questions.value = questions.value.filter(q => q.id !== questionId);
        
        questionForm.delete(route('question.destroy', questionId), {
            onSuccess: () => {
                loading.close();
                ElMessage.success("Question deleted successfully!");
                // No need to update questions.value again since we already removed it
            },
            onError: () => {
                loading.close();
                ElMessage.error("Failed to delete the question. Please try again.");
                // Rollback the UI change if deletion failed
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

// Topic CRUD operations
const openTopicModal = () => {
    topicForm.reset();
    topicForm.chapter_id = selectedChapter.value || '';
    isTopicModalVisible.value = true;
};

const editTopic = (topic) => {
    topicForm.id = topic.id;
    topicForm.name = topic.name;
    topicForm.chapter_id = topic.chapter_id;
    isTopicModalVisible.value = true;
};

const submitTopic = () => {
    const isUpdate = Boolean(topicForm.id);
    const routeName = isUpdate ? 'topic.update' : 'topic.store';
    const routeParams = isUpdate ? [topicForm.id] : [];
    
    const loading = ElLoading.service({
        lock: true,
        text: isUpdate ? 'Updating topic...' : 'Creating topic...',
        background: 'rgba(0, 0, 0, 0.7)'
    });
    
    topicForm[isUpdate ? 'put' : 'post'](route(routeName, ...routeParams), {
        onSuccess: () => {
            loading.close();
            isTopicModalVisible.value = false;
            ElMessage.success(`Topic ${isUpdate ? 'updated' : 'created'} successfully!`);
            
            // Refresh topics data instantly
            if (isUpdate) {
                // Update existing topic
                const index = topics.value.findIndex(t => t.id === topicForm.id);
                if (index !== -1) {
                    topics.value[index] = {
                        ...topics.value[index],
                        ...topicForm.data()
                    };
                }
            } else {
                // Add new topic at the beginning of the list
                topics.value.unshift({
                    ...topicForm.data(),
                    id: Math.max(...topics.value.map(t => t.id)) + 1 // Temporary ID until real response comes
                });
                
                // When the real response comes from the server, replace the temporary topic
                // This assumes your backend returns the created topic in the response
            }
        },
        onError: () => {
            loading.close();
            ElMessage.error("Failed to submit the topic. Please try again.");
        }
    });
};

const deleteTopic = (topicId) => {
    if (confirm("Are you sure you want to delete this topic?")) {
        const loading = ElLoading.service({
            lock: true,
            text: 'Deleting topic...',
            background: 'rgba(0, 0, 0, 0.7)'
        });
        
        // Store the index before deletion for rollback
        const topicIndex = topics.value.findIndex(t => t.id === topicId);
        
        // Optimistically remove from UI
        topics.value = topics.value.filter(t => t.id !== topicId);
        
        // Also remove any questions associated with this topic
        const questionsToRemove = questions.value.filter(q => q.topic_id === topicId);
        questions.value = questions.value.filter(q => q.topic_id !== topicId);
        
        topicForm.delete(route('topic.destroy', topicId), {
            onSuccess: () => {
                loading.close();
                ElMessage.success("Topic deleted successfully!");
                // No need to update topics.value again since we already removed it
            },
            onError: () => {
                loading.close();
                ElMessage.error("Failed to delete the topic. Please try again.");
                // Rollback the UI changes if deletion failed
                if (topicIndex !== -1) {
                    const deletedTopic = props.topics.find(t => t.id === topicId);
                    if (deletedTopic) {
                        topics.value.splice(topicIndex, 0, deletedTopic);
                        // Also restore any questions that were removed
                        questions.value = [...questions.value, ...questionsToRemove];
                    }
                }
            }
        });
    }
};
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
    <el-select
      v-model="selectedEducation"
      placeholder="All Education Levels"
      clearable
      class="w-full lg:w-48"
      size="large"
    >
      <el-option
        v-for="edu in education"
        :key="edu.id"
        :label="edu.name"
        :value="edu.id"
      />
    </el-select>

    <!-- Class Dropdown -->
    <el-select
      v-model="selectedClass"
      placeholder="All Classes"
      clearable
      :disabled="!selectedEducation"
      class="w-full lg:w-48"
      size="large"
    >
      <el-option
        v-for="cls in filteredClasses"
        :key="cls.id"
        :label="cls.name"
        :value="cls.id"
      />
    </el-select>

    <!-- Subject Dropdown -->
    <el-select
      v-model="selectedSubject"
      placeholder="All Subjects"
      clearable
      :disabled="!selectedClass"
      class="w-full lg:w-48"
      size="large"
    >
      <el-option
        v-for="subject in filteredSubjects"
        :key="subject.id"
        :label="subject.name"
        :value="subject.id"
      />
    </el-select>

    <!-- Chapter Dropdown -->
    <el-select
      v-model="selectedChapter"
      placeholder="All Chapters"
      clearable
      :disabled="!selectedSubject"
      class="w-full lg:w-48"
      size="large"
    >
      <el-option
        v-for="chapter in filteredChapters"
        :key="chapter.id"
        :label="chapter.name"
        :value="chapter.id"
      />
    </el-select>

    <!-- Topic Dropdown -->
    <el-select
      v-model="selectedTopic"
      placeholder="All Topics"
      clearable
      :disabled="!selectedChapter"
      class="w-full lg:w-48"
      size="large"
    >
      <el-option
        v-for="topic in filteredTopics"
        :key="topic.id"
        :label="topic.name"
        :value="topic.id"
      />
    </el-select>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto mt-4 lg:mt-0">
      <el-button
        @click="openQuestionModal()"
        type="primary"
        size="large"
        class="w-full lg:w-auto"
      >
        <el-icon class="mr-2"><Plus /></el-icon>
        Add Question
      </el-button>
      <el-button
        @click="openTopicModal()"
        type="success"
        size="large"
        class="w-full lg:w-auto"
      >
        <el-icon class="mr-2"><Plus /></el-icon>
        Add Topic
      </el-button>
    </div>
  </div>
</div>

            <!-- Main Content Area -->
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                <!-- Left column - Tree Structure -->
                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">Curriculum Structure</h3>
                        <div class="flex space-x-2">
                            <el-button @click="toggleExpandAll(true)" size="small" plain>
                                Expand All
                            </el-button>
                            <el-button @click="toggleExpandAll(false)" size="small" plain>
                                Collapse All
                            </el-button>
                        </div>
                    </div>
                    <div class="overflow-y-auto" style="max-height: calc(100vh - 300px)">
                        <ul class="divide-y divide-gray-200">
                            <li v-for="item in flattenedData" :key="item.id" 
                                class="px-4 py-3 hover:bg-gray-50 transition-colors duration-150"
                                :class="{
                                    'bg-blue-50': item.type === 'class',
                                    'bg-green-50': item.type === 'subject',
                                    'bg-purple-50': item.type === 'chapter',
                                    'bg-white': item.type === 'topic'
                                }">
                                <div class="flex items-center" :style="{ 'padding-left': `${item.indentLevel * 1.5}rem` }">
                                    <button v-if="['class', 'subject', 'chapter'].includes(item.type)" 
                                        @click="toggleExpand(item)"
                                        class="mr-2 text-gray-500 hover:text-gray-700">
                                        <svg class="w-4 h-4 transition-transform duration-200" 
                                            :class="{ 'transform rotate-90': item.isExpanded }" 
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </button>
                                    <span class="font-medium">
                                        {{ item.name }}
                                        <span v-if="item.type === 'topic'" 
                                            class="ml-2 text-xs text-gray-500">
                                            ({{ questions.filter(q => q.topic_id === item.id).length }} questions)
                                        </span>
                                    </span>
                                    <div v-if="item.type === 'topic'" class="ml-auto flex space-x-2">
                                        <button @click.stop="openQuestionModal(item.id)"
                                            class="text-blue-600 hover:text-blue-800 p-1 rounded-full hover:bg-blue-100">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                        </button>
                                        <button @click.stop="editTopic(item)"
                                            class="text-yellow-600 hover:text-yellow-800 p-1 rounded-full hover:bg-yellow-100">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        <button @click.stop="deleteTopic(item.id)"
                                            class="text-red-600 hover:text-red-800 p-1 rounded-full hover:bg-red-100">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Right column - Questions List -->
                <div class="lg:col-span-3 bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Questions</h3>
                            <p class="text-sm text-gray-500 mt-1">
                                Showing {{ filteredQuestions.length }} questions for selected filters
                            </p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-500">
                                Page {{ currentPage }} of {{ totalQuestionPages }}
                            </span>
                            <div class="flex space-x-2">
                                <el-button @click="prevPage" :disabled="currentPage <= 1" size="small">
                                    Previous
                                </el-button>
                                <el-button @click="nextPage" :disabled="currentPage >= totalQuestionPages" size="small">
                                    Next
                                </el-button>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-y-auto" style="max-height: calc(100vh - 300px)">
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
                                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                            No questions found matching your criteria.
                                        </td>
                                    </tr>
                                </template>
                                <template v-else>
                                    <tr v-for="(question, index) in paginatedQuestions" :key="question.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ (currentPage - 1) * itemsPerPage + index + 1 }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <div class="font-medium">{{ question.question_text }}</div>
                                            <div v-if="question.explanation" class="text-xs text-gray-500 mt-1">
                                                Explanation: {{ question.explanation }}
                                            </div>
                                            <div v-if="question.options && question.options.length > 0" class="mt-2">
                                                <div v-for="(option, optIndex) in question.options" :key="optIndex" 
                                                    class="text-xs flex items-center">
                                                    <span class="mr-2">{{ String.fromCharCode(97 + optIndex) }}.</span>
                                                    <span :class="{ 'font-semibold text-green-600': option.is_correct }">
                                                        {{ option.option_text }}
                                                    </span>
                                                    <span v-if="option.is_correct" class="ml-1 text-green-500">
                                                        ✓
                                                    </span>
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
                                                <button @click="editQuestion(question)"
                                                    class="text-blue-600 hover:text-blue-900 p-1 rounded-full hover:bg-blue-50 transition duration-200"
                                                    title="Edit">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                        </path>
                                                    </svg>
                                                </button>
                                                <button @click="deleteQuestion(question.id)"
                                                    class="text-red-600 hover:text-red-900 p-1 rounded-full hover:bg-red-50 transition duration-200"
                                                    title="Delete">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
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
        </div>

        <!-- Question Modal Dialog -->
        <el-dialog 
            :title="questionForm.id ? 'Edit Question' : 'Add New Question'" 
            v-model="isQuestionModalVisible" 
            width="60%" 
            class="rounded-lg"
        >
            <div class="space-y-4">
                <!-- Topic Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Topic</label>
                    <el-select 
                        v-model="questionForm.topic_id" 
                        placeholder="Select Topic" 
                        class="w-full" 
                        required
                        filterable
                        :class="{ 'is-invalid': errors.topic_id }"
                    >
                        <el-option 
                            v-for="topic in topics" 
                            :key="topic.id" 
                            :label="topic.name" 
                            :value="topic.id"
                        />
                    </el-select>
                    <p v-if="errors.topic_id" class="mt-1 text-sm text-red-600">{{ errors.topic_id }}</p>
                </div>

                <!-- Question Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Question Type</label>
                    <el-select 
                        v-model="questionForm.type_id" 
                        placeholder="Select Type" 
                        class="w-full" 
                        required
                        :class="{ 'is-invalid': errors.type_id }"
                    >
                        <el-option 
                            v-for="type in types" 
                            :key="type.id" 
                            :label="type.name" 
                            :value="type.id"
                        />
                    </el-select>
                    <p v-if="errors.type_id" class="mt-1 text-sm text-red-600">{{ errors.type_id }}</p>
                </div>

                <!-- Difficulty Level -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Difficulty Level</label>
                    <el-select 
                        v-model="questionForm.level_id" 
                        placeholder="Select Level" 
                        class="w-full" 
                        required
                        :class="{ 'is-invalid': errors.level_id }"
                    >
                        <el-option 
                            v-for="level in levels" 
                            :key="level.id" 
                            :label="level.name" 
                            :value="level.id"
                        />
                    </el-select>
                    <p v-if="errors.level_id" class="mt-1 text-sm text-red-600">{{ errors.level_id }}</p>
                </div>

                <!-- Board -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Board</label>
                    <el-select 
                        v-model="questionForm.board_id" 
                        placeholder="Select Board" 
                        class="w-full" 
                        required
                        :class="{ 'is-invalid': errors.board_id }"
                    >
                        <el-option 
                            v-for="board in boards" 
                            :key="board.id" 
                            :label="board.name" 
                            :value="board.id"
                        />
                    </el-select>
                    <p v-if="errors.board_id" class="mt-1 text-sm text-red-600">{{ errors.board_id }}</p>
                </div>

                <!-- Question Format -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Format</label>
                    <el-radio-group v-model="questionForm.format" class="w-full">
                        <el-radio-button label="mcq">MCQ</el-radio-button>
                        <el-radio-button label="cq">CQ</el-radio-button>
                        <el-radio-button label="mix">Mixed</el-radio-button>
                    </el-radio-group>
                    <p v-if="errors.format" class="mt-1 text-sm text-red-600">{{ errors.format }}</p>
                </div>

                <!-- Question Text -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Question Text</label>
                    <el-input
                        v-model="questionForm.question_text"
                        type="textarea"
                        :rows="3"
                        placeholder="Enter the question text"
                        class="w-full"
                        :class="{ 'is-invalid': errors.question_text }"
                    />
                    <p v-if="errors.question_text" class="mt-1 text-sm text-red-600">{{ errors.question_text }}</p>
                </div>

                <!-- Explanation -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Explanation (Optional)</label>
                    <el-input
                        v-model="questionForm.explanation"
                        type="textarea"
                        :rows="2"
                        placeholder="Enter explanation if needed"
                        class="w-full"
                        :class="{ 'is-invalid': errors.explanation }"
                    />
                    <p v-if="errors.explanation" class="mt-1 text-sm text-red-600">{{ errors.explanation }}</p>
                </div>

                <!-- Options (only show for MCQ or Mixed) -->
                <div v-if="['mcq', 'mix'].includes(questionForm.format)">
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm font-medium text-gray-700">Options</label>
                        <el-button @click="addOption" size="small" type="primary" plain>
                            Add Option
                        </el-button>
                    </div>
                    
                    <div v-for="(option, index) in questionForm.options" :key="index" class="flex items-center mb-2 space-x-2">
                        <el-input
                            v-model="option.option_text"
                            placeholder="Option text"
                            class="flex-1"
                            :class="{ 'is-invalid': errors[`options.${index}.option_text`] }"
                        />
                        <el-checkbox v-model="option.is_correct">Correct</el-checkbox>
                        <el-button 
                            @click="removeOption(index)" 
                            type="danger" 
                            plain 
                            size="small" 
                            :disabled="questionForm.options.length <= 2"
                        >
                            Remove
                        </el-button>
                    </div>
                    <p v-if="errors.options" class="mt-1 text-sm text-red-600">{{ errors.options }}</p>
                </div>
            </div>

            <template #footer>
                <div class="flex justify-end space-x-3">
                    <el-button @click="isQuestionModalVisible = false" class="border border-gray-300 text-gray-700 hover:bg-gray-50">
                        Cancel
                    </el-button>
                    <el-button 
                        @click="submitQuestion" 
                        :disabled="questionForm.processing" 
                        type="primary"
                        class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 shadow-md"
                    >
                        <span v-if="questionForm.processing">Processing...</span>
                        <span v-else>{{ questionForm.id ? 'Update' : 'Create' }}</span>
                    </el-button>
                </div>
            </template>
        </el-dialog>

        <!-- Topic Modal Dialog -->
        <el-dialog 
            :title="topicForm.id ? 'Edit Topic' : 'Add New Topic'" 
            v-model="isTopicModalVisible" 
            width="40%" 
            class="rounded-lg"
        >
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Chapter</label>
                    <el-select 
                        v-model="topicForm.chapter_id" 
                        placeholder="Select Chapter" 
                        class="w-full" 
                        required
                        filterable
                        :class="{ 'is-invalid': errors.chapter_id }"
                    >
                        <el-option 
                            v-for="chapter in filteredChapters" 
                            :key="chapter.id" 
                            :label="chapter.name" 
                            :value="chapter.id"
                        />
                    </el-select>
                    <p v-if="errors.chapter_id" class="mt-1 text-sm text-red-600">{{ errors.chapter_id }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Topic Name</label>
                    <el-input 
                        v-model="topicForm.name" 
                        placeholder="Enter topic name" 
                        class="w-full" 
                        required
                        :class="{ 'is-invalid': errors.name }"
                    >
                        <template #prefix>
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                        </template>
                    </el-input>
                    <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
                </div>
            </div>

            <template #footer>
                <div class="flex justify-end space-x-3">
                    <el-button @click="isTopicModalVisible = false" class="border border-gray-300 text-gray-700 hover:bg-gray-50">
                        Cancel
                    </el-button>
                    <el-button 
                        @click="submitTopic" 
                        :disabled="topicForm.processing" 
                        type="primary"
                        class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 shadow-md"
                    >
                        <span v-if="topicForm.processing">Processing...</span>
                        <span v-else>{{ topicForm.id ? 'Update' : 'Create' }}</span>
                    </el-button>
                </div>
            </template>
        </el-dialog>
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

/* Responsive adjustments */
@media (max-width: 1024px) {
  .el-select, .el-button {
    width: 100%;
  }
}
</style>