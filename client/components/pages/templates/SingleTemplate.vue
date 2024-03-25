<template>
  <div v-if="template" class="relative group">
    <div v-if="template.is_new" class="absolute top-0 right-0 p-3 z-10">
      <span
        class="inline-flex items-center gap-1 rounded-full bg-blue-500 px-2 py-1 text-xs font-medium text-white"
      >
        <svg aria-hidden="true" class="h-3 w-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
             fill="currentColor"
        >
          <path fill-rule="evenodd"
                d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z"
                clip-rule="evenodd"
          />
        </svg>
        New
      </span>
    </div>

    <div class="aspect-[4/3] rounded-lg shadow-sm overflow-hidden">
      <img class="group-hover:scale-110 transition-all duration-200 h-full object-cover w-full" v-if="template.image_url"
           :src="template.image_url" alt="" width="450px"
      />
    </div>
    <p
      class="text-lg font-semibold leading-tight tracking-tight text-gray-900 mt-4 group-hover:text-blue-500 transition-all duration-150"
    >
      {{ template.name }}
    </p>
    <p class="line-clamp-2 mt-2 text-sm font-normal text-gray-600">
      {{ cleanQuotes(template.short_description) }}
    </p>
    <template-tags :template="template"
                   class="flex mt-4 items-center flex-wrap gap-3"
    />
    <NuxtLink :to="{params:{slug:template.slug},name:'templates-slug'}" title="">
      <span class="absolute inset-0" aria-hidden="true" />
    </NuxtLink>
  </div>
</template>

<script>
import TemplateTags from './TemplateTags.vue'

export default {
  components: { TemplateTags },

  props: {
    template: {
      type: Object
    }
  },

  methods: {
    cleanQuotes (str) {
      // Remove starting and ending quotes if any
      return (str) ? str.replace(/^"/, '').replace(/"$/, '') : ''
    }
  }
}
</script>
