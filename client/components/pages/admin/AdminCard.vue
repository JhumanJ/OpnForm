<template>
  <div class="w-full bg-white border border-gray-200 rounded-lg shadow flex flex-col">
    <collapse
      v-model="show"
      class="p-2 w-full"
    >
      <template #title>
        <div class="w-full flex  px-4 py-2">
          <Icon
            :name="props.icon"
            class="w-6 h-6 text-nt-blue"
          />
          <h3 class="text-md font-semibold ml-2">
            {{ props.title }}
          </h3>
        </div>
      </template>
      <div class="p-4 flex-grow">
        <slot />
      </div>
    </collapse>
  </div>
</template>

<script setup>
const props = defineProps({
  title: { type: String, required: true },
  icon: { type: String, required: true }
})

const show = ref(true)
</script>
