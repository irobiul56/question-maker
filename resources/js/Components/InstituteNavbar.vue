<!-- Navbar.vue - Result Management System -->
<template>
  <header class="bg-white shadow-sm sticky top-0 z-50">
    <div class="px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">
        <!-- Left Section: Sidebar Toggle & Brand -->
        <div class="flex items-center">
          <!-- Sidebar Toggle Button -->
          <button
            @click="$emit('toggle-sidebar')"
            class="p-2 rounded-lg hover:bg-gray-100 transition-colors text-gray-600 hover:text-gray-900"
          >
            <i class="fa-solid fa-bars text-xl"></i>
          </button>

          <!-- Mobile Menu Toggle -->
          <button
            @click="showMobileMenu = !showMobileMenu"
            class="ml-2 p-2 rounded-lg hover:bg-gray-100 transition-colors text-gray-600 hover:text-gray-900 lg:hidden"
          >
            <i class="fa-solid fa-chevron-down text-lg" :class="showMobileMenu ? 'rotate-180' : ''"></i>
          </button>

          <!-- Brand (Hidden on mobile when collapsed) -->
          <div class="hidden sm:flex items-center ml-4">
            <div class="flex items-center">
              <div class="w-8 h-8 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center shadow-md">
                <i class="fa-solid fa-graduation-cap text-white text-sm"></i>
              </div>
              <span class="ml-2 text-lg font-bold text-gray-800 hidden md:block">
                School RMS
              </span>
            </div>
          </div>

          <!-- Breadcrumb -->
          <nav class="hidden lg:flex ml-6" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2 text-sm">
              <li>
                <a href="#" class="text-gray-400 hover:text-gray-600">
                  <i class="fa-solid fa-house"></i>
                </a>
              </li>
              <li>
                <i class="fa-solid fa-chevron-right text-gray-300 text-xs"></i>
              </li>
              <li>
                <a href="#" class="text-gray-600 hover:text-gray-900 font-medium">
                  {{ currentPage }}
                </a>
              </li>
              <li v-if="currentSubPage" class="flex items-center">
                <i class="fa-solid fa-chevron-right text-gray-300 text-xs mx-2"></i>
                <span class="text-gray-500">{{ currentSubPage }}</span>
              </li>
            </ol>
          </nav>
        </div>

        <!-- Right Section -->
        <div class="flex items-center space-x-1 sm:space-x-3">
          <!-- Search (Desktop) -->
          <div class="relative hidden md:block">
            <div class="relative">
              <input
                type="text"
                placeholder="Search..."
                v-model="searchQuery"
                @keyup.enter="handleSearch"
                class="w-48 lg:w-64 px-4 py-2 pl-10 pr-4 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
              />
              <i class="fa-solid fa-search absolute left-3 top-3 text-gray-400 text-sm"></i>
              <kbd class="absolute right-3 top-2.5 text-xs text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded border border-gray-200 hidden lg:block">
                ⌘K
              </kbd>
            </div>
          </div>

          <!-- Notifications -->
          <div class="relative">
            <button
              @click="toggleNotifications"
              class="relative p-2 rounded-lg hover:bg-gray-100 transition-colors text-gray-600 hover:text-gray-900"
            >
              <i class="fa-regular fa-bell text-xl"></i>
              <span
                v-if="notificationCount > 0"
                class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center animate-pulse"
              >
                {{ notificationCount > 9 ? '9+' : notificationCount }}
              </span>
            </button>

            <!-- Notification Dropdown -->
            <div
              v-if="showNotifications"
              class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-xl shadow-2xl border border-gray-100 overflow-hidden z-50"
            >
              <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">Notifications</h3>
                <button class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                  Mark all as read
                </button>
              </div>
              <div class="max-h-96 overflow-y-auto">
                <div
                  v-for="notification in notifications"
                  :key="notification.id"
                  class="p-4 hover:bg-gray-50 transition-colors border-b border-gray-50 last:border-0 cursor-pointer"
                >
                  <div class="flex items-start space-x-3">
                    <div
                      :class="[
                        'w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0',
                        notification.type === 'success' ? 'bg-green-100 text-green-600' :
                        notification.type === 'warning' ? 'bg-yellow-100 text-yellow-600' :
                        notification.type === 'error' ? 'bg-red-100 text-red-600' :
                        'bg-blue-100 text-blue-600'
                      ]"
                    >
                      <i :class="notification.icon"></i>
                    </div>
                    <div class="flex-1">
                      <p class="text-sm font-medium text-gray-800">{{ notification.title }}</p>
                      <p class="text-xs text-gray-500 mt-0.5">{{ notification.message }}</p>
                      <p class="text-xs text-gray-400 mt-1">{{ notification.time }}</p>
                    </div>
                    <button class="text-gray-300 hover:text-gray-500">
                      <i class="fa-solid fa-times text-xs"></i>
                    </button>
                  </div>
                </div>
              </div>
              <div class="p-3 bg-gray-50 text-center">
                <a href="#" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                  View all notifications
                </a>
              </div>
            </div>
          </div>

          <!-- Quick Actions -->
          <div class="relative hidden sm:block">
            <button
              @click="toggleQuickActions"
              class="p-2 rounded-lg hover:bg-gray-100 transition-colors text-gray-600 hover:text-gray-900"
            >
              <i class="fa-solid fa-plus text-xl"></i>
            </button>

            <!-- Quick Actions Dropdown -->
            <div
              v-if="showQuickActions"
              class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-2xl border border-gray-100 overflow-hidden z-50"
            >
              <div class="p-2 space-y-1">
                <a
                  href="#"
                  class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-50 transition-colors"
                >
                  <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-user-plus text-blue-600"></i>
                  </div>
                  <span class="text-sm text-gray-700">Add Student</span>
                </a>
                <a
                  href="#"
                  class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-50 transition-colors"
                >
                  <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-pen-fancy text-green-600"></i>
                  </div>
                  <span class="text-sm text-gray-700">Enter Marks</span>
                </a>
                <a
                  href="#"
                  class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-50 transition-colors"
                >
                  <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-file-lines text-purple-600"></i>
                  </div>
                  <span class="text-sm text-gray-700">Generate Result</span>
                </a>
                <a
                  href="#"
                  class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-50 transition-colors"
                >
                  <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-calendar-plus text-yellow-600"></i>
                  </div>
                  <span class="text-sm text-gray-700">Schedule Exam</span>
                </a>
                <hr class="my-1 border-gray-100">
                <a
                  href="#"
                  class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-50 transition-colors"
                >
                  <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-file-pdf text-orange-600"></i>
                  </div>
                  <span class="text-sm text-gray-700">Export Report</span>
                </a>
              </div>
            </div>
          </div>

          <!-- User Profile -->
          <div class="relative">
            <button
              @click="toggleProfileMenu"
              class="flex items-center space-x-2 p-1.5 rounded-lg hover:bg-gray-100 transition-colors"
            >
              <div class="w-8 h-8 rounded-full bg-gradient-to-r from-blue-500 to-indigo-500 flex items-center justify-center text-white font-bold text-sm">
                {{ userInitials }}
              </div>
              <div class="hidden lg:block text-left">
                <p class="text-sm font-medium text-gray-800">{{ userName }}</p>
                <p class="text-xs text-gray-500">{{ userRole }}</p>
              </div>
              <i class="fa-solid fa-chevron-down text-xs text-gray-400 hidden lg:block"></i>
            </button>

            <!-- Profile Dropdown -->
            <div
              v-if="showProfileMenu"
              class="absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-2xl border border-gray-100 overflow-hidden z-50"
            >
              <div class="p-4 border-b border-gray-100">
                <div class="flex items-center space-x-3">
                  <div class="w-12 h-12 rounded-full bg-gradient-to-r from-blue-500 to-indigo-500 flex items-center justify-center text-white font-bold text-lg">
                    {{ userInitials }}
                  </div>
                  <div>
                    <p class="font-semibold text-gray-800">{{ userName }}</p>
                    <p class="text-sm text-gray-500">{{ userEmail }}</p>
                    <span class="inline-block mt-1 px-2 py-0.5 text-xs font-medium bg-green-100 text-green-600 rounded-full">
                      {{ userRole }}
                    </span>
                  </div>
                </div>
              </div>
              <div class="p-2 space-y-1">
                <a
                  href="#"
                  class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-50 transition-colors"
                >
                  <i class="fa-regular fa-user w-5 text-gray-400"></i>
                  <span class="text-sm text-gray-700">My Profile</span>
                </a>
                <a
                  href="#"
                  class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-50 transition-colors"
                >
                  <i class="fa-regular fa-gear w-5 text-gray-400"></i>
                  <span class="text-sm text-gray-700">Account Settings</span>
                </a>
                <a
                  href="#"
                  class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-50 transition-colors"
                >
                  <i class="fa-regular fa-circle-question w-5 text-gray-400"></i>
                  <span class="text-sm text-gray-700">Help & Support</span>
                </a>
                <hr class="my-1 border-gray-100">
                <a
                  href="#"
                  class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-red-50 transition-colors text-red-600"
                >
                  <i class="fa-solid fa-right-from-bracket w-5"></i>
                  <span class="text-sm font-medium">Logout</span>
                </a>
              </div>
            </div>
          </div>

          <!-- Fullscreen Toggle -->
          <button
            @click="toggleFullscreen"
            class="hidden lg:block p-2 rounded-lg hover:bg-gray-100 transition-colors text-gray-600 hover:text-gray-900"
          >
            <i :class="isFullscreen ? 'fa-solid fa-compress' : 'fa-solid fa-expand'" class="text-lg"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile Menu Dropdown -->
    <div
      v-if="showMobileMenu"
      class="lg:hidden bg-white border-t border-gray-100 shadow-lg"
    >
      <div class="px-4 py-3 space-y-2">
        <!-- Mobile Search -->
        <div class="relative">
          <input
            type="text"
            placeholder="Search..."
            v-model="searchQuery"
            @keyup.enter="handleSearch"
            class="w-full px-4 py-2 pl-10 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          />
          <i class="fa-solid fa-search absolute left-3 top-3 text-gray-400 text-sm"></i>
        </div>

        <!-- Mobile Navigation Links -->
        <div class="grid grid-cols-2 gap-2 pt-2">
          <a href="#" class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-gray-50 transition-colors">
            <i class="fa-solid fa-gauge-high text-blue-600"></i>
            <span class="text-sm text-gray-700">Dashboard</span>
          </a>
          <a href="#" class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-gray-50 transition-colors">
            <i class="fa-solid fa-user-graduate text-green-600"></i>
            <span class="text-sm text-gray-700">Students</span>
          </a>
          <a href="#" class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-gray-50 transition-colors">
            <i class="fa-solid fa-chalkboard-user text-purple-600"></i>
            <span class="text-sm text-gray-700">Teachers</span>
          </a>
          <a href="#" class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-gray-50 transition-colors">
            <i class="fa-solid fa-pen-to-square text-yellow-600"></i>
            <span class="text-sm text-gray-700">Exams</span>
          </a>
          <a href="#" class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-gray-50 transition-colors">
            <i class="fa-solid fa-file-lines text-red-600"></i>
            <span class="text-sm text-gray-700">Results</span>
          </a>
          <a href="#" class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-gray-50 transition-colors">
            <i class="fa-solid fa-gear text-gray-600"></i>
            <span class="text-sm text-gray-700">Settings</span>
          </a>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { usePage } from '@inertiajs/inertia-vue3';

