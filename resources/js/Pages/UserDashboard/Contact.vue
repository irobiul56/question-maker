<script setup>
import UserDashboardLayout from '@/Layouts/UserDashboardLayout.vue';
import { Head, Link, usePage, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
</script>

<template>
    <Head title="Contact" />

    <UserDashboardLayout>
        <div class="dashboard w-full min-h-[calc(100vh-4rem)] bg-gradient-to-br from-indigo-50 to-blue-50 py-8 px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-indigo-600 to-blue-600 p-4 sm:p-6 text-center">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-bold text-white">Need Help? We're Here For You!</h2>
                    <p class="mt-1 sm:mt-2 text-sm sm:text-base text-indigo-100">Get in touch through your preferred channel</p>
                </div>
                
                <!-- Contact Options -->
                <div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
                    <!-- Phone -->
                    <a href="tel:01763264270" class="block group">
                        <div class="flex items-center gap-3 sm:gap-4 p-3 sm:p-4 bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 group-hover:border-indigo-300">
                            <div class="flex-shrink-0 bg-indigo-100 p-2 sm:p-3 rounded-full text-indigo-600 group-hover:bg-indigo-200 transition-colors">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="currentColor" viewBox="0 0 512 512">
                                    <path d="M280 0C408.1 0 512 103.9 512 232c0 13.3-10.7 24-24 24s-24-10.7-24-24c0-101.6-82.4-184-184-184c-13.3 0-24-10.7-24-24s10.7-24 24-24zm8 192a32 32 0 1 1 0 64 32 32 0 1 1 0-64zm-32-72c0-13.3 10.7-24 24-24c75.1 0 136 60.9 136 136c0 13.3-10.7 24-24 24s-24-10.7-24-24c0-48.6-39.4-88-88-88c-13.3 0-24-10.7-24-24zM117.5 1.4c19.4-5.3 39.7 4.6 47.4 23.2l40 96c6.8 16.3 2.1 35.2-11.6 46.3L144 207.3c33.3 70.4 90.3 127.4 160.7 160.7L345 318.7c11.2-13.7 30-18.4 46.3-11.6l96 40c18.6 7.7 28.5 28 23.2 47.4l-24 88C481.8 499.9 466 512 448 512C200.6 512 0 311.4 0 64C0 46 12.1 30.2 29.5 25.4l88-24z"></path>
                                </svg>
                            </div>
                            <div class="flex-grow min-w-0">
                                <h3 class="text-base sm:text-lg font-semibold text-gray-800 truncate">Call Us</h3>
                                <p class="text-sm sm:text-base text-indigo-600 font-medium truncate">+8801763264270</p>
                            </div>
                            <div class="text-gray-400 group-hover:text-indigo-500 transition-colors">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </div>
                        </div>
                    </a>
                    
                    <!-- WhatsApp -->
                    <a href="https://wa.me/8801763264270" target="_blank" rel="noopener noreferrer" class="block group">
                        <div class="flex items-center gap-3 sm:gap-4 p-3 sm:p-4 bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 group-hover:border-green-300">
                            <div class="flex-shrink-0 bg-green-100 p-2 sm:p-3 rounded-full text-green-600 group-hover:bg-green-200 transition-colors">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="currentColor" viewBox="0 0 448 512">
                                    <path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"></path>
                                </svg>
                            </div>
                            <div class="flex-grow min-w-0">
                                <h3 class="text-base sm:text-lg font-semibold text-gray-800 truncate">WhatsApp</h3>
                                <p class="text-sm sm:text-base text-green-600 font-medium truncate">Chat with us instantly</p>
                            </div>
                            <div class="text-gray-400 group-hover:text-green-500 transition-colors">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </div>
                        </div>
                    </a>
                    
                    <!-- Messenger -->
                    <a href="https://www.facebook.com/LearnAndTeachBangla" target="_blank" rel="noopener noreferrer" class="block group">
                        <div class="flex items-center gap-3 sm:gap-4 p-3 sm:p-4 bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 group-hover:border-blue-300">
                            <div class="flex-shrink-0 bg-blue-100 p-2 sm:p-3 rounded-full text-blue-600 group-hover:bg-blue-200 transition-colors">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="currentColor" viewBox="0 0 512 512">
                                    <path d="M256.55 8C116.52 8 8 110.34 8 248.57c0 72.3 29.71 134.78 78.07 177.94 8.35 7.51 6.63 11.86 8.05 58.23A19.92 19.92 0 0 0 122 502.31c52.91-23.3 53.59-25.14 62.56-22.7C337.85 521.8 504 423.7 504 248.57 504 110.34 396.59 8 256.55 8zm149.24 185.13l-73 115.57a37.37 37.37 0 0 1-53.91 9.93l-58.08-43.47a15 15 0 0 0-18 0l-78.37 59.44c-10.46 7.93-24.16-4.6-17.11-15.67l73-115.57a37.36 37.36 0 0 1 53.91-9.93l58.06 43.46a15 15 0 0 0 18 0l78.41-59.38c10.44-7.98 24.14 4.54 17.09 15.62z"></path>
                                </svg>
                            </div>
                            <div class="flex-grow min-w-0">
                                <h3 class="text-base sm:text-lg font-semibold text-gray-800 truncate">Messenger</h3>
                                <p class="text-sm sm:text-base text-blue-600 font-medium truncate">Message us on Facebook</p>
                            </div>
                            <div class="text-gray-400 group-hover:text-blue-500 transition-colors">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </div>
                        </div>
                    </a>
                    
                    <!-- Divider -->
                    <div class="relative flex items-center py-2 sm:py-4">
                        <div class="flex-grow border-t border-gray-300"></div>
                        <span class="flex-shrink mx-2 sm:mx-4 text-xs sm:text-sm text-gray-500 font-medium">OR</span>
                        <div class="flex-grow border-t border-gray-300"></div>
                    </div>
                    
                    <!-- Email -->
                    <a href="mailto:irobiul56@gmail.com" class="block group">
                        <div class="flex items-center gap-3 sm:gap-4 p-3 sm:p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-300">
                            <div class="flex-shrink-0 bg-gray-200 p-2 sm:p-3 rounded-full text-gray-600 group-hover:bg-gray-300 transition-colors">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="currentColor" viewBox="0 0 512 512">
                                    <path d="M512 464c0 26.51-21.49 48-48 48H48c-26.51 0-48-21.49-48-48V200.724a48 48 0 0 1 18.387-37.776c24.913-19.529 45.501-35.365 164.2-121.511C199.412 29.17 232.797-.347 256 .003c23.198-.354 56.596 29.172 73.413 41.433 118.687 86.137 139.303 101.995 164.2 121.512A48 48 0 0 1 512 200.724V464zm-65.666-196.605c-2.563-3.728-7.7-4.595-11.339-1.907-22.845 16.873-55.462 40.705-105.582 77.079-16.825 12.266-50.21 41.781-73.413 41.43-23.211.344-56.559-29.143-73.413-41.43-50.114-36.37-82.734-60.204-105.582-77.079-3.639-2.688-8.776-1.821-11.339 1.907l-9.072 13.196a7.998 7.998 0 0 0 1.839 10.967c22.887 16.899 55.454 40.69 105.303 76.868 20.274 14.781 56.524 47.813 92.264 47.573 35.724.242 71.961-32.771 92.263-47.573 49.85-36.179 82.418-59.97 105.303-76.868a7.998 7.998 0 0 0 1.839-10.967l-9.071-13.196z"></path>
                                </svg>
                            </div>
                            <div class="flex-grow min-w-0">
                                <h3 class="text-base sm:text-lg font-semibold text-gray-800 truncate">Email Us</h3>
                                <p class="text-sm sm:text-base text-gray-600 font-medium truncate">irobiul56@gmail.com</p>
                            </div>
                            <div class="text-gray-400 group-hover:text-gray-600 transition-colors">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </UserDashboardLayout>
</template>