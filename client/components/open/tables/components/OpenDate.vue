<template>
  <span v-if="valueIsObject">
    <template v-if="value[0]">{{ value[0] }}</template>
    <template v-if="value[1]"><b>to</b> {{ value[1] }}</template>
  </span>
  <span v-else>
    {{ value }}
  </span>
</template>

<script>
export default {
  components: {},
  props: {
    value: {
      required: true
    }
  },
  data () {
    return {}
  },
  computed: {
    valueIsObject () {
      if (typeof this.value === 'object' && this.value !== null) {
        return true
      }
      return false
    }
  },
  mounted () {
  },
  methods: {
  }
}
</script>
