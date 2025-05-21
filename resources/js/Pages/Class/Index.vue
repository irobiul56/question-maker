<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, usePage, useForm, Link } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import { ElMessage } from "element-plus";

const { props } = usePage();
const data = ref(props.data);
const itemsPerPage = ref(10);
const currentPage = ref(1);
const searchQuery = ref("");

const groupeddata = computed(() => {
  const grouped = {};
  data.value.forEach((item) => {
    if (!grouped[item.education_id]) {
      grouped[item.education_id] = { ...item, name: [] };
    }
    grouped[item.education_id].name.push({ name: item.name });
  });
  return Object.values(grouped);
});

const filtereddata = computed(() => {
  const query = searchQuery.value.toLowerCase();
  return groupeddata.value
    .filter(({ education }) => education.name.toLowerCase().includes(query))
    .slice((currentPage.value - 1) * itemsPerPage.value, currentPage.value * itemsPerPage.value);
});

const totalPages = computed(() => Math.ceil(filtereddata.value.length / itemsPerPage.value));
const nextPage = () => currentPage.value < totalPages.value && currentPage.value++;
const prevPage = () => currentPage.value > 1 && currentPage.value--;

const deletedata = (dataId) => {
  if (confirm("Are you sure you want to delete this data?")) {
    useForm().delete(route("class.destroy", dataId), {
      onSuccess: ({ props }) => {
        data.value = props.data;
        ElMessage.success("Class deleted successfully!");
      },
      onError: () => ElMessage.error("Failed to delete the class. Please try again."),
    });
  }
};
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Class List" />
    
    <!-- Flash Message -->
    <div v-if="$page.props.flash?.message" class="fixed top-4 right-4 z-50">
      <div class="p-4 mb-4 text-sm text-white rounded-lg shadow-lg bg-emerald-500 animate-fade-in-down">
        {{ $page.props.flash?.message }}
      </div>
    </div>
    
    <div class="container mx-auto px-4 py-6">
      <!-- Header Section -->
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
          <h1 class="text-2xl font-bold text-gray-800">Class Management</h1>
          <p class="text-gray-600">View and manage all classes by education level</p>
        </div>
        <Link :href="route('class.create')">
          <el-button type="primary" class="flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Create New Class
          </el-button>
        </Link>
      </div>
      
      <!-- Search and Filter Section -->
      <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
          <div class="relative w-full md:w-96">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
            </div>
            <input 
              v-model="searchQuery" 
              class="block w-full pl-10 pr-3 py-2 border border-gray-200 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
              type="text" 
              placeholder="Search by education level..."
            />
          </div>
          
          <div class="flex items-center gap-2">
            <span class="text-sm text-gray-500">Items per page:</span>
            <select v-model="itemsPerPage" class="text-sm border border-gray-200 rounded-md px-2 py-1 focus:ring-2 focus:ring-blue-500">
              <option value="5">5</option>
              <option value="10">10</option>
              <option value="20">20</option>
              <option value="50">50</option>
            </select>
          </div>
        </div>
      </div>
      
      <!-- Class Table -->
      <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Education Level</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Classes</th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="(curr, index) in filtereddata" :key="curr.education_id" class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  {{ index + 1 + (currentPage - 1) * itemsPerPage }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                      </svg>
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900">{{ curr.education.name }}</div>
                      <div class="text-sm text-gray-500">{{ curr.name.length }} classes</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <div class="flex flex-wrap gap-2">
                    <span v-for="(cls, idx) in curr.name" :key="idx" class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                      {{ cls.name }}
                    </span>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div class="flex justify-end gap-2">
                    <Link :href="route('class.edit', curr.education_id)">
                      <el-button class="flex items-center gap-1" type="warning" plain>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit
                      </el-button>
                    </Link>
                    <el-button @click="deletedata(curr.education_id)" class="flex items-center gap-1" type="danger" plain>
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                      Delete
                    </el-button>
                  </div>
                </td>
              </tr>
              <tr v-if="filtereddata.length === 0">
                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                  No classes found matching your search criteria.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-gray-50 px-6 py-3 flex items-center justify-between border-t border-gray-200">
          <div class="text-sm text-gray-500">
            Showing <span class="font-medium">{{ (currentPage - 1) * itemsPerPage + 1 }}</span> to 
            <span class="font-medium">{{ Math.min(currentPage * itemsPerPage, filtereddata.length) }}</span> of 
            <span class="font-medium">{{ filtereddata.length }}</span> results
          </div>
          <div class="flex space-x-2">
            <button 
              @click="prevPage" 
              :disabled="currentPage === 1" 
              :class="{'opacity-50 cursor-not-allowed': currentPage === 1, 'hover:bg-gray-200': currentPage > 1}"
              class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white flex items-center gap-1"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
              </svg>
              Previous
            </button>
            <button 
              @click="nextPage" 
              :disabled="currentPage === totalPages" 
              :class="{'opacity-50 cursor-not-allowed': currentPage === totalPages, 'hover:bg-gray-200': currentPage < totalPages}"
              class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white flex items-center gap-1"
            >
              Next
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style>
.animate-fade-in-down {
  animation: fadeInDown 0.5s ease-out;
}

@keyframes fadeInDown {
  0% {
    opacity: 0;
    transform: translateY(-20px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>