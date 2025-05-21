<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import { Head } from "@inertiajs/vue3";
import { Link } from "@inertiajs/vue3";
import { ref, computed } from 'vue'
import { usePage, useForm } from '@inertiajs/vue3';
import { ElMessage, ElMessageBox } from "element-plus";

const { props } = usePage()
const data = ref(props.data)

const itemsPerPage = ref(10)
const currentPage = ref(1)
const searchQuery = ref('')

const filtereddata = computed(() => {
  const filtered = data.value.filter(data => 
  data.name.toLowerCase().includes(searchQuery.value.toLowerCase()) 
  )
  const start = (currentPage.value - 1) * itemsPerPage.value
  const end = start + itemsPerPage.value
  return filtered.slice(start, end)
})

const totalPages = computed(() => {
  return Math.ceil(
    data.value.filter(data => 
    data.name.toLowerCase().includes(searchQuery.value.toLowerCase()) 
    ).length / itemsPerPage.value
  )
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
const isModalVisible = ref(false);

// Form to add a new customer
const dataForm = useForm({
    name: '',
});

const Adddata = () => {
    dataForm.reset();
    dataForm.id = null;
    isModalVisible.value = true;
};

// Method to open modal with permission data for editing
const editdata = (data) => {
    dataForm.id = data.id;
    dataForm.name = data.name;
    isModalVisible.value = true;
};

const submitdata = () => {
    const isUpdate = Boolean(dataForm.id);
    const routeName = isUpdate ? 'education.update' : 'education.store';
    const routeParams = isUpdate ? [dataForm.id] : [];
    const method = isUpdate ? 'put' : 'post';

    dataForm[method](route(routeName, ...routeParams), {
        onSuccess: (page) => {
            isModalVisible.value = false;
            data.value = page.props.data;
            dataForm.reset();
            dataForm.id = null;
            const message = isUpdate ? "Education Level updated successfully!" : "Education Level added successfully!";
            ElMessage.success({
                message: message,
                customClass: 'success-message',
                duration: 3000
            });
        },
        onError: () => {
            ElMessage.error({
                message: "Failed to submit the data. Please try again.",
                customClass: 'error-message',
                duration: 3000
            });
        }
    });
};

const closeModal = () => {
    isModalVisible.value = false;
};

const deletedata = (dataId) => {
    ElMessageBox.confirm(
        'This will permanently delete the Education Level. Continue?',
        'Warning',
        {
            confirmButtonText: 'Delete',
            cancelButtonText: 'Cancel',
            type: 'warning',
            customClass: 'delete-confirm-dialog'
        }
    ).then(() => {
        dataForm.delete(route('education.destroy', dataId), {
            onSuccess: (page) => {
                data.value = page.props.data;
                ElMessage.success({
                    message: "Education Level deleted successfully!",
                    customClass: 'success-message',
                    duration: 3000
                });
            },
            onError: () => {
                ElMessage.error({
                    message: "Failed to delete the data. Please try again.",
                    customClass: 'error-message',
                    duration: 3000
                });
            },
        });
    }).catch(() => {
        ElMessage.info({
            message: 'Deletion canceled',
            customClass: 'info-message',
            duration: 2000
        });
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Education Level Management" />
        

        
        <!-- Flash Message -->
        <div v-if="$page.props.flash?.message" class="fixed top-6 right-6 z-50 animate-fade-in">
            <div class="bg-emerald-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                {{ $page.props.flash?.message }}
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="min-h-screen w-full bg-gray-50 px-6 py-8">
            <div class="w-full mx-auto">
                <!-- Action Bar -->
                <div class="relative mx-4 lg:mx-0 mb-2">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3"><svg class="w-5 h-5 text-gray-500"
                        viewBox="0 0 24 24" fill="none">
                        <path
                            d="M21 21L15 15M17 10C17 13.866 13.866 17 10 17C6.13401 17 3 13.866 3 10C3 6.13401 6.13401 3 10 3C13.866 3 17 6.13401 17 10Z"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        </path>
                    </svg></span>
                <input v-model="searchQuery"
                    class="w-32 pl-10 pr-4 text-indigo-600 border-gray-200 rounded-md sm:w-64 focus:border-indigo-600 focus:ring focus:ring-opacity-40 focus:ring-indigo-500"
                    type="text" placeholder="Search">
                    <el-button class=" ml-3" @click="Adddata" type="success">Add</el-button>

            </div>
                
                <!-- Table Container -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gradient-to-r from-indigo-500 to-indigo-600">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                                        ID
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                                        Education Level
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-white uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="(item, index) in filtereddata" :key="index" class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ item.id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ item.name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <button 
                                                @click="editdata(item)"
                                                class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 p-2 rounded-lg transition-colors duration-200"
                                                title="Edit"
                                            >
                                                Edit
                                            </button>
                                            <button 
                                                @click="deletedata(item.id)"
                                                class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 p-2 rounded-lg transition-colors duration-200"
                                                title="Delete"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="bg-gray-50 px-6 py-4 flex flex-col md:flex-row justify-between items-center border-t border-gray-200">
                        <div class="mb-4 md:mb-0">
                            <p class="text-sm text-gray-700">
                                Showing <span class="font-medium">{{ (currentPage - 1) * itemsPerPage + 1 }}</span> to 
                                <span class="font-medium">{{ Math.min(currentPage * itemsPerPage, data.length) }}</span> of 
                                <span class="font-medium">{{ data.length }}</span> results
                            </p>
                        </div>
                        <div class="flex space-x-2">
                            <button 
                                @click="prevPage" 
                                :disabled="currentPage === 1"
                                class="px-4 py-2 border border-gray-300 rounded-md bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                Previous
                            </button>
                            <button 
                                @click="nextPage" 
                                :disabled="currentPage === totalPages"
                                class="px-4 py-2 border border-gray-300 rounded-md bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                Next
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add/Edit Modal -->
        <div v-if="isModalVisible" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-6 pt-6 pb-4">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">
                            {{ dataForm.id ? 'Edit Education Level' : 'Create New Education Level' }}
                        </h3>
                        
                        <div class="mb-6">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Education Level Name</label>
                            <input
                                v-model="dataForm.name"
                                type="text"
                                id="name"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="e.g. Bachelor's Degree"
                            >
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3">
                        <button
                            @click="closeModal"
                            class="px-4 py-2 border border-gray-300 rounded-md bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            Cancel
                        </button>
                        <button
                            @click="submitdata"
                            :disabled="dataForm.processing"
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {{ dataForm.id ? 'Update' : 'Create' }}
                            <i v-if="dataForm.processing" class="fas fa-spinner fa-spin ml-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
/* Animation for flash message */
@keyframes fade-in {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a1a1a1;
}

/* Delete confirmation dialog */
.delete-confirm-dialog {
    border-radius: 0.75rem !important;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
}

/* Hover effects */
button {
    transition: all 0.2s ease;
}

button:hover {
    transform: translateY(-1px);
}

/* Table row hover effect */
tr {
    transition: background-color 0.2s ease;
}
</style>