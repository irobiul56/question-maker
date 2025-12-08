<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
const imageUrl = ref('/storage/img/logo.png');
const settingUrl = ref('/storage/img/setting.png'); 

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
            <path d="M3 18a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h5a4 4 0 0 1 4 4 4 4 0 0 1 4-4h5a1 1 0 0 1 1 1v13a1 1 0 0 1 1 1h-6a3 3 0 0 0-3 3 3 3 0 0 0-3-3z"></path>
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

<!-- Premium Features Section - White Background Version -->
<section class="relative py-20 bg-white overflow-hidden">
  <!-- Animated Background Elements -->
  <div class="absolute inset-0 overflow-hidden">
    <div class="absolute top-1/4 -left-20 w-96 h-96 bg-indigo-100 rounded-full filter blur-3xl opacity-70 animate-float"></div>
    <div class="absolute bottom-1/3 -right-20 w-80 h-80 bg-blue-100 rounded-full filter blur-3xl opacity-70 animate-float-delay"></div>
    <div class="absolute top-1/2 left-1/2 w-64 h-64 bg-purple-100 rounded-full filter blur-3xl opacity-50 animate-pulse"></div>
  </div>

  <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Section Header with Colorful Accents -->
    <div class="text-center mb-20">
      <span class="inline-block px-6 py-2 bg-gradient-to-r from-blue-100 to-indigo-100 text-indigo-800 rounded-full text-sm font-medium mb-6 border border-indigo-200 shadow-sm">
        শিক্ষকদের ডিজিটাল অস্ত্র
      </span>
      <h2 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-6">
        <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600 animate-text-shine">এক ক্লিকেই</span> তৈরি করুন প্রশ্নপত্র
      </h2>
      <p class="text-lg text-gray-600 max-w-3xl mx-auto font-medium">
        সকল শ্রেণির সকল বিষয়ের <span class="text-indigo-600 font-bold">লাখ লাখ প্রশ্নের ডাটাবেজ</span>। টাইপিং বা প্রুফ রিডিংয়ের ঝামেলা নেই!
      </p>
    </div>

    <div class="flex flex-col lg:flex-row gap-12 items-center">
      <!-- Features List -->
      <div class="lg:w-1/2 space-y-8">
        <!-- Feature 1 - Colorful Card -->
        <div class="feature-card group relative bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl border border-gray-100 transition-all duration-300 hover:-translate-y-2">
          <div class="absolute -top-4 -left-4 w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center text-3xl font-bold text-white shadow-md">
            ১
          </div>
          <div class="pl-12">
            <h3 class="text-2xl font-bold text-gray-800 mb-2"> ৩০k+ শিক্ষক</h3>
            <p class="text-gray-600">নিয়মিত ব্যবহার করছেন আমাদের প্ল্যাটফর্ম</p>
          </div>
          <div class="absolute bottom-4 right-4 w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center group-hover:bg-blue-200 transition-colors duration-300">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
            </svg>
          </div>
        </div>

        <!-- Feature 2 - Colorful Card -->
        <div class="feature-card group relative bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl border border-gray-100 transition-all duration-300 hover:-translate-y-2">
          <div class="absolute -top-4 -left-4 w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center text-3xl font-bold text-white shadow-md">
            ২
          </div>
          <div class="pl-12">
            <h3 class="text-2xl font-bold text-gray-800 mb-2">এক ক্লিকে তৈরি</h3>
            <p class="text-gray-600">প্রশ্নপত্র, ওয়ার্কশীট, সাজেশন এবং অনলাইন পরীক্ষা</p>
          </div>
          <div class="absolute bottom-4 right-4 w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center group-hover:bg-purple-200 transition-colors duration-300">
            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
            </svg>
          </div>
        </div>

        <!-- Feature 3 - Colorful Card -->
        <div class="feature-card group relative bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl border border-gray-100 transition-all duration-300 hover:-translate-y-2">
          <div class="absolute -top-4 -left-4 w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center text-3xl font-bold text-white shadow-md">
            ৩
          </div>
          <div class="pl-12">
            <h3 class="text-2xl font-bold text-gray-800 mb-2">১০ লক্ষাধিক প্রশ্ন</h3>
            <p class="text-gray-600">নির্ভুল ও ইউনিক প্রশ্নের বিশাল ডাটাবেজ</p>
          </div>
          <div class="absolute bottom-4 right-4 w-10 h-10 bg-green-100 rounded-full flex items-center justify-center group-hover:bg-green-200 transition-colors duration-300">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
            </svg>
          </div>
        </div>

        <!-- Feature 4 - Colorful Card -->
        <div class="feature-card group relative bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl border border-gray-100 transition-all duration-300 hover:-translate-y-2">
          <div class="absolute -top-4 -left-4 w-16 h-16 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-2xl flex items-center justify-center text-3xl font-bold text-white shadow-md">
            ৪
          </div>
          <div class="pl-12">
            <h3 class="text-2xl font-bold text-gray-800 mb-2">ঝামেলামুক্ত</h3>
            <p class="text-gray-600">টাইপিং ও প্রুফ রিডিংয়ের কোনো ঝামেলা নেই</p>
          </div>
          <div class="absolute bottom-4 right-4 w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center group-hover:bg-yellow-200 transition-colors duration-300">
            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
            </svg>
          </div>
        </div>
      </div>

      <!-- Image/Video Section -->
      <div class="lg:w-1/2 relative z-10">
        <div class="relative rounded-3xl overflow-hidden shadow-xl border border-blue-600 transform hover:-translate-y-2 transition-transform duration-500">
          <div class="absolute inset-0 bg-gradient-to-br from-blue-100/50 to-indigo-100/50 rounded-3xl"></div>
          <img :src="settingUrl" alt="Question Creation Interface" class="w-full h-auto rounded-3xl transform hover:scale-102 transition-transform duration-700">
          <div class="absolute -bottom-4 -right-4 w-20 h-20 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition-transform duration-300">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
        </div>
      </div>
    </div>

