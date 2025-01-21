<template>
  <header class="w-full h-[68px] border-b">
    <div class="max-w-[1440px] m-auto h-full flex items-center">
      <div class="h-full w-[72px] flex justify-center items-center border-r">
        <button
          v-if="user"
          class="bg-[#1C1D35] rounded w-6 h-6"
        />
      </div>
      <div class="px-6">
        <p class="text-sm font-medium">
          Contact Form
        </p>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { computed } from "vue"

const authStore = useAuthStore()
const user = computed(() => authStore.user)
</script>
