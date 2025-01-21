<template>
  <div class="border-r p-3 h-full">
    <ul class="flex flex-col gap-5">
      <li
        v-if="!isSubSidebar"
        class="cursor-pointer border border-[#E4E8EA] rounded-lg w-12 h-9 flex justify-center"
      >
        <nuxt-link
          :to="{ name: 'forms-create' }"
          class="hover:no-underline m-auto"
        >
          <img
            src="/img/sidebar/search.svg"
            alt="icon"
          >
        </nuxt-link>
      </li>
      <li
        v-for="tab in tabsList"
        :key="tab.label"
      >
        <nuxt-link
          :to="{ name: tab.route, params: tab.params ?? {} }"
          :class="isSubSidebar ? 'p-2.5' : 'flex flex-col gap-2 w-12 py-2.5'"
          class="flex items-center transition-all duration-300 ease-in gap-3 rounded-lg hover:no-underline hover:bg-[#F6F8FA]"
          :active-class="
            isSubSidebar
              ? 'text-blue-600 bg-[#F6F8FA]'
              : 'text-blue-600 bg-[#F6F8FA] border'
          "
        >
          <img
            alt="icon"
            :src="tab.iconPath"
            :class="{
              'filter-none': $route.name === tab.route,
              grayscale: $route.name !== tab.route,
            }"
          >
          <span
            class="text-center"
            :class="[isSubSidebar ? 'text-sm' : 'text-[8px]']"
          >
            {{ tab.name }}
          </span>
        </nuxt-link>
      </li>
      <li
        v-if="!isSubSidebar"
        class="py-2.5 bg-[#2A6EFF] rounded-lg cursor-pointer"
      >
        <nuxt-link
          :to="{ name: 'forms-create' }"
          class="hover:no-underline flex flex-col items-center gap-[10px]"
        >
          <img
            src="/img/sidebar/plus.svg"
            alt="icon"
          >
          <span class="text-[8px] text-white">Create</span>
        </nuxt-link>
      </li>
    </ul>
  </div>
</template>

<script setup>
const props = defineProps({
  tabsList: { type: Array, default: null },
  isSubSidebar: { type: Boolean, default: false },
})
</script>
