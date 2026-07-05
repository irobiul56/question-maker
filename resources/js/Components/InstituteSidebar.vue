<!-- UserSidebar.vue - Result Management System -->
<script setup>
import { defineProps } from 'vue';
import NavLink from '@/Components/NavLink.vue';

const props = defineProps({
  isCollapsed: Boolean
});

// Route helper function for active state
const isRouteActive = (patterns) => {
  const currentRoute = route().current();
  if (!currentRoute) return false;

  return patterns.some(pattern => {
    if (pattern.endsWith('*')) {
      const basePattern = pattern.replace('*', '');
      return currentRoute.startsWith(basePattern);
    }
    return currentRoute === pattern;
  });
};
</script>

<template>
  <aside 
    class="min-h-screen bg-white shadow-lg p-4 transition-all duration-300 border-r border-gray-100 flex flex-col overflow-y-auto"
    :class="isCollapsed ? 'w-20' : 'w-64'"
  >
    <!-- Logo/Brand Section -->
    <!-- <div class="flex items-center justify-center mb-6 pb-4 border-b border-gray-100" 
         :class="isCollapsed ? 'px-2' : 'px-3'">
      <div class="flex items-center">
        <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center shadow-md flex-shrink-0">
          <i class="fa-solid fa-graduation-cap text-white text-lg"></i>
        </div>
        <span v-if="!isCollapsed" class="ml-3 text-lg font-bold text-gray-800 whitespace-nowrap">
          School RMS
        </span>
      </div>
    </div> -->

    <!-- Navigation -->
    <nav class="flex-1">
      <ul class="space-y-1">
        <!-- Dashboard -->
        <li>
          <NavLink 
            :href="route('userdashboard')" 
            :active="isRouteActive(['userdashboard', 'userdashboard.*'])"
            class="flex items-center p-3 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group"
            :class="[
              isCollapsed ? 'justify-center px-2' : 'px-3',
              isRouteActive(['userdashboard', 'userdashboard.*']) ? 'bg-blue-50 text-blue-600' : 'text-gray-600'
            ]"
          >
            <div class="flex items-center w-full" :class="isCollapsed ? 'justify-center' : ''">
              <i class="fa-solid fa-gauge-high text-lg w-6 text-center" 
                 :class="isRouteActive(['userdashboard', 'userdashboard.*']) ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600'"></i>
              <span v-if="!isCollapsed" class="ml-3 whitespace-nowrap">Dashboard</span>
              <span v-if="!isCollapsed" class="ml-auto">
                <span class="px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-600 rounded-full">New</span>
              </span>
            </div>
          </NavLink>
        </li>

        <!-- Student Management -->
        <li>
          <NavLink 
            
            :active="isRouteActive(['students', 'students.*'])"
            class="flex items-center p-3 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group"
            :class="[
              isCollapsed ? 'justify-center px-2' : 'px-3',
              isRouteActive(['students', 'students.*']) ? 'bg-blue-50 text-blue-600' : 'text-gray-600'
            ]"
          >
            <div class="flex items-center w-full" :class="isCollapsed ? 'justify-center' : ''">
              <i class="fa-solid fa-user-graduate text-lg w-6 text-center" 
                 :class="isRouteActive(['students', 'students.*']) ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600'"></i>
              <span v-if="!isCollapsed" class="ml-3 whitespace-nowrap">Students</span>
              <span v-if="!isCollapsed" class="ml-auto text-xs text-gray-400">2,847</span>
            </div>
          </NavLink>
        </li>

        <!-- Teachers Management -->
        <li>
          <NavLink 
             
            :active="isRouteActive(['teachers', 'teachers.*'])"
            class="flex items-center p-3 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group"
            :class="[
              isCollapsed ? 'justify-center px-2' : 'px-3',
              isRouteActive(['teachers', 'teachers.*']) ? 'bg-blue-50 text-blue-600' : 'text-gray-600'
            ]"
          >
            <div class="flex items-center w-full" :class="isCollapsed ? 'justify-center' : ''">
              <i class="fa-solid fa-chalkboard-user text-lg w-6 text-center" 
                 :class="isRouteActive(['teachers', 'teachers.*']) ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600'"></i>
              <span v-if="!isCollapsed" class="ml-3 whitespace-nowrap">Teachers</span>
              <span v-if="!isCollapsed" class="ml-auto text-xs text-gray-400">124</span>
            </div>
          </NavLink>
        </li>

        <!-- Subjects -->
        <li>
          <NavLink 
           
            :active="isRouteActive(['subjects', 'subjects.*'])"
            class="flex items-center p-3 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group"
            :class="[
              isCollapsed ? 'justify-center px-2' : 'px-3',
              isRouteActive(['subjects', 'subjects.*']) ? 'bg-blue-50 text-blue-600' : 'text-gray-600'
            ]"
          >
            <div class="flex items-center w-full" :class="isCollapsed ? 'justify-center' : ''">
              <i class="fa-solid fa-book-open text-lg w-6 text-center" 
                 :class="isRouteActive(['subjects', 'subjects.*']) ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600'"></i>
              <span v-if="!isCollapsed" class="ml-3 whitespace-nowrap">Subjects</span>
              <span v-if="!isCollapsed" class="ml-auto text-xs text-gray-400">15</span>
            </div>
          </NavLink>
        </li>

        <!-- Exams Management -->
        <li>
          <NavLink 
            
            :active="isRouteActive(['exams', 'exams.*'])"
            class="flex items-center p-3 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group"
            :class="[
              isCollapsed ? 'justify-center px-2' : 'px-3',
              isRouteActive(['exams', 'exams.*']) ? 'bg-blue-50 text-blue-600' : 'text-gray-600'
            ]"
          >
            <div class="flex items-center w-full" :class="isCollapsed ? 'justify-center' : ''">
              <i class="fa-solid fa-pen-to-square text-lg w-6 text-center" 
                 :class="isRouteActive(['exams', 'exams.*']) ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600'"></i>
              <span v-if="!isCollapsed" class="ml-3 whitespace-nowrap">Exams</span>
              <span v-if="!isCollapsed" class="ml-auto">
                <span class="px-2 py-0.5 text-xs font-medium bg-yellow-100 text-yellow-600 rounded-full">3</span>
              </span>
            </div>
          </NavLink>
        </li>

        <!-- Assign Subjects to Exam -->
        <li>
          <NavLink 
            
            :active="isRouteActive(['exam-subjects', 'exam-subjects.*'])"
            class="flex items-center p-3 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group"
            :class="[
              isCollapsed ? 'justify-center px-2' : 'px-3',
              isRouteActive(['exam-subjects', 'exam-subjects.*']) ? 'bg-blue-50 text-blue-600' : 'text-gray-600'
            ]"
          >
            <div class="flex items-center w-full" :class="isCollapsed ? 'justify-center' : ''">
              <i class="fa-solid fa-list-check text-lg w-6 text-center" 
                 :class="isRouteActive(['exam-subjects', 'exam-subjects.*']) ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600'"></i>
              <span v-if="!isCollapsed" class="ml-3 whitespace-nowrap">Exam Subjects</span>
            </div>
          </NavLink>
        </li>

        <!-- Marks Entry -->
        <li>
          <NavLink 
             
            :active="isRouteActive(['marks', 'marks.*'])"
            class="flex items-center p-3 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group"
            :class="[
              isCollapsed ? 'justify-center px-2' : 'px-3',
              isRouteActive(['marks', 'marks.*']) ? 'bg-blue-50 text-blue-600' : 'text-gray-600'
            ]"
          >
            <div class="flex items-center w-full" :class="isCollapsed ? 'justify-center' : ''">
              <i class="fa-solid fa-pen-fancy text-lg w-6 text-center" 
                 :class="isRouteActive(['marks', 'marks.*']) ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600'"></i>
              <span v-if="!isCollapsed" class="ml-3 whitespace-nowrap">Marks Entry</span>
              <span v-if="!isCollapsed" class="ml-auto">
                <span class="px-2 py-0.5 text-xs font-medium bg-red-100 text-red-600 rounded-full">Pending</span>
              </span>
            </div>
          </NavLink>
        </li>

        <!-- Results Management -->
        <li>
          <NavLink 
             
            :active="isRouteActive(['results', 'results.*'])"
            class="flex items-center p-3 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group"
            :class="[
              isCollapsed ? 'justify-center px-2' : 'px-3',
              isRouteActive(['results', 'results.*']) ? 'bg-blue-50 text-blue-600' : 'text-gray-600'
            ]"
          >
            <div class="flex items-center w-full" :class="isCollapsed ? 'justify-center' : ''">
              <i class="fa-solid fa-file-lines text-lg w-6 text-center" 
                 :class="isRouteActive(['results', 'results.*']) ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600'"></i>
              <span v-if="!isCollapsed" class="ml-3 whitespace-nowrap">Results</span>
              <span v-if="!isCollapsed" class="ml-auto text-xs text-gray-400">2,847</span>
            </div>
          </NavLink>
        </li>

        <!-- Publish Results -->
        <li>
          <NavLink 
            
            :active="isRouteActive(['results.publish', 'results.publish.*'])"
            class="flex items-center p-3 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group"
            :class="[
              isCollapsed ? 'justify-center px-2' : 'px-3',
              isRouteActive(['results.publish', 'results.publish.*']) ? 'bg-blue-50 text-blue-600' : 'text-gray-600'
            ]"
          >
            <div class="flex items-center w-full" :class="isCollapsed ? 'justify-center' : ''">
              <i class="fa-solid fa-globe text-lg w-6 text-center" 
                 :class="isRouteActive(['results.publish', 'results.publish.*']) ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600'"></i>
              <span v-if="!isCollapsed" class="ml-3 whitespace-nowrap">Publish Results</span>
              <span v-if="!isCollapsed" class="ml-auto">
                <span class="px-2 py-0.5 text-xs font-medium bg-green-100 text-green-600 rounded-full">12</span>
              </span>
            </div>
          </NavLink>
        </li>

        <!-- Grade System -->
        <li>
          <NavLink 
            
            :active="isRouteActive(['grades', 'grades.*'])"
            class="flex items-center p-3 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group"
            :class="[
              isCollapsed ? 'justify-center px-2' : 'px-3',
              isRouteActive(['grades', 'grades.*']) ? 'bg-blue-50 text-blue-600' : 'text-gray-600'
            ]"
          >
            <div class="flex items-center w-full" :class="isCollapsed ? 'justify-center' : ''">
              <i class="fa-solid fa-star text-lg w-6 text-center" 
                 :class="isRouteActive(['grades', 'grades.*']) ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600'"></i>
              <span v-if="!isCollapsed" class="ml-3 whitespace-nowrap">Grade System</span>
            </div>
          </NavLink>
        </li>

        <!-- Separator -->
        <li class="relative my-4">
          <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-200"></div>
          </div>
          <div class="relative flex justify-center" v-if="!isCollapsed">
            <span class="px-3 bg-white text-xs text-gray-400">Settings</span>
          </div>
        </li>

        <!-- Class Management -->
        <li>
          <NavLink 
            :href="route('classes.index')" 
            :active="isRouteActive(['classes', 'classes.*'])"
            class="flex items-center p-3 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group"
            :class="[
              isCollapsed ? 'justify-center px-2' : 'px-3',
              isRouteActive(['classes', 'classes.*']) ? 'bg-blue-50 text-blue-600' : 'text-gray-600'
            ]"
          >
            <div class="flex items-center w-full" :class="isCollapsed ? 'justify-center' : ''">
              <i class="fa-solid fa-layer-group text-lg w-6 text-center" 
                 :class="isRouteActive(['classes', 'classes.*']) ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600'"></i>
              <span v-if="!isCollapsed" class="ml-3 whitespace-nowrap">Classes</span>
              <span v-if="!isCollapsed" class="ml-auto text-xs text-gray-400">8</span>
            </div>
          </NavLink>
        </li>

        <!-- Sections -->
        <li>
          <NavLink 
             
            :active="isRouteActive(['sections', 'sections.*'])"
            class="flex items-center p-3 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group"
            :class="[
              isCollapsed ? 'justify-center px-2' : 'px-3',
              isRouteActive(['sections', 'sections.*']) ? 'bg-blue-50 text-blue-600' : 'text-gray-600'
            ]"
          >
            <div class="flex items-center w-full" :class="isCollapsed ? 'justify-center' : ''">
              <i class="fa-solid fa-grip text-lg w-6 text-center" 
                 :class="isRouteActive(['sections', 'sections.*']) ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600'"></i>
              <span v-if="!isCollapsed" class="ml-3 whitespace-nowrap">Sections</span>
            </div>
          </NavLink>
        </li>

        <!-- Academic Years -->
        <li>
          <NavLink 
            :href="route('academic-years.index')" 
            :active="isRouteActive(['academic-years', 'academic-years.*'])"
            class="flex items-center p-3 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group"
            :class="[
              isCollapsed ? 'justify-center px-2' : 'px-3',
              isRouteActive(['academic-years', 'academic-years.*']) ? 'bg-blue-50 text-blue-600' : 'text-gray-600'
            ]"
          >
            <div class="flex items-center w-full" :class="isCollapsed ? 'justify-center' : ''">
              <i class="fa-solid fa-calendar-days text-lg w-6 text-center" 
                 :class="isRouteActive(['academic-years', 'academic-years.*']) ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600'"></i>
              <span v-if="!isCollapsed" class="ml-3 whitespace-nowrap">Academic Years</span>
            </div>
          </NavLink>
        </li>

        <!-- Attendance -->
        <li>
          <NavLink 
            
            :active="isRouteActive(['attendance', 'attendance.*'])"
            class="flex items-center p-3 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group"
            :class="[
              isCollapsed ? 'justify-center px-2' : 'px-3',
              isRouteActive(['attendance', 'attendance.*']) ? 'bg-blue-50 text-blue-600' : 'text-gray-600'
            ]"
          >
            <div class="flex items-center w-full" :class="isCollapsed ? 'justify-center' : ''">
              <i class="fa-solid fa-clipboard-check text-lg w-6 text-center" 
                 :class="isRouteActive(['attendance', 'attendance.*']) ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600'"></i>
              <span v-if="!isCollapsed" class="ml-3 whitespace-nowrap">Attendance</span>
            </div>
          </NavLink>
        </li>

        <!-- Settings -->
        <li>
          <NavLink 
            
            :active="isRouteActive(['settings', 'settings.*'])"
            class="flex items-center p-3 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group"
            :class="[
              isCollapsed ? 'justify-center px-2' : 'px-3',
              isRouteActive(['settings', 'settings.*']) ? 'bg-blue-50 text-blue-600' : 'text-gray-600'
            ]"
          >
            <div class="flex items-center w-full" :class="isCollapsed ? 'justify-center' : ''">
              <i class="fa-solid fa-gear text-lg w-6 text-center" 
                 :class="isRouteActive(['settings', 'settings.*']) ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600'"></i>
              <span v-if="!isCollapsed" class="ml-3 whitespace-nowrap">Settings</span>
            </div>
          </NavLink>
        </li>
      </ul>
    </nav>

    <!-- Bottom Section -->
    <div class="mt-auto pt-4 border-t border-gray-100">
      <!-- User Profile (when not collapsed) -->
      <div v-if="!isCollapsed" class="flex items-center p-3 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
        <div class="w-10 h-10 rounded-full bg-gradient-to-r from-blue-500 to-indigo-500 flex items-center justify-center text-white font-bold">
          A
        </div>
        <div class="ml-3 flex-1">
          <p class="text-sm font-medium text-gray-800">Admin User</p>
          <p class="text-xs text-gray-500">admin@school.edu</p>
        </div>
        <i class="fa-solid fa-ellipsis-vertical text-gray-400"></i>
      </div>

      <!-- Collapsed User Icon -->
      <div v-if="isCollapsed" class="flex justify-center">
        <div class="w-10 h-10 rounded-full bg-gradient-to-r from-blue-500 to-indigo-500 flex items-center justify-center text-white font-bold cursor-pointer hover:shadow-lg transition-shadow">
          A
        </div>
      </div>

      <!-- Collapse/Expand Button -->
      <button 
        @click="$emit('toggle-collapse')" 
        class="w-full mt-2 p-2 rounded-lg hover:bg-gray-100 transition-colors flex items-center justify-center text-gray-500"
        :class="isCollapsed ? 'justify-center' : 'justify-end'"
      >
        <i :class="isCollapsed ? 'fa-solid fa-arrow-right' : 'fa-solid fa-arrow-left'"></i>
      </button>
    </div>
  </aside>
