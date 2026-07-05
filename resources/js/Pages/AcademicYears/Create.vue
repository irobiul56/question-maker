<!-- resources/js/Pages/AcademicYears/Create.vue -->
<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import InstituteLayout from '@/Layouts/InstituteLayout.vue';

const form = useForm({
    name: '',
    year: new Date().getFullYear(),
    session: `${new Date().getFullYear()}-${new Date().getFullYear() + 1}`,
    start_date: '',
    end_date: '',
    is_current: false
});

const submit = () => {
    form.post(route('academic-years.store'), {
        onSuccess: () => {
            // Optional: Show success message
        }
    });
};
</script>

<template>
    <Head title="Create Academic Year" />
    
    <InstituteLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-800">Create Academic Year</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Add a new academic year or session
                    </p>
                </div>
                <Link
                    :href="route('academic-years.index')"
                    class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium rounded-lg transition"
                >
                    <i class="fa-solid fa-arrow-left mr-2"></i>
                    Back
                </Link>
            </div>
        </template>

        <div class="max-w-3xl mx-auto">
            <form @submit.prevent="submit" class="bg-white rounded-xl shadow-sm p-6">
                <div class="space-y-6">
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
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Year <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="number"
                                v-model="form.year"
                                placeholder="e.g., 2024"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                :class="{ 'border-red-500': form.errors.year }"
                            />
                            <p v-if="form.errors.year" class="mt-1 text-sm text-red-600">{{ form.errors.year }}</p>
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
                        </div>
                    </div>

                    <!-- Is Current -->
                    <div class="flex items-center">
                        <input
                            type="checkbox"
                            v-model="form.is_current"
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                        />
                        <label class="ml-2 text-sm text-gray-700">
                            Set as current academic year
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                        <Link
                            :href="route('academic-years.index')"
                            class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium"
                            :disabled="form.processing"
                        >
                            <i v-if="form.processing" class="fa-solid fa-spinner fa-spin mr-2"></i>
                            <i v-else class="fa-solid fa-save mr-2"></i>
                            {{ form.processing ? 'Saving...' : 'Save Academic Year' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </InstituteLayout>
</template>