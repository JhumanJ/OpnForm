<template>
  <div class="main-layout min-h-screen flex flex-col h-full">
    <!--    <Navbar />-->
    <Header />

    <div class="flex w-full mx-auto max-w-[1440px] h-full">
      <sidebar />

      <slot class="flex-grow" />
    </div>
  </div>
</template>
<script setup lang="ts">
import Sidebar from "~/layouts/sidebar.vue"
import Header from "~/components/global/Header.vue"
</script>
