<template>
    <div class="flex flex-col min-h-full mt-6">
      <div class="w-full flex-grow md:w-3/5 lg:w-1/2 md:mx-auto md:max-w-2xl px-4">
        <div>
          <div class="flex flex-wrap items-center mt-6 mb-4">
            <h2 class="text-nt-blue text-3xl font-bold flex-grow">
              Templates
            </h2>
          </div>
          <div v-if="templatesLoading" class="text-center">
            <loader class="h-6 w-6 text-nt-blue mx-auto" />
          </div>
          <p v-else-if="templates.length === 0">
            No any templates found.
          </p>
          <div v-else class="mb-10">
            <div v-if="templates && templates.length" class="border border border-gray-300 dark:bg-notion-dark-light rounded-md w-full">
              <div v-for="(template, index) in templates" :key="template.id"
                   class="p-4 w-full mx-auto border-gray-300 hover:bg-blue-100 dark:hover:bg-blue-900 transition-colors cursor-pointer relative" 
                   :class="{'border-t':index!==0}"
              >
                <div class="items-center space-x-4 truncate">
                  <p class="truncate float-left">
                    {{ template.name }}
                  </p>
                </div>
                <router-link class="absolute inset-0"
                    :to="{params:{slug:template.slug},name:'templates.show'}"
                />
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
  import { mapGetters, mapState } from 'vuex'
  import Fuse from 'fuse.js'
  import OpenFormFooter from '../../components/pages/OpenFormFooter'
  
  const loadTemplates = function () {
    store.commit('open/templates/startLoading')
    store.dispatch('open/templates/load').then(() => {
        store.commit('open/templates/stopLoading')
    })
  }
  
  export default {
    components: { OpenFormFooter },
  
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
        templates: state => state['open/templates'].content,
        templatesLoading: state => state['open/templates'].loading
      }),
    }
  }
  </script>
  