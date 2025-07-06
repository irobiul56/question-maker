<script setup>
import UserDashboardLayout from "@/Layouts/UserDashboardLayout.vue"
import { Head, router  } from "@inertiajs/vue3";
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
    data.exam_name.toLowerCase().includes(searchQuery.value.toLowerCase()) 
  )
  const start = (currentPage.value - 1) * itemsPerPage.value
  const end = start + itemsPerPage.value
  return filtered.slice(start, end)
})

const totalPages = computed(() => {
  return Math.ceil(
    data.value.filter(data => 
    data.exam_name.toLowerCase().includes(searchQuery.value.toLowerCase()) 
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


const deletedata = (dataId) => {
    if (confirm("Are you sure you want to delete this question?")) {
        router.delete(route('my-questions.destroy', dataId), {
            onSuccess: (page) => {
                data.value = page.props.data; // Update the data list after deletion
                ElMessage.success("My Question deleted successfully!");
            },
            onError: () => {
                ElMessage.error("Failed to delete the data. Please try again.");
            },
        });
    }
};

</script>

<template>
    <UserDashboardLayout>
        <Head title="My Question" />
    
        
        <!-- Flash Message -->
        <div v-if="$page.props.flash?.message" class="fixed top-6 right-6 z-50 animate-fade-in">
            <div class="bg-emerald-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                {{ $page.props.flash?.message }}
            </div>
        </div>
        
        <!-- Main Content Area -->
        <div class="min-h-screen w-full bg-gray-50 px-6 py-8">
            <div class="max-w-7xl mx-auto">
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
                    <Link :href="route('qstIndex')"> <el-button class=" ml-3" type="success">প্রশ্ন তৈরি</el-button> </Link>

            </div>
                
                <!-- Table Container -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gradient-to-r from-blue-500 to-blue-600">
                                <tr>
                                    <th scope="col" class="px-8 py-4 text-left text-sm font-bold text-white uppercase tracking-wider">
                                        #
                                    </th>
                                    <th scope="col" class="px-8 py-4 text-left text-sm font-bold text-white uppercase tracking-wider">
                                        Exam Name
                                    </th>
                                    <th scope="col" class="px-8 py-4 text-left text-sm font-bold text-white uppercase tracking-wider">
                                        Subject
                                    </th>
                                    <th scope="col" class="px-8 py-4 text-left text-sm font-bold text-white uppercase tracking-wider">
                                        Class
                                    </th>
                                    <th scope="col" class="px-8 py-4 text-right text-sm font-bold text-white uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="(item, index) in filtereddata" :key="index" class="hover:bg-blue-50 transition-colors duration-150">
                                    <td class="px-8 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ index + 1 }}
                                    </td>
                                    <td class="flex gap-2 px-8 py-4 whitespace-nowrap text-sm text-gray-700">
                                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 384 512" class=" shrink-0 text-2xl text-gray-500" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M224 136V0H24C10.7 0 0 10.7 0 24v464c0 13.3 10.7 24 24 24h336c13.3 0 24-10.7 24-24V160H248c-13.2 0-24-10.8-24-24zm64 236c0 6.6-5.4 12-12 12H108c-6.6 0-12-5.4-12-12v-8c0-6.6 5.4-12 12-12h168c6.6 0 12 5.4 12 12v8zm0-64c0 6.6-5.4 12-12 12H108c-6.6 0-12-5.4-12-12v-8c0-6.6 5.4-12 12-12h168c6.6 0 12 5.4 12 12v8zm0-72v8c0 6.6-5.4 12-12 12H108c-6.6 0-12-5.4-12-12v-8c0-6.6 5.4-12 12-12h168c6.6 0 12 5.4 12 12zm96-114.1v6.1H256V0h6.1c6.4 0 12.5 2.5 17 7l97.9 98c4.5 4.5 7 10.6 7 16.9z"></path>
                                        </svg>
                                        <p class="line-clamp-1">{{ item.exam_name }}</p>
                                        <p class="line-clamp-1 bg-green-50 text-green-700 rounded px-1.5 py-0.5">{{ item.questions[0]?.format }}</p>
                                    </td>
                                    <td class="px-8 py-4 whitespace-nowrap text-sm text-gray-700">
                                        <p class="bg-gray-50 rounded-full px-1">{{ item.questions[0]?.subject.name }}</p>
                                    </td>
                                    <td class="px-8 py-4 whitespace-nowrap text-sm text-gray-700">
                                        <p class="bg-green-50 text-green-700 rounded-full px-1.5 py-0.5">{{ item.questions[0]?.academic_class.name }}</p>
                                    </td>
                                    <td class="px-8 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-3">
                                            
                                            <Link :href="route('qstshow', item.id)"> 
                                                <button 
                                                class="text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 p-2 rounded-lg transition-all duration-200"
                                                title="Edit"
                                            >
                                                Show
                                            </button>
                                            </Link>
                                           
                                            <button 
                                                @click="deletedata(item.id)"
                                                class="text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 p-2 rounded-lg transition-all duration-200"
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
                                class="px-4 py-2 border border-gray-300 rounded-md bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
                            >
                                Previous
                            </button>
                            <button 
                                @click="nextPage" 
                                :disabled="currentPage === totalPages"
                                class="px-4 py-2 border border-gray-300 rounded-md bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
                            >
                                Next
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
    </UserDashboardLayout>
</template>

<style>
/* Animation for flash message */
@keyframes bounce-in {
    0% { opacity: 0; transform: translateY(-20px) scale(0.9); }
    50% { transform: translateY(5px) scale(1.02); }
    100% { opacity: 1; transform: translateY(0) scale(1); }
}

.animate-bounce-in {
    animation: bounce-in 0.5s ease-out;
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