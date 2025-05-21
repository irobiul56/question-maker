<script setup>
import { ref, watch } from 'vue';
import { usePage, useForm, Head } from '@inertiajs/vue3';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ElMessage } from 'element-plus';

const { props } = usePage();
const education = ref(props.education);

const selectededucation = ref('');
const selectedclass = ref('');
const subject = ref([{ name: '' }]);

const classes = ref([]);
const loadingClasses = ref(false);

// ✅ Fetch classes using axios instead of router
watch(selectededucation, async (id) => {
    if (id) {
        loadingClasses.value = true;
        try {
            const response = await axios.get(route('classes.by-education', id));
            classes.value = response.data;
        } catch (error) {
            console.error('Error fetching classes:', error);
            ElMessage.error('Failed to load classes.');
        } finally {
            loadingClasses.value = false;
        }
    } else {
        classes.value = [];
        selectedclass.value = '';
    }
});

const addsubject = () => {
    subject.value.push({ name: '', });
};

const removesubject = (index) => {
    subject.value.splice(index, 1);
};

const form = useForm({
  education_id: '',
  class_id: '',
  subject: [],
});

const submitForm = () => {
  if (!selectededucation.value || !selectedclass.value) {
    ElMessage.error('Please select education and class.');
    return;
  }

  // Update form values before submit
  form.education_id = selectededucation.value;
  form.class_id = selectedclass.value;
  form.subject = subject.value;

  form.post(route('subject.store'), {
    onSuccess: () => {
      ElMessage.success('Subjects added successfully!');
      selectedclass.value = '';
      subject.value = [{ name: '' }];
    },
    onError: () => {
      ElMessage.error('Failed to add subject.');
    },
  });
};

</script>


<template>
    <Head title="Add Subject" />
    <AuthenticatedLayout>
       <div class="container mx-auto px-4 py-8">
  <div class="max-w-3xl mx-auto">
    <!-- Header Section -->
    <div class="text-center mb-8">
      <h1 class="text-4xl font-bold text-indigo-700 mb-2">Add New Subjects</h1>
      <p class="text-lg text-gray-600">Fill in the details below to add subjects to your curriculum</p>
    </div>

    <!-- Form Container -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
      <!-- Form Header -->
      <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
        <h2 class="text-xl font-semibold text-white">Subject Information</h2>
      </div>
      
      <!-- Form Body -->
      <form @submit.prevent="submitForm" class="p-6 space-y-6">
        <!-- Education and Class in one row -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Education Dropdown -->
          <div class="space-y-2">
            <label class="block text-sm font-medium text-gray-700">Education Level</label>
            <div class="relative">
              <select v-model="selectededucation" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all appearance-none bg-white">
                <option value="" disabled selected class="text-gray-400">Select an Education Level</option>
                <option v-for="edu in education" :key="edu.id" :value="edu.id" class="text-gray-800">{{ edu.name }}</option>
              </select>
              <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
              </div>
            </div>
          </div>

          <!-- Class Dropdown -->
          <div class="space-y-2">
            <label class="block text-sm font-medium text-gray-700">Class</label>
            <div class="relative">
              <select v-model="selectedclass" :disabled="loadingClasses || classes.length === 0" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all appearance-none bg-white disabled:bg-gray-100 disabled:cursor-not-allowed">
                <option value="" disabled selected class="text-gray-400">
                  {{ loadingClasses ? "Loading classes..." : "Select a Class" }}
                </option>
                <option v-for="cls in classes" :key="cls.id" :value="cls.id" class="text-gray-800">{{ cls.name }}</option>
              </select>
              <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Subject Inputs -->
        <div class="space-y-4">
          <h3 class="text-lg font-medium text-gray-800">Subjects</h3>
          
          <div v-for="(s, i) in subject" :key="i" class="bg-gray-50 rounded-lg p-4 border border-gray-200 transition-all hover:border-indigo-300">
            <div class="flex justify-between items-center mb-3">
              <span class="font-medium text-indigo-600">Subject {{ i + 1 }}</span>
              <button type="button" @click="removesubject(i)" class="text-red-500 hover:text-red-700 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
              </button>
            </div>
            <input v-model="s.name" type="text" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all" placeholder="Enter subject name" required />
          </div>
        </div>

        <!-- Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 pt-4">
          <button type="button" @click="addsubject" class="flex items-center justify-center gap-2 px-6 py-3 bg-white border border-indigo-600 text-indigo-600 rounded-lg hover:bg-indigo-50 transition-colors font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Add More Subject
          </button>
          <button type="submit" class="flex-1 px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all shadow-md hover:shadow-lg font-medium">
            Submit Subjects
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
    </AuthenticatedLayout>
</template>

<style scoped>
.input-field {
    @apply mt-1 block w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:outline-none;
}
.btn-upload {
    @apply mt-4 bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700;
}
.btn-submit {
    @apply w-full mt-4 bg-blue-600 text-white py-3 px-4 rounded hover:bg-blue-700;
}
</style>
