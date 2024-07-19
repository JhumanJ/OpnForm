<template>
  <span
    v-if="value"
    @click="test"
    class="-mb-2"
  >
    <template v-if="matrixData">
      <div
        v-for="(data, index) in matrixData"
        :key="data.label+data.value"
        class="mr-2 text-gray-700 bg-gray-100 dark:text-gray-300 rounded-md flex px-2 text-sm w-max"
      >
        <span class="py-0.5 pr-1 border-r border-gray-300">{{data.label}}</span>
        <span class="py-0.5 pl-1">{{data.value}}</span>
      </div>

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
