<template>
  <div class="main-layout min-h-screen flex flex-col">
    <div class="w-full p-3 bg-blue-500 text-center font-weight-bolder relative cursor-pointer group" v-if="['index','home','templates'].includes($route.name)">
      <a href="https://www.producthunt.com/posts/opnform?ref=opnform" target="_blank" class="absolute inset-0"/>
      <p class="text-white">We're live on ProductHunt<span class="px-2 -my-2 text-xl inline-block group-hover:animate-bounce">🚀</span>Thank you for supporting us!</p>
    </div>
    <Navbar />

    <slot class="flex-grow">
    </slot>
  </div>
</template>
<script setup lang="ts">
</script>
