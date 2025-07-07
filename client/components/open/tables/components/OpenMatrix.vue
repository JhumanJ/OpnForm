<template>
  <span
    v-if="value"
    class="-mb-2 flex flex-wrap gap-1"
  >
    <template v-if="matrixData">
      <UBadge
        v-for="(data) in matrixData"
        :key="data.label+data.value"
        size="sm"
        color="neutral"
        variant="soft"
      >
        <span class="py-0.5 pr-1 border-r border-gray-300">{{ data.label }}</span>
        <span class="py-0.5 pl-1">{{ data.value }}</span>
      </UBadge>

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
            return { label, value }
        })
      }
      return null
    },
  },
}
</script>
