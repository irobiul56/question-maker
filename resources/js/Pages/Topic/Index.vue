<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import { Head } from "@inertiajs/vue3";
import { Link } from "@inertiajs/vue3";
import { ref, computed, watch } from 'vue'
import { usePage, useForm } from '@inertiajs/vue3';
import { ElMessage } from "element-plus";

const { props } = usePage()
const chapters = ref(props.chapters)
const topics = ref(props.topics) // Add topics data
const education = ref(props.education)
const classes = ref(props.classes)
const subjects = ref(props.subjects)

const itemsPerPage = ref(10)
const currentPage = ref(1)
const searchQuery = ref('')
const selectedEducation = ref('')
const selectedClass = ref('')
const selectedSubject = ref('')
const selectedChapter = ref('')

// Track expanded state for each level
const expandedClasses = ref({});
const expandedSubjects = ref({});
const expandedChapters = ref({});

// Computed properties for dependent dropdowns
const filteredClasses = computed(() => {
    if (!selectedEducation.value) return []
    return classes.value.filter(cls => cls.education_id == selectedEducation.value)
})

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

// Watch for changes in filters and reset dependent filters
watch(selectedEducation, (newVal) => {
    selectedClass.value = ''
    selectedSubject.value = ''
    selectedChapter.value = ''
    currentPage.value = 1
    expandedClasses.value = {};
    expandedSubjects.value = {};
    expandedChapters.value = {};
})

watch(selectedClass, (newVal) => {
    selectedSubject.value = ''
    selectedChapter.value = ''
    currentPage.value = 1
    expandedSubjects.value = {};
    expandedChapters.value = {};
})

watch(selectedSubject, () => {
    selectedChapter.value = ''
    currentPage.value = 1
    expandedChapters.value = {};
})

watch(selectedChapter, () => {
    currentPage.value = 1
})

// Group data hierarchically
const groupedData = computed(() => {
    const groups = {}
    
    // First group by education (though we're filtering by education already)
    // Then by class
    filteredClasses.value.forEach(cls => {
        if (!groups[cls.id]) {
            groups[cls.id] = {
                class: cls,
                subjects: {}
            }
        }
        
        // Then by subject
        filteredSubjects.value.forEach(subject => {
            if (subject.academic_classes_id === cls.id) {
                if (!groups[cls.id].subjects[subject.id]) {
                    groups[cls.id].subjects[subject.id] = {
                        subject: subject,
                        chapters: {}
                    }
                }
                
                // Then by chapter
                filteredChapters.value.forEach(chapter => {
                    if (chapter.subject_id === subject.id) {
                        if (!groups[cls.id].subjects[subject.id].chapters[chapter.id]) {
                            groups[cls.id].subjects[subject.id].chapters[chapter.id] = {
                                chapter: chapter,
                                topics: []
                            }
                        }
                        
                        // Finally, add topics
                        filteredTopics.value.forEach(topic => {
                            if (topic.chapter_id === chapter.id) {
                                groups[cls.id].subjects[subject.id].chapters[chapter.id].topics.push(topic)
                            }
                        });
                    }
                });
            }
        });
    });
    
    return groups;
});

// Flatten the grouped structure for pagination with proper hierarchy
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

// Pagination for flattened structure
const paginatedData = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value
    const end = start + itemsPerPage.value
    return flattenedData.value.slice(start, end)
})

const totalPages = computed(() => {
    return Math.ceil(flattenedData.value.length / itemsPerPage.value)
})

const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value++
    }
}

const prevPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--
    }
}

defineProps({
    errors: Object
})

// Modal visibility
const isModalVisible = ref(false)

// Form to add/edit topic
const topicForm = useForm({
    id: null,
    name: '',
    chapter_id: selectedChapter.value || ''
})

const openAddModal = () => {
    topicForm.reset()
    topicForm.chapter_id = selectedChapter.value || ''
    isModalVisible.value = true
}

const editTopic = (topic) => {
    topicForm.id = topic.id
    topicForm.name = topic.name
    topicForm.chapter_id = topic.chapter_id
    isModalVisible.value = true
}

