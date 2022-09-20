<template>
  <div>
    <Motion
      v-model="value"
      :options="{
        duration: 150,
      }"
      :trigger="[
        'bg-gray-200 border-gray-300 duration-100 dark:bg-gray-700 dark:border-gray-600',
        'bg-gray-200 dark:bg-gray-700',
        'bg-nt-blue border-nt-blue',
        'bg-nt-blue duration-100',
      ]"
      class="inline-flex items-center h-6 w-12 p-1 border rounded-full cursor-pointer focus:outline-none"
      @click="$emit('input',!internalValue)"
    >
      <Motion
        v-model="internalValue"
        tag="span"
        :options="{
          duration: 150,
        }"
        :trigger="[
          'translate-x-0 duration-150',
          'rounded-2xl scale-75 duration-100',
          'translate-x-6 duration-100',
          'scale-100 duration-150',
        ]"
        class="inline-block h-4 w-4 rounded-full bg-white dark:bg-gray-500 shadow"
      />
    </Motion>
  </div>
</template>

<script>
import Motion from 'tinymotion'
export default {
  name: 'VSwitch',
  components: { Motion },

  props: {
    value: { type: Boolean, default: false }
  },

  data () {
    return {
      internalValue: this.value
    }
  },

  computed: {
    sizeClasses () {
      if (this.size === 'small') {
        return 'w-3 h-3'
      }
      return 'w-5 h-5'
    }
  },

  watch: {
    value (val) {
      this.internalValue = val
    }
  },

  mounted () {
    this.internalValue = this.value
  },

  methods: {
  }
}
</script>

<style scoped>
.translate-x-6 {
  --tw-translate-x: 1.4rem !important;
}
</style>
