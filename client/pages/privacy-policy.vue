<template>
  <div>
    <div class="mt-6 flex flex-col">
      <div class="w-full md:max-w-3xl md:mx-auto px-4 md:pt-16">
        <h1 class="sm:text-5xl">
          Privacy Policy
        </h1>
        <NotionPage
          :block-map="page.blocks"
          :loading="loading"
        />
      </div>
    </div>
    <open-form-footer />
  </div>
</template>

<script setup>
definePageMeta({
  middleware: ["self-hosted"]
})
useOpnSeoMeta({
  title: "Privacy Policy",
})
defineRouteRules({
  swr: 3600
})

const pageId = '9c97349ceda7455aab9b341d1ff70f79'
const notionCmsStore = useNotionCmsStore()
const loading = computed(() => notionCmsStore.loading)
await notionCmsStore.loadPage(pageId)
const page = notionCmsStore.getPage(pageId)
</script>
