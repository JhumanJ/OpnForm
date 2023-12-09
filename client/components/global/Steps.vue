<template>
  <div class="py-4" :class="{'border-b-2':borderBottom}">
    <div class="uppercase tracking-wide text-xs font-bold dark:text-gray-400 text-gray-500 mb-1 leading-tight">
      Step: {{ Math.min(current + 1, steps.length) }} of {{ steps.length }}
    </div>
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
      <div class="flex-1">
        <div class="text-lg font-bold dark:text-gray-300 text-gray-700 leading-tight">
          {{ steps[current] ? steps[current] : 'Complete!' }}
        </div>
      </div>

      <div class="flex items-center md:w-64">
        <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full mr-2">
          <div class="rounded-full bg-nt-blue text-xs leading-none h-2 text-center text-white transition-all"
               :style="{'width': parseInt(current / steps.length * 100) +'%', 'min-width': '8px'}"
          />
        </div>
        <div class="text-xs w-10 text-gray-600 dark:text-gray-400" v-text="parseInt(current / steps.length * 100) +'%'" />
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Steps',

  props: {
    steps: {
      type: Array,
      required: true
    },
    borderBottom: {
      type: Boolean,
      default: true
    },
    current: {
      type: Number,
      default: 0
    }
  }
}
</script>
