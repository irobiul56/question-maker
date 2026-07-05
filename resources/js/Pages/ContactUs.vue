<template>
  <AppLayout title="Contact Us">
    <div>
      <!-- Hero Section -->
      <section class="py-16 sm:py-24 bg-cover bg-center relative -mt-5" 
               :style="{ backgroundImage: `url('${heroBackground}')` }">
        <div class="absolute inset-0 bg-gray-900 opacity-70"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
          <h1 class="text-4xl sm:text-5xl font-extrabold text-white mb-6 text-center">
            Contact
          </h1>
          <div class="w-24 h-1 bg-green-400 mx-auto rounded-full mb-12"></div>
        </div>
      </section>

      <!-- Success Alert -->
      <div v-if="showSuccessAlert" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50 transition-opacity duration-300">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all duration-300 scale-100 opacity-100" 
             :class="successAlertAnimation">
          <div class="p-6 text-center">
            <!-- Success Icon -->
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
              <svg class="h-10 w-10 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
            </div>
            
            <!-- Success Message -->
            <h3 class="text-xl font-bold text-gray-900 mb-2">Message Sent Successfully!</h3>
            <p class="text-gray-600 mb-6">
              Thank you for contacting us. We'll get back to you as soon as possible.
            </p>
            
            <!-- Action Button -->
            <button 
              @click="showSuccessAlert = false"
              class="w-full bg-green-600 text-white font-medium py-3 px-4 rounded-lg hover:bg-green-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
            >
              Continue
            </button>
          </div>
        </div>
      </div>

      <!-- Contact Content -->
      <div class="max-w-6xl mx-auto p-8 bg-white">
        <div class="flex flex-col md:flex-row gap-12">
          
          <!-- Contact Form -->
          <div class="w-full md:w-1/2">
            <h2 class="text-2xl font-semibold mb-6 text-gray-800">Contact Form</h2>
            
            <form @submit.prevent="handleSubmit" class="space-y-4">
              <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Your Name *</label>
                <input 
                  type="text" 
                  v-model="form.name"
                  id="name" 
                  required 
                  placeholder="Type your name"
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
                  :class="{ 'border-red-500': errors.name }"
                >
                <p v-if="errors.name" class="text-red-500 text-xs mt-1">{{ errors.name }}</p>
              </div>
              
              <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Your Email *</label>
                <input 
                  type="email" 
                  v-model="form.email"
                  id="email" 
                  required 
                  placeholder="Type your email"
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
                  :class="{ 'border-red-500': errors.email }"
                >
                <p v-if="errors.email" class="text-red-500 text-xs mt-1">{{ errors.email }}</p>
              </div>
              
              <div>
                <label for="subject" class="block text-sm font-medium text-gray-700">Subject *</label>
                <input 
                  type="text" 
                  v-model="form.subject"
                  id="subject" 
                  required 
                  placeholder="Type subject"
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm-text-sm"
                  :class="{ 'border-red-500': errors.subject }"
                >
                <p v-if="errors.subject" class="text-red-500 text-xs mt-1">{{ errors.subject }}</p>
              </div>
              
              <div>
                <label for="message" class="block text-sm font-medium text-gray-700">Message *</label>
                <textarea 
                  v-model="form.message"
                  id="message" 
                  rows="7" 
                  required 
                  placeholder="Type your message"
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm-text-sm resize-none"
                  :class="{ 'border-red-500': errors.message }"
                ></textarea>
                <p v-if="errors.message" class="text-red-500 text-xs mt-1">{{ errors.message }}</p>
              </div>
              
              <div>
                <button 
                  type="submit"
                  :disabled="isSubmitting"
                  class="inline-flex items-center px-6 py-2 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
                >
                  <span v-if="isSubmitting" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Sending...
                  </span>
                  <span v-else>Send &rarr;</span>
                </button>
              </div>
            </form>
          </div>
          
          <!-- Contact Information -->
          <div class="w-full md:w-1/2">
            <h2 class="text-2xl font-semibold mb-6 text-gray-800">Our Location</h2>
            
            <div class="mb-6 h-64 bg-gray-200 border border-gray-300 rounded-lg overflow-hidden flex items-center justify-center">
              <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3642.50280252591!2d90.31558607403888!3d24.083807425897437!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755de380168f9cb%3A0x9f6b0103c4bb5609!2sJamalpur%20Chowrasta%20Bazar!5e0!3m2!1sen!2sbd!4v1779851022994!5m2!1sen!2sbd" 
                width="100%" 
                height="100%" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade"
              ></iframe>
            </div>
            
            <div class="space-y-4">
              <div class="flex items-start">
                <svg class="h-6 w-6 text-green-600 mr-3 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                <div>
                  <p class="text-sm font-medium text-gray-700">Phone</p>
                  <p class="text-base text-gray-900">{{ contactInfo.phone }}</p>
                </div>
              </div>
              
              <div class="flex items-start">
                <svg class="h-6 w-6 text-green-600 mr-3 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <div>
                  <p class="text-sm font-medium text-gray-700">Location</p>
                  <p class="text-base text-gray-900">{{ contactInfo.address }}</p>
                </div>
              </div>
              
              <div class="flex items-start">
                <svg class="h-6 w-6 text-green-600 mr-3 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <div>
                  <p class="text-sm font-medium text-gray-700">Email</p>
                  <p class="text-base text-gray-900">{{ contactInfo.email }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, nextTick } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

