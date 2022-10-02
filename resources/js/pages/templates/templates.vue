<template>
  <div class="flex flex-col min-h-full mt-6">
    <div class="w-full flex-grow md:w-4/5 lg:w-2/3 md:mx-auto md:max-w-4xl px-4">
      <div>
        <div class="flex flex-wrap items-center mt-6 mb-4">
          <h2 class="text-nt-blue text-3xl font-bold flex-grow">
            Templates
          </h2>
        </div>
        <div v-if="templatesLoading" class="text-center">
          <loader class="h-6 w-6 text-nt-blue mx-auto"/>
        </div>
        <p v-else-if="templates.length === 0">
          No any templates found.
        </p>
        <div v-else class="mb-4">
          <div v-if="templates && templates.length"
               class="grid max-w-3xl grid-cols-1 mx-auto text-center sm:text-left sm:grid-cols-2 gap-y-8 gap-x-8 lg:gap-x-20">
            <div class="relative group" v-for="(template, index) in templates" :key="template.id">
              <div class="overflow-hidden rounded-lg aspect-w-16 aspect-h-9">
                <img class="object-cover w-full h-full transition-all duration-300 transform group-hover:scale-125"
                     :src="template.image_url" alt=""/>
              </div>
              <p class="mt-3 mb-2 text-sm font-normal text-gray-600 font-pj">
                {{ formatCreatedDate(template.created_at) }}</p>
              <p class="text-xl font-bold text-gray-900 font-pj">{{ template.name }}</p>
              <router-link :to="{params:{slug:template.slug},name:'templates.show'}" title="">
                <span class="absolute inset-0" aria-hidden="true"></span>
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
    <open-form-footer class="mt-8 border-t"/>
  </div>
</template>

<script>
import store from '~/store'
import {mapGetters, mapState} from 'vuex'
import Fuse from 'fuse.js'
import OpenFormFooter from '../../components/pages/OpenFormFooter'

const loadTemplates = function () {
  store.commit('open/templates/startLoading')
  store.dispatch('open/templates/load').then(() => {
    store.commit('open/templates/stopLoading')
  })
}

export default {
  components: {OpenFormFooter},

  beforeRouteEnter(to, from, next) {
    loadTemplates()
    next()
  },

  props: {
    metaTitle: {type: String, default: 'Templates'},
    metaDescription: {type: String, default: 'Public templates for create form quickly!'}
  },

  data() {
    return {}
  },

  mounted() {
  },

  methods: {
    formatCreatedDate(createdDate) {
      const date = new Date(createdDate)
      const dateTimeFormat = new Intl.DateTimeFormat('en', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
      })
      return dateTimeFormat.format(date)
    }
  },

  computed: {
    ...mapState({
      templates: state => state['open/templates'].content,
      templatesLoading: state => state['open/templates'].loading
    }),
  }
}
</script>
