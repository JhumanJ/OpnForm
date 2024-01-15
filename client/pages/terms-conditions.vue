<template>
  <div>
    <div class="mt-6 flex flex-col">
      <div class="w-full md:max-w-3xl md:mx-auto px-4 md:pt-16">
        <h1 class="sm:text-5xl">
          Terms & Conditions
        </h1>
        <NotionPage :block-map="blockMap" :loading="loading"  />
      </div>
    </div>
    <open-form-footer/>
  </div>
</template>

<script setup>
import {useNotionPagesStore} from "~/stores/notion_pages.js";
import {computed} from "vue";

useOpnSeoMeta({
  title: 'Terms & Conditions'
})
defineRouteRules({
  swr: 3600
})

const notionPageStore = useNotionPagesStore()
await notionPageStore.load('246420da2834480ca04047b0c5a00929')

const loading = computed(() => notionPageStore.loading)
const blockMap = computed(() => notionPageStore.getByKey('246420da2834480ca04047b0c5a00929'))
</script>