<!-- Call to Action Buttons -->
    <div class="flex flex-col mt-10 space-y-4 sm:flex-row sm:space-y-0 sm:space-x-6 justify-center">
      <Link :href="route('login')">
      <button class="relative px-6 py-3 bg-blue-600 text-white font-semibold rounded-full transition-all duration-300 hover:scale-105 active:scale-95">
        <span class="relative">প্রশ্ন তৈরি করা শুরু করুন</span>
      </button>
      </Link>
    </div>

    <!-- Video Demo Section - Increased Height -->
    <div class="mt-24 max-w-4xl mx-auto relative">
      <div class="relative bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-200">
        <!-- Increased height video container -->
        <div class="aspect-w-16 aspect-h-9 h-[500px]"> <!-- Increased from default height -->
          <iframe class="w-full h-full" src="https://www.youtube-nocookie.com/embed/xDzQTYPSVXU?si=CVZPW8qj5h-5xRtT" title="YouTube video player" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"></iframe>
        </div>
        <div class="p-6 bg-gradient-to-r from-blue-50 to-indigo-50">
          <h3 class="text-2xl font-bold text-gray-800 mb-2 flex items-center">
            <svg class="w-6 h-6 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            কিভাবে ব্যবহার করবেন?
          </h3>
          <p class="text-gray-600">১ মিনিটের ভিডিও টিউটোরিয়ালে দেখে নিন কিভাবে সহজেই প্রশ্ন তৈরি করবেন</p>
        </div>
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

/* Animation Keyframes */
@keyframes float {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-15px); }
}
@keyframes float-delay {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(10px); }
}
@keyframes pulse {
  0%, 100% { opacity: 0.5; transform: scale(1); }
  50% { opacity: 0.7; transform: scale(1.05); }
}
@keyframes textShine {
  0% { background-position: 0% 50%; }
  100% { background-position: 100% 50%; }
}

/* Custom Animations */
.animate-float { animation: float 8s ease-in-out infinite; }
.animate-float-delay { animation: float-delay 8s ease-in-out infinite 2s; }
.animate-pulse { animation: pulse 6s ease-in-out infinite; }
.animate-text-shine { 
  background-size: 200% auto;
  animation: textShine 3s linear infinite; 
}

/* Custom Effects */
.feature-card:hover {
  border-color: rgba(99, 102, 241, 0.5) !important;
}
.hover\:scale-102:hover {
  transform: scale(1.02);
}

/* Video Container Styles */
.aspect-w-16 {
  position: relative;
  padding-bottom: 0; /* Override default aspect ratio */
}
.aspect-h-9 {
  height: 500px; /* Fixed height */
}
.aspect-w-16 iframe {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}
</style>