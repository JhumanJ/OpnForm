<template>
  <div class="main-layout min-h-screen flex flex-col h-full">
    <Header v-if="isFormsPage" />
    <Navbar v-else />

    <div
      class="w-full mx-auto max-w-[1440px] h-full"
      :class="{ flex: isFormsPage }"
    >
      <sidebar v-if="isFormsPage" />

      <slot class="flex-grow" />
    </div>
  </div>
</template>

<script setup lang="ts">
import Sidebar from "~/layouts/sidebar.vue"
import Header from "~/components/global/Header.vue"
import Navbar from "~/components/global/Navbar.vue"
import { useRoute } from "vue-router"
import { computed } from "vue"

const route = useRoute()

const isFormsPage = computed(() => route.path.includes("forms"))
</script>
