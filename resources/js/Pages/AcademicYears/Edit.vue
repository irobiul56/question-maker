<!-- resources/js/Pages/AcademicYears/Edit.vue -->
<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import InstituteLayout from '@/Layouts/InstituteLayout.vue';
import { Inertia } from '@inertiajs/inertia';

// Props
const props = defineProps({
    academicYear: {
        type: Object,
        required: true
    }
});

// Form
const form = useForm({
    name: props.academicYear.name || '',
    year: props.academicYear.year || new Date().getFullYear(),
    session: props.academicYear.session || '',
    start_date: props.academicYear.start_date || '',
    end_date: props.academicYear.end_date || '',
    is_current: props.academicYear.is_current || false
});

// State
const showDeleteModal = ref(false);
const isSubmitting = ref(false);

// Computed
const duration = computed(() => {
    if (form.start_date && form.end_date) {
        const start = new Date(form.start_date);
        const end = new Date(form.end_date);
        const diffTime = Math.abs(end - start);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        return diffDays;
    }
    return 0;
});

const isCurrentYear = computed(() => form.is_current);

// Methods
const submit = () => {
    isSubmitting.value = true;
    form.put(route('academic-years.update', props.academicYear.id), {
        onSuccess: () => {
            isSubmitting.value = false;
        },
        onError: () => {
            isSubmitting.value = false;
        }
    });
};

const confirmDelete = () => {
    showDeleteModal.value = true;
};

const deleteYear = () => {
    Inertia.delete(route('academic-years.destroy', props.academicYear.id), {
        onSuccess: () => {
            showDeleteModal.value = false;
        }
    });
};

const formatDateForInput = (date) => {
    if (!date) return '';
    const d = new Date(date);
    return d.toISOString().split('T')[0];
};

// Format date for display
const formatDateDisplay = (date) => {
    if (!date) return 'Not set';
    return new Date(date).toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'long',
        year: 'numeric'
    });
};

// Lifecycle
onMounted(() => {
    // Set default dates if not set
    if (!form.start_date) {
        form.start_date = new Date().toISOString().split('T')[0];
    }
    if (!form.end_date) {
        const endDate = new Date();
        endDate.setFullYear(endDate.getFullYear() + 1);
        form.end_date = endDate.toISOString().split('T')[0];
    }
    if (!form.session) {
        const currentYear = new Date().getFullYear();
        form.session = `${currentYear}-${currentYear + 1}`;
    }
});
</script>

