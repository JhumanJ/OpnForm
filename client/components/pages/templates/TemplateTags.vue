<template>
  <div>
    <template v-if="displayAll">
      <span v-if="template.is_new"
            class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-white bg-blue-500 rounded-full"
      >
        <svg aria-hidden="true" class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
             fill="currentColor"
        >
          <path fill-rule="evenodd"
                d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z"
                clip-rule="evenodd"
          />
        </svg>
        New
      </span>
      <span v-for="item in types" :key="item.slug"
            class="inline-flex items-center rounded-full bg-gray-50 dark:bg-gray-800 dark:text-gray-400 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10"
      >
        {{ item.name }}
      </span>
      <span v-for="item in industries" :key="item.slug"
            class="inline-flex items-center rounded-full bg-blue-50 dark:bg-blue-900 dark:text-gray-400 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10"
      >
        {{ item.name }}
      </span>
    </template>
    <template v-else>
      <span v-if="types.length > 0"
            class="inline-flex items-center rounded-full bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10"
      >
        {{ types[0].name }} <template v-if="types.length > 1">+{{ types.length - 1 }}</template>
      </span>
      <span v-if="industries.length > 0"
            class="inline-flex items-center rounded-full bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10"
      >
        {{ industries[0].name }} <template v-if="industries.length > 1">+{{ industries.length - 1 }}</template>
      </span>
    </template>
  </div>
</template>

<script setup>

const props = defineProps({
    template: {
      type: Object,
      required: true
    },
    displayAll: {
      type: Boolean,
      default: false
    }
  })

const templatesStore = useTemplatesStore()
const types = computed(() => templatesStore.getTemplateTypes(props.template.types))
const industries = computed(() => templatesStore.getTemplateIndustries(props.template.industries))
</script>
