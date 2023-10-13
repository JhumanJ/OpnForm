<template>
  <div class="flex flex-col min-h-full">
    <breadcrumb :path="breadcrumbs">
      <template #left>
        <div v-if="canEditTemplate" class="ml-5">
          <v-button color="gray" size="small" @click.prevent="showFormTemplateModal=true">
            Edit Template
          </v-button>
          <form-template-modal v-if="form" :form="form" :template="template" :show="showFormTemplateModal"
                               @close="showFormTemplateModal=false"
          />
        </div>
      </template>
      <template #right>
        <v-button v-if="canEditTemplate" v-track.copy_template_button_clicked size="small" color="white" class="mr-5"
                  @click.prevent="copyTemplateUrl"
        >
          Copy Template URL
        </v-button>
        <v-button v-track.use_template_button_clicked size="small" class="mr-5"
                  :to="{path: createFormWithTemplateUrl}"
        >
          Use this template
        </v-button>
      </template>
    </breadcrumb>

    <div v-if="templatesLoading" class="text-center my-4">
      <loader class="h-6 w-6 text-nt-blue mx-auto" />
    </div>
    <p v-else-if="template === null || !template" class="text-center my-4">
      We could not find this template.
    </p>
    <template v-else>
      <section class="pt-12 bg-gray-50 sm:pt-16 border-b pb-[250px] relative">
        <div class="px-4 mx-auto sm:px-6 lg:px-8 max-w-7xl">
          <div class="flex flex-col items-center justify-center max-w-4xl gap-8 mx-auto md:gap-12 md:flex-row">
            <div class="aspect-[4/3] shrink-0 rounded-lg shadow-sm overflow-hidden group max-w-xs">
              <img class="object-cover w-full h-full transition-all duration-200 group-hover:scale-110"
                   :src="template.image_url" alt="Template cover image"
              >
            </div>

            <div class="flex-1 text-center md:text-left relative">
              <h1 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                {{ template.name }}
              </h1>
              <p class="mt-2 text-lg font-normal text-gray-600">
                {{ cleanQuotes(template.short_description) }}
              </p>
              <template-tags :slug="template.slug" :display-all="true"
                             class="flex flex-wrap items-center justify-center gap-3 mt-4 md:justify-start"
              />
            </div>
          </div>
        </div>
      </section>

      <section class="relative px-4 mx-auto sm:px-6 lg:px-8 -mt-[210px]">
        <div class="max-w-7xl">
          <div
            class="max-w-2xl p-4 mx-auto bg-white shadow-lg sm:p-6 lg:p-8 rounded-xl ring-1 ring-inset ring-gray-200 isolate"
          >
            <p class="text-sm font-medium text-center text-gray-500 -mt-2 mb-2">
              Template Preview
            </p>
            <open-complete-form ref="open-complete-form" :form="form" :creating="true"
                                  class="mb-4 p-4 bg-gray-50 border border-gray-200 border-dashed rounded-lg"
            />
          </div>
        </div>

        <div class="absolute bottom-0 translate-y-full inset-x-0">
          <div class="px-4 mx-auto sm:px-6 lg:px-8 max-w-7xl -mt-[20px]">
            <div class="flex items-center justify-center">
              <v-button v-track.use_template_button_clicked class="mx-auto w-full max-w-[300px]" :to="{path: createFormWithTemplateUrl}">
                Use this template
              </v-button>
            </div>
            <div class="flex items-center justify-center">
              <div class="text-left mx-auto text-gray-500 text-xs mt-4">
                ✓ Core features 100% free<br>
                ✓ No credit card required<br>
                ✓ No submissions limit on Free plan
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="pt-20 pb-12 bg-white sm:pb-16">
        <div class="px-4 mx-auto sm:px-6 lg:px-8 max-w-7xl">
          <div class="max-w-2xl mx-auto mt-16 space-y-12 sm:mt-16 sm:space-y-16">
            <div class="nf-text" v-html="template.description" />

            <template v-if="template.questions.length > 0">
              <hr class="mt-12 border-gray-200">
              <div>
                <div class="text-center">
                  <h3 class="text-xl font-bold tracking-tight text-gray-900 sm:text-2xl">
                    Frequently asked questions
                  </h3>
                  <p class="mt-2 text-base font-normal text-gray-600">
                    Everything you need to know about this template.
                  </p>
                </div>
                <dl class="mt-12 space-y-10">
                  <div v-for="(ques,ques_key) in template.questions" :key="ques_key" class="space-y-4">
                    <dt class="font-semibold text-gray-900 dark:text-gray-100">
                      {{ ques.question }}
                    </dt>
                    <dd class="mt-2 leading-6 text-gray-600 dark:text-gray-400" v-html="ques.answer" />
                  </div>
                </dl>
              </div>
            </template>
          </div>
        </div>
      </section>

      <section v-if="template.related_templates.length > 0" class="py-12 bg-white border-t border-gray-200 sm:py-16">
        <div class="px-4 mx-auto sm:px-6 lg:px-8 max-w-7xl">
          <div class="flex items-center justify-between">
            <h4 class="text-xl font-bold tracking-tight text-gray-900 sm:text-2xl">
              Related templates
            </h4>
            <v-button :to="{name:'templates'}" color="white" size="small" :arrow="true">
              View All
            </v-button>
          </div>

          <div class="grid grid-cols-1 gap-8 mt-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 sm:gap-y-12">
            <single-template v-for="related in template.related_templates" :key="related" :slug="related" />
          </div>
        </div>
      </section>

      <section class="py-12 bg-white border-t border-gray-200 sm:py-16">
        <div class="px-4 mx-auto sm:px-6 lg:px-8 max-w-7xl">
          <div class="text-center">
            <h4 class="text-xl font-bold tracking-tight text-gray-900 sm:text-2xl">
              How OpnForm works
            </h4>
          </div>

          <div class="grid grid-cols-1 mt-12 md:grid-cols-2 gap-x-8 gap-y-12">
            <div
              class="flex flex-col items-center gap-4 text-center lg:items-start sm:text-left sm:items-start xl:flex-row"
            >
              <div
                class="inline-flex items-center justify-center w-10 h-10 text-base font-bold bg-white rounded-full shadow-sm ring-1 ring-inset ring-gray-200 text-blue-500 shrink-0"
              >
                1
              </div>
              <div>
                <h5 class="text-base font-bold leading-tight text-gray-900">
                  Copy the template and change it the way you like
                </h5>
                <p class="mt-2 text-sm font-normal text-gray-600">
                  <router-link :to="{path:createFormWithTemplateUrl}">
                    Click here to copy this template
                  </router-link>
                  and start customizing it. Change the questions, add new ones, choose colors and
                  more.
                </p>
              </div>
            </div>

            <div
              class="flex flex-col items-center gap-4 text-center lg:items-start sm:text-left sm:items-start xl:flex-row"
            >
              <div
                class="inline-flex items-center justify-center w-10 h-10 text-base font-bold bg-white rounded-full shadow-sm ring-1 ring-inset ring-gray-200 text-blue-500 shrink-0"
              >
                2
              </div>
              <div>
                <h5 class="text-base font-bold leading-tight text-gray-900">
                  Embed the form or share it via a link
                </h5>
                <p class="mt-2 text-sm font-normal text-gray-600">
                  You can directly share your form link, or embed the form on your website. It's magic! 🪄
                </p>
              </div>
            </div>
          </div>

          <!-- add video here -->
          <!--          <div class="max-w-5xl mx-auto mt-12 shadow-sm rounded-xl bg-blue-50 aspect-video" />-->
        </div>
      </section>
    </template>

    <open-form-footer class="mt-8 border-t"/>
  </div>
