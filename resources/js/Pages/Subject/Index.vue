<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import { Head } from "@inertiajs/vue3";
import { Link } from "@inertiajs/vue3";
import { ref, computed, watch } from 'vue'
import { usePage, useForm } from '@inertiajs/vue3';
import { ElMessage } from "element-plus";

const { props } = usePage()
const data = ref(props.data)
const classes = ref(props.classs || [])

const itemsPerPage = ref(10)
const currentPage = ref(1)
const searchQuery = ref('')
const selectedClass = ref('')

// Watch for changes in selected class and reset pagination
watch(selectedClass, () => {
  currentPage.value = 1
})

const filteredSubjects = computed(() => {
  let filtered = data.value
  
  // Filter by class if selected
  if (selectedClass.value) {
    filtered = filtered.filter(subject => 
      subject.academic_classes_id == selectedClass.value
    )
  }
  
  // Filter by search query
  filtered = filtered.filter(subject => 
    subject.name.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
  
  // Apply pagination
  const start = (currentPage.value - 1) * itemsPerPage.value
  const end = start + itemsPerPage.value
  return filtered.slice(start, end)
})

const totalPages = computed(() => {
  let filtered = data.value
  
  if (selectedClass.value) {
    filtered = filtered.filter(subject => 
      subject.academic_classes_id == selectedClass.value
    )
  }
  
  filtered = filtered.filter(subject => 
    subject.name.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
  
  return Math.ceil(filtered.length / itemsPerPage.value)
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

// Form to add/edit subject
const subjectForm = useForm({
    id: null,
    name: '',
    academic_classes_id: ''
})

const openAddModal = () => {
    subjectForm.reset()
    subjectForm.academic_classes_id = selectedClass.value || ''
    isModalVisible.value = true
}

const editSubject = (subject) => {
    subjectForm.id = subject.id
    subjectForm.name = subject.name
    subjectForm.academic_classes_id = subject.academic_classes_id
    isModalVisible.value = true
}

const submitSubject = () => {
    const isUpdate = Boolean(subjectForm.id)
    const routeName = isUpdate ? 'subject.update' : 'subject.store'
    const routeParams = isUpdate ? [subjectForm.id] : []
    const method = isUpdate ? 'put' : 'post'

    subjectForm[method](route(routeName, ...routeParams), {
        onSuccess: (page) => {
            isModalVisible.value = false
            data.value = page.props.data
            subjectForm.reset()
            const message = isUpdate ? "Subject updated successfully!" : "Subject added successfully!"
            ElMessage.success(message)
        },
        onError: () => {
            ElMessage.error("Failed to submit the data. Please try again.")
        }
    }
)}

const closeModal = () => {
    isModalVisible.value = false
}

const deleteSubject = (subjectId) => {
    if (confirm("Are you sure you want to delete this subject?")) {
        subjectForm.delete(route('subject.destroy', subjectId), {
            onSuccess: (page) => {
                data.value = page.props.data
                ElMessage.success("Subject deleted successfully!")
            },
            onError: () => {
                ElMessage.error("Failed to delete the subject. Please try again.")
            }
        }
    )}
}
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Subjects" />
        
        <!-- Flash Message -->
        <div v-if="$page.props.flash?.message" class="p-4 mb-6 text-sm text-white rounded-lg bg-gradient-to-r from-green-400 to-blue-500 shadow-lg">
            <div class="flex items-center justify-between">
                <span>{{ $page.props.flash?.message }}</span>
                <button @click="$page.props.flash.message = null" class="text-white hover:text-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input 
                            v-model="searchQuery"
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition duration-200"
                            placeholder="Search subjects..."
                            type="text"
                        >
                    </div>

                    <div class="flex items-center space-x-2 w-full sm:w-auto">
                      <Link :href="route('subject.create')">
                        <el-button 
                            type="primary" 
                            class="mt-4 sm:mt-0 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 shadow-md"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Subject
                        </el-button>
                        </Link>
                    </div>

                    <div class="flex items-center space-x-2 w-full sm:w-auto">
                        <select 
                            v-model="selectedClass" 
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all appearance-none bg-white"
                        >
                            <option value="">All Classes</option>
                            <option 
                                v-for="cls in classes" 
                                :key="cls.id" 
                                :value="cls.id"
                                class="text-gray-800"
                            >
                                {{ cls.name }}
                            </option>
                        </select>
                    </div>
                    
                    <div class="flex items-center space-x-2 w-full sm:w-auto">
                        <el-select 
                            class="w-full sm:w-28"
                            v-model="itemsPerPage"
                        >
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
                                    Subject Name
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Class
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="(subject, index) in filteredSubjects" :key="index" class="hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ (currentPage - 1) * itemsPerPage + index + 1 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ subject.name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ classes.find(c => c.id === subject.academic_classes_id)?.name || 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <button 
                                            @click="editSubject(subject)"
                                            class="text-blue-600 hover:text-blue-900 p-1 rounded-full hover:bg-blue-50 transition duration-200"
                                            title="Edit"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        <button 
                                            @click="deleteSubject(subject.id)"
                                            class="text-red-600 hover:text-red-900 p-1 rounded-full hover:bg-red-50 transition duration-200"
                                            title="Delete"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="filteredSubjects.length === 0">
                                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No subjects found matching your criteria.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6 rounded-b-xl">
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Showing <span class="font-medium">{{ (currentPage - 1) * itemsPerPage + 1 }}</span>
                                to <span class="font-medium">{{ Math.min(currentPage * itemsPerPage, filteredSubjects.length) }}</span>
                                of <span class="font-medium">{{ filteredSubjects.length }}</span> results
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <button 
                                    @click="prevPage"
                                    :disabled="currentPage === 1"
                                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <span class="sr-only">Previous</span>
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <template v-for="page in totalPages" :key="page">
                                    <button 
                                        @click="currentPage = page"
                                        :class="[page === currentPage ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50', 'relative inline-flex items-center px-4 py-2 border text-sm font-medium']"
                                    >
                                        {{ page }}
                                    </button>
                                </template>
                                <button 
                                    @click="nextPage"
                                    :disabled="currentPage === totalPages"
                                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <span class="sr-only">Next</span>
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Dialog -->
        <el-dialog
            :title="subjectForm.id ? 'Edit Subject' : 'Add New Subject'"
            :model-value="isModalVisible"
            @close="closeModal"
            width="40%"
            class="rounded-lg"
        >
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Class</label>
                    <el-select
                        v-model="subjectForm.academic_classes_id"
                        placeholder="Select Class"
                        disabled selected
                        class="w-full"
                        required
                    >
                        <el-option
                            v-for="cls in classes"
                            :key="cls.id"
                            :label="cls.name"
                            :value="cls.id"
                        />
                    </el-select>
                    <p v-if="errors.academic_classes_id" class="mt-1 text-sm text-red-600">{{ errors.academic_classes_id }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subject Name</label>
                    <el-input
                        v-model="subjectForm.name"
                        placeholder="Enter subject name"
                        class="w-full"
                        required
                    >
                        <template #prefix>
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
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
                    <el-button 
                        @click="submitSubject" 
                        :disabled="subjectForm.processing" 
                        type="primary" 
                        class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 shadow-md"
                    >
                        <span v-if="subjectForm.processing">Processing...</span>
                        <span v-else>{{ subjectForm.id ? 'Update' : 'Create' }}</span>
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
</style>