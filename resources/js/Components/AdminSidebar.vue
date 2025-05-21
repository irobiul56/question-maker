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
          <NavLink :href="route('dashboard')" :active="isRouteActive(['dashboard', 'dashboard.*'])"
            class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors"
            :class="isCollapsed ? 'justify-center px-2' : 'px-3'">
            <div class="flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0"
                :class="isCollapsed ? 'h-6 w-6' : 'h-5 w-5 mr-3'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
              </svg>
              <span v-if="!isCollapsed" class="whitespace-nowrap">Dashboard</span>
            </div>
          </NavLink>
        </li>

        <li>
          <NavLink href="#"
            class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors"
            :class="isCollapsed ? 'justify-center px-2' : 'px-3'">
            <div class="flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0"
                :class="isCollapsed ? 'h-6 w-6' : 'h-5 w-5 mr-3'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
              </svg>
              <span v-if="!isCollapsed" class="whitespace-nowrap">Questions</span>
            </div>
          </NavLink>
        </li>

        <li>
          <NavLink :href="route('class.index')" :active="isRouteActive(['class', 'class.*'])"
                class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors"
                :class="isCollapsed ? 'justify-center px-2' : 'px-3'">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0"
                    :class="isCollapsed ? 'h-6 w-6' : 'h-5 w-5 mr-3'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <span v-if="!isCollapsed" class="whitespace-nowrap">Class</span>
            </div>
        </NavLink>
        </li>

        <li>
          <NavLink :href="route('subject.index')" :active="isRouteActive(['subject', 'subject.*'])"
            class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors"
            :class="isCollapsed ? 'justify-center px-2' : 'px-3'">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0"
                    :class="isCollapsed ? 'h-6 w-6' : 'h-5 w-5 mr-3'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                </svg>
                <span v-if="!isCollapsed" class="whitespace-nowrap">Subject</span>
            </div>
        </NavLink>
        </li>

        <li>
          <NavLink :href="route('chapter.index')" :active="isRouteActive(['chapter', 'chapter.*'])"
            class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors"
            :class="isCollapsed ? 'justify-center px-2' : 'px-3'">
            <div class="flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0"
                :class="isCollapsed ? 'h-6 w-6' : 'h-5 w-5 mr-3'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              </svg>
              <span v-if="!isCollapsed" class="whitespace-nowrap">Chapter</span>
            </div>
      </NavLink>
        </li>

        <li>
          <NavLink href="#"
            class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors"
            :class="isCollapsed ? 'justify-center px-2' : 'px-3'">
            <div class="flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0"
                :class="isCollapsed ? 'h-6 w-6' : 'h-5 w-5 mr-3'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
              </svg>
              <span v-if="!isCollapsed" class="whitespace-nowrap">Topic</span>
            </div>
          </NavLink>
        </li>

        <li>
          <NavLink :href="route('type.index')" :active="isRouteActive(['type', 'type.*'])"
            class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors"
            :class="isCollapsed ? 'justify-center px-2' : 'px-3'">
            <div class="flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0"
                :class="isCollapsed ? 'h-6 w-6' : 'h-5 w-5 mr-3'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
              </svg>
              <span v-if="!isCollapsed" class="whitespace-nowrap">Type</span>
            </div>
          </NavLink>
        </li>

        <li>
          <NavLink :href="route('level.index')" :active="isRouteActive(['level', 'level.*'])"
            class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors"
            :class="isCollapsed ? 'justify-center px-2' : 'px-3'">
            <div class="flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0" 
                  :class="isCollapsed ? 'h-6 w-6' : 'h-5 w-5 mr-3'" 
                  fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M12 14l9-5-9-5-9 5 9 5zm0 0l6-3m-6 3l-6-3m6 3v5m0-5v5m6-5v5m-6-4v4"/>
              </svg>
              <span v-if="!isCollapsed" class="whitespace-nowrap">Level</span>
            </div>
          </NavLink>
        </li> 

        <li>
          <NavLink :href="route('board.index')" :active="isRouteActive(['board', 'board.*'])"
            class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors"
            :class="isCollapsed ? 'justify-center px-2' : 'px-3'">
            <div class="flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0" 
                  :class="isCollapsed ? 'h-6 w-6' : 'h-5 w-5 mr-3'" 
                  fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10M9 16h6" />
              </svg>
              <span v-if="!isCollapsed" class="whitespace-nowrap">Board</span>
            </div>
          </NavLink>
        </li>

        <li>
          <NavLink :href="route('education.index')" :active="isRouteActive(['education', 'education.*'])"
            class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors"
            :class="isCollapsed ? 'justify-center px-2' : 'px-3'">
            <div class="flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0" 
                  :class="isCollapsed ? 'h-6 w-6' : 'h-5 w-5 mr-3'" 
                  fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              </svg>
              <span v-if="!isCollapsed" class="whitespace-nowrap">Education Level</span>
            </div>
          </NavLink>
        </li>

      </ul>
    </nav>
  </aside>
</template>