<template>
    <Head :title="`Edit Academic Year - ${academicYear.name}`" />
    
    <InstituteLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-800">Edit Academic Year</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Update academic year details
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <Link
                        :href="route('academic-years.show', academicYear.id)"
                        class="inline-flex items-center px-4 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 text-sm font-medium rounded-lg transition"
                    >
                        <i class="fa-solid fa-eye mr-2"></i>
                        View
                    </Link>
                    <Link
                        :href="route('academic-years.index')"
                        class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium rounded-lg transition"
                    >
                        <i class="fa-solid fa-arrow-left mr-2"></i>
                        Back
                    </Link>
                </div>
            </div>
        </template>

        <div class="max-w-3xl mx-auto">
            <form @submit.prevent="submit" class="bg-white rounded-xl shadow-sm overflow-hidden">
                <!-- Current Year Banner -->
                <div v-if="academicYear.is_current" 
                     class="bg-green-50 border-b border-green-200 px-6 py-4">
                    <div class="flex items-center">
                        <i class="fa-solid fa-circle-check text-green-600 text-xl mr-3"></i>
                        <div>
                            <p class="text-sm font-medium text-green-800">This is the current academic year</p>
                            <p class="text-xs text-green-600">This year is active and being used for all operations</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Status Indicator -->
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div>
                            <span class="text-sm font-medium text-gray-700">Status:</span>
                            <span 
                                class="ml-2 px-2 py-1 text-xs font-semibold rounded-full"
                                :class="academicYear.is_current ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                            >
                                {{ academicYear.is_current ? 'Current' : 'Inactive' }}
                            </span>
                        </div>
                        <div class="text-sm text-gray-500">
                            Created: {{ new Date(academicYear.created_at).toLocaleDateString('en-GB') }}
                        </div>
                    </div>

                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            v-model="form.name"
                            placeholder="e.g., Academic Year 2024-2025"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            :class="{ 'border-red-500': form.errors.name }"
                        />
                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                        <p class="mt-1 text-xs text-gray-500">Give a descriptive name for this academic year</p>
                    </div>

                    <!-- Session & Year -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Session <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                v-model="form.session"
                                placeholder="e.g., 2024-2025"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                :class="{ 'border-red-500': form.errors.session }"
                            />
                            <p v-if="form.errors.session" class="mt-1 text-sm text-red-600">{{ form.errors.session }}</p>
                            <p class="mt-1 text-xs text-gray-500">Format: YYYY-YYYY (e.g., 2024-2025)</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Year <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="number"
                                v-model="form.year"
                                placeholder="e.g., 2024"
                                min="2000"
                                max="2100"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                :class="{ 'border-red-500': form.errors.year }"
                            />
                            <p v-if="form.errors.year" class="mt-1 text-sm text-red-600">{{ form.errors.year }}</p>
                            <p class="mt-1 text-xs text-gray-500">Year between 2000 and 2100</p>
                        </div>
                    </div>

                    <!-- Start & End Date -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Start Date <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="date"
                                v-model="form.start_date"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                :class="{ 'border-red-500': form.errors.start_date }"
                            />
                            <p v-if="form.errors.start_date" class="mt-1 text-sm text-red-600">{{ form.errors.start_date }}</p>
                            <p class="mt-1 text-xs text-gray-500">
                                Current: {{ formatDateDisplay(academicYear.start_date) }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                End Date <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="date"
                                v-model="form.end_date"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                :class="{ 'border-red-500': form.errors.end_date }"
                            />
                            <p v-if="form.errors.end_date" class="mt-1 text-sm text-red-600">{{ form.errors.end_date }}</p>
                            <p class="mt-1 text-xs text-gray-500">
                                Current: {{ formatDateDisplay(academicYear.end_date) }}
                            </p>
                        </div>
                    </div>

                    <!-- Duration Info -->
                    <div v-if="form.start_date && form.end_date" 
                         class="flex items-center space-x-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                        <i class="fa-solid fa-clock text-blue-600"></i>
                        <div>
                            <span class="text-sm font-medium text-blue-800">Duration:</span>
                            <span class="text-sm text-blue-700 ml-2">{{ duration }} days</span>
                            <span class="text-xs text-blue-600 ml-2">
                                ({{ formatDateDisplay(form.start_date) }} - {{ formatDateDisplay(form.end_date) }})
                            </span>
                        </div>
                    </div>

                    <!-- Is Current -->
                    <div class="flex items-center p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                        <input
                            type="checkbox"
                            v-model="form.is_current"
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                            :disabled="academicYear.is_current"
                        />
                        <label class="ml-2 text-sm text-gray-700">
                            Set as current academic year
                            <span v-if="academicYear.is_current" class="text-xs text-green-600 ml-2">
                                (Currently active)
                            </span>
                            <span v-else class="text-xs text-yellow-600 ml-2">
                                (This will make it the active year)
                            </span>
                        </label>
                    </div>

                    <!-- Warning when unsetting current -->
                    <div v-if="academicYear.is_current && !form.is_current" 
                         class="flex items-start space-x-3 p-4 bg-red-50 rounded-lg border border-red-200">
                        <i class="fa-solid fa-triangle-exclamation text-red-600 mt-0.5"></i>
                        <div>
                            <p class="text-sm font-medium text-red-800">Warning: Unsetting Current Year</p>
                            <p class="text-xs text-red-700 mt-1">
                                This will remove the current academic year status. 
                                Make sure to set another year as current if needed.
                            </p>
                        </div>
                    </div>

                    <!-- Warning when setting as current -->
                    <div v-if="!academicYear.is_current && form.is_current" 
                         class="flex items-start space-x-3 p-4 bg-green-50 rounded-lg border border-green-200">
                        <i class="fa-solid fa-circle-info text-green-600 mt-0.5"></i>
                        <div>
                            <p class="text-sm font-medium text-green-800">Setting as Current Year</p>
                            <p class="text-xs text-green-700 mt-1">
                                This will set this academic year as the active year. 
                                The previous current year will be deactivated.
                            </p>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <button
                            type="button"
                            @click="confirmDelete"
                            class="inline-flex items-center px-4 py-2 bg-red-50 hover:bg-red-100 text-red-700 text-sm font-medium rounded-lg transition"
                        >
                            <i class="fa-solid fa-trash mr-2"></i>
                            Delete
                        </button>
                        <div class="flex items-center space-x-3">
                            <Link
                                :href="route('academic-years.index')"
                                class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium"
                                :disabled="form.processing || isSubmitting"
                            >
                                <i v-if="form.processing || isSubmitting" class="fa-solid fa-spinner fa-spin mr-2"></i>
                                <i v-else class="fa-solid fa-save mr-2"></i>
                                {{ (form.processing || isSubmitting) ? 'Updating...' : 'Update Academic Year' }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
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
                                        Are you sure you want to delete <strong>{{ academicYear.name }}</strong>?
                                    </p>
                                    <div class="mt-3 p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                                        <p class="text-sm text-yellow-800">
                                            <i class="fa-solid fa-circle-info mr-1"></i>
                                            This action cannot be undone. All data associated with this academic year will be permanently removed.
                                        </p>
                                    </div>
                                    <div class="mt-3">
                                        <p class="text-sm font-medium text-gray-700">Academic Year Details:</p>
                                        <ul class="mt-1 text-sm text-gray-600 space-y-1">
                                            <li>• Name: {{ academicYear.name }}</li>
                                            <li>• Session: {{ academicYear.session }}</li>
                                            <li>• Year: {{ academicYear.year }}</li>
                                            <li>• Duration: {{ formatDateDisplay(academicYear.start_date) }} - {{ formatDateDisplay(academicYear.end_date) }}</li>
                                            <li>• Status: {{ academicYear.is_current ? 'Current' : 'Inactive' }}</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button
                            @click="deleteYear"
                            class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm"
                            :disabled="form.processing"
                        >
                            <i v-if="form.processing" class="fa-solid fa-spinner fa-spin mr-2"></i>
                            Delete
                        </button>
                        <button
                            @click="showDeleteModal = false"
                            class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </InstituteLayout>
</template>

<style scoped>
/* Form input focus effects */
input:focus {
    outline: none;
}

/* Checkbox custom styles */
input[type="checkbox"] {
    cursor: pointer;
}

/* Modal animation */
.modal-enter-active,
.modal-leave-active {
    transition: all 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
    transform: scale(0.95);
}

/* Hover effects */
.hover\:bg-blue-50:hover {
    background-color: #eff6ff;
}

.hover\:bg-red-50:hover {
    background-color: #fef2f2;
}

/* Transition for form elements */
input, select, textarea {
    transition: all 0.2s ease;
}
</style>