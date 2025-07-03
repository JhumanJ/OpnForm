<template>
  <div class="dashboard-layout min-h-screen bg-gray-50">
    <!-- Sidebar -->
    <AppSidebar />
    
    <!-- Main content area -->
    <main class="sm:pl-58">
      <!-- Content wrapper with proper spacing -->
      <div class="min-h-screen bg-white">
        <slot />
      </div>
    </main>
  </div>
</template>

<script setup>
import AppSidebar from "~/components/layouts/AppSidebar.vue"
</script>

<style scoped>
.dashboard-layout {
  /* Ensure proper layout structure */
}
</style> 