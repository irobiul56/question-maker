<script setup>
import { ref } from 'vue';
import AdminNavbar from '@/Components/AdminNavbar.vue';
import AdminSidebar from '@/Components/AdminSidebar.vue';

const isCollapsed = ref(false);
const toggleSidebar = () => {
    isCollapsed.value = !isCollapsed.value;
};
</script>

<template>
  <AdminNavbar @toggle-sidebar="toggleSidebar" />
  <div class="flex">
    <AdminSidebar :is-collapsed="isCollapsed" />
    <slot />
  </div>
</template>

<style scoped>
</style>
