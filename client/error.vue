<template>
  <div>
    <NuxtLayout>
      <div class="flex mt-6">
        <div class="w-full md:w-2/3 md:mx-auto md:max-w-md">
          <img alt="Nice plant as we have nothing else to show!" src="/img/icons/plant.png" class="w-56 mb-5"/>

          <h1 class="mb-4 font-semibold text-3xl text-gray-900">
            Page Not Found
          </h1>

          <div class="links">
            <NuxtLink :to="{ name: 'index' }" class="hover:underline text-gray-700">
              Go Home
            </NuxtLink>
          </div>
        </div>
      </div>
    </NuxtLayout>
  </div>
</template>

<script setup>
useOpnSeoMeta({
  title: '404 - Page not found'
})
</script>
