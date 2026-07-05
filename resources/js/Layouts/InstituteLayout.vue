<script setup>
import { ref } from 'vue';
import InstituteNavbar from '@/Components/InstituteNavbar.vue';
import InstituteSidebar from '../Components/InstituteSidebar.vue';

const isCollapsed = ref(false);
const toggleSidebar = () => {
    isCollapsed.value = !isCollapsed.value;
};
</script>

<template>
  <InstituteNavbar @toggle-sidebar="toggleSidebar" />
  <div class="flex">
    <InstituteSidebar :is-collapsed="isCollapsed" />
    <slot />
  </div>
</template>

<style scoped>
</style>
