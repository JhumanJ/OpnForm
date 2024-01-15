<template>
  <div class="flex flex-col w-full bg-white rounded-lg shadow"
       :class="{'px-4 py-8 sm:px-6 md:px-8 lg:px-10':padding}"
  >
    <div v-if="title" class="self-center mb-6 text-xl font-light text-gray-900 sm:text-3xl font-bold dark:text-white">
      {{ title }}
    </div>
    <slot />
  </div>
</template>

<script>
export default {
  name: 'Card',

  props: {
    padding: {
      type: Boolean,
      default: true
    },
    title: {
      type: String,
      default: null
    }
  }
}
</script>
