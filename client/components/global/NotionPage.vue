<template>
  <notion-renderer :block-map="blockMap"/>
</template>

<script>
import {NotionRenderer} from 'vue-notion'

export default {
  name: 'NotionPage',
  components: {NotionRenderer},
  props: {
    pageId: {
      type: String,
      required: true
    }
  },

  async setup(props) {
    const apiUrl = useConfig().notion.worker
    const {data} = await useFetch(`${apiUrl}/page/${props.pageId}`)

    return {
      apiUrl: useConfig().notion.worker,
      blockMap: data,
    }
  }
}
</script>

<style lang="scss">
@import "styles.css";

.notion-blue {
  @apply text-nt-blue;
}
</style>
