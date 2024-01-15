<template>
  <div>
    <section class="bg-white py-12">
      <div class="px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 sm:gap-6 relative z-20">
          <div class="flex items-center gap-4">
            <div class="flex-1 sm:flex-none">
              <select-input v-model="selectedType" name="type" v-if="filterTypes"
                            :options="typesOptions" class="w-full sm:w-auto md:w-56"
              />
            </div>
            <div class="flex-1 sm:flex-none">
              <select-input v-model="selectedIndustry" name="industry" v-if="filterIndustries"
                            :options="industriesOptions" class="w-full sm:w-auto md:w-56"
              />
            </div>
          </div>
          <div class="flex-1 w-full md:max-w-xs">
            <text-input autocomplete="off" name="search" v-model="search" placeholder="Search..."/>
          </div>
        </div>

        <div v-if="loading" class="text-center mt-4">
          <Loader class="h-6 w-6 text-nt-blue mx-auto"/>
        </div>
        <p v-else-if="enrichedTemplates.length === 0" class="text-center mt-4">
          No templates found.
        </p>
        <div v-else class="relative z-10">
          <div class="grid grid-cols-1 mt-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 sm:gap-y-12">
            <single-template v-for="template in enrichedTemplates" :key="template.id" :template="template"/>
          </div>
        </div>
      </div>
    </section>

    <slot name="before-lists"/>

    <section class="py-12 bg-white border-t border-gray-200 sm:py-16" v-if="showTypes">
      <div class="px-4 mx-auto sm:px-6 lg:px-8 max-w-7xl">
        <div class="flex items-center justify-between">
          <h4 class="text-xl font-bold tracking-tight text-gray-900 sm:text-2xl">
            All Types
          </h4>
          <v-button :to="{name:'templates'}" color="white" size="small" :arrow="true" v-if="$route.name !== 'templates'">
            View All Templates
          </v-button>
        </div>

        <div class="grid grid-cols-1 gap-x-8 gap-y-4 mt-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
          <NuxtLink v-for="row in types" :key="row.slug"
                       :to="{params: {slug:row.slug}, name:'templates-types-slug'}"
                       :title="row.name"
                       class="text-gray-600 dark:text-gray-400 transition-colors duration-300 hover:text-nt-blue"
          >
            {{ row.name }}
          </NuxtLink>
        </div>
      </div>
    </section>

    <section class="py-12 bg-white border-t border-gray-200 sm:py-16" v-if="showIndustries">
      <div class="px-4 mx-auto sm:px-6 lg:px-8 max-w-7xl">
        <div class="flex items-center justify-between">
          <h4 class="text-xl font-bold tracking-tight text-gray-900 sm:text-2xl">
            All Industries
          </h4>
          <v-button :to="{name:'templates'}" color="white" size="small" :arrow="true" v-if="$route.name !== 'templates'">
            View All Templates
          </v-button>
        </div>

        <div class="grid grid-cols-1 gap-x-8 gap-y-4 mt-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
          <NuxtLink v-for="row in industries" :key="row.slug"
                       :to="{params:{slug:row.slug}, name:'templates-industries-slug'}"
                       :title="row.name"
                       class="text-gray-600 dark:text-gray-400 transition-colors duration-300 hover:text-nt-blue"
          >
            {{ row.name }}
          </NuxtLink>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import {computed} from 'vue'
import Fuse from 'fuse.js'
import SingleTemplate from './SingleTemplate.vue'
import {refDebounced} from "@vueuse/core";

export default {
  name: 'TemplatesList',
  components: {SingleTemplate},
  props: {
    templates: {
      type: Array,
      required: true
    },
    loading: {
      type: Boolean,
      default: false
    },
    showTypes: {
      type: Boolean,
      default: true
    },
    filterTypes: {
      type: Boolean,
      default: true
    },
    showIndustries: {
      type: Boolean,
      default: true
    },
    filterIndustries: {
      type: Boolean,
      default: true
    }
  },

  setup() {
    const authStore = useAuthStore()
    const templatesStore = useTemplatesStore()
    const search = ref('')
    const debouncedSearch = refDebounced(search, 500)
    return {
      search,
      debouncedSearch,
      user: computed(() => authStore.user),
      industries: computed(() => [...templatesStore.industries.values()]),
      types: computed(() => [...templatesStore.types.values()])
    }
  },

  data: () => ({
    selectedType: 'all',
    selectedIndustry: 'all',
  }),

  computed: {
    industriesOptions() {
      return [{name: 'All Industries', value: 'all'}].concat(Object.values(this.industries).map((industry) => {
        return {
          name: industry.name,
          value: industry.slug
        }
      }))
    },
    typesOptions() {
      return [{name: 'All Types', value: 'all'}].concat(Object.values(this.types).map((type) => {
        return {
          name: type.name,
          value: type.slug
        }
      }))
    },
    enrichedTemplates() {
      let enrichedTemplates = this.templates

      // Filter by Selected Type
      if (this.filterTypes && this.selectedType && this.selectedType !== 'all') {
        enrichedTemplates = enrichedTemplates.filter((item) => {
          return (item.types && item.types.length > 0) ? item.types.includes(this.selectedType) : false
        })
      }

      // Filter by Selected Industry
      if (this.filterIndustries && this.selectedIndustry && this.selectedIndustry !== 'all') {
        enrichedTemplates = enrichedTemplates.filter((item) => {
          return (item.industries && item.industries.length > 0) ? item.industries.includes(this.selectedIndustry) : false
        })
      }

      if (!this.debouncedSearch || this.debouncedSearch === '' || this.debouncedSearch === null) {
        return enrichedTemplates
      }

      // Fuze search
      const fuzeOptions = {
        keys: [
          'name',
          'slug',
          'description',
          'short_description'
        ]
      }
      const fuse = new Fuse(enrichedTemplates, fuzeOptions)
      return fuse.search(this.debouncedSearch).map((res) => {
        return res.item
      })
    }
  },
}
</script>
