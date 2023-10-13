<template>
  <section class="bg-white py-12">
    <div class="px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 sm:gap-6 relative z-20">
        <div class="flex items-center gap-4">
          <div class="flex-1 sm:flex-none">
            <select-input v-model="selectedType" name="type"
                          :options="typesOptions" class="w-full sm:w-auto md:w-56"
            />
          </div>
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
        <loader class="h-6 w-6 text-nt-blue mx-auto" />
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
</template>

<script>
import store from '~/store'
import { mapGetters, mapState } from 'vuex'
import Form from 'vform'
import Fuse from 'fuse.js'
import SingleTemplate from './SingleTemplate.vue'

const loadTemplates = function () {
  store.commit('open/templates/startLoading')
  store.dispatch('open/templates/loadIfEmpty').then(() => {
    store.commit('open/templates/stopLoading')
  })
}

export default {
  name: 'TemplatesList',
  components: { SingleTemplate },

  props: {
    onlyMy: {
      type: Boolean,
      required: false
    }
  },

  data: () => ({
    selectedType: 'all',
    selectedIndustry: 'all',
    searchTemplate: new Form({
      search: ''
    })
  }),

  watch: {},

  mounted () {
    loadTemplates()
  },

  computed: {
    ...mapState({
      templates: state => state['open/templates'].content,
      templatesLoading: state => state['open/templates'].loading,
      industries: state => state['open/templates'].industries,
      types: state => state['open/templates'].types
    }),
    ...mapGetters({
      user: 'auth/user'
    }),
    industriesOptions () {
      return [{ name: 'All Industries', value: 'all' }].concat(Object.values(this.industries).map((industry) => {
        return {
          name: industry.name,
          value: industry.slug
        }
      }))
    },
    typesOptions () {
      return [{ name: 'All Types', value: 'all' }].concat(Object.values(this.types).map((type) => {
        return {
          name: type.name,
          value: type.slug
        }
      }))
    },
    enrichedTemplates () {
      let enrichedTemplates = (this.onlyMy && this.user) ? this.templates.filter((item) => { return item.creator_id === this.user.id}) : this.templates

      // Filter by Selected Type
      if (this.selectedType && this.selectedType !== 'all') {
        enrichedTemplates = enrichedTemplates.filter((item) => {
          return (item.types && item.types.length > 0) ? item.types.includes(this.selectedType) : false
        })
      }

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
    }
  },

  methods: {}
}
</script>