// Props
const props = defineProps({
  user: {
    type: Object,
    default: () => ({
      name: 'Admin User',
      email: 'admin@school.edu',
      role: 'Administrator',
      initials: 'AU'
    })
  },
  notificationCount: {
    type: Number,
    default: 5
  },
  currentPage: {
    type: String,
    default: 'Dashboard'
  },
  currentSubPage: {
    type: String,
    default: ''
  }
});

// Emits
const emit = defineEmits(['toggle-sidebar']);

// State
const showNotifications = ref(false);
const showQuickActions = ref(false);
const showProfileMenu = ref(false);
const showMobileMenu = ref(false);
const searchQuery = ref('');
const isFullscreen = ref(false);

// Computed
const userInitials = computed(() => {
  return props.user.initials || props.user.name.split(' ').map(n => n[0]).join('').toUpperCase();
});

const userName = computed(() => props.user.name);
const userEmail = computed(() => props.user.email);
const userRole = computed(() => props.user.role);

// Notifications Data
const notifications = ref([
  {
    id: 1,
    type: 'success',
    icon: 'fa-solid fa-check-circle',
    title: 'Results Published',
    message: 'Class 10 Final Exam results have been published.',
    time: '2 hours ago'
  },
  {
    id: 2,
    type: 'info',
    icon: 'fa-solid fa-user-plus',
    title: 'New Student Registered',
    message: 'Md. Kamal Hossain has been registered in Class 9.',
    time: '5 hours ago'
  },
  {
    id: 3,
    type: 'warning',
    icon: 'fa-solid fa-exclamation-triangle',
    title: 'Pending Marks Entry',
    message: 'Marks for 3 subjects in Class 8 are pending.',
    time: '1 day ago'
  },
  {
    id: 4,
    type: 'error',
    icon: 'fa-solid fa-calendar-times',
    title: 'Exam Rescheduled',
    message: 'Physics exam has been rescheduled to Dec 20.',
    time: '2 days ago'
  }
]);

