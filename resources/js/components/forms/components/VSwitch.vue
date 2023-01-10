<template>
  <div @click="onClick">
    <div class="inline-flex items-center h-6 w-12 p-1 bg-gray-300 border rounded-full cursor-pointer focus:outline-none transition-all transform ease-in-out duration-100" :class="{'bg-nt-blue': internalValue}">
      <div class="inline-block h-4 w-4 rounded-full bg-white shadow transition-all transform ease-in-out duration-150 rounded-2xl scale-100" :class="{'translate-x-5.5': internalValue}" />
    </div>
  </div>
</template>

<script>
export default {
  name: 'VSwitch',
  components: { },

  props: {
    value: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false }
  },

  data () {
    return {
      internalValue: this.value
    }
  },

  computed: {},

  watch: {
    value (val) {
      this.internalValue = val
    }
  },

  mounted () {
    this.internalValue = this.value
  },

  methods: {
    onClick () {
      if(this.disabled) return
      this.$emit('input', !this.internalValue)
      this.internalValue = !this.internalValue
    }
  }
}
</script>