<template>
  <span
    v-if="value"
    class="flex flex-wrap gap-1"
  >
    <template v-if="matrixData">
      <UBadge
        v-for="(data) in matrixData"
        :key="data.label+data.value"
        size="sm"
        color="neutral"
        variant="soft"
      >
        <span class="border-r pr-1 border-neutral-300">{{ data.label }}</span>
        <span>{{ data.value }}</span>
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