// Methods
const toggleNotifications = () => {
  showNotifications.value = !showNotifications.value;
  showQuickActions.value = false;
  showProfileMenu.value = false;
};

const toggleQuickActions = () => {
  showQuickActions.value = !showQuickActions.value;
  showNotifications.value = false;
  showProfileMenu.value = false;
};

const toggleProfileMenu = () => {
  showProfileMenu.value = !showProfileMenu.value;
  showNotifications.value = false;
  showQuickActions.value = false;
};

const toggleFullscreen = () => {
  if (!document.fullscreenElement) {
    document.documentElement.requestFullscreen();
    isFullscreen.value = true;
  } else {
    if (document.exitFullscreen) {
      document.exitFullscreen();
      isFullscreen.value = false;
    }
  }
};

const handleSearch = () => {
  if (searchQuery.value.trim()) {
    // Handle search
    console.log('Searching for:', searchQuery.value);
    // You can emit an event or navigate to search results
  }
};

// Close dropdowns when clicking outside
const handleClickOutside = (event) => {
  const dropdowns = document.querySelectorAll('.relative');
  let clickedInside = false;
  
  dropdowns.forEach(dropdown => {
    if (dropdown.contains(event.target)) {
      clickedInside = true;
    }
  });
  
  if (!clickedInside) {
    showNotifications.value = false;
    showQuickActions.value = false;
    showProfileMenu.value = false;
  }
};

