<template>
  <div class="flex mt-6">
    <div class="w-full md:w-4/5 lg:w-3/5 md:mx-auto md:max-w-4xl px-4">
      <breadcrumb class="sm:px-6" :path="breadcrumbs" />
      <div v-if="form" class="sm:px-6">
        <h2 class="text-nt-blue text-3xl font-bold z-10 mt-6 mb-3">
          {{ form.title }}
        </h2>

        <p class="mb-3">
          <span v-if="form.views_count">This form has been seen
            <span class="font-semibold">{{ form.views_count }}</span> time{{ form.views_count > 0 ? 's' : '' }}
            and it has received
            <span class="font-semibold">{{ form.submissions_count }}</span> submission{{ form.submissions_count > 0 ? 's' : '' }}.</span>
        </p>

        <p v-if="form.closes_at" class="text-yellow-500">
          <span v-if="form.is_closed"> This form stopped accepting submissions on the  {{ displayClosesDate }} </span>
          <span v-else> This form will stop accepting submissions on the {{ displayClosesDate }} </span>
        </p>

        <p v-if="form.max_submissions_count > 0" class="text-yellow-500">
          <span v-if="form.max_number_of_submissions_reached"> The form is now closed because it reached its limit of {{ form.max_submissions_count }} submissions. </span>
          <span v-else> This form will stop accepting submissions after {{ form.max_submissions_count }} submissions. </span>
        </p>

        <div class="flex justify-center">
          <share-form-url :form="form" :link="true" />
        </div>

        <!--  Open Form  -->
        <div class="flex flex-wrap -mx-2">
          <!--  Edit Form -->
          <div class="w-full sm:w-1/2 px-2 flex">
            <div v-track.edit_form_click="{form_id:form.id, form_slug:form.slug}"
                 class="group relative transition-all mt-4 flex items-center p-3 px-6 w-full rounded-md bg-gray-50 dark:bg-gray-700 hover:bg-blue-50 dark:hover:bg-blue-900 cursor-pointer hover:text-blue-500"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-4 "
                   fill="none" viewBox="0 0 24 24" stroke="currentColor"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                />
              </svg>
              <span class="font-semibold group relative-hover:text-blue-500">
                Edit form
              </span>
              <router-link :to="{name:'forms.edit',params:{slug:form.slug}}" class="absolute inset-0" />
            </div>
          </div>
          <!--  Open Form -->
          <div class="w-full sm:w-1/2 px-2 flex">
            <div
              v-track.open_form_click="{form_id:form.id, form_slug:form.slug}" class="group relative transition-all mt-4 flex items-center p-3 px-6 w-full rounded-md bg-gray-50 dark:bg-gray-700
              hover:bg-blue-50 dark:hover:bg-blue-500 cursor-pointer hover:text-blue-500 dark:hover:text-white"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-4 "
                   fill="none" viewBox="0 0 24 24" stroke="currentColor"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"
                />
              </svg>
              <span class="font-semibold group relative-hover:text-blue-500">
                Open form
              </span>
              <a target="_blank" :href="form.share_url" class="absolute inset-0" />
            </div>
          </div>

          <!--  Share/Embed form table  -->
          <div class="w-full sm:w-1/2 px-2 flex">
            <div
              v-track.share_embed_form_click="{form_id:form.id, form_slug:form.slug}"
              class="group relative transition-all mt-4 flex items-center p-3 px-6 w-full rounded-md bg-gray-50 dark:bg-gray-700 hover:bg-blue-50 dark:hover:bg-blue-900 cursor-pointer hover:text-blue-500"
              @click.prevent="showShareEmbedFormModal=true"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-4 "
                   fill="none" viewBox="0 0 24 24" stroke="currentColor"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"
                />
              </svg>
              <span class="font-semibold group relative-hover:text-blue-500">
                Share/Embed form
              </span>
            </div>
          </div>
          <!--  Regenerate form link  -->
          <div class="w-full sm:w-1/2 px-2 flex">
            <div v-track.regenerate_form_link_click="{form_id:form.id, form_slug:form.slug}"
                 class="group relative transition-all mt-4 flex items-center p-3 px-6 w-full rounded-md bg-gray-50 dark:bg-gray-700 hover:bg-blue-50 dark:hover:bg-blue-900 cursor-pointer hover:text-blue-500"
                 @click="showGenerateFormLinkModal=true"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-4 "
                   fill="none" viewBox="0 0 24 24" stroke="currentColor"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"
                />
              </svg>
              <span class="font-semibold group relative-hover:text-blue-500">
                Regenerate form link
              </span>
            </div>
          </div>
          <div class="w-full sm:w-1/2 px-2 flex">
            <div v-track.url_form_prefill_click="{form_id:form.id, form_slug:form.slug}"
                 class="group relative transition-all mt-4 flex items-center p-3 px-6 w-full rounded-md bg-gray-50 dark:bg-gray-700 hover:bg-blue-50 dark:hover:bg-blue-900 cursor-pointer hover:text-blue-500"
                 @click="showUrlFormPrefillModal=true"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-4" fill="none" viewBox="0 0 24 24"
                   stroke="currentColor"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 16v2a2 2 0 01-2 2H5a2 2 0 01-2-2v-7a2 2 0 012-2h2m3-4H9a2 2 0 00-2 2v7a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-1m-1 4l-3 3m0 0l-3-3m3 3V3"
                />
              </svg>
              <span class="font-semibold group relative-hover:text-blue-500">
                Url form pre-fill <pro-tag class="ml-2" />
              </span>
            </div>
          </div>
          <div class="w-full sm:w-1/2 px-2 flex">
            <div v-track.duplicate_form_click="{form_id:form.id, form_slug:form.slug}"
                 class="group relative transition-all mt-4 flex items-center p-3 px-6 w-full rounded-md bg-gray-50 dark:bg-gray-700 hover:bg-blue-50 dark:hover:bg-blue-900 cursor-pointer hover:text-blue-500"
                 @click="duplicateForm"
            >
              <template v-if="!loadingDuplicate">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-4 "
                     fill="none" viewBox="0 0 24 24" stroke="currentColor"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"
                  />
                </svg>
                <span class="font-semibold group relative-hover:text-blue-500">
                  Duplicate form
                </span>
              </template>
              <template v-else>
                <loader class="h-6 w-6 text-nt-blue mx-auto" />
              </template>
            </div>
          </div>
          <div class="w-full sm:w-1/2 px-2 flex mb-5">
            <div v-track.delete_form_click="{form_id:form.id, form_slug:form.slug}"
                 class="group relative transition-all mt-4 flex items-center p-3 px-6 w-full rounded-md bg-gray-50 dark:bg-gray-700 hover:bg-red-50 dark:hover:bg-red-900 cursor-pointer hover:text-red-500"
                 @click="alertConfirm('Do you really want to delete this form?',deleteForm)"
            >
              <template v-if="!loadingDelete">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-4"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                  />
                </svg>
                <span class="font-semibold group relative-hover:text-red-500">
                  Delete form
                </span>
              </template>
              <loader v-else class="h-6 w-6 text-nt-blue mx-auto" />
            </div>
          </div>
        </div>

        <!-- Form Submissions -->
        <div class="pt-5 mt-5 border-t" id="table-page" v-if="form">
          <form-submissions />
        </div>

        <!-- Form Analytics -->
        <div class="pt-5 mt-5 border-t">
          <h3 class="font-semibold">
            Form Analytics (last 30 days)
          </h3>
          <form-stats :form="form" />
        </div>

        <!--  Share/Embed form modal  -->
        <modal :show="showShareEmbedFormModal" @close="showShareEmbedFormModal=false">
          <div class="px-4">
            <h2 class="text-nt-blue text-3xl font-bold mb-6">
              Share/Embed your form
            </h2>

            <!--   Link   -->
            <h3 class="font-bold text-xl border-t pt-4">
              Share
            </h3>
            <p>Share your form using the link below:</p>
            <share-form-url :form="form" />

            <!--   Embed   -->
            <h3 class="font-bold text-xl border-t pt-4">
              Embed
            </h3>
            <p>
              Embed your form on your website by copying the html code below.
            </p>
            <embed-form-code :form="form" />

            <div class="flex justify-end mt-4">
              <v-button color="gray" shade="light" @click="showShareEmbedFormModal=false">Close</v-button>
            </div>

          </div>
        </modal>

        <!--  Regenerate form link modal  -->
        <modal :show="showGenerateFormLinkModal" @close="showGenerateFormLinkModal=false">
          <div class="-m-6">
            <div class="p-6">
              <h2 class="text-nt-blue text-3xl font-bold mb-6">
                Generate new form link
              </h2>
              <p>
                You can choose between two different URL formats for your form. <span class="font-semibold">Be careful, changing your form URL
                  is not a reversible operation</span>. Make sure to udpate your form URL everywhere where it's used.
              </p>
            </div>
            <div class="border-t py-4 mt-4 px-6">
              <h3 class="text-xl text-gray-700 font-semibold">
                Human Readable URL
              </h3>
              <p>If your users are going to see this url, you might want to make nice and readable. Example:</p>
              <p class="text-gray-600 p-4 bg-gray-100 rounded-md mt-4">
                https://opnform.com/forms/contact
              </p>
              <div class="text-center mt-4">
                <v-button :loading="loadingNewLink" @click="regenerateLink('slug')">
                  Generate a Human Readable URL
                </v-button>
              </div>
            </div>
            <div class="border-t pt-4 mt-4 px-6 pb-10">
              <h3 class="text-xl text-gray-700 font-semibold">
                Random ID URL
              </h3>
              <p>
                If your user are not going to see your form url (if it's embedded), and if you prefer to have a random
                non-guessable URL. Example:
              </p>
              <p class="text-gray-600 p-4 bg-gray-100 rounded-md mt-4">
                https://opnform.com/forms/b4417f9c-34ae-4421-8006-832ee47786e7
              </p>
              <div class="text-center mt-4">
                <v-button :loading="loadingNewLink" @click="regenerateLink('uuid')">
                  Generate a Random ID URL
                </v-button>
              </div>
            </div>

            <div class="flex justify-end mt-4 pb-5 px-6">
              <v-button color="gray" shade="light" @click="showGenerateFormLinkModal=false">Close</v-button>
            </div>

          </div>
        </modal>

        <url-form-prefill-modal :form="form" :show="showUrlFormPrefillModal" @close="showUrlFormPrefillModal=false" />
      </div>
      <div v-else-if="loading" class="text-center w-full p-5">
        <loader class="h-6 w-6 mx-auto" />
      </div>
      <div v-else>
        Form not found.
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import store from '~/store'
import Form from 'vform'
import ShareFormUrl from '../../components/open/forms/components/ShareFormUrl'
import EmbedFormCode from '../../components/open/forms/components/EmbedFormCode'
import Breadcrumb from '../../components/common/Breadcrumb'
import { mapGetters, mapState } from 'vuex'
import ProTag from '../../components/common/ProTag'
import UrlFormPrefillModal from '../../components/pages/forms/UrlFormPrefillModal'
import FormStats from '../../components/open/forms/components/FormStats'
import FormSubmissions from '../../components/open/forms/components/FormSubmissions'

const loadForms = function () {
  store.commit('open/forms/startLoading')
  store.dispatch('open/workspaces/loadIfEmpty').then(() => {
    store.dispatch('open/forms/load', store.state['open/workspaces'].currentId)
  })
}

export default {
  name: 'EditForm',
  components: { UrlFormPrefillModal, ProTag, Breadcrumb, ShareFormUrl, EmbedFormCode, FormStats, FormSubmissions },

  beforeRouteEnter (to, from, next) {
    loadForms()
    next()
  },

  beforeRouteLeave (to, from, next) {
    this.workingForm = null
    next()
  },
  middleware: 'auth',

  data () {
    return {
      loadingDuplicate: false,
      loadingDelete: false,
      loadingNewLink: false,
      showNotionEmbedModal: false,
      showShareEmbedFormModal: false,
      showUrlFormPrefillModal: false,
      showGenerateFormLinkModal: false
    }
  },

  computed: {
    ...mapGetters({
      user: 'auth/user'
    }),
    ...mapState({
      formsLoading: state => state['open/forms'].loading,
      workspacesLoading: state => state['open/workspaces'].loading
    }),
    workingForm: {
      get () {
        return this.$store.state['open/working_form'].content
      },
      set (value) {
        this.$store.commit('open/working_form/set', value)
      }
    },
    workspace () {
      if (!this.form) return null
      return this.$store.getters['open/workspaces/getById'](this.form.workspace_id)
    },
    form () {
      return this.$store.getters['open/forms/getBySlug'](this.$route.params.slug)
    },
    formEndpoint: () => '/api/open/forms/{id}',
    breadcrumbs () {
      if (!this.form) {
        return [{ route: { name: 'home' }, label: 'Your Forms' }]
      }
      return [{ route: { name: 'home' }, label: 'Your Forms' }, { label: this.form.title }]
    },
    loading () {
      return this.formsLoading || this.workspacesLoading
    },
    displayClosesDate(){
      if(this.form.closes_at){
        let dateObj = new Date(this.form.closes_at)
        return dateObj.getFullYear() + "-" +
                String(dateObj.getMonth() + 1).padStart(2, '0') + "-" +
                String(dateObj.getDate()).padStart(2, '0') + " " +
                String(dateObj.getHours()).padStart(2, '0') + ":" +
                String(dateObj.getMinutes()).padStart(2, '0')
      }
      return "";
    }
  },

  watch: {
    form () {
      this.workingForm = new Form(this.form)
    }
  },

  mounted () {
    this.updatedForm = new Form(this.form)

    if (this.$route.params.hasOwnProperty('new_form') && this.$route.params.new_form) {
      // if (!this.user.is_subscribed && !this.user.has_customer_id) {
      //   // Crisp offer
      //   this.$getCrisp().push(['set', 'session:event', [[['first_form_created', { form_id: this.form.id, form_slug: this.form.slug }, 'blue']]]])
      //
      //   setTimeout(
      //     function () {
      //       window.$crisp.push(['do', 'chat:show'])
      //       window.$crisp.push(['do', 'chat:open'])
      //       window.$crisp.push([
      //         'do',
      //         'message:show',
      //         ['text',
      //           'Hey there! I\m Julien the founder of NotionForms. Congrats on setting up your first OpnForm 🎉']
      //       ])
      //       setTimeout(
      //         function () {
      //           window.$crisp.push(['do', 'chat:show'])
      //           window.$crisp.push(['do', 'chat:open'])
      //           window.$crisp.push([
      //             'do',
      //             'message:show',
      //             ['text',
      //               'A small gift to congratulate you? 🎁 I\'d be happy to offer you a 40% discount on your first month of a Pro subscription. Let me know if you\'re interested!']
      //           ])
      //           setTimeout(
      //             function () {
      //               window.$crisp.push(['do', 'chat:show'])
      //               window.$crisp.push(['do', 'chat:open'])
      //               window.$crisp.push([
      //                 'do',
      //                 'message:show',
      //                 ['text',
      //                   'Just use the code "FIRSTFORM40" in the next 24 hours to get the discount! 🎉']
      //               ])
      //             }, 20000)
      //         }, 4000)
      //     }, 4000)
      // }
    }
  },

  metaInfo () {
    return { title: this.$t('home') }
  },

  methods: {
    openCrisp () {
      window.$crisp.push(['do', 'chat:show'])
      window.$crisp.push(['do', 'chat:open'])
    },
    duplicateForm () {
      if (this.loadingDuplicate) return
      this.loadingDuplicate = true
      axios.post(this.formEndpoint.replace('{id}', this.form.id) + '/duplicate').then((response) => {
        this.$store.commit('open/forms/addOrUpdate', response.data.new_form)
        this.$router.push({ name: 'forms.show', params: { slug: response.data.new_form.slug } })
        this.alertSuccess('Form was successfully duplicated.')
        this.loadingDuplicate = false
      })
    },
    regenerateLink (option) {
      if (this.loadingNewLink) return
      this.loadingNewLink = true
      axios.put(this.formEndpoint.replace('{id}', this.form.id) + '/regenerate-link/' + option).then((response) => {
        this.$store.commit('open/forms/addOrUpdate', response.data.form)
        this.$router.push({ name: 'forms.show', params: { slug: response.data.form.slug } })
        this.alertSuccess(response.data.message)
        this.loadingNewLink = false
      }).finally(() => {
        this.showGenerateFormLinkModal = false
      })
    },
    deleteForm () {
      if (this.loadingDelete) return
      this.loadingDelete = true
      axios.delete(this.formEndpoint.replace('{id}', this.form.id)).then(() => {
        this.$store.commit('open/forms/remove', this.form)
        this.$router.push({ name: 'home' })
        this.alertSuccess('Form was deleted.')
        this.loadingDelete = false
      })
    }
  }
}
</script>
