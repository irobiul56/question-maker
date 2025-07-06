<script setup>
import UserDashboardLayout from '@/Layouts/UserDashboardLayout.vue';
import { Head, Link, usePage, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
</script>

<template>
    <Head title="Opinion" />

    <UserDashboardLayout>
            <div class="w-full bg-gradient-to-br from-blue-50 to-indigo-50 py-8 px-4 sm:px-6">
  <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden">
    <!-- Form Header with Gradient -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-6 text-center">
      <h2 class="text-2xl sm:text-3xl font-bold text-white">মতামত জানান!</h2>
      <p class="mt-2 text-blue-100">আপনার মূল্যবান মতামত আমাদের সেবা উন্নত করতে সাহায্য করে</p>
    </div>

    <!-- Form Content -->
    <div class="p-6 sm:p-8 space-y-6">
      <form class="space-y-6">
        <!-- Category Select -->
        <div class="space-y-2">
          <label for="category" class="block text-sm font-medium text-gray-700">ক্যাটাগরি <span class="text-red-500">*</span></label>
          <div class="relative">
            <select id="category" name="category" class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white pr-10">
              <option value="">ক্যাটাগরি নির্বাচন করুন</option>
              <option value="Automatic Question Maker">প্রশ্ন তৈরী সফটওয়্যার</option>
              <option value="Others">অন্যান্য</option>
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
              <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
              </svg>
            </div>
          </div>
        </div>

        <!-- Title Input -->
        <div class="space-y-2">
          <label for="title" class="block text-sm font-medium text-gray-700">মতামতের বিষয় <span class="text-red-500">*</span></label>
          <input id="title" type="text" name="title" class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="মতামতের সংক্ষিপ্ত শিরোনাম">
        </div>

        <!-- Description Textarea -->
        <div class="space-y-2">
          <label for="description" class="block text-sm font-medium text-gray-700">বিস্তারিত লিখুন <span class="text-red-500">*</span></label>
          <textarea id="description" name="description" rows="4" class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="আপনার মতামত/পরামর্শ বিস্তারিত লিখুন"></textarea>
        </div>


        <!-- Submit Button -->
        <button type="submit" class="w-full py-3 px-4 bg-gradient-to-r from-blue-600 to-indigo-700 text-white font-medium rounded-lg shadow-md hover:from-blue-700 hover:to-indigo-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-300">
          সাবমিট করুন
        </button>
      </form>
    </div>
  </div>
</div>
    </UserDashboardLayout>
</template>