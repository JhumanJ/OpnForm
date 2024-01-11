<template>
  <div>
    <div class="mt-6 flex flex-col">
      <div class="w-full md:max-w-3xl md:mx-auto px-4 md:pt-16">
        <h1 class="sm:text-5xl">
          Privacy Policy
        </h1>
        <NotionPage :block-map="blockMap" :loading="loading"  />
      </div>
    </div>
    <open-form-footer />
  </div>
</template>

<script setup>
import {useNotionPagesStore} from "~/stores/notion_pages.js";
import {computed} from "vue";

useOpnSeoMeta({
  title: 'Privacy Policy'
})
defineRouteRules({
  swr: 3600
})

const notionPageStore = useNotionPagesStore()
await notionPageStore.load('9c97349ceda7455aab9b341d1ff70f79')

const loading = computed(() => notionPageStore.loading)
const blockMap = computed(() => notionPageStore.getByKey('9c97349ceda7455aab9b341d1ff70f79'))
</script>
