<template>
  <notion-renderer
    v-if="!loading"
    :block-map="blockMap"
  />
  <div
    v-else
    class="p-6 flex items-center justify-center"
  >
    <loader class="w-6 h-6" />
  </div>
</template>

<script>
import { NotionRenderer } from "vue-notion"

export default {
  name: "NotionPage",
  components: { NotionRenderer },
  props: {
    blockMap: {
      type: Object,
    },
    loading: {
      type: Boolean,
      required: true,
    },
  },
}
</script>

<style lang="scss">
@import "vue-notion/src/styles.css";

.notion-blue {
  @apply text-nt-blue;
}
</style>
