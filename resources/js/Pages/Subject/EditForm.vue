<script setup>
import { ref } from 'vue';
import { usePage, useForm, Head } from '@inertiajs/vue3';
import { ElMessage } from 'element-plus';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const { props } = usePage();
const education = ref(props.education);
const classs = ref(props.classs);

// Use Inertia form to handle the update
const form = useForm({
  education_id: education.value.id,
  classs: classs.value,
});

// Handle classs update
const updateclass = () => {
  // Copy the updated class data to the form
  form.classs = classs.value.map(cls => ({ ...cls }));

  form.put(route('class.bulk-update', education.value.id), {
    preserveScroll: true,
    onSuccess: () => {
      ElMessage.success('Class updated successfully!');
      // Inertia automatically updates props after a successful PUT
      // You can explicitly refresh the form state here if needed
    },
    onError: () => {
      ElMessage.error('Failed to update class.');
    },
  });
};


// Add new classs entry
const addclass = () => {
    classs.value.push({ id: null, name: '' });
};

// Remove classs entry
const removeclass = (index) => {
  if (classs.value.length > 1) {
    classs.value.splice(index, 1);
  } else {
    ElMessage.error('At least one class entry is required.');
  }
};
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Edit Class"/>

    <div class="container mx-auto mt-5">
      <h1 class="text-3xl font-bold text-gray-800 text-center mb-5">Edit class for <span class="bg-green-200"> {{ education.name }} </span></h1>

      <form @submit.prevent="updateclass" class="max-w-4xl mx-auto bg-white p-6 shadow-md rounded-lg border border-gray-200">
        <div v-for="(cls, index) in classs" :key="index" class="mb-4">
          <div class="flex justify-between">
            <h2 class="text-xl font-semibold">Class Name:  {{ cls.name }}</h2>
            <button type="button" @click="removeclass(index)" class="text-red-500">Remove</button>
          </div>

          <label class="block mt-2">
            <input v-model="cls.name" type="text" class="input-field" required />
          </label>
        </div>

        <button type="button" @click="addclass" class="btn-upload">Add More classs</button>
        <button type="submit" class="btn-submit">Update classs</button>
      </form>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
.input-field {
  @apply mt-1 block w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none;
}

.btn-upload {
  @apply mt-4 bg-blue-600 text-white py-2 px-4 rounded-lg font-semibold hover:bg-blue-700;
}

.btn-submit {
  @apply w-full mt-4 bg-green-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-green-700 transition duration-200;
}
</style>
