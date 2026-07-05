<!-- resources/js/Pages/Classes/Index.vue -->
<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import InstituteLayout from '@/Layouts/InstituteLayout.vue';
import { Inertia } from '@inertiajs/inertia';

// Props
const props = defineProps({
    classes: {
        type: Array,
        default: () => []
    },
    stats: {
        type: Object,
        default: () => ({
            total: 0,
            with_science: 0,
            with_commerce: 0,
            with_arts: 0,
            with_general: 0,
            total_students: 0,
            total_sections: 0,
            with_elective: 0
        })
    },
    academicYears: {
        type: Array,
        default: () => []
    }
});

// State
const searchQuery = ref('');
const selectedAcademicYear = ref('');
const showCreateModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const showClassDetailsModal = ref(false);
const selectedClass = ref(null);
const isSubmitting = ref(false);

// Group options
const groupOptions = [
    { value: 'science', label: 'Science (বিজ্ঞান)', icon: 'fa-solid fa-flask', color: 'blue' },
    { value: 'commerce', label: 'Commerce (বাণিজ্য)', icon: 'fa-solid fa-chart-line', color: 'green' },
    { value: 'arts', label: 'Arts (মানবিক)', icon: 'fa-solid fa-palette', color: 'purple' },
    { value: 'general', label: 'General (সাধারণ)', icon: 'fa-solid fa-users', color: 'gray' }
];

// Create Form
const createForm = useForm({
    name: '',
    bn_name: '',
    numeric_value: '',
    group: '',
    display_order: 0,
    has_elective: false
});

// Edit Form
const editForm = useForm({
    name: '',
    bn_name: '',
    numeric_value: '',
    group: '',
    display_order: 0,
    has_elective: false
});

// Computed
const filteredClasses = computed(() => {
    let filtered = props.classes;
    
    // Filter by search query
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(cls => {
            return cls.name?.toLowerCase().includes(query) ||
                   cls.bn_name?.toLowerCase().includes(query) ||
                   cls.group_label?.toLowerCase().includes(query) ||
                   cls.numeric_value?.toString().includes(query);
        });
    }
    
    // Filter by academic year
    if (selectedAcademicYear.value) {
        filtered = filtered.filter(cls => {
            return cls.academic_year_id == selectedAcademicYear.value;
        });
    }
    
    return filtered;
});

// Stats for display
const displayStats = computed(() => [
    {
        label: 'Total Classes',
        value: props.stats.total,
        icon: 'fa-solid fa-school',
        color: 'blue'
    },
    {
        label: 'Science',
        value: props.stats.with_science,
        icon: 'fa-solid fa-flask',
        color: 'blue'
    },
    {
        label: 'Commerce',
        value: props.stats.with_commerce,
        icon: 'fa-solid fa-chart-line',
        color: 'green'
    },
    {
        label: 'Arts',
        value: props.stats.with_arts,
        icon: 'fa-solid fa-palette',
        color: 'purple'
    }
]);

// Methods
const openCreateModal = () => {
    createForm.reset();
    createForm.clearErrors();
    showCreateModal.value = true;
};

const openEditModal = (classItem) => {
    selectedClass.value = classItem;
    editForm.name = classItem.name;
    editForm.bn_name = classItem.bn_name || '';
    editForm.numeric_value = classItem.numeric_value;
    editForm.group = classItem.group || '';
    editForm.display_order = classItem.display_order || 0;
    editForm.has_elective = classItem.has_elective || false;
    editForm.clearErrors();
    showEditModal.value = true;
};

const openDeleteModal = (classItem) => {
    selectedClass.value = classItem;
    showDeleteModal.value = true;
};

const openClassDetails = (classItem) => {
    selectedClass.value = classItem;
    showClassDetailsModal.value = true;
};

const createClass = () => {
    isSubmitting.value = true;
    createForm.post(route('classes.store'), {
        preserveScroll: true,
        onSuccess: () => {
            showCreateModal.value = false;
            isSubmitting.value = false;
            createForm.reset();
        },
        onError: () => {
            isSubmitting.value = false;
        }
    });
};

