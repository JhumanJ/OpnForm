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
    const apiUrl = useAppConfig().notion.worker
    const {data} = await useFetch(`${apiUrl}/page/${props.pageId}`)

    return {
      apiUrl: useAppConfig().notion.worker,
      blockMap: data,
    }
  }
}
</script>

<style lang="scss">
@import "vue-notion/src/styles.css";

.notion-blue {
  @apply text-nt-blue;
}
</style>
