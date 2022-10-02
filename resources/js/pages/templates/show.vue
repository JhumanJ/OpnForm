<template>
    <div class="flex flex-col min-h-full mt-6">
      <div class="w-full flex-grow md:w-4/5 lg:w-2/3 md:mx-auto md:max-w-4xl px-4">

        <breadcrumb :path="breadcrumbs" />
        <div v-if="templatesLoading" class="text-center">
            <loader class="h-6 w-6 text-nt-blue mx-auto" />
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
            <img :src="template.image_url" alt="" class="w-full shadow-xl rounded-lg my-5"/>
            <div v-html="template.description"></div>
            <div class="mt-5 text-center">
                <fancy-link class="mt-4 sm:mt-0" :to="{path:'/forms/create?template='+template.slug}" color="nt-blue">
                    Use this template
                </fancy-link>
            </div>

            <h3 class="text-center text-gray-500">Template Preview</h3>
            <open-complete-form ref="open-complete-form" :form="form" :creating="true" class="my-5 p-4 bg-gray-50 rounded-lg"/>

            <h3 class="text-xl font-semibold mb-3">Frequently asked questions</h3>
            <div v-if="template.questions.length > 0" class="mt-5 pt-2">
              <div v-for="(ques,ques_key) in template.questions" :key="ques_key" class="my-3 border rounded-lg">
                <h5 class="border-b p-2">{{ ques.question }}</h5>
                <div class="p-2" v-html="ques.answer"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <open-form-footer class="mt-8 border-t" />
    </div>
  </template>

  <script>
  import store from '~/store'
  import Form from 'vform'
  import { mapGetters, mapState } from 'vuex'
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
    components: {Breadcrumb, OpenFormFooter, OpenCompleteForm },

    beforeRouteEnter (to, from, next) {
      loadTemplates()
      next()
    },

    props: {
      metaTitle: { type: String, default: 'Templates' },
      metaDescription: { type: String, default: 'Public templates for create form quickly!' }
    },

    data () {
      return {
      }
    },

    mounted () {},

    methods: {},

    computed: {
      ...mapState({
        templatesLoading: state => state['open/templates'].loading
      }),
      breadcrumbs () {
        if (!this.template) {
          return [{ route: { name: 'templates' }, label: 'Templates' }]
        }
        return [{ route: { name: 'templates' }, label: 'Templates' }, { label: this.template.name }]
      },
      template () {
        return this.$store.getters['open/templates/getBySlug'](this.$route.params.slug)
      },
      form (){
        return new Form(this.template.structure)
      }
    }
  }
  </script>