const submitTopic = () => {
    const isUpdate = Boolean(topicForm.id)
    const routeName = isUpdate ? 'topic.update' : 'topic.store'
    const routeParams = isUpdate ? [topicForm.id] : []
    const method = isUpdate ? 'put' : 'post'

    topicForm[method](route(routeName, ...routeParams), {
        onSuccess: (page) => {
            isModalVisible.value = false
            topics.value = page.props.topics
            topicForm.reset()
            const message = isUpdate ? "Topic updated successfully!" : "Topic added successfully!"
            ElMessage.success(message)
        },
        onError: () => {
            ElMessage.error("Failed to submit the data. Please try again.")
        }
    })
}

const closeModal = () => {
    isModalVisible.value = false
}

const deleteTopic = (topicId) => {
    if (confirm("Are you sure you want to delete this topic?")) {
        topicForm.delete(route('topic.destroy', topicId), {
            onSuccess: (page) => {
                topics.value = page.props.topics
                ElMessage.success("Topic deleted successfully!")
            },
            onError: () => {
                ElMessage.error("Failed to delete the topic. Please try again.")
            }
        })
    }
}
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Topics" />

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
            <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="relative w-full sm:w-64">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input v-model="searchQuery"
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition duration-200"
                            placeholder="Search topics..." type="text">
                    </div>

                    <!-- Education Level Dropdown -->
                    <div class="flex items-center space-x-2 w-full sm:w-auto">
                        <select v-model="selectedEducation"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all appearance-none bg-white">
                            <option value="">All Education Levels</option>
                            <option v-for="edu in education" :key="edu.id" :value="edu.id" class="text-gray-800">
                                {{ edu.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Class Dropdown (dependent on education) -->
                    <div class="flex items-center space-x-2 w-full sm:w-auto">
                        <select v-model="selectedClass" :disabled="!selectedEducation"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all appearance-none bg-white disabled:opacity-50">
                            <option value="">All Classes</option>
                            <option v-for="cls in filteredClasses" :key="cls.id" :value="cls.id" class="text-gray-800">
                                {{ cls.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Subject Dropdown -->
                    <div class="flex items-center space-x-2 w-full sm:w-auto">
                        <select v-model="selectedSubject" :disabled="!selectedClass"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all appearance-none bg-white disabled:opacity-50">
                            <option value="">All Subjects</option>
                            <option v-for="subject in filteredSubjects" :key="subject.id" :value="subject.id">
                                {{ subject.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Chapter Dropdown -->
                    <div class="flex items-center space-x-2 w-full sm:w-auto">
                        <select v-model="selectedChapter" :disabled="!selectedSubject"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all appearance-none bg-white disabled:opacity-50">
                            <option value="">All Chapters</option>
                            <option v-for="chapter in filteredChapters" :key="chapter.id" :value="chapter.id">
                                {{ chapter.name }}
                            </option>
                        </select>
                    </div>

                    <div class="flex items-center space-x-2 w-full sm:w-auto">
                        <el-button @click="openAddModal()" type="primary"
                            class="mt-4 sm:mt-0 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 shadow-md">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Topic
                        </el-button>
                    </div>

                    <div class="flex items-center space-x-2 w-full sm:w-auto">
                        <el-select class="w-full sm:w-28" v-model="itemsPerPage">
                            <el-option :value="5" label="5"></el-option>
                            <el-option :value="10" label="10"></el-option>
                            <el-option :value="20" label="20"></el-option>
                            <el-option :value="50" label="50"></el-option>
                        </el-select>
                    </div>
                </div>
            </div>

            <!-- Table Section -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    #
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Name
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
                            <template v-if="paginatedData.length === 0">
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No data found matching your criteria.
                                    </td>
                                </tr>
                            </template>
                            
                            <template v-else>
                                <tr v-for="(item, index) in paginatedData" :key="item.id" 
                                    :class="{
                                        'bg-gray-50 hover:bg-gray-100': item.type === 'class',
                                        'bg-gray-100 hover:bg-gray-150': item.type === 'subject',
                                        'bg-gray-150 hover:bg-gray-200': item.type === 'chapter',
                                        'hover:bg-gray-50': item.type === 'topic'
                                    }">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ (currentPage - 1) * itemsPerPage + index + 1 }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" 
                                        :style="{ paddingLeft: `${item.indentLevel * 2 + 0.5}rem` }">
                                        <div class="flex items-center">
                                            <!-- Toggle button for expandable rows -->
                                            <button v-if="item.type !== 'topic'" 
                                                    @click="toggleExpand(item)" 
                                                    class="flex items-center focus:outline-none mr-2">
                                                <svg class="w-5 h-5 transition-transform duration-200" 
                                                    :class="{ 'transform rotate-90': item.isExpanded }" 
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </button>
                                            
                                            <!-- Item name with different styling based on type -->
                                            <template v-if="item.type === 'class'">
                                                <span class="font-semibold">{{ item.name }}</span>
                                            </template>
                                            <template v-else-if="item.type === 'subject'">
                                                <span class="font-medium">{{ item.name }}</span>
                                            </template>
                                            <template v-else-if="item.type === 'chapter'">
                                                <span>{{ item.name }}</span>
                                            </template>
                                            <template v-else>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ item.name }}
                                                </span>
                                            </template>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span :class="{
                                            'px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800': item.type === 'class',
                                            'px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800': item.type === 'subject',
                                            'px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800': item.type === 'chapter',
                                            'px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800': item.type === 'topic'
                                        }">
                                            {{ item.type.charAt(0).toUpperCase() + item.type.slice(1) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div v-if="item.type === 'topic'" class="flex items-center justify-end space-x-2">
                                            <button @click="editTopic(item)"
                                                class="text-blue-600 hover:text-blue-900 p-1 rounded-full hover:bg-blue-50 transition duration-200"
                                                title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                    </path>
                                                </svg>
                                            </button>
                                            <button @click="deleteTopic(item.id)"
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

                <!-- Pagination -->
                <div
                    class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6 rounded-b-xl">
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Showing <span class="font-medium">{{ (currentPage - 1) * itemsPerPage + 1 }}</span>
                                to <span class="font-medium">{{ Math.min(currentPage * itemsPerPage,
                                    flattenedData.length) }}</span>
                                of <span class="font-medium">{{ flattenedData.length }}</span> results
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
                                aria-label="Pagination">
                                <button @click="prevPage" :disabled="currentPage === 1"
                                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span class="sr-only">Previous</span>
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                        fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd"
                                            d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <template v-for="page in totalPages" :key="page">
                                    <button @click="currentPage = page"
                                        :class="[page === currentPage ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50', 'relative inline-flex items-center px-4 py-2 border text-sm font-medium']">
                                        {{ page }}
                                    </button>
                                </template>
                                <button @click="nextPage" :disabled="currentPage === totalPages"
                                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span class="sr-only">Next</span>
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                        fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd"
                                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Dialog -->
        <el-dialog :title="topicForm.id ? 'Edit Topic' : 'Add New Topic'" :model-value="isModalVisible"
            @close="closeModal" width="40%" class="rounded-lg">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Chapter</label>
                    <el-select v-model="topicForm.chapter_id" placeholder="Select Chapter" class="w-full" required>
                        <el-option v-for="chapter in filteredChapters" :key="chapter.id" :label="chapter.name" :value="chapter.id" />
                    </el-select>
                    <p v-if="errors.chapter_id" class="mt-1 text-sm text-red-600">{{ errors.chapter_id }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Topic Name</label>
                    <el-input v-model="topicForm.name" placeholder="Enter topic name" class="w-full" required>
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
                    <el-button @click="closeModal" class="border border-gray-300 text-gray-700 hover:bg-gray-50">
                        Cancel
                    </el-button>
                    <el-button @click="submitTopic" :disabled="topicForm.processing" type="primary"
                        class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 shadow-md">
                        <span v-if="topicForm.processing">Processing...</span>
                        <span v-else>{{ topicForm.id ? 'Update' : 'Create' }}</span>
                    </el-button>
                </div>
            </template>
        </el-dialog>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Custom styles */
:deep(.el-dialog) {
    border-radius: 0.75rem;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

:deep(.el-dialog__header) {
    border-bottom: 1px solid #e5e7eb;
    padding: 1.25rem 1.5rem;
}

:deep(.el-dialog__body) {
    padding: 1.5rem;
}

:deep(.el-dialog__footer) {
    border-top: 1px solid #e5e7eb;
    padding: 1rem 1.5rem;
}

/* Hover effects */
tr {
    transition: all 0.2s ease;
}

/* Gradient button effect */
.bg-gradient-to-r {
    background-size: 200% auto;
    transition: 0.5s;
}

.bg-gradient-to-r:hover {
    background-position: right center;
}

/* Indentation for tree structure */
.tree-indent {
    display: inline-block;
    width: 1.5rem;
    height: 1.5rem;
}
</style>