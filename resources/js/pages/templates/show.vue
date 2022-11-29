<template>
  <div class="flex flex-col min-h-full mt-6">
    <div class="w-full flex-grow md:w-4/5 lg:w-2/3 md:mx-auto md:max-w-4xl px-4">

      <breadcrumb :path="breadcrumbs"/>
      <div v-if="templatesLoading" class="text-center">
        <loader class="h-6 w-6 text-nt-blue mx-auto"/>
      </div>
      <p v-else-if="template === null || !template">
        Template does not exist.
      </p>
      <div v-else>
        <div class="flex flex-wrap items-center mt-6 mb-4">
          <h2 class="text-nt-blue text-3xl font-bold flex-grow">
            {{ template.name }}
          </h2>
        </div>
        <div class="mb-10">
          <div class="w-full shadow-xl rounded-lg my-5 max-h-72 flex items-center justify-center overflow-hidden">
            <img :src="template.image_url" alt="Template cover image" class="w-full object-cover"/>
          </div>
          <div v-html="template.description"></div>
          <div class="mt-5 text-center">
            <v-button v-if="authenticated" class="mt-4 sm:mt-0" :to="{path:'/forms/create?template='+template.slug}">
              Use this template
            </v-button>
            <v-button v-else class="mt-4 sm:mt-0" :to="{path:'/forms/create/guest?template='+template.slug}">
              Use this template
            </v-button>
          </div>

          <h3 class="text-center text-gray-500 mt-8 mb-2">Template Preview</h3>
          <open-complete-form ref="open-complete-form" :form="form" :creating="true"
                              class="mb-4 p-4 bg-gray-50 rounded-lg overflow-hidden"/>

          <div v-if="template.questions.length > 0" id="questions">
            <h3 class="text-xl font-semibold mt-8">Frequently asked questions</h3>
            <div class="pt-2">
              <div v-for="(ques,ques_key) in template.questions" :key="ques_key" class="my-3 border rounded-lg">
                <h5 class="border-b p-2">{{ ques.question }}</h5>
                <div class="p-2" v-html="ques.answer"></div>
              </div>
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
import Form from 'vform'
import {mapGetters, mapState} from 'vuex'
import Fuse from 'fuse.js'
import OpenFormFooter from '../../components/pages/OpenFormFooter'
import OpenCompleteForm from '../../components/open/forms/OpenCompleteForm'
import Breadcrumb from "../../components/common/Breadcrumb";

const loadTemplates = function () {
  store.commit('open/templates/startLoading')
  store.dispatch('open/templates/loadIfEmpty').then(() => {
    store.commit('open/templates/stopLoading')
  })
}

export default {
  components: {Breadcrumb, OpenFormFooter, OpenCompleteForm},

  beforeRouteEnter(to, from, next) {
    loadTemplates()
    next()
  },

  data() {
    return {}
  },

  mounted() {
  },

  methods: {},

  computed: {
    ...mapGetters({
      authenticated: 'auth/check'
    }),
    ...mapState({
      templatesLoading: state => state['open/templates'].loading
    }),
    breadcrumbs() {
      if (!this.template) {
        return [{route: {name: 'templates'}, label: 'Templates'}]
      }
      return [{route: {name: 'templates'}, label: 'Templates'}, {label: this.template.name}]
    },
    template() {
      return this.$store.getters['open/templates/getBySlug'](this.$route.params.slug)
    },
    form() {
      return new Form(this.template.structure)
    },
    metaTitle () {
      return this.template ? this.template.name : 'Template'
    },
  }
}
</script>