// Keyboard shortcuts
const handleKeyDown = (event) => {
  // Ctrl+K or Cmd+K for search
  if ((event.ctrlKey || event.metaKey) && event.key === 'k') {
    event.preventDefault();
    const searchInput = document.querySelector('input[type="text"]');
    if (searchInput) {
      searchInput.focus();
    }
  }
  
  // Escape key to close dropdowns
  if (event.key === 'Escape') {
    showNotifications.value = false;
    showQuickActions.value = false;
    showProfileMenu.value = false;
    showMobileMenu.value = false;
  }
};

// Lifecycle hooks
onMounted(() => {
  document.addEventListener('click', handleClickOutside);
  document.addEventListener('keydown', handleKeyDown);
  
  // Handle fullscreen change
  document.addEventListener('fullscreenchange', () => {
    isFullscreen.value = !!document.fullscreenElement;
  });
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
  document.removeEventListener('keydown', handleKeyDown);
  document.removeEventListener('fullscreenchange', () => {});
});
</script>

<style scoped>
/* Custom styles */
.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: .5;
  }
}

/* Dropdown animations */
.absolute {
  animation: slideDown 0.2s ease-out;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Rotate animation for chevron */
.rotate-180 {
  transform: rotate(180deg);
  transition: transform 0.3s ease;
}

/* Search input focus ring */
input:focus {
  outline: none;
}

/* Notification scrollbar */
.max-h-96::-webkit-scrollbar {
  width: 4px;
}

.max-h-96::-webkit-scrollbar-track {
  background: transparent;
}

.max-h-96::-webkit-scrollbar-thumb {
  background: #e5e7eb;
  border-radius: 2px;
}

.max-h-96::-webkit-scrollbar-thumb:hover {
  background: #d1d5db;
}

/* Mobile menu transition */
.showMobileMenu-enter-active,
.showMobileMenu-leave-active {
  transition: all 0.3s ease;
}

.showMobileMenu-enter-from,
.showMobileMenu-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

/* Badge pulse animation */
.badge-pulse {
  animation: badgePulse 2s infinite;
}

@keyframes badgePulse {
  0% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.1);
  }
  100% {
    transform: scale(1);
  }
}
</style>