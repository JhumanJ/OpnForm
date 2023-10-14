<template>
  <span class="-mb-2" v-if="value">
    <template v-if="valueIsObject">
      <open-tag v-for="val,index in value" :key="index" :opt="val" />
    </template>
    <open-tag v-else :opt="value" />
  </span>
</template>

<script>
import OpenTag from './OpenTag.vue'

export default {
  components: { OpenTag },
  props: {
    value: {
      type: Object | null,
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
  methods: {}
}
</script>
