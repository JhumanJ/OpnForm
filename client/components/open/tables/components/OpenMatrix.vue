<template>
  <span
    v-if="value"
    @click="test"
    class="-mb-2"
  >
    <template v-if="matrixData">
      <open-tag
        v-for="(data, index) in matrixData"
        :key="data.label"
        :opt="data.label + ': '+ data.value"
      />
    </template>
    <open-tag
      v-else
      :opt="value"
    />
  </span>
</template>

<script>
import OpenTag from "./OpenTag.vue"

export default {
  components: { OpenTag },
  props: {
    value: {
      type: Object,
    },
  },

  data() {
    return {}
  },

  computed: {
    matrixData() {
      if (typeof this.value === "object" && this.value !== null) {
        return Object.entries(this.value).map(([label, value]) => {
            return { label, value };
        });
      }
      return null
    },
  },

  mounted()
  {
    console.log( (this.value))
  },
  methods: {
    test(){
      console.log(this.matrixData)
      
    }
  }
}
</script>