</template>

<style scoped>
/* Custom scrollbar for sidebar */
aside::-webkit-scrollbar {
  width: 4px;
}

aside::-webkit-scrollbar-track {
  background: transparent;
}

aside::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 2px;
}

aside::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* Active link indicator */
.router-link-active {
  position: relative;
}

.router-link-active::before {
  content: '';
  position: absolute;
  left: 0;
  top: 50%;
  transform: translateY(-50%);
  width: 3px;
  height: 60%;
  background: #3b82f6;
  border-radius: 0 2px 2px 0;
}

/* Smooth hover transitions */
.nav-item {
  transition: all 0.2s ease-in-out;
}

.nav-item:hover {
  transform: translateX(4px);
}

/* Badge animations */
.badge-pulse {
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.05);
  }
  100% {
    transform: scale(1);
  }
}

/* Collapsed state styles */
.collapsed .nav-text {
  display: none;
}

.collapsed .nav-icon {
  margin-right: 0;
}

/* Tooltip for collapsed state */
.tooltip {
  position: relative;
}

.tooltip:hover::after {
  content: attr(data-tooltip);
  position: absolute;
  left: calc(100% + 10px);
  top: 50%;
  transform: translateY(-50%);
  background: #1f2937;
  color: white;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  white-space: nowrap;
  z-index: 50;
}
</style>