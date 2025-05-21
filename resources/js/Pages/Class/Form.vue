<script setup>
import { ref } from "vue";
import { usePage, useForm, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ElMessage } from 'element-plus';

const { props } = usePage();
const education = ref(props.education);

const selectededucation = ref(""); // Store selected education ID
const classs = ref([{ name: "" }]);

const addclass = () => {
    classs.value.push({ name: "" });
};

const removeclass = (index) => {
    if (classs.value.length > 1) {
        classs.value.splice(index, 1);
    } else {
        ElMessage.warning("At least one class is required");
    }
};

const resetForm = () => {
    selectededucation.value = "";
    classs.value = [{ name: "" }];
};

const submitForm = () => {
    if (!selectededucation.value) {
        ElMessage.error("Please select an education level");
        return;
    }

    // Validate all class names are filled
    if (classs.value.some(cls => !cls.name.trim())) {
        ElMessage.error("Please fill in all class names");
        return;
    }

    // Send data to Laravel backend
    useForm({
        education_id: selectededucation.value,
        classs: classs.value,
    }).post(route("class.store"), {
        onSuccess: () => {
            ElMessage.success("Classes created successfully!");
            resetForm();
        },
        onError: (errors) => {
            console.log(errors);
            ElMessage.error("Failed to create classes. Please check your inputs.");
        },
    });
};
</script>

<template>
    <Head title="Create New Classes"/>
    <AuthenticatedLayout>
        <div class="min-h-screen flex items-center justify-center py-8 px-4 sm:px-6 lg:px-8">
            <div class="w-full max-w-3xl">
                <!-- Form Card -->
                <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
                    <!-- Form Header -->
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-4">
                        <h2 class="text-xl font-semibold text-white">
                            Create New Classes
                        </h2>
                    </div>

                    <form @submit.prevent="submitForm" class="px-6 py-6 space-y-6">
                        <!-- Education Level Selection -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">
                                Education Level <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select 
                                    v-model="selectededucation" 
                                    class="block w-full pl-4 pr-10 py-3 text-base border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 rounded-xl shadow-sm appearance-none bg-white"
                                    required
                                >
                                    <option value="" disabled selected>Select education level</option>
                                    <option 
                                        v-for="educations in education" 
                                        :key="educations.id" 
                                        :value="educations.id"
                                        class="py-2"
                                    >
                                        {{ educations.name }}
                                    </option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Dynamic Class Fields -->
                        <div class="space-y-4">
                            <div v-for="(cls, index) in classs" :key="index" class="bg-gray-50 p-4 rounded-lg border border-gray-200 relative">
                                <div class="flex justify-between items-center mb-3">
                                    <span class="text-sm font-medium text-gray-700">
                                        Class #{{ index + 1 }}
                                    </span>
                                    <button 
                                        type="button" 
                                        @click="removeclass(index)"
                                        class="text-red-500 hover:text-red-700 transition-colors duration-200"
                                        :class="{ 'opacity-50 cursor-not-allowed': classs.length <= 1 }"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                                
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">
                                        Class Name <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                            </svg>
                                        </div>
                                        <input 
                                            v-model="cls.name" 
                                            type="text" 
                                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm" 
                                            placeholder="e.g. Class A, Grade 1, etc." 
                                            required
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Add More Class Button -->
                        <button 
                            type="button" 
                            @click="addclass"
                            class="w-full flex items-center justify-center space-x-2 py-2 px-4 border border-dashed border-gray-300 rounded-lg hover:border-gray-400 hover:bg-gray-50 transition-colors duration-200 text-gray-600"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            <span>Add Another Class</span>
                        </button>

                        <!-- Form Actions -->
                        <div class="pt-4 flex flex-col sm:flex-row sm:justify-between space-y-4 sm:space-y-0 sm:space-x-4">
                            <button 
                                type="button" 
                                @click="resetForm"
                                class="px-6 py-3 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200"
                            >
                                Reset Form
                            </button>
                            <button 
                                type="submit" 
                                class="px-6 py-3 border border-transparent rounded-lg font-medium text-white bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 shadow-sm transition-all duration-200"
                            >
                                Create Classes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>