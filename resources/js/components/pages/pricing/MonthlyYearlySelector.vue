<template>
  <div class="border border-gray-300 rounded-xl flex p-1 relative">
    <button class="font-semibold block flex-grow cursor-pointer">
      <div class="p-2 px-3 rounded-lg transition-colors" :class="{'bg-blue-500 text-white':!value}"
           @click="set(false)"
      >
        Monthly
      </div>
    </button>
    <button class="font-semibold block flex-grow cursor-pointer" @click="set(true)">
      <div class="p-2 px-4 rounded-lg transition-colors" :class="{'bg-blue-500 text-white':value}">
        Yearly
      </div>
    </button>
    <div class="absolute hidden sm:block right-0 text-gray-500 text-xs top-0 translate-x-full translate-y-full pl-2">
      → Save almost 2 months!
    </div>
  </div>
</template>

<script>
export default {

  components: {},
  props: {
    value: {
      type: Boolean,
      default: false
    }
  },
  data: () => ({}),

  computed: {},

  methods: {
    set (value) {
      this.$emit('input', value)
    }
  }
}
</script>
