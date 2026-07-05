<!-- resources/js/Pages/AcademicYears/Index.vue -->
<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/inertia-vue3';
import InstituteLayout from '@/Layouts/InstituteLayout.vue';
import { Inertia } from '@inertiajs/inertia';

// Props
const props = defineProps({
    academicYears: Array,
    currentYear: Object,
    stats: Object
});

// State
const searchQuery = ref('');
const confirmDelete = ref(null);
const showDeleteModal = ref(false);
const isDeleting = ref(false);

// Computed
const filteredYears = computed(() => {
    if (!searchQuery.value) return props.academicYears;
    
    return props.academicYears.filter(year => {
        return year.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
               year.session.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
               year.year.toString().includes(searchQuery.value);
    });
});

// Methods
const setCurrentYear = (year) => {
    if (confirm('Are you sure you want to set this as the current academic year?')) {
        Inertia.post(`/academic-years/${year.id}/set-current`);
    }
};

const confirmDeleteYear = (year) => {
    confirmDelete.value = year;
    showDeleteModal.value = true;
};

const deleteYear = () => {
    if (!confirmDelete.value) return;
    
    isDeleting.value = true;
    
    Inertia.delete(route('academic-years.destroy', confirmDelete.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false;
            confirmDelete.value = null;
            isDeleting.value = false;
        },
        onError: (errors) => {
            console.error('Delete error:', errors);
            isDeleting.value = false;
        }
    });
};

const formatDate = (date) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });
};

const calculateDuration = (startDate, endDate) => {
    if (!startDate || !endDate) return 0;
    
    const start = new Date(startDate);
    const end = new Date(endDate);
    const diffTime = Math.abs(end - start);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays;
};

const getStatusBadge = (isCurrent) => {
    return isCurrent 
        ? 'bg-green-100 text-green-800' 
        : 'bg-gray-100 text-gray-800';
};

const getStatusText = (isCurrent) => {
    return isCurrent ? 'Current' : 'Inactive';
};
</script>

<template>
    <Head title="Academic Years" />
    
    <InstituteLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-800">Academic Years</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Manage academic years and sessions
                    </p>
                </div>
                <Link
                    :href="route('academic-years.create')"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition"
                >
                    <i class="fa-solid fa-plus mr-2"></i>
                    Add Academic Year
                </Link>
            </div>
        </template>

        <div class="space-y-6 w-full p-5">
        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-sm font-medium text-gray-500">Total Years</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ stats.total }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-l-green-500">
                <p class="text-sm font-medium text-gray-500">Current Year</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">
                    {{ stats.current > 0 ? 'Active' : 'None' }}
                </p>
                <p v-if="currentYear" class="text-sm text-gray-500 mt-1">
                    {{ currentYear.session }}
                </p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-l-yellow-500">
                <p class="text-sm font-medium text-gray-500">Inactive Years</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ stats.inactive }}</p>
            </div>
        </div>

        <!-- Search -->
        <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
            <div class="relative">
                <i class="fa-solid fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input
                    type="text"
                    v-model="searchQuery"
                    placeholder="Search academic years..."
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                />
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Name
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Session
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Year
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Start Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                End Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Duration
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-if="filteredYears.length === 0">
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <i class="fa-solid fa-calendar-times text-4xl mb-3 block"></i>
                                <p>No academic years found</p>
                                <Link
                                    :href="route('academic-years.create')"
                                    class="inline-block mt-2 text-blue-600 hover:text-blue-800 font-medium"
                                >
                                    Create your first academic year
                                </Link>
                            </td>
                        </tr>
                        <tr 
                            v-for="year in filteredYears" 
                            :key="year.id"
                            class="hover:bg-gray-50 transition-colors"
                            :class="{ 'bg-green-50': year.is_current }"
                        >
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full flex items-center justify-center" 
                                         :class="year.is_current ? 'bg-green-100' : 'bg-gray-100'">
                                        <i class="fa-solid fa-calendar" 
                                           :class="year.is_current ? 'text-green-600' : 'text-gray-600'"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">{{ year.name }}</p>
                                        <p v-if="year.is_current" class="text-xs text-green-600">Active Year</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ year.session }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ year.year }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ formatDate(year.start_date) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ formatDate(year.end_date) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ calculateDuration(year.start_date, year.end_date) }} days
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span 
                                    class="px-2 py-1 text-xs font-semibold rounded-full"
                                    :class="getStatusBadge(year.is_current)"
                                >
                                    {{ getStatusText(year.is_current) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <Link
                                        :href="route('academic-years.show', year.id)"
                                        class="text-blue-600 hover:text-blue-900 transition"
                                        title="View"
                                    >
                                        <i class="fa-solid fa-eye"></i>
                                    </Link>
                                    <Link
                                        :href="route('academic-years.edit', year.id)"
                                        class="text-green-600 hover:text-green-900 transition"
                                        title="Edit"
                                    >
                                        <i class="fa-solid fa-edit"></i>
                                    </Link>
                                    <button
                                        v-if="!year.is_current"
                                        @click="setCurrentYear(year)"
                                        class="text-indigo-600 hover:text-indigo-900 transition"
                                        title="Set as Current"
                                    >
                                        <i class="fa-solid fa-check-circle"></i>
                                    </button>
                                    <button
                                        @click="confirmDeleteYear(year)"
                                        class="text-red-600 hover:text-red-900 transition"
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
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" @click="showDeleteModal = false">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-red-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fa-solid fa-exclamation-triangle text-red-600"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg font-medium leading-6 text-gray-900">
                                    Delete Academic Year
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Are you sure you want to delete <strong>{{ confirmDelete?.name }}</strong>? 
                                        This action cannot be undone.
                                    </p>
                                    <p class="mt-2 text-sm text-red-600">
                                        <i class="fa-solid fa-circle-exclamation mr-1"></i>
                                        This will also remove all associated data.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button
                            @click="deleteYear"
                            class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm"
                            :disabled="isDeleting"
                        >
                            <i v-if="isDeleting" class="fa-solid fa-spinner fa-spin mr-2"></i>
                            {{ isDeleting ? 'Deleting...' : 'Delete' }}
                        </button>
                        <button
                            @click="showDeleteModal = false"
                            class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                            :disabled="isDeleting"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </InstituteLayout>
</template>