const updateClass = () => {
    isSubmitting.value = true;
    editForm.put(route('classes.update', selectedClass.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showEditModal.value = false;
            isSubmitting.value = false;
            selectedClass.value = null;
        },
        onError: () => {
            isSubmitting.value = false;
        }
    });
};

const deleteClass = () => {
    Inertia.delete(route('classes.destroy', selectedClass.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false;
            selectedClass.value = null;
        }
    });
};

const getGroupBadgeClass = (group) => {
    const classes = {
        science: 'bg-blue-100 text-blue-800',
        commerce: 'bg-green-100 text-green-800',
        arts: 'bg-purple-100 text-purple-800',
        general: 'bg-gray-100 text-gray-800'
    };
    return classes[group] || 'bg-gray-100 text-gray-800';
};

const getGroupLabel = (group) => {
    const labels = {
        science: 'বিজ্ঞান',
        commerce: 'বাণিজ্য',
        arts: 'মানবিক',
        general: 'সাধারণ'
    };
    return labels[group] || group;
};

const getGroupIcon = (group) => {
    const icons = {
        science: 'fa-solid fa-flask',
        commerce: 'fa-solid fa-chart-line',
        arts: 'fa-solid fa-palette',
        general: 'fa-solid fa-users'
    };
    return icons[group] || 'fa-solid fa-school';
};

const getStatusBadgeClass = (status) => {
    const classes = {
        active: 'bg-green-100 text-green-800',
        inactive: 'bg-gray-100 text-gray-800',
        passed: 'bg-blue-100 text-blue-800',
        transferred: 'bg-yellow-100 text-yellow-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const getStatusLabel = (status) => {
    const labels = {
        active: 'Active',
        inactive: 'Inactive',
        passed: 'Passed',
        transferred: 'Transferred'
    };
    return labels[status] || status;
};

const formatDate = (date) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });
};

const closeModal = (modalType) => {
    if (modalType === 'create') {
        showCreateModal.value = false;
        createForm.reset();
    } else if (modalType === 'edit') {
        showEditModal.value = false;
        editForm.reset();
        selectedClass.value = null;
    } else if (modalType === 'delete') {
        showDeleteModal.value = false;
        selectedClass.value = null;
    } else if (modalType === 'details') {
        showClassDetailsModal.value = false;
        selectedClass.value = null;
    }
};

// Get student count for a class
const getStudentCount = (classId) => {
    const cls = props.classes.find(c => c.id === classId);
    return cls?.student_count || 0;
};

// Get active student count for a class
const getActiveStudentCount = (classId) => {
    const cls = props.classes.find(c => c.id === classId);
    return cls?.active_student_count || 0;
};
</script>

