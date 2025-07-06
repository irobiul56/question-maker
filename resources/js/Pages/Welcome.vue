<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
const imageUrl = ref('/storage/img/logo.png');

</script>

<template>
    <Head title="Welcome" />
    <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50 hind-siliguri-light">
       <section class="relative py-16 bg-gray-50 overflow-hidden">
  <!-- Gradient Background Elements -->
  <div aria-hidden="true" class="absolute inset-0 grid grid-cols-2 -space-x-52 opacity-40 dark:opacity-20">
    <div class="blur-[106px] h-56 bg-gradient-to-br from-blue-600 to-purple-400 dark:from-blue-700"></div>
    <div class="blur-[106px] h-32 bg-gradient-to-r from-blue-600 to-sky-300 dark:to-indigo-600"></div>
  </div>

  <!-- Main Content -->
  <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="text-center mb-16">
      <div class="gap-2 inline-flex items-center justify-center px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 mb-4">
        <img  :src="imageUrl" class="h-5 w-5 md:h-5 md:w-5 object-contain" alt="">
        <span class="text-sm font-medium">learnandteach.app</span>
      </div>
      
      <div>
      <h2 class="text-4xl font-bold text-gray-900 mb-4 relative inline-block">
        <span class="relative">
          <svg viewBox="0 0 52 24" fill="currentColor" class="absolute top-0 left-0 z-0 hidden w-32 -mt-8 -ml-20 text-gray-400 lg:w-32 lg:-ml-28 lg:-mt-10 sm:block">
            <defs>
              <pattern id="dc223fcc-6d72-4ebc-b4ef-abe121034d6e" x="0" y="0" width=".135" height=".30">
                <circle cx="1" cy="1" r=".7"></circle>
              </pattern>
            </defs>
            <rect fill="url(#dc223fcc-6d72-4ebc-b4ef-abe121034d6e)" width="52" height="24"></rect>
          </svg>
          <span class="relative">ফ্রিতে ক্লিকেই</span>
        </span> প্রশ্ন রেডি
      </h2>
      </div>
      
      <p class="text-lg text-gray-600 max-w-2xl mx-auto">
        স্কুল, কলেজ ও মাদ্ররাসার শিশু থেকে দ্বাদশ শ্রেণির যেকোন প্রশ্ন তৈরি করুন ক্লিকেই | শিক্ষক ও শিক্ষার্থীদের জন্য অসাধারণ একটি সফটওয়্যার।
      </p>
    </div>

    <!-- Call to Action Buttons -->
    <div class="flex flex-col space-y-4 sm:flex-row sm:space-y-0 sm:space-x-6 justify-center">
        <Link :href="route('login')">
      <button class="relative px-6 py-3 bg-blue-600 text-white font-semibold rounded-full transition-all duration-300 hover:scale-105 active:scale-95">
        <span class="relative">প্রশ্ন তৈরি করুন</span>
      </button>
      </Link>

      <button class="relative px-6 py-3 bg-white text-blue-600 font-semibold border border-gray-200 rounded-full transition-all duration-300 hover:scale-105 active:scale-95">
        <span class="relative">অনলাইন পরীক্ষা</span>
      </button>
    </div>

    <!-- Statistics Cards -->
    <div class="mt-20 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 max-w-5xl mx-auto">
      <!-- Card 1 -->
      <div class="bg-white p-8 rounded-lg shadow-md text-center">
        <div class="w-12 h-12 mx-auto mb-4 rounded-full bg-indigo-50 flex items-center justify-center">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-indigo-600">
            <circle cx="12" cy="12" r="10"></circle>
            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
            <path d="M12 17h.01"></path>
          </svg>
        </div>
        <p class="font-bold text-gray-800">১০০০০+ শিক্ষা প্রতিষ্ঠান যুক্ত</p>
      </div>
      
      <!-- Card 2 -->
      <div class="bg-white p-8 rounded-lg shadow-md text-center">
        <div class="w-12 h-12 mx-auto mb-4 rounded-full bg-indigo-50 flex items-center justify-center">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-indigo-600">
            <path d="M12 7v14"></path>
            <path d="M16 12h2"></path>
            <path d="M16 8h2"></path>
            <path d="M3 18a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h5a4 4 0 0 1 4 4 4 4 0 0 1 4-4h5a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1h-6a3 3 0 0 0-3 3 3 3 0 0 0-3-3z"></path>
            <path d="M6 12h2"></path>
            <path d="M6 8h2"></path>
          </svg>
        </div>
        <p class="font-bold text-gray-800">২১৩+ বিষয়</p>
      </div>
      
      <!-- Card 3 -->
      <div class="bg-white p-8 rounded-lg shadow-md text-center">
        <div class="w-12 h-12 mx-auto mb-4 rounded-full bg-indigo-50 flex items-center justify-center">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-indigo-600">
            <path d="M12 21V7"></path>
            <path d="m16 12 2 2 4-4"></path>
            <path d="M22 6V4a1 1 0 0 0-1-1h-5a4 4 0 0 0-4 4 4 4 0 0 0-4-4H3a1 1 0 0 0-1 1v13a1 1 0 0 0 1 1h6a3 3 0 0 1 3 3 3 3 0 0 1 3-3h6a1 1 0 0 0 1-1v-1.3"></path>
          </svg>
        </div>
        <p class="font-bold text-gray-800">৭০০+ প্রশ্ন তৈরী করছে প্রতিদিন </p>
      </div>
      
      <!-- Card 4 -->
      <div class="bg-white p-8 rounded-lg shadow-md text-center">
        <div class="w-12 h-12 mx-auto mb-4 rounded-full bg-indigo-50 flex items-center justify-center">
          <svg stroke="currentColor" fill="currentColor" stroke-width="2" viewBox="0 0 256 256" class="text-indigo-600" height="24" width="24" xmlns="http://www.w3.org/2000/svg">
            <path d="M226.53,56.41l-96-32a8,8,0,0,0-5.06,0l-96,32A8,8,0,0,0,24,64v80a8,8,0,0,0,16,0V75.1L73.59,86.29a64,64,0,0,0,20.65,88.05c-18,7.06-33.56,19.83-44.94,37.29a8,8,0,1,0,13.4,8.74C77.77,197.25,101.57,184,128,184s50.23,13.25,65.3,36.37a8,8,0,0,0,13.4-8.74c-11.38-17.46-27-30.23-44.94-37.29a64,64,0,0,0,20.65-88l44.12-14.7a8,8,0,0,0,0-15.18ZM176,120A48,48,0,1,1,89.35,91.55l36.12,12a8,8,0,0,0,5.06,0l36.12-12A47.89,47.89,0,0,1,176,120ZM128,87.57,57.3,64,128,40.43,198.7,64Z"></path>
          </svg>
        </div>
        <p class="font-bold text-gray-800">৩০k+ শিক্ষক যুক্ত</p>
      </div>
    </div>
  </div>
</section>
    </div>
</template>
<style scoped>
 @import url('https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&display=swap');
        .hind-siliguri-light {
        font-family: "Hind Siliguri", sans-serif;
        font-weight: 300;
        font-style: normal;
        }

</style>