</template>

<script>
import store from '~/store'
import Form from 'vform'
import { mapGetters, mapState } from 'vuex'
import OpenFormFooter from '../../components/pages/OpenFormFooter.vue'
import OpenCompleteForm from '../../components/open/forms/OpenCompleteForm.vue'
import Breadcrumb from '../../components/common/Breadcrumb.vue'
import SeoMeta from '../../mixins/seo-meta.js'
import TemplateTags from '../../components/pages/templates/TemplateTags.vue'
import SingleTemplate from '../../components/pages/templates/SingleTemplate.vue'
import FormTemplateModal from '../../components/open/forms/components/templates/FormTemplateModal.vue'

export default {

  components: { Breadcrumb, OpenFormFooter, OpenCompleteForm, TemplateTags, SingleTemplate, FormTemplateModal },
  mixins: [SeoMeta],

  beforeRouteEnter (to, from, next) {
    if (to.params?.slug) {
      store.dispatch('open/templates/loadTemplate', to.params?.slug)
      store.dispatch('open/templates/loadTypesAndIndustries')
    }
    next()
  },

  data () {
    return {
      showFormTemplateModal: false
    }
  },

  mounted () {
  },

  methods: {
    cleanQuotes (str) {
      // Remove starting and ending quotes if any
      return (str) ? str.replace(/^"/, '').replace(/"$/, '') : ''
    },
    copyTemplateUrl(){
      const str = this.template.share_url
      const el = document.createElement('textarea')
      el.value = str
      document.body.appendChild(el)
      el.select()
      document.execCommand('copy')
      document.body.removeChild(el)
      this.alertSuccess('Copied!')
    }
  },

  computed: {
    ...mapGetters({
      authenticated: 'auth/check',
      user: 'auth/user'
    }),
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
    form () {
      return this.template ? new Form(this.template.structure) : null
    },
    canEditTemplate () {
      return this.user && this.template && (this.user.admin || this.user.template_editor || this.template.creator_id === this.user.id)
    },
    metaTitle () {
      return this.template ? this.template.name : 'Form Template'
    },
    metaDescription () {
      if (!this.template) return null
      // take the first 140 characters of the description
      return this.template.short_description?.substring(0, 140) + '... | Customize any template and create your own form in minutes.'
    },
    metaImage () {
      if (!this.template) return null
      return this.template.image_url
    },
    metaTags () {
      if (!this.template) {
        return [];
      }
      return this.template.publicly_listed ? [] : [{ name: 'robots', content: 'noindex' }]
    },
    createFormWithTemplateUrl () {
      if(this.authenticated) {
        return '/forms/create?template=' + this.template?.slug
      }
      return '/forms/create/guest?template=' + this.template?.slug
    }
  }
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

.aspect-video {
  aspect-ratio: 16/9;
}
</style>
