<template>
<AppLayout title="About Us">
  <div class="bg-white py-16 px-6 lg:px-20">
    <!-- Header Section -->
    <div class="max-w-4xl mx-auto text-center">
      <h1 class="text-4xl font-bold text-gray-900 mb-6">আমাদের সম্পর্কে</h1>
      <p class="text-lg text-gray-600 leading-relaxed">
        শিক্ষার দ্রুত পরিবর্তনশীল এই যুগে, <strong>Learn and Teach</strong> প্রযুক্তি এবং শিক্ষার সমন্বয়ে এক নতুন দিগন্ত উন্মোচন করেছে। আমরা শিক্ষক এবং শিক্ষার্থীদের একাডেমিক যাত্রাকে সহজ করতে প্রতিশ্রুতিবদ্ধ।
      </p>
    </div>

    <!-- Main Content Grid -->
    <div class="max-w-6xl mx-auto mt-16 grid grid-cols-1 md:grid-cols-2 gap-12">
      
      <!-- Mission -->
      <div class="bg-gray-50 p-8 rounded-2xl">
        <h2 class="text-2xl font-semibold text-blue-600 mb-4">আমাদের লক্ষ্য</h2>
        <p class="text-gray-700 leading-relaxed">
          আমাদের মূল লক্ষ্য হলো শিক্ষকদের এমন সব স্মার্ট ও স্বয়ংক্রিয় টুলস প্রদান করা, যা তাদের মূল্যবান সময় বাঁচাবে। আমরা বিশ্বাস করি, প্রশাসনিক কাজের চাপ কমিয়ে শিক্ষকরা যাতে শিক্ষার্থীদের অনুপ্রাণিত করায় বেশি মনোযোগী হতে পারেন, সেটিই আমাদের সাফল্য।
        </p>
      </div>

      <!-- Vision -->
      <div class="bg-gray-50 p-8 rounded-2xl">
        <h2 class="text-2xl font-semibold text-blue-600 mb-4">আমাদের ভিশন</h2>
        <p class="text-gray-700 leading-relaxed">
          আমরা এমন একটি ভবিষ্যৎ কল্পনা করি যেখানে প্রযুক্তি শিক্ষাকে আরও অন্তর্ভুক্তিমূলক এবং কার্যকর করে তুলবে। উদ্ভাবনী ও সহজ ব্যবহারযোগ্য ডিজিটাল সলিউশন প্রদানের মাধ্যমে আমরা প্রতিটি শিক্ষা প্রতিষ্ঠানের বিশ্বস্ত সঙ্গী হতে চাই।
        </p>
      </div>
    </div>

    <!-- Highlights Section -->
    <div class="max-w-4xl mx-auto mt-16">
      <h3 class="text-xl font-bold text-gray-900 mb-6 text-center">কেন Learn and Teach বেছে নেবেন?</h3>
      <ul class="space-y-4">
        <li v-for="item in features" :key="item" class="flex items-start">
          <span class="text-blue-500 mr-3">✔</span>
          <span class="text-gray-700">{{ item }}</span>
        </li>
      </ul>
    </div>
  </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const features = ref([
  "নির্ভুলতা ও শৃঙ্খলা: জটিল পাঠ্য উপকরণকে সহজ ডিজিটাল ফরম্যাটে রূপান্তর।",
  "সময়ের সাশ্রয়: মুহূর্তের মধ্যে প্রশ্নপত্র তৈরি ও পরিচালনার সক্ষমতা।",
  "সবার জন্য সহজলভ্যতা: স্কুল, কলেজ ও মাদ্রাসার জন্য একটি বহুমুখী প্ল্যাটফর্ম।"
]);
</script>

<style scoped>
/* প্রয়োজন অনুযায়ী এখানে কাস্টম CSS যোগ করতে পারেন */
</style>