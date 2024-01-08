<template>
  <div class="flex flex-col min-h-full">
    <breadcrumb :path="breadcrumbs"/>

    <p v-if="type === null || !type" class="text-center my-4">
      We could not find this type.
    </p>
    <template v-else>
      <section class="py-12 sm:py-16 bg-gray-50 border-b border-gray-200">
        <div class="px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
          <div class="text-center mx-auto">
            <div class="font-semibold sm:w-full text-blue-500 mb-3">
              {{ type.name }}
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight text-gray-900">
              {{ type.meta_title }}
            </h1>
            <p class="max-w-xl mx-auto text-gray-600 mt-4 text-lg font-normal">
              {{ type.meta_description }}
            </p>
          </div>
        </div>
      </section>


      <templates-list :templates="templates" :filter-types="false" :show-industries="false">
        <template #before-lists>
          <section class="py-12 bg-white border-t border-gray-200 sm:py-16">
            <div class="px-4 mx-auto sm:px-6 lg:px-8 max-w-7xl">
              <p class="text-gray-600 font-normal">
                {{ type.description }}
              </p>
            </div>
          </section>
        </template>
      </templates-list>
    </template>

    <open-form-footer class="mt-8 border-t"/>
  </div>
</template>

<script setup>
import {computed} from 'vue'
import OpenFormFooter from '../../../components/pages/OpenFormFooter.vue'
import Breadcrumb from '~/components/global/Breadcrumb.vue'
import {loadAllTemplates} from "~/stores/templates.js";

defineRouteRules({
  swr: 3600
})

const route = useRoute()
const authStore = useAuthStore()
const templatesStore = useTemplatesStore()

loadAllTemplates(templatesStore)

// Computed
const authenticated = computed(() => authStore.check)
const user = computed(() => authStore.user)
const templates = computed(() => templatesStore.getAll.filter((item) => {
  return (item.types && item.types.length > 0) ? item.types.includes(route.params.slug) : false
}))
const breadcrumbs = computed(() => {
  if (!type) {
    return [{route: {name: 'templates'}, label: 'Templates'}]
  }
  return [{route: {name: 'templates'}, label: 'Templates'}, {label: type.value.name}]
})

const type = computed(() => templatesStore.types.get(route.params.slug))

useOpnSeoMeta({
  title: () => {
    if (!type.value) return 'Form Templates'
    if (type.value.meta_title.length > 60) {
      return type.value.meta_title
    }
    return type.value.meta_title
  },
  description: () => type.value ? type.value.meta_description: 'Our collection of beautiful templates to create your own forms!'
})
useHead({
  titleTemplate: (titleChunk) => {
    // Disable title template for longer titles
    if (type.value
      && type.value.meta_title.length < 60
      && !type.value.meta_title.toLowerCase().includes('opnform')
    ) {
      return titleChunk ? `${titleChunk} - OpnForm` : 'Form Templates - OpnForm'
    }
    return titleChunk ? titleChunk : 'Form Templates - OpnForm'
  }
})

</script>

<style lang='scss'>
.nf-text {
  @apply space-y-4;
  h2 {
    @apply text-sm font-normal tracking-widest text-gray-500 uppercase;
  }

  p {
    @apply font-normal leading-7 text-gray-900 dark:text-gray-100;
  }

  ol {
    @apply list-decimal list-inside;
  }

  ul {
    @apply list-disc list-inside;
  }
}
</style>

