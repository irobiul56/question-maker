<template>
  <div class="min-h-screen bg-gray-50">
    <Head :title="title" />
    
    <!-- Header -->
    <header ref="headerElement" class="sticky-header sticky top-5 z-50 bg-white bg-opacity-95 shadow-2xl duration-300 mx-4 rounded-xl backdrop-blur-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16 md:h-20">
          
          <!-- Logo Area -->
          <Link  class="flex-shrink-0">
            <img class="h-12 w-auto" 
                 src="/storage/img/logos.png" 
                 alt="Organization Logo"
                 @error="handleImageError">
          </Link>

          <!-- Desktop Navigation -->
          <nav class="hidden lg:block ml-10">
            <ul class="flex space-x-6 text-gray-700 font-medium">
              <li>
                <Link :href="route('home')" class="hover:text-blue-600 py-2 px-3 rounded-lg" :class="{ 'text-blue-600 border-b-2 border-blue-600': $page.url === '/' }">
                  Home
                </Link>
              </li>
              <li>
                <Link :href="route('about')" class="hover:text-blue-600 transition-colors py-2 px-3 rounded-lg" :class="{ 'text-blue-600 border-b-2 border-blue-600': $page.url === '/about-us' }">
                  About Us
                </Link>
              </li>
              
              <li>
                <Link :href="route('qstIndex')" class="hover:text-blue-600 transition-colors py-2 px-3 rounded-lg" :class="{ 'text-blue-600 border-b-2 border-blue-600': $page.url === '/gallery' }">
                  Question Making
                </Link>
              </li>
              <li>
                <Link :href="route('home') + '#onlineexam'" class="hover:text-blue-600 transition-colors py-2 px-3 rounded-lg" :class="{ 'text-blue-600 border-b-2 border-blue-600': $page.url === '/'+'#onlineexam' }">
                  Model Test
                </Link>
              </li>
              
              <li>
                <Link :href="route('contact')"  class="hover:text-blue-600 transition-colors py-2 px-3 rounded-lg" :class="{ 'text-blue-600 border-b-2 border-blue-600': $page.url === '/contact-us' }">
                  Contact
                </Link>
              </li>
            </ul>
          </nav>

          <!-- Utility Group -->
          <div class="flex items-center space-x-2 sm:space-x-4">
            
            <!-- Language Switcher -->
            <div class="hidden sm:flex text-sm border border-gray-300 rounded-lg overflow-hidden shadow-sm">
              <button 
                class="px-2 py-1 transition-colors" 
                :class="locale === 'bn' ? 'bg-blue-600 text-white font-semibold shadow-inner' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                @click="switchLanguage('bn')"
                aria-label="বাংলা"
              >
                বাং
              </button>
              <button 
                class="px-2 py-1 transition-colors" 
                :class="locale === 'en' ? 'bg-blue-600 text-white font-semibold shadow-inner' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                @click="switchLanguage('en')"
                aria-label="English"
              >
                En
              </button>
            </div>

            <!-- User Icon Button -->
            <Link :href="route('login')" class="p-2 text-gray-600 hover:text-blue-600 transition-colors">
              <svg class="w-5 h-5" focusable="false" aria-hidden="true" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10.0026 10.4896C12.3038 10.4896 14.1693 8.6241 14.1693 6.32292C14.1693 4.02173 12.3038 2.15625 10.0026 2.15625C7.70142 2.15625 5.83594 4.02173 5.83594 6.32292C5.83594 8.6241 7.70142 10.4896 10.0026 10.4896Z" stroke="currentColor" stroke-width="1.66667"></path>
                <path d="M14.168 12.1563H14.4614C15.0706 12.1564 15.6588 12.3791 16.1155 12.7823C16.5721 13.1856 16.8658 13.7417 16.9414 14.3463L17.2672 16.9496C17.2965 17.1841 17.2756 17.4222 17.2058 17.648C17.1361 17.8738 17.0191 18.0822 16.8627 18.2594C16.7063 18.4366 16.5139 18.5784 16.2985 18.6756C16.083 18.7728 15.8494 18.823 15.613 18.8229H4.38969C4.15334 18.823 3.91968 18.7728 3.70422 18.6756C3.48877 18.5784 3.29644 18.4366 3.14 18.2594C2.98356 18.0822 2.86659 17.8738 2.79686 17.648C2.72712 17.4222 2.70621 17.1841 2.73552 16.9496L3.06052 14.3463C3.1361 13.7414 3.43003 13.1851 3.88705 12.7818C4.34406 12.3785 4.93267 12.156 5.54219 12.1563H5.83469" stroke="currentColor" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"></path>
              </svg>
            </Link>

           

            <!-- Mobile Menu Button -->
            <button 
              class="lg:hidden p-2 text-gray-800 hover:bg-gray-100 rounded-md transition duration-150" 
              aria-label="Open menu" 
              @click="toggleMenu"
            >
              <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path :class="{'hidden': isMenuOpen}" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                <path :class="{'hidden': !isMenuOpen}" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>
      </div>
        
      <!-- Mobile Menu -->
      <div :class="['lg:hidden transition-all duration-300 ease-in-out', {'hidden': !isMenuOpen}]">
        <div class="px-2 pt-2 pb-4 space-y-1 sm:px-3">
          <Link  class="block px-3 py-2 rounded-md text-base font-medium transition-colors" :class="$page.url === '/' ? 'text-blue-600 bg-blue-50' : 'text-gray-700 hover:bg-gray-100'">Home</Link>
          <Link  class="block px-3 py-2 rounded-md text-base font-medium transition-colors" :class="$page.url === '/about' ? 'text-blue-600 bg-blue-50' : 'text-gray-700 hover:bg-gray-100'">About Us</Link>
          <Link  class="block px-3 py-2 rounded-md text-base font-medium transition-colors" :class="$page.url === '/activities' ? 'text-blue-600 bg-blue-50' : 'text-gray-700 hover:bg-gray-100'">Activities</Link>
         </div>
      </div>
    </header>

    <hr class="border-gray-200">

    <!-- Page Content -->
    <main>
      <slot />
    </main>

    <!-- Footer -->
    <Footer />
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { Link, Head } from '@inertiajs/vue3'
import Footer from '@/Components/Footer.vue'

const props = defineProps({
  title: String
})

const isMenuOpen = ref(false)
const locale = ref('en')
const headerElement = ref(null)

let lastScrollY = ref(0)

const toggleMenu = () => {
  isMenuOpen.value = !isMenuOpen.value
}

const switchLanguage = (lang) => {
  locale.value = lang
  // You can add logic to change the application language here
  // For example: window.location.href = `/locale/${lang}`
}

// const handleImageError = (event) => {
//   event.target.src = 'Storage/img/logo'
// }

const handleScroll = () => {
  const currentScrollY = window.scrollY
  const scrollThreshold = 100

  if (currentScrollY > lastScrollY.value && currentScrollY > scrollThreshold) {
    headerElement.value?.classList.add('header-slide-up')
  } else if (currentScrollY < lastScrollY.value) {
    headerElement.value?.classList.remove('header-slide-up')
  }

  lastScrollY.value = currentScrollY
}

onMounted(() => {
  lastScrollY.value = window.scrollY
  window.addEventListener('scroll', handleScroll)
  
  window.addEventListener('resize', () => {
    if (window.innerWidth >= 1024) {
      isMenuOpen.value = false
    }
  })
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>

<style scoped>
.sticky-header {
  will-change: transform;
  transition: all 0.3s ease-in-out;
}

.header-slide-up {
  transform: translateY(-200%);
}
</style>