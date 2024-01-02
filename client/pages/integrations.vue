<template>
  <div>
    <div class="mt-6 flex flex-col">
      <div class="w-full md:max-w-3xl md:mx-auto px-4 md:pt-16">
        <h1 class="sm:text-5xl mb-4">
          Integrations
        </h1>
        <NotionPage :block-map="blockMap" :loading="loading"  />
      </div>
    </div>
    <open-form-footer/>
  </div>
</template>

<script setup>
// metaTitle: { type: String, default: 'Integrations' },
// metaDescription: { type: String, default: 'You can connect your OpnForms to other services via our two integrations: Zapier and Webhooks. Use our integrations to automate your various workflows.' }

import {useNotionPagesStore} from "~/stores/notion_pages.js";
import {computed} from "vue";

const notionPageStore = useNotionPagesStore()
await notionPageStore.load('492c2bbb31404481b9faaaf407e59640')

const loading = computed(() => notionPageStore.loading)
const blockMap = computed(() => notionPageStore.getByKey('492c2bbb31404481b9faaaf407e59640'))
</script>

<style lang="scss">
.integration-page {
  .notion-asset-wrapper {
    max-width: 200px;
  }
}
</style>
