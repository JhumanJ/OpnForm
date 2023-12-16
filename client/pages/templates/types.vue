<template>
  <div class="flex flex-col min-h-full">
    <breadcrumb :path="breadcrumbs" />

    <div v-if="templatesLoading" class="text-center my-4">
      <Loader class="h-6 w-6 text-nt-blue mx-auto" />
    </div>
    <p v-else-if="type === null || !type" class="text-center my-4">
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

      <section class="bg-white py-12 sm:py-16">
        <div class="px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 sm:gap-6 relative z-20">
            <div class="flex items-center gap-4">
              <div class="flex-1 sm:flex-none">
                <select-input v-model="selectedIndustry" name="industry"
                            :options="industriesOptions" class="w-full sm:w-auto md:w-56"
                />
              </div>
            </div>
            <div class="flex-1 w-full md:max-w-xs">
              <text-input name="search" :form="searchTemplate" placeholder="Search..." />
            </div>
          </div>

          <div v-if="templatesLoading" class="text-center mt-4">
            <Loader class="h-6 w-6 text-nt-blue mx-auto" />
          </div>
          <p v-else-if="enrichedTemplates.length === 0" class="text-center mt-4">
            No templates found.
          </p>
          <div v-else class="relative z-10">
            <div class="grid grid-cols-1 mt-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 sm:gap-y-12">
              <single-template v-for="template in enrichedTemplates" :key="template.id" :slug="template.slug" />
            </div>
          </div>
        </div>
      </section>

      <section class="py-12 bg-white border-t border-gray-200 sm:py-16">
        <div class="px-4 mx-auto sm:px-6 lg:px-8 max-w-7xl">
          <p class="text-gray-600 font-normal">
            {{ type.description }}
          </p>
        </div>
      </section>

      <section class="py-12 bg-white border-t border-gray-200 sm:py-16">
        <div class="px-4 mx-auto sm:px-6 lg:px-8 max-w-7xl">
          <div class="flex items-center justify-between">
            <h4 class="text-xl font-bold tracking-tight text-gray-900 sm:text-2xl">
              Other Types
            </h4>

            <v-button :to="{name:'templates'}" color="white" size="small" :arrow="true">
              View All Templates
            </v-button>
          </div>

          <div class="grid grid-cols-1 gap-8 mt-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            <router-link v-for="row in otherTypes" :key="row.slug"
                        :to="{params:{slug:row.slug}, name:'templates-types'}"
                        :title="row.name"
                        class="text-gray-600 dark:text-gray-400 transition-colors duration-300 hover:text-nt-blue"
            >
              {{ row.name }}
            </router-link>
          </div>
        </div>
      </section>

    </template>

    <open-form-footer class="mt-8 border-t"/>
  </div>
</template>

<script>
import Form from 'vform'
import Fuse from 'fuse.js'
import { computed } from 'vue'
import { useAuthStore } from '../../stores/auth'
import { useTemplatesStore } from '../../stores/templates'
import SeoMeta from '../../mixins/seo-meta.js'
import OpenFormFooter from '../../components/pages/OpenFormFooter.vue'
import Breadcrumb from '~/components/global/Breadcrumb.vue'
import SingleTemplate from '../../components/pages/templates/SingleTemplate.vue'

const loadTemplates = function () {
  const templatesStore = useTemplatesStore()
  templatesStore.startLoading()
  templatesStore.loadIfEmpty().then(() => {
    templatesStore.stopLoading()
  })
}

export default {
  components: { Breadcrumb, OpenFormFooter, SingleTemplate },
  mixins: [SeoMeta],

  beforeRouteEnter (to, from, next) {
    loadTemplates()
    next()
  },

  setup () {
    const authStore = useAuthStore()
    const templatesStore = useTemplatesStore()
    return {
      authenticated : computed(() => authStore.check),
      user : computed(() => authStore.user),
      templates : computed(() => templatesStore.content),
      templatesLoading : computed(() => templatesStore.loading),
      industries : computed(() => templatesStore.industries),
      types : computed(() => templatesStore.types)
    }
  },

  data () {
    return {
      selectedIndustry: 'all',
      searchTemplate: new Form({
        search: ''
      })
    }
  },

  mounted () {},

  computed: {
    breadcrumbs () {
      if (!this.type) {
        return [{ route: { name: 'templates' }, label: 'Templates' }]
      }
      return [{ route: { name: 'templates' }, label: 'Templates' }, { label: this.type.name }]
    },
    type () {
      return Object.values(this.types).find((type) => {
        return type.slug === this.$route.params.slug
      })
    },
    industriesOptions () {
      return [{ name: 'All Industries', value: 'all' }].concat(Object.values(this.industries).map((industry) => {
        return {
          name: industry.name,
          value: industry.slug
        }
      }))
    },
    otherTypes() {
      return Object.values(this.types).filter((type) => {
        return type.slug !== this.$route.params.slug
      })
    },
    enrichedTemplates () {
      let enrichedTemplates = this.templates

      // Filter by current Type only
      enrichedTemplates = enrichedTemplates.filter((item) => {
        return (item.types && item.types.length > 0) ? item.types.includes(this.$route.params.slug) : false
      })

      // Filter by Selected Industry
      if (this.selectedIndustry && this.selectedIndustry !== 'all') {
        enrichedTemplates = enrichedTemplates.filter((item) => {
          return (item.industries && item.industries.length > 0) ? item.industries.includes(this.selectedIndustry) : false
        })
      }

      if (this.searchTemplate.search === '' || this.searchTemplate.search === null) {
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
      return fuse.search(this.searchTemplate.search).map((res) => {
        return res.item
      })
    },
    metaTitle () {
      return this.type ? this.type.meta_title : 'Form Template Type'
    },
    metaDescription () {
      if (!this.type) return null
      return this.type.meta_description.substring(0, 140)
    }
  },

  methods: {}
}
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