<template>
    <Head title="Class Management" />
    
    <InstituteLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-800">
                        <i class="fa-solid fa-chalkboard text-blue-600 mr-2"></i>
                        Class Management
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Manage all classes with student information
                    </p>
                </div>
                <button
                    @click="openCreateModal"
                    class="inline-flex items-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition shadow-sm hover:shadow-md"
                >
                    <i class="fa-solid fa-plus mr-2"></i>
                    Add Class
                </button>
            </div>
        </template>

        <!-- Stats Cards -->
        <div class="space-y-6 w-full p-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div 
                v-for="stat in displayStats" 
                :key="stat.label"
                class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition-shadow border-l-4"
                :class="{
                    'border-l-blue-500': stat.color === 'blue',
                    'border-l-green-500': stat.color === 'green',
                    'border-l-purple-500': stat.color === 'purple',
                    'border-l-yellow-500': stat.color === 'yellow'
                }"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ stat.label }}</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ stat.value }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                         :class="{
                             'bg-blue-100 text-blue-600': stat.color === 'blue',
                             'bg-green-100 text-green-600': stat.color === 'green',
                             'bg-purple-100 text-purple-600': stat.color === 'purple',
                             'bg-yellow-100 text-yellow-600': stat.color === 'yellow'
                         }"
                    >
                        <i :class="[stat.icon, 'text-lg']"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
            <div class="bg-gradient-to-r from-indigo-50 to-blue-50 rounded-xl p-4 border border-indigo-100">
                <p class="text-xs text-indigo-600 font-medium">Total Students</p>
                <p class="text-xl font-bold text-indigo-800">{{ props.stats.total_students || 0 }}</p>
            </div>
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-4 border border-green-100">
                <p class="text-xs text-green-600 font-medium">Active Students</p>
                <p class="text-xl font-bold text-green-800">{{ props.stats.active_students || 0 }}</p>
            </div>
            <div class="bg-gradient-to-r from-yellow-50 to-amber-50 rounded-xl p-4 border border-yellow-100">
                <p class="text-xs text-yellow-600 font-medium">Total Sections</p>
                <p class="text-xl font-bold text-yellow-800">{{ props.stats.total_sections || 0 }}</p>
            </div>
            <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-4 border border-purple-100">
                <p class="text-xs text-purple-600 font-medium">With Elective</p>
                <p class="text-xl font-bold text-purple-800">{{ props.stats.with_elective || 0 }}</p>
            </div>
        </div>

        <!-- Search & Filter -->
        <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1 relative">
                    <i class="fa-solid fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input
                        type="text"
                        v-model="searchQuery"
                        placeholder="Search classes by name, Bangla name, or group..."
                        class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                    />
                    <div v-if="searchQuery" class="absolute right-3 top-1/2 transform -translate-y-1/2">
                        <button @click="searchQuery = ''" class="text-gray-400 hover:text-gray-600">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    </div>
                </div>
                
                <div class="sm:w-48">
                    <select
                        v-model="selectedAcademicYear"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                    >
                        <option value="">All Years</option>
                        <option 
                            v-for="year in academicYears" 
                            :key="year.id" 
                            :value="year.id"
                        >
                            {{ year.session }} {{ year.is_current ? '(Current)' : '' }}
                        </option>
                    </select>
                </div>

                <button
                    @click="openCreateModal"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition"
                >
                    <i class="fa-solid fa-plus mr-2"></i>
                    Add Your Class
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center space-x-1">
                                    <i class="fa-solid fa-hashtag"></i>
                                    <span>SL</span>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center space-x-1">
                                    <i class="fa-solid fa-school"></i>
                                    <span>Class Name</span>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center space-x-1">
                                    <i class="fa-solid fa-layer-group"></i>
                                    <span>Group</span>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center space-x-1">
                                    <i class="fa-solid fa-users"></i>
                                    <span>Total Students</span>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center space-x-1">
                                    <i class="fa-solid fa-user-check"></i>
                                    <span>Active</span>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center space-x-1">
                                    <i class="fa-solid fa-grip-lines"></i>
                                    <span>Sections</span>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center space-x-1">
                                    <i class="fa-solid fa-book-open"></i>
                                    <span>Type</span>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center space-x-1">
                                    <i class="fa-solid fa-grip-vertical"></i>
                                    <span>Order</span>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center space-x-1">
                                    <i class="fa-solid fa-cog"></i>
                                    <span>Actions</span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-if="filteredClasses.length === 0">
                            <td colspan="9" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fa-solid fa-school text-5xl text-gray-300 mb-4"></i>
                                    <p class="text-gray-500 font-medium">No classes found</p>
                                    <p class="text-sm text-gray-400 mt-1">Try adjusting your search or add a new class</p>
                                    <button
                                        @click="openCreateModal"
                                        class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition"
                                    >
                                        <i class="fa-solid fa-plus mr-2"></i>
                                        Add Your First Class
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr 
                            v-for="(cls, index) in filteredClasses" 
                            :key="cls.id"
                            class="hover:bg-gray-50 transition-colors duration-150"
                        >
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ index + 1 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-9 w-9 rounded-full bg-blue-100 flex items-center justify-center">
                                        <i class="fa-solid fa-chalkboard text-blue-600 text-sm"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">{{ cls.name }}</p>
                                        <p class="text-xs text-gray-500">Class {{ cls.numeric_value }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span 
                                    v-if="cls.group"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                    :class="getGroupBadgeClass(cls.group)"
                                >
                                    <i :class="[getGroupIcon(cls.group), 'mr-1 text-xs']"></i>
                                    {{ getGroupLabel(cls.group) }}
                                </span>
                                <span v-else class="text-sm text-gray-400">—</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                <span class="font-medium">{{ cls.student_count || 0 }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                <span class="text-green-600 font-medium">{{ cls.active_student_count || 0 }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ cls.section_count || 0 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span 
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                    :class="cls.class_type_badge_class || 'bg-gray-100 text-gray-800'"
                                >
                                    <i :class="cls.has_elective ? 'fa-solid fa-star' : 'fa-solid fa-check'" class="mr-1 text-xs"></i>
                                    {{ cls.class_type || 'Regular' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ cls.display_order || 0 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <button
                                        @click="openClassDetails(cls)"
                                        class="p-1.5 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded-lg transition"
                                        title="View Details"
                                    >
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    <button
                                        @click="openEditModal(cls)"
                                        class="p-1.5 text-green-600 hover:text-green-900 hover:bg-green-50 rounded-lg transition"
                                        title="Edit"
                                    >
                                        <i class="fa-solid fa-edit"></i>
                                    </button>
                                    <button
                                        @click="openDeleteModal(cls)"
                                        class="p-1.5 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-lg transition"
                                        title="Delete"
                                    >
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Table Footer -->
            <div class="px-6 py-3 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                <div class="text-sm text-gray-500">
                    Showing <span class="font-medium">{{ filteredClasses.length }}</span> of 
                    <span class="font-medium">{{ props.classes.length }}</span> classes
                </div>
                <div class="text-xs text-gray-400">
                    <i class="fa-regular fa-clock mr-1"></i>
                    Last updated: {{ formatDate(new Date()) }}
                </div>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- CREATE MODAL -->
        <!-- ============================================ -->
        <div v-if="showCreateModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" @click="closeModal('create')">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <!-- Header -->
                    <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">
                                    <i class="fa-solid fa-plus-circle text-blue-600 mr-2"></i>
                                    Add New Class
                                </h3>
                                <p class="text-xs text-gray-500 mt-0.5">Create a new class for your institute</p>
                            </div>
                            <button @click="closeModal('create')" class="text-gray-400 hover:text-gray-600 transition">
                                <i class="fa-solid fa-times text-xl"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Form -->
                    <form @submit.prevent="createClass" class="p-6">
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Class Name (English) <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        v-model="createForm.name"
                                        placeholder="e.g., Class 6"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                        :class="{ 'border-red-500': createForm.errors.name }"
                                    />
                                    <p v-if="createForm.errors.name" class="mt-1 text-sm text-red-600">
                                        {{ createForm.errors.name }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Class Name (Bangla)
                                    </label>
                                    <input
                                        type="text"
                                        v-model="createForm.bn_name"
                                        placeholder="e.g., ষষ্ঠ শ্রেণি"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Numeric Value <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        v-model="createForm.numeric_value"
                                        placeholder="e.g., 6"
                                        min="1"
                                        max="12"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                        :class="{ 'border-red-500': createForm.errors.numeric_value }"
                                    />
                                    <p v-if="createForm.errors.numeric_value" class="mt-1 text-sm text-red-600">
                                        {{ createForm.errors.numeric_value }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Group
                                    </label>
                                    <select
                                        v-model="createForm.group"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                    >
                                        <option value="">Select Group</option>
                                        <option 
                                            v-for="option in groupOptions" 
                                            :key="option.value" 
                                            :value="option.value"
                                        >
                                            {{ option.label }}
                                        </option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Display Order
                                    </label>
                                    <input
                                        type="number"
                                        v-model="createForm.display_order"
                                        placeholder="0"
                                        min="0"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                    />
                                </div>

                                <div class="flex items-center pt-6">
                                    <input
                                        type="checkbox"
                                        id="create_has_elective"
                                        v-model="createForm.has_elective"
                                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                    />
                                    <label for="create_has_elective" class="ml-2 text-sm text-gray-700 cursor-pointer">
                                        <i class="fa-solid fa-star text-yellow-500 mr-1"></i>
                                        Has Elective Subjects
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-3 pt-6 mt-6 border-t border-gray-200">
                            <button
                                type="button"
                                @click="closeModal('create')"
                                class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium disabled:opacity-50"
                                :disabled="isSubmitting"
                            >
                                <i v-if="isSubmitting" class="fa-solid fa-spinner fa-spin mr-2"></i>
                                <i v-else class="fa-solid fa-save mr-2"></i>
                                {{ isSubmitting ? 'Creating...' : 'Create Class' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- EDIT MODAL -->
        <!-- ============================================ -->
        <div v-if="showEditModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" @click="closeModal('edit')">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-emerald-50 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">
                                    <i class="fa-solid fa-edit text-green-600 mr-2"></i>
                                    Edit Class
                                </h3>
                                <p class="text-xs text-gray-500 mt-0.5">Update class information</p>
                            </div>
                            <button @click="closeModal('edit')" class="text-gray-400 hover:text-gray-600 transition">
                                <i class="fa-solid fa-times text-xl"></i>
                            </button>
                        </div>
                    </div>

                    <form @submit.prevent="updateClass" class="p-6">
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Class Name (English) <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        v-model="editForm.name"
                                        placeholder="e.g., Class 6"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                        :class="{ 'border-red-500': editForm.errors.name }"
                                    />
                                    <p v-if="editForm.errors.name" class="mt-1 text-sm text-red-600">
                                        {{ editForm.errors.name }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Class Name (Bangla)
                                    </label>
                                    <input
                                        type="text"
                                        v-model="editForm.bn_name"
                                        placeholder="e.g., ষষ্ঠ শ্রেণি"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Numeric Value <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        v-model="editForm.numeric_value"
                                        placeholder="e.g., 6"
                                        min="1"
                                        max="12"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                        :class="{ 'border-red-500': editForm.errors.numeric_value }"
                                    />
                                    <p v-if="editForm.errors.numeric_value" class="mt-1 text-sm text-red-600">
                                        {{ editForm.errors.numeric_value }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Group
                                    </label>
                                    <select
                                        v-model="editForm.group"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                    >
                                        <option value="">Select Group</option>
                                        <option 
                                            v-for="option in groupOptions" 
                                            :key="option.value" 
                                            :value="option.value"
                                        >
                                            {{ option.label }}
                                        </option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Display Order
                                    </label>
                                    <input
                                        type="number"
                                        v-model="editForm.display_order"
                                        placeholder="0"
                                        min="0"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                    />
                                </div>

                                <div class="flex items-center pt-6">
                                    <input
                                        type="checkbox"
                                        id="edit_has_elective"
                                        v-model="editForm.has_elective"
                                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                    />
                                    <label for="edit_has_elective" class="ml-2 text-sm text-gray-700 cursor-pointer">
                                        <i class="fa-solid fa-star text-yellow-500 mr-1"></i>
                                        Has Elective Subjects
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-3 pt-6 mt-6 border-t border-gray-200">
                            <button
                                type="button"
                                @click="closeModal('edit')"
                                class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium disabled:opacity-50"
                                :disabled="isSubmitting"
                            >
                                <i v-if="isSubmitting" class="fa-solid fa-spinner fa-spin mr-2"></i>
                                <i v-else class="fa-solid fa-save mr-2"></i>
                                {{ isSubmitting ? 'Updating...' : 'Update Class' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- DELETE MODAL -->
        <!-- ============================================ -->
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" @click="closeModal('delete')">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="px-6 py-4 bg-gradient-to-r from-red-50 to-rose-50 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">
                                    <i class="fa-solid fa-trash text-red-600 mr-2"></i>
                                    Delete Class
                                </h3>
                                <p class="text-xs text-gray-500 mt-0.5">This action cannot be undone</p>
                            </div>
                            <button @click="closeModal('delete')" class="text-gray-400 hover:text-gray-600 transition">
                                <i class="fa-solid fa-times text-xl"></i>
                            </button>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                                <i class="fa-solid fa-exclamation-triangle text-red-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-700">
                                    Are you sure you want to delete <strong class="text-gray-900">{{ selectedClass?.name }}</strong>?
                                </p>
                                
                                <div class="mt-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <p class="text-xs font-medium text-gray-700 mb-2">Class Details:</p>
                                    <div class="space-y-1 text-xs text-gray-600">
                                        <div class="flex justify-between">
                                            <span>Name:</span>
                                            <span class="font-medium">{{ selectedClass?.name }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Class:</span>
                                            <span class="font-medium">Class {{ selectedClass?.numeric_value }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Group:</span>
                                            <span class="font-medium">{{ selectedClass?.group_label || '—' }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Total Students:</span>
                                            <span class="font-medium text-red-600">{{ selectedClass?.student_count || 0 }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="selectedClass?.student_count > 0" class="mt-3 p-3 bg-red-50 rounded-lg border border-red-200">
                                    <p class="text-xs text-red-700 flex items-center">
                                        <i class="fa-solid fa-users mr-2"></i>
                                        <span>This class has <strong>{{ selectedClass?.student_count }}</strong> enrolled students. You cannot delete it.</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end space-x-3">
                        <button
                            @click="closeModal('delete')"
                            class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium"
                        >
                            Cancel
                        </button>
                        <button
                            @click="deleteClass"
                            class="px-6 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium disabled:opacity-50"
                            :disabled="selectedClass?.student_count > 0"
                        >
                            <i class="fa-solid fa-trash mr-2"></i>
                            Delete Class
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- CLASS DETAILS MODAL -->
        <!-- ============================================ -->
        <div v-if="showClassDetailsModal && selectedClass" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" @click="closeModal('details')">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-purple-50 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">
                                    <i class="fa-solid fa-school text-indigo-600 mr-2"></i>
                                    Class Details
                                </h3>
                                <p class="text-xs text-gray-500 mt-0.5">Complete information about this class</p>
                            </div>
                            <button @click="closeModal('details')" class="text-gray-400 hover:text-gray-600 transition">
                                <i class="fa-solid fa-times text-xl"></i>
                            </button>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <p class="text-xs text-gray-500">Class Name</p>
                                <p class="text-sm font-medium text-gray-900">{{ selectedClass.name }}</p>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <p class="text-xs text-gray-500">Bangla Name</p>
                                <p class="text-sm font-medium text-gray-900">{{ selectedClass.bn_name || '—' }}</p>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <p class="text-xs text-gray-500">Numeric Value</p>
                                <p class="text-sm font-medium text-gray-900">Class {{ selectedClass.numeric_value }}</p>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <p class="text-xs text-gray-500">Group</p>
                                <p class="text-sm font-medium text-gray-900">
                                    <span 
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                        :class="getGroupBadgeClass(selectedClass.group)"
                                    >
                                        {{ selectedClass.group_label || '—' }}
                                    </span>
                                </p>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <p class="text-xs text-gray-500">Total Students</p>
                                <p class="text-sm font-bold text-blue-600">{{ selectedClass.student_count || 0 }}</p>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <p class="text-xs text-gray-500">Active Students</p>
                                <p class="text-sm font-bold text-green-600">{{ selectedClass.active_student_count || 0 }}</p>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <p class="text-xs text-gray-500">Sections</p>
                                <p class="text-sm font-medium text-gray-900">{{ selectedClass.section_count || 0 }}</p>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <p class="text-xs text-gray-500">Type</p>
                                <p class="text-sm font-medium text-gray-900">{{ selectedClass.class_type || 'Regular' }}</p>
                            </div>
                        </div>

                        <div class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                            <p class="text-xs text-blue-700">
                                <i class="fa-solid fa-circle-info mr-1"></i>
                                This class has {{ selectedClass.student_count || 0 }} students enrolled in 
                                {{ selectedClass.section_count || 0 }} sections.
                            </p>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end space-x-3">
                        <button
                            @click="closeModal('details')"
                            class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium"
                        >
                            Close
                        </button>
                        <Link
                            :href="route('classes.show', selectedClass.id)"
                            class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium"
                        >
                            <i class="fa-solid fa-arrow-right mr-2"></i>
                            View Full Details
                        </Link>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </InstituteLayout>
</template>

<style scoped>
/* Custom scrollbar */
::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}

/* Modal animations */
.fixed {
    animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

/* Table row hover */
.hover\:bg-gray-50:hover {
    transition: background-color 0.15s ease;
}

/* Input focus effects */
input:focus, select:focus {
    outline: none;
}

/* Checkbox custom style */
input[type="checkbox"] {
    cursor: pointer;
}

/* Button transitions */
button {
    transition: all 0.15s ease;
}

/* Stats card hover */
.hover\:shadow-md:hover {
    transition: box-shadow 0.2s ease;
}
</style>