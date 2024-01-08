<template>
  <div class="flex flex-col min-h-full border-t">
    <section class="py-12 sm:py-16 bg-gray-50 border-b border-gray-200">
      <div class="px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="text-center max-w-xl mx-auto">
          <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight text-gray-900">
            Form Templates
          </h1>
          <p class="text-gray-600 mt-4 text-lg font-normal">
            Our collection of beautiful templates to create your own forms!
          </p>
        </div>
      </div>
    </section>

    <templates-list :templates="templates" :loading="loading"/>

    <open-form-footer class="mt-8 border-t"/>
  </div>
</template>

<script setup>
import {loadAllTemplates} from "~/stores/templates.js";

defineRouteRules({
  swr: 3600
})

useOpnSeoMeta({
  title: 'Form Templates',
  description: 'Our collection of beautiful templates to create your own forms!'
})

const templatesStore = useTemplatesStore()
loadAllTemplates(templatesStore)

const loading = computed(() => templatesStore.loading)
const templates = computed(() => templatesStore.getAll)
</script>
