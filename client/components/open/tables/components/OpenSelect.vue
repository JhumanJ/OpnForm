<template>
  <span v-if="value" class="-mb-2">
    <template v-if="valueIsObject">
      <open-tag v-for="(val,index) in value" :key="index+val" :opt="val" />
    </template>
    <open-tag v-else :opt="value" />
  </span>
</template>

<script>
import OpenTag from './OpenTag.vue'

export default {
  components: { OpenTag },
  props: {
    value: {}
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
}
</script>
