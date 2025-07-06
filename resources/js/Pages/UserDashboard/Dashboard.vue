<script setup>
import UserDashboardLayout from '@/Layouts/UserDashboardLayout.vue';
import { Head, Link, usePage, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const { props } = usePage();
const institute = ref(props.institute || []);
const savedQuestion = ref(props.savedQuestion || []);

const showModal = ref(false);

const form = useForm({
    name: institute.value?.name || '',
    address: institute.value?.address || '',
    phone: institute.value?.phone || '',
    // Add other fields as needed
});

const openModal = () => {
    form.name = institute.value?.name || '';
    form.address = institute.value?.address || '';
    form.phone = institute.value?.phone || '';
    showModal.value = true;
};

const updateInstitute = () => {
    form.put(route('institute.update'), {
        preserveScroll: true,
        onSuccess: () => {
            showModal.value = false;
            // Refresh the institute data
            institute.value = usePage().props.institute;
        },
    });
};

</script>

<template>
    <Head title="Dashboard" />

    <UserDashboardLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Dashboard
            </h2>
        </template>

        <div class="container p-4">
  <!-- Header Section -->
  <div class="relative  p-8 py-14 border border-green-700 bg-gradient-to-r from-white via-green-50 to-green-100 my-2 text-center rounded-lg">
    <div class="flex items-center gap-4 relative text-center">
      <div class="flex-1">
        <h1 class="flex gap-2 justify-center items-center text-xl md:text-2xl lg:text-3xl font-bold text-green-800">
          <span class="line-clamp-1">{{ institute.name }}</span>
        </h1>
        <div class="text-gray-800 mt-1">
          <span>{{institute.address}}</span>
        </div>
        <div class="mt-2 flex justify-center">
        <button 
            @click="openModal"
            class="hover:bg-green-600 text-sm hover:text-white px-4 py-1.5 rounded-full bg-white border border-green-700 transition duration-200"
        >
            প্রতিষ্ঠানের তথ্য আপডেট করুন
        </button>
      </div>
      </div>
    </div>
  </div>

  

  <!-- Quick Actions Section -->
  <div class="lg:flex flex-wrap gap-4 my-5">
    <!-- Quick Actions Grid -->
    <div>
      <div class="md:w-96">
        <div class="grid grid-cols-2 gap-1">
          <!-- Create Question -->
          <Link :href="route('qstIndex')">
            <div class="relative bg-white h-40 flex flex-col items-center gap-2 justify-center rounded hover:border hover:bg-blue-500 hover:text-white transition-all py-2 px-3 border">
              <span class="absolute text-sm right-2 top-2 bg-green-500 text-white px-2.5 py-0.5 rounded-full">৩য়-১২শ</span>
              <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 448 512" class="text-2xl" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                <path d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32V224H48c-17.7 0-32 14.3-32 32s14.3 32 32 32H192V432c0 17.7 14.3 32 32 32s32-14.3 32-32V288H400c17.7 0 32-14.3 32-32s-14.3-32-32-32H256V80z"></path>
              </svg>
              <div><span>ক্লিকেই প্রশ্ন তৈরী</span></div>
            </div>
          </Link>

           <a href="#">
            <div class="relative bg-white h-40 flex flex-col items-center gap-2 justify-center rounded hover:border hover:bg-blue-500 hover:text-white transition-all py-2 px-3 border">
              <span class="absolute text-sm right-2 top-2 bg-green-500 text-white px-2.5 py-0.5 rounded-full">Comming Soon...</span>
              <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 448 512" class="text-2xl" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                <path d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32V224H48c-17.7 0-32 14.3-32 32s14.3 32 32 32H192V432c0 17.7 14.3 32 32 32s32-14.3 32-32V288H400c17.7 0 32-14.3 32-32s-14.3-32-32-32H256V80z"></path>
              </svg>
              <div><span>অনলাইন পরীক্ষা তৈরী</span></div>
            </div>
          </a>
          
        </div>
      </div>
    </div>

    <!-- Updates Card -->
    <div class="border rounded-lg bg-white flex-1 mt-5 lg:mt-0 overflow-hidden">
      <p class="bg-amber-100 text-center font-semibold text-lg py-2 flex items-center justify-center gap-2 border-b">
        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 576 512" class="text-amber-500" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
          <path d="M576 240c0-23.63-12.95-44.04-32-55.12V32.01C544 23.26 537.02 0 512 0c-7.12 0-14.19 2.38-19.98 7.02l-85.03 68.03C364.28 109.19 310.66 128 256 128H64c-35.35 0-64 28.65-64 64v96c0 35.35 28.65 64 64 64h33.7c-1.39 10.48-2.18 21.14-2.18 32 0 39.77 9.26 77.35 25.56 110.94 5.19 10.69 16.52 17.06 28.4 17.06h74.28c26.05 0 41.69-29.84 25.9-50.56-16.4-21.52-26.15-48.36-26.15-77.44 0-11.11 1.62-21.79 4.41-32H256c54.66 0 108.28 18.81 150.98 52.95l85.03 68.03a32.023 32.023 0 0 0 19.98 7.02c24.92 0 32-22.78 32-32V295.13C563.05 284.04 576 263.63 576 240zm-96 141.42l-33.05-26.44C392.95 311.78 325.12 288 256 288v-96c69.12 0 136.95-23.78 190.95-66.98L480 98.58v282.84z"></path>
        </svg>
        লার্ন এন্ড টিচ আপডেটস
      </p>
      <div class="p-5 space-y-4">
        <p class="text-gray-600 text-base leading-relaxed mb-10">
          সফটওয়্যারের <span class="font-bold">নতুন ফিচার, জরুরি আপডেট এবং নির্দেশনা</span> — যেকোন আপডেট আগে দেওয়া হয় আমাদের গ্রুপে। 
          <br>✅ <span class="font-medium">আপডেট থাকতে গ্রুপে জয়েন হয়ে থাকুন।</span>
        </p>
        <div class="flex justify-center">
          <a rel="noopener noreferrer" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 text-center" href="https://www.facebook.com/messages/t/103497647929617" target="_blank">🔔 চ্যানেলে এখনই যুক্ত হোন</a>
        </div>
      </div>
    </div>

    <!-- Feedback Card -->
    <div class="mt-5 lg:mt-0 flex-1 flex-col border rounded flex items-center">
      <p class="bg-emerald-100 text-lg w-full font-bold text-center flex items-center gap-2 justify-center p-2">
        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" class="text-emerald-600" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
          <path d="M448 0H64C28.7 0 0 28.7 0 64v288c0 35.3 28.7 64 64 64h96v84c0 9.8 11.2 15.5 19.1 9.7L304 416h144c35.3 0 64-28.7 64-64V64c0-35.3-28.7-64-64-64z"></path>
        </svg>
        মতামত জানান !
      </p>
      <div class="flex-1 flex items-center p-2">
        <div>
          <div class="text-center my-4">
            <p class="font-bold text-lg mb-2">আপনার সন্তুষ্টি, আমাদের প্রেরণা!</p>
            যেকোন সমস্যা বা পরামর্শের জন্য আপনার মূল্যবান মতামত প্রকাশ করতে পারেন। আপনার মতামত আমাদের কাছে অত্যান্ত গুরুত্বপূর্ণ।
          </div>
          <div class="flex items-center justify-center">
            <a class="bg-gray-200 hover:bg-gray-100 px-3 py-1 rounded-full" href="/teacher/contact/feedback">মতামত দিন</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bottom Section -->
  <div class="lg:flex gap-2">
    <!-- Updates Section -->
    <div>
      <div class="md:my-0 flex-1 lg:max-w-96 border rounded">
        <div class="py-2 bg-gray-100 text-center flex items-center justify-center gap-2">
          <div class="flex items-center space-x-2">
            <div class="relative flex items-center justify-center">
              <div class="absolute w-6 h-6 bg-red-500 rounded-full" style="opacity: 0.382695; transform: scale(0.741623) translateZ(0px);"></div>
              <div class="relative w-3 h-3 bg-red-500 rounded-full"></div>
            </div>
            <span class="text-red-500 font-bold">LIVE</span>
          </div>
          <span class="font-bold">আপডেট !</span>
        </div>
        <div class="text-center my-2 text-lg text-gray-700 px-2">লার্ন এন্ড টিচের ডেটাবেজে সর্বশেষ নতুন প্রশ্ন যুক্ত হয়েছে</div>
        <div class="p-2 border-y border-dashed border-gray-300">
          <div>
            <span class="flex gap-2 items-center justify-center">
              <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" class="text-sm text-green-600 mt-0.5" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                <path d="M504 256c0 136.967-111.033 248-248 248S8 392.967 8 256 119.033 8 256 8s248 111.033 248 248zM227.314 387.314l184-184c6.248-6.248 6.248-16.379 0-22.627l-22.627-22.627c-6.248-6.249-16.379-6.249-22.628 0L216 308.118l-70.059-70.059c-6.248-6.248-16.379-6.248-22.628 0l-22.627 22.627c-6.248 6.248-6.248 16.379 0 22.627l104 104c6.249 6.249 16.379 6.249 22.628.001z"></path>
              </svg>
              4 minutes ago
            </span>
          </div>
        </div>
        <p class="text-center text-gray-500 italic py-2">প্রতি ৩০ মিনিট পর পর এই তথ্য আপডেট হয়</p>
      </div>
    </div>

    <!-- Recent Questions -->
    <div class="flex-1 mt-5 lg:mt-0">
      <div class="w-full my-4 md:my-0 border rounded">
        <h3 class="flex items-center gap-2 justify-center text-center rounded-t bg-gray-100 p-2">
          <span class="text-lg font-bold">আপনার তৈরী সর্বশেষ প্রশ্ন</span>
        </h3>
        <!-- Question 1 -->
        <Link v-for="(questions, index) in savedQuestion" :key='index' :href="route('qstshow', questions.id )">
          <div class="bg-white hover:border-gray-400 flex gap-2 items-center transition-all border-b hover:bg-gray-50 p-1.5 w-full text-start">
            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 384 512" class="shrink-0 text-2xl text-gray-500" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
              <path d="M224 136V0H24C10.7 0 0 10.7 0 24v464c0 13.3 10.7 24 24 24h336c13.3 0 24-10.7 24-24V160H248c-13.2 0-24-10.8-24-24zm64 236c0 6.6-5.4 12-12 12H108c-6.6 0-12-5.4-12-12v-8c0-6.6 5.4-12 12-12h168c6.6 0 12 5.4 12 12v8zm0-64c0 6.6-5.4 12-12 12H108c-6.6 0-12-5.4-12-12v-8c0-6.6 5.4-12 12-12h168c6.6 0 12 5.4 12 12v8zm0-72v8c0 6.6-5.4 12-12 12H108c-6.6 0-12-5.4-12-12v-8c0-6.6 5.4-12 12-12h168c6.6 0 12 5.4 12 12zm96-114.1v6.1H256V0h6.1c6.4 0 12.5 2.5 17 7l97.9 98c4.5 4.5 7 10.6 7 16.9z"></path>
            </svg>
            <div class="w-full">
              <p class="line-clamp-1">{{ questions.exam_name }}</p>
              <div class="text-xs text-gray-700 mt-0.5 flex justify-between">
                <p class="bg-gray-50 rounded-full px-1">{{ questions.questions[0]?.subject.name }}</p>
                <p class="bg-green-50 text-green-700 rounded-full px-1.5 py-0.5">{{ questions.questions[0]?.academic_class.name }}</p>
              </div>
            </div>
          </div>
        </Link>


        <!-- View All Questions -->
        <a href="/teacher/my-questions">
          <p class="rounded-b-lg text-center flex items-center justify-between p-2 bg-gray-50">
            <span>সকল প্রশ্ন দেখুন</span>
            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
              <path d="M502.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-128-128c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L402.7 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l370.7 0-73.4 73.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l128-128z"></path>
            </svg>
          </p>
        </a>
      </div>
    </div>

    <!-- Live Chat Section -->
    <div class="mt-5 lg:mt-0 flex-1 flex-col border rounded flex items-center">
      <p class="text-lg w-full font-bold text-center flex items-center gap-2 justify-center bg-gray-100 p-2">লাইভ চ্যাট</p>
      <div class="flex-1 flex items-center p-2">
        <div>
          <div class="text-center my-4">
            <p class="font-bold text-lg mb-2">আপনার কথা আমাদের কাছে সবচেয়ে মূল্যবান!</p>
            যেকোনো প্রশ্ন, পরামর্শ বা সমস্যার দ্রুত সমাধানে আমরা সবসময় আছি আপনার পাশে।
          </div>
          <div class="flex items-center justify-center">
            <div class="flex gap-2 flex-col items-center justify-center mt-5">
              <!-- Phone -->
              <a href="tel:01763264270">
                <div class="flex gap-2 items-center justify-center bg-gray-200 hover:bg-gray-100 w-60 p-2 rounded-full">
                  <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                    <path d="M280 0C408.1 0 512 103.9 512 232c0 13.3-10.7 24-24 24s-24-10.7-24-24c0-101.6-82.4-184-184-184c-13.3 0-24-10.7-24-24s10.7-24 24-24zm8 192a32 32 0 1 1 0 64 32 32 0 1 1 0-64zm-32-72c0-13.3 10.7-24 24-24c75.1 0 136 60.9 136 136c0 13.3-10.7 24-24 24s-24-10.7-24-24c0-48.6-39.4-88-88-88c-13.3 0-24-10.7-24-24zM117.5 1.4c19.4-5.3 39.7 4.6 47.4 23.2l40 96c6.8 16.3 2.1 35.2-11.6 46.3L144 207.3c33.3 70.4 90.3 127.4 160.7 160.7L345 318.7c11.2-13.7 30-18.4 46.3-11.6l96 40c18.6 7.7 28.5 28 23.2 47.4l-24 88C481.8 499.9 466 512 448 512C200.6 512 0 311.4 0 64C0 46 12.1 30.2 29.5 25.4l88-24z"></path>
                  </svg>
                  <span>+880 17632 64270</span>
                </div>
              </a>

              <!-- WhatsApp -->
              <a href="https://wa.me/8801763264270" target="_blank" rel="noopener noreferrer">
                <div class="flex gap-2 items-center justify-center bg-[#25d366] hover:bg-green-400 text-white w-60 p-2 rounded-full">
                  <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 448 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                    <path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"></path>
                  </svg>
                  <span>Open WhatsApp</span>
                </div>
              </a>

              <!-- Messenger -->
              <a href="https://www.facebook.com/LearnAndTeachBangla" target="_blank" rel="noopener noreferrer">
                <div class="flex gap-2 items-center justify-center bg-blue-600 hover:bg-blue-500 text-white w-60 p-2 rounded-full">
                  <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                    <path d="M256.55 8C116.52 8 8 110.34 8 248.57c0 72.3 29.71 134.78 78.07 177.94 8.35 7.51 6.63 11.86 8.05 58.23A19.92 19.92 0 0 0 122 502.31c52.91-23.3 53.59-25.14 62.56-22.7C337.85 521.8 504 423.7 504 248.57 504 110.34 396.59 8 256.55 8zm149.24 185.13l-73 115.57a37.37 37.37 0 0 1-53.91 9.93l-58.08-43.47a15 15 0 0 0-18 0l-78.37 59.44c-10.46 7.93-24.16-4.6-17.11-15.67l73-115.57a37.36 37.36 0 0 1 53.91-9.93l58.06 43.46a15 15 0 0 0 18 0l78.41-59.38c10.44-7.98 24.14 4.54 17.09 15.62z"></path>
                  </svg>
                  <span>Open Messenger</span>
                </div>
              </a>

              <!-- Divider -->
              <div class="flex items-center justify-center mt-3 w-full">
                <div class="border-t border-gray-300 flex-grow mr-2"></div>
                <span class="text-gray-500">OR</span>
                <div class="border-t border-gray-300 flex-grow ml-2"></div>
              </div>

              <!-- Email -->
              <a href="mailto:irobiul56@gmail.com">
                <div class="flex gap-2 items-center text-gray-600 hover:text-gray-500">
                  <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                    <path d="M512 464c0 26.51-21.49 48-48 48H48c-26.51 0-48-21.49-48-48V200.724a48 48 0 0 1 18.387-37.776c24.913-19.529 45.501-35.365 164.2-121.511C199.412 29.17 232.797-.347 256 .003c23.198-.354 56.596 29.172 73.413 41.433 118.687 86.137 139.303 101.995 164.2 121.512A48 48 0 0 1 512 200.724V464zm-65.666-196.605c-2.563-3.728-7.7-4.595-11.339-1.907-22.845 16.873-55.462 40.705-105.582 77.079-16.825 12.266-50.21 41.781-73.413 41.43-23.211.344-56.559-29.143-73.413-41.43-50.114-36.37-82.734-60.204-105.582-77.079-3.639-2.688-8.776-1.821-11.339 1.907l-9.072 13.196a7.998 7.998 0 0 0 1.839 10.967c22.887 16.899 55.454 40.69 105.303 76.868 20.274 14.781 56.524 47.813 92.264 47.573 35.724.242 71.961-32.771 92.263-47.573 49.85-36.179 82.418-59.97 105.303-76.868a7.998 7.998 0 0 0 1.839-10.967l-9.071-13.196z"></path>
                  </svg>
                  <span>Mail: irobiul56@gmail.com</span>
                </div>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

 <!-- Modal -->
            <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
                <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Update Institute Information</h3>
                        
                        <form @submit.prevent="updateInstitute">
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                                    Institute Name
                                </label>
                                <input
                                    id="name"
                                    v-model="form.name"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    type="text"
                                />
                                <p v-if="form.errors.name" class="text-red-500 text-xs italic">{{ form.errors.name }}</p>
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="address">
                                    Address
                                </label>
                                <input
                                    id="address"
                                    v-model="form.address"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    type="text"
                                />
                                <p v-if="form.errors.address" class="text-red-500 text-xs italic">{{ form.errors.address }}</p>
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="phone">
                                    Phone
                                </label>
                                <input
                                    id="phone"
                                    v-model="form.phone"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    type="text"
                                />
                                <p v-if="form.errors.phone" class="text-red-500 text-xs italic">{{ form.errors.phone }}</p>
                            </div>

                            <!-- Add other fields as needed -->

                            <div class="flex justify-end space-x-3 mt-6">
                                <button
                                    type="button"
                                    @click="showModal = false"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                                    :disabled="form.processing"
                                >
                                    <span v-if="form.processing">Updating...</span>
                                    <span v-else>Update</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

    </UserDashboardLayout>
</template>
