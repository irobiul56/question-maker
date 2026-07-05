<!-- UserSidebar.vue -->
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
  <aside class="min-h-screen bg-white shadow-lg p-4 transition-all duration-300 border-r border-gray-100 flex flex-col"
    :class="isCollapsed ? 'w-20' : 'w-64'">
    <nav>
      <ul class="space-y-2">
      <li>
          <NavLink :href="route('userdashboard')" :active="isRouteActive(['userdashboard', 'userdashboard.*'])"
            class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors"
            :class="isCollapsed ? 'justify-center px-2' : 'px-3'">
            <div class="flex items-center">
              <i class="fa-solid fa-chart-column mr-2"></i>
              <span v-if="!isCollapsed" class="whitespace-nowrap">ড্যাশবোর্ড</span>
            </div>
          </NavLink>
        </li>

        <li>
          <NavLink :href="route('qstIndex')" :active="isRouteActive(['qstIndex', 'qstIndex.*'])"
            class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors"
            :class="isCollapsed ? 'justify-center px-2' : 'px-3'">
            <div class="flex items-center">
              <i class="fa-regular fa-circle-question mr-2"></i>
              <span v-if="!isCollapsed" class="whitespace-nowrap">প্রশ্ন তৈরি</span>
            </div>
          </NavLink>
        </li>

        <li>
          <NavLink :href="route('my-questions.index')" :active="isRouteActive(['my-questions', 'my-questions.*'])"
            class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors"
            :class="isCollapsed ? 'justify-center px-2' : 'px-3'">
            <div class="flex items-center">
              <i class="fa-solid fa-file-circle-question mr-2"></i>
              <span v-if="!isCollapsed" class="whitespace-nowrap">আমার তৈরি প্রশ্ন</span>
            </div>
          </NavLink>
        </li>


        <li>
          <NavLink :href="route('contactus')" :active="isRouteActive(['contactus', 'contactus.*'])"
            class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors"
            :class="isCollapsed ? 'justify-center px-2' : 'px-3'">
            <div class="flex items-center">
              <i class="fa-solid fa-phone mr-2"></i>
              <span v-if="!isCollapsed" class="whitespace-nowrap">যোগাযোগ</span>
            </div>
          </NavLink>
        </li>

       <li>
          <NavLink :href="route('opinion')" :active="isRouteActive(['opinion', 'opinion.*'])"
            class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors"
            :class="isCollapsed ? 'justify-center px-2' : 'px-3'">
            <div class="flex items-center">
              <i class="fa-solid fa-envelope-open-text mr-2"></i>
              <span v-if="!isCollapsed" class="whitespace-nowrap">মতামত</span>
            </div>
          </NavLink>
        </li>

        <li>
          <NavLink
            class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors"
            :class="isCollapsed ? 'justify-center px-2' : 'px-3'">
            <div class="flex items-center">
              <i class="fa-solid fa-circle-info mr-2"></i>
              <span v-if="!isCollapsed" class="whitespace-nowrap">নির্দেশিকা</span>
            </div>
          </NavLink>
        </li>
        

      </ul>
    </nav>
  </aside>
</template>