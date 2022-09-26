<template>
    <div class="flex flex-col min-h-full mt-6">
      <div class="w-full flex-grow md:w-3/5 lg:w-1/2 md:mx-auto md:max-w-2xl px-4">
        
        <div v-if="templatesLoading" class="text-center">
            <loader class="h-6 w-6 text-nt-blue mx-auto" />
        </div>
        <p v-else-if="template === null || !template">
            Invalid Template
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
            <div v-if="template.questions.length > 0" class="mt-5 pt-2 border-t">
              <h4 class="text-2xl font-bold flex-grow mb-3">Frequently asked questions</h4>
              <div v-for="(ques,ques_key) in template.questions" :key="ques_key" class="mb-3 mt-3 pb-4 border-2 p-2 pt-0">
                <h3 class="font-bold border-b-4 py-2">{{ ques.question }}</h3>
                <div class="pt-4" v-html="ques.answer"></div>
              </div>
            </div>

            <open-complete-form ref="open-complete-form" :form="form" :creating="true" class="my-5 pt-2 border-t"/>
            
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
  
  const loadTemplates = function () {
    store.commit('open/templates/startLoading')
    store.dispatch('open/templates/loadIfEmpty').then(() => {
        store.commit('open/templates/stopLoading')
    })
  }
  
  export default {
    components: { OpenFormFooter, OpenCompleteForm },
  
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
      template () {
        return this.$store.getters['open/templates/getBySlug'](this.$route.params.slug)
      },
      form (){
        return new Form(this.template.structure)
      }
    }
  }
  </script>
  