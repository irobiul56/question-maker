<template>
  <footer class="bg-gray-800 text-white pt-16 pb-8">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
      
      <!-- Newsletter Subscription -->
      <div class="bg-gray-700 p-8 rounded-lg mb-12 flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
        <h3 class="text-2xl font-bold text-white text-center md:text-left">Subscribe to receive regular newsletters.</h3>
        <form @submit.prevent="subscribeNewsletter" class="flex space-x-2 w-full md:w-auto">
          <input 
            type="email" 
            v-model="email" 
            placeholder="Email Address" 
            class="p-3 rounded-lg w-2/3 md:w-64 text-gray-900 focus:outline-none focus:ring-2 focus:ring-green-500" 
            required
          >
          <button 
            type="submit"
            class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition duration-300 w-1/3 md:w-auto"
            :disabled="subscribing"
          >
            <span v-if="!subscribing">Subscribe</span>
            <span v-else class="flex items-center">
              <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Subscribing...
            </span>
          </button>
        </form>
      </div>

      <!-- Main Footer Content -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8 border-b border-gray-700 pb-8 mb-8">
        
        <!-- Organization Info -->
        <div>
          <h4 class="text-xl font-bold mb-4 text-green-400">Learn and Teach</h4>
          <p class="text-sm text-gray-400 leading-relaxed">
            Learn and Teach একটি প্রশ্ন তৈরির সফটওয়্যার। এই সফটওয়্যারে স্কুল, কলেজ ও মাদ্ররাসার শিশু থেকে দ্বাদশ শ্রেণির যেকোন প্রশ্ন তৈরি করা যাবে এবং পরীক্ষাও দেওয়া যাবে | শিক্ষক ও শিক্ষার্থীদের জন্য অসাধারণ একটি সফটওয়্যার।
          </p>
          
          <!-- Contact Info -->
          <div class="mt-4 space-y-2">
            <div class="flex items-center text-gray-400 text-sm">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
              <span>Jamalpur, Kaliakair, Gazipur</span>
            </div>
            <div class="flex items-center text-gray-400 text-sm">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
              </svg>
              <span>+880 1763-264270</span>
            </div>
            <div class="flex items-center text-gray-400 text-sm">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
              </svg>
              <span>info@learnandteach.app</span>
            </div>
          </div>
        </div>

        <!-- Quick Links -->
        <div>
          <h4 class="text-lg font-bold mb-4">Menu</h4>
          <ul class="space-y-2 text-gray-400">
            <li>
              <Link class="hover:text-green-400 transition duration-150 flex items-center">
                <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Home
              </Link>
            </li>
            <li>
              <Link  class="hover:text-green-400 transition duration-150 flex items-center">
                <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                About Us
              </Link>
            </li>
            <li>
              <Link  class="hover:text-green-400 transition duration-150 flex items-center">
                <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Activities
              </Link>
            </li>
            <li>
              <Link  class="hover:text-green-400 transition duration-150 flex items-center">
                <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Online Exam
              </Link>
            </li>
          </ul>
        </div>

        <!-- Get Involved -->
        <div>
          <h4 class="text-lg font-bold mb-4">Join</h4>
          <ul class="space-y-2 text-gray-400">
            <li>
              <Link href="/join/donor" class="hover:text-green-400 transition duration-150 flex items-center">
                <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Regular Donor Member
              </Link>
            </li>
            <li>
              <Link href="/join/lifetime" class="hover:text-green-400 transition duration-150 flex items-center">
                <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Lifetime and Donor Members
              </Link>
            </li>
            <li>
              <Link href="/join/volunteer" class="hover:text-green-400 transition duration-150 flex items-center">
                <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Volunteer
              </Link>
            </li>
            <li>
              <Link href="/careers" class="hover:text-green-400 transition duration-150 flex items-center">
                <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Career
              </Link>
            </li>
          </ul>
        </div>

        <!-- Other Links -->
        <div>
          <h4 class="text-lg font-bold mb-4">Other</h4>
          <ul class="space-y-2 text-gray-400">
            <li>
              <Link class="hover:text-green-400 transition duration-150 flex items-center">
                <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Contact
              </Link>
            </li>
            <li>
              <Link class="hover:text-green-400 transition duration-150 flex items-center">
                <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Terms of Service
              </Link>
            </li>
            <li>
              <Link href="/privacy" class="hover:text-green-400 transition duration-150 flex items-center">
                <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Privacy Policy
              </Link>
            </li>
            <li>
              <Link href="/sitemap" class="hover:text-green-400 transition duration-150 flex items-center">
                <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Sitemap
              </Link>
            </li>
          </ul>
        </div>

      </div>

      <!-- Bottom Section -->
      <div class="flex flex-col sm:flex-row justify-between items-center text-sm text-gray-400">
        <!-- Copyright -->
        <div class="mb-4 sm:mb-0 text-center sm:text-left">
          <p>Copyright © {{ currentYear }} Learn and Teach - All rights reserved.</p>
        </div>
        
        <!-- Social Media Links -->
        <div class="flex space-x-4 text-2xl">
          <a 
            href="https://facebook.com/jagoroni" 
            target="_blank" 
            rel="noopener noreferrer"
            class="hover:text-green-400 transition duration-150 transform hover:scale-110"
            title="Facebook"
          >
            <i class="fab fa-facebook"></i>
          </a>
          <a 
            href="https://youtube.com/jagoroni" 
            target="_blank" 
            rel="noopener noreferrer"
            class="hover:text-green-400 transition duration-150 transform hover:scale-110"
            title="YouTube"
          >
            <i class="fab fa-youtube"></i>
          </a>
          <a 
            href="https://twitter.com/jagoroni" 
            target="_blank" 
            rel="noopener noreferrer"
            class="hover:text-green-400 transition duration-150 transform hover:scale-110"
            title="Twitter"
          >
            <i class="fab fa-x-twitter"></i>
          </a>
          <a 
            href="https://linkedin.com/company/jagoroni" 
            target="_blank" 
            rel="noopener noreferrer"
            class="hover:text-green-400 transition duration-150 transform hover:scale-110"
            title="LinkedIn"
          >
            <i class="fab fa-linkedin"></i>
          </a>
          <a 
            href="https://instagram.com/jagoroni" 
            target="_blank" 
            rel="noopener noreferrer"
            class="hover:text-green-400 transition duration-150 transform hover:scale-110"
            title="Instagram"
          >
            <i class="fab fa-instagram"></i>
          </a>
        </div>
      </div>

      <!-- Back to Top Button -->
      <button
        @click="scrollToTop"
        class="fixed bottom-8 right-8 bg-green-600 hover:bg-green-700 text-white p-3 rounded-full shadow-lg transition duration-300 transform hover:scale-110 z-40"
        title="Back to Top"
      >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
        </svg>
      </button>
    </div>
  </footer>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const email = ref('')
const subscribing = ref(false)

// Get current year for copyright
const currentYear = computed(() => new Date().getFullYear())

// Newsletter subscription
const subscribeNewsletter = async () => {
  if (!email.value) return
  
  subscribing.value = true
  
  try {
    // Simulate API call - replace with actual newsletter subscription
    await new Promise(resolve => setTimeout(resolve, 1000))
    
    alert('Thank you for subscribing to our newsletter!')
    email.value = ''
  } catch (error) {
    alert('Subscription failed. Please try again.')
  } finally {
    subscribing.value = false
  }
}

// Scroll to top function
const scrollToTop = () => {
  window.scrollTo({
    top: 0,
    behavior: 'smooth'
  })
}
</script>

<style scoped>
/* Smooth transitions for all interactive elements */
a, button {
  transition: all 0.3s ease;
}

/* Custom scrollbar for the footer */
footer ::-webkit-scrollbar {
  width: 6px;
}

footer ::-webkit-scrollbar-track {
  background: #374151;
}

footer ::-webkit-scrollbar-thumb {
  background: #10B981;
  border-radius: 3px;
}

footer ::-webkit-scrollbar-thumb:hover {
  background: #059669;
}
</style>