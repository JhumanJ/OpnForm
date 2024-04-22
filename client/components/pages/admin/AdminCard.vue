<template>
  <div class="w-full bg-white border border-gray-200 rounded-lg shadow flex flex-col">
    <div class="w-full flex border-b px-4 py-2">
      <Icon
        :name="props.icon"
        class="w-6 h-6 text-nt-blue"
      />
      <h3 class="text-md font-semibold ml-2">
        {{ props.title }}
      </h3>
    </div>
    <div class="p-4 flex-grow">
      <slot />
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  title: { type: String, required: true },
  icon: { type: String, required: true }
})
</script>