// Reactive data
const heroBackground = ref('storage/images/headerbackground.jpeg')
const isSubmitting = ref(false)
const showSuccessAlert = ref(false)
const successAlertAnimation = ref('')

const contactInfo = reactive({
  phone: '+8801763-264270',
  address: 'Jamalpur, Kaliakair, Gazipur',
  email: 'contact@learnandteach.app'
})

const form = reactive({
  name: '',
  email: '',
  subject: '',
  message: ''
})

const errors = reactive({
  name: '',
  email: '',
  subject: '',
  message: ''
})

// Validation function
const validateForm = () => {
  let isValid = true
  
  // Reset errors
  Object.keys(errors).forEach(key => errors[key] = '')
  
  // Name validation
  if (!form.name.trim()) {
    errors.name = 'Name is required'
    isValid = false
  }
  
  // Email validation
  if (!form.email.trim()) {
    errors.email = 'Email is required'
    isValid = false
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
    errors.email = 'Please enter a valid email address'
    isValid = false
  }
  
  // Subject validation
  if (!form.subject.trim()) {
    errors.subject = 'Subject is required'
    isValid = false
  }
  
  // Message validation
  if (!form.message.trim()) {
    errors.message = 'Message is required'
    isValid = false
  } else if (form.message.trim().length < 10) {
    errors.message = 'Message should be at least 10 characters long'
    isValid = false
  }
  
  return isValid
}

// Show success alert with animation
const showSuccessMessage = async () => {
  showSuccessAlert.value = true
  await nextTick()
  successAlertAnimation.value = 'animate-scale-in'
}

// Hide success alert with animation
const hideSuccessMessage = async () => {
  successAlertAnimation.value = 'animate-scale-out'
  await new Promise(resolve => setTimeout(resolve, 300))
  showSuccessAlert.value = false
  successAlertAnimation.value = ''
}

// Form submission handler
const handleSubmit = async () => {
  if (!validateForm()) {
    return
  }
  
  isSubmitting.value = true
  
  try {
    // Submit form using Inertia
    router.post(route('contact.submit'), form, {
      onSuccess: () => {
        // Reset form on success
        Object.keys(form).forEach(key => form[key] = '')
        
        // Show success message
        showSuccessMessage()
      },
      onError: (errors) => {
        // Handle server-side validation errors
        if (errors) {
          Object.keys(errors).forEach(key => {
            if (errors[key]) {
              // You can map server errors to your local error state
              console.error(`Server error for ${key}:`, errors[key])
            }
          })
          alert('There was an error with your submission. Please check the form and try again.')
        }
      },
      onFinish: () => {
        isSubmitting.value = false
      }
    })
    
  } catch (error) {
    console.error('Error submitting form:', error)
    alert('There was an error sending your message. Please try again.')
    isSubmitting.value = false
  }
}
</script>

<style scoped>
/* Your existing styles remain the same */
@import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali:wght@400;500;700;900&display=swap');

.text-green-brand { color: #10B981; } 
.bg-green-brand { background-color: #10B981; }
.hover\:bg-green-brand-dark:hover { background-color: #059669; }
.border-green-brand { border-color: #10B981; }

.bg-pattern {
  background-image: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), url('placeholder-background.jpg'); 
  background-color: #dab15a; 
  background-blend-mode: multiply;
}

html {
  scroll-behavior: smooth;
}

.tab-button {
  transition: all 0.2s ease-in-out;
  border-left: 4px solid transparent;
}

.active-tab-style {
  color: #047857;
  background-color: rgba(209, 250, 229, 0.7);
  border-color: #10B981;
  font-weight: 600;
}

.inactive-tab-style {
  color: #374151;
  font-weight: 500;
}

.inactive-tab-style:hover {
  background-color: #f3f4f6;
}

/* Success alert animations */
@keyframes scale-in {
  0% {
    transform: scale(0.9);
    opacity: 0;
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}

@keyframes scale-out {
  0% {
    transform: scale(1);
    opacity: 1;
  }
  100% {
    transform: scale(0.9);
    opacity: 0;
  }
}

.animate-scale-in {
  animation: scale-in 0.3s ease-out forwards;
}

.animate-scale-out {
  animation: scale-out 0.3s ease-in forwards;
}
</style>