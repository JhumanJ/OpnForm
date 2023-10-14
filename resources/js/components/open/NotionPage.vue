<template>
  <notion-renderer v-if="!loading" :block-map="blockMap" />
  <div v-else class="my-10 py-20 flex items-center justify-center">
    <loader class="h-6 w-6 text-nt-blue mx-auto" />
  </div>
</template>

<script>
import { NotionRenderer, getPageBlocks } from 'vue-notion'

export default {
  name: 'NotionPage',
  components: { NotionRenderer },
  props: {
    pageId: {
      type: String,
      required: true
    }
  },
  data () {
    return {
      loading: false,
      blockMap: null
    }
  },

  computed: {
    apiUrl: () => window.config.notion.worker
  },

  watch: {},

  mounted () {
    // get Notion blocks from the API via a Notion pageId
    this.loading = true
    getPageBlocks(this.pageId, this.apiUrl).then((blocks) => {
      this.blockMap = blocks
      this.loading = false
    })
  },

  methods: {}
}
</script>

<style lang="scss">
@import "vue-notion/src/styles.css"; /* optional Notion-like styles */

.notion-blue {
  @apply text-nt-blue;
}
</style>
