<template>
  <div class="flex mt-6">
    <div class="w-full md:w-2/3 md:mx-auto md:max-w-md">
      <img alt="Nice plant as we have nothing else to show!" :src="asset('img/icons/plant.png')" class="w-56 mb-5">

      <h1 class="mb-4 font-semibold text-3xl text-gray-900">
        {{ $t('page_not_found') }}
      </h1>

      <div class="links">
        <router-link :to="{ name: 'welcome' }" class="hover:underline text-gray-700">
          {{ $t('go_home') }}
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'NotFound'
}
</script>
