<template>
  <div class="bg-white">
    <template v-if="form">
      <div class="flex bg-gray-50">
        <div class="w-full md:w-4/5 lg:w-3/5 md:mx-auto md:max-w-4xl px-4">
          <div class="pt-4 pb-0">
            <a href="#" @click.prevent="goBack" class="flex text-blue mb-2 font-semibold text-sm">
              <svg class="w-3 h-3 text-blue mt-1 mr-1" viewBox="0 0 6 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M5 9L1 5L5 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                      stroke-linejoin="round"/>
              </svg>
              Go back
            </a>

            <div class="flex">
              <h2 class="flex-grow text-gray-900">
                {{ form.title }}
              </h2>
              <dropdown dusk="nav-dropdown" v-if="!loadingDuplicate">
                <template #trigger="{toggle}">
                  <button type="button" class="mr-2 p-4 rounded-lg border-2 border-gray-300 bg-white" dusk="nav-dropdown-button" @click.prevent="toggle()">
                    <svg width="16" height="4" viewBox="0 0 16 4" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M8.00016 2.83366C8.4604 2.83366 8.8335 2.46056 8.8335 2.00033C8.8335 1.54009 8.4604 1.16699 8.00016 1.16699C7.53993 1.16699 7.16683 1.54009 7.16683 2.00033C7.16683 2.46056 7.53993 2.83366 8.00016 2.83366Z" stroke="#344054" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M13.8335 2.83366C14.2937 2.83366 14.6668 2.46056 14.6668 2.00033C14.6668 1.54009 14.2937 1.16699 13.8335 1.16699C13.3733 1.16699 13.0002 1.54009 13.0002 2.00033C13.0002 2.46056 13.3733 2.83366 13.8335 2.83366Z" stroke="#344054" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M2.16683 2.83366C2.62707 2.83366 3.00016 2.46056 3.00016 2.00033C3.00016 1.54009 2.62707 1.16699 2.16683 1.16699C1.70659 1.16699 1.3335 1.54009 1.3335 2.00033C1.3335 2.46056 1.70659 2.83366 2.16683 2.83366Z" stroke="#344054" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                  </button>
                </template>
                <a href="#"
                    class="block block px-4 py-2 text-md text-gray-700 dark:text-white hover:bg-gray-100 hover:text-gray-900 dark:text-gray-100 dark:hover:text-white dark:hover:bg-gray-600 flex items-center"
                    v-track.duplicate_form_click="{form_id:form.id, form_slug:form.slug}"
                    @click.prevent="duplicateForm"
                >
                  <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"
                    />
                  </svg>
                  Duplicate form
                </a>
                <a href="#"
                    class="block block px-4 py-2 text-md text-red-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-100 dark:hover:text-white dark:hover:bg-gray-600 flex items-center"
                    v-track.delete_form_click="{form_id:form.id, form_slug:form.slug}"
                    @click.prevent="alertConfirm('Do you really want to delete this form?',deleteForm)"
                >
                  <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                    />
                  </svg>
                  Delete form
                </a>
                <a href="#" v-if="user.admin"
                    class="block block px-4 py-2 text-md text-gray-700 dark:text-white hover:bg-gray-100 hover:text-gray-900 dark:text-gray-100 dark:hover:text-white dark:hover:bg-gray-600 flex items-center"
                    @click.prevent="showCreateTemplateModal=true"
                >
                  <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 14v6m-3-3h6M6 10h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2zm10 0h2a2 2 0 002-2V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2zM6 20h2a2 2 0 002-2v-2a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2z" />
                  </svg>
                  Create Template
                </a>
              </dropdown>
              <loader v-else class="h-6 w-6 text-nt-blue mx-auto mt-2 mr-4" />
              <v-button class="text-white" @click="openEdit">
                <svg class="inline mr-1" width="18" height="17" viewBox="0 0 18 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M8.99998 15.6662H16.5M1.5 15.6662H2.89545C3.3031 15.6662 3.50693 15.6662 3.69874 15.6202C3.8688 15.5793 4.03138 15.512 4.1805 15.4206C4.34869 15.3175 4.49282 15.1734 4.78107 14.8852L15.25 4.4162C15.9404 3.72585 15.9404 2.60656 15.25 1.9162C14.5597 1.22585 13.4404 1.22585 12.75 1.9162L2.28105 12.3852C1.9928 12.6734 1.84867 12.8175 1.7456 12.9857C1.65422 13.1348 1.58688 13.2974 1.54605 13.4675C1.5 13.6593 1.5 13.8631 1.5 14.2708V15.6662Z" stroke="currentColor" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round"/>
                </svg> Edit form
              </v-button>
            </div>

            <ul class="flex text-gray-500">
              <li class="pr-1">{{ form.views_count }} view{{ form.views_count > 0 ? 's' : '' }}</li>
              <li class="list-disc ml-6 pr-1">{{ form.submissions_count }} submission{{ form.submissions_count > 0 ? 's' : '' }}</li>
              <li class="list-disc ml-6 pr-1 text-blue-500" v-if="form.visibility=='draft'">Draft (not public)</li>
              <li class="list-disc ml-6">Edited {{ form.last_edited_human }}</li>
            </ul>
            
            <p v-if="form.closes_at" class="text-yellow-500">
              <span v-if="form.is_closed"> This form stopped accepting submissions on the  {{ displayClosesDate }} </span>
              <span v-else> This form will stop accepting submissions on the {{ displayClosesDate }} </span>
            </p>
            <p v-if="form.max_submissions_count > 0" class="text-yellow-500">
              <span v-if="form.max_number_of_submissions_reached"> The form is now closed because it reached its limit of {{ form.max_submissions_count }} submissions. </span>
              <span v-else> This form will stop accepting submissions after {{ form.max_submissions_count }} submissions. </span>
            </p>

            <div class="mt-4 border-b border-gray-200 dark:border-gray-700">
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
                    <li class="mr-6" v-for="(tabName, i) in tabsList" :key="i+1">
                        <button @click="onTabClick(i+1)" 
                            class="inline-block py-4 rounded-t-lg border-b-2 border-transparent"
                            :class="{'text-blue-600 hover:text-blue-900 dark:text-blue-500 dark:hover:text-blue-500 border-blue-600 dark:border-blue-500':(currentTab==i+1), 'border-gray-100 hover:border-gray-300 hover:text-gray-600':(currentTab!=i+1)}"
                            >{{tabName}}</button>
                    </li>
                </ul>
            </div>

          </div>
        </div>
      </div>
      <div class="flex bg-white">
        <div class="w-full md:w-4/5 lg:w-3/5 md:mx-auto md:max-w-4xl px-4">
          <div class="pb-0">
            <!-- Submissions Tab-->
            <div v-if="currentTab===1" class="py-4">
              <div id="table-page" v-if="form">
                <form-submissions />
              </div>
            </div>

            <!-- Analytics Tab-->
            <div v-if="currentTab===2" class="py-4">
                <h3 class="font-semibold">
                  Form Analytics (last 30 days)
                </h3>
                <form-stats :form="form" />
            </div>
            
            <!-- Share Tab -->
            <div v-if="currentTab===3" class="py-4">
              <div class="mt-4">
                <h3 class="font-semibold text-xl">Share Link</h3>
                <p>Your form is now published and ready to be shared with the world! Copy this link to share your form on social media, messaging apps or via email.</p>
                <copy-content :content="form.share_url" buttonIcon='link' buttonText="Copy Link" />
              </div>

              <div class="mt-6">
                <h3 class="font-semibold text-xl">Embed</h3>
                <p>Embed your form on your website by copying the HTML code below.</p>
                <copy-content :content="embedCode" buttonIcon='code' buttonText="Copy Code" />
              </div>

              <div class="mt-6 w-full flex">
                <v-button 
                  class="sm:w-1/2 mr-4" color="light-gray"
                  v-track.regenerate_form_link_click="{form_id:form.id, form_slug:form.slug}"
                  @click="showGenerateFormLinkModal=true"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-4 text-blue-600 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"
                    />
                  </svg>
                  Regenerate form link
                </v-button>
                <v-button 
                  class="sm:w-1/2" color="light-gray"
                  v-track.url_form_prefill_click="{form_id:form.id, form_slug:form.slug}"
                  @click="showUrlFormPrefillModal=true"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-4 text-blue-600 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16v2a2 2 0 01-2 2H5a2 2 0 01-2-2v-7a2 2 0 012-2h2m3-4H9a2 2 0 00-2 2v7a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-1m-1 4l-3 3m0 0l-3-3m3 3V3"
                      />
                  </svg>
                  Url form pre-fill <pro-tag class="ml-2" />
                </v-button>
              </div>

            </div>
          </div>

          <!--  Regenerate form link modal  -->
          <modal :show="showGenerateFormLinkModal" @close="showGenerateFormLinkModal=false">
            <template #icon>
              <svg class="w-10 h-10 text-blue" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M17 27C16.0681 27 15.6022 27 15.2346 26.8478C14.7446 26.6448 14.3552 26.2554 14.1522 25.7654C14 25.3978 14 24.9319 14 24V17.2C14 16.0799 14 15.5198 14.218 15.092C14.4097 14.7157 14.7157 14.4097 15.092 14.218C15.5198 14 16.0799 14 17.2 14H24C24.9319 14 25.3978 14 25.7654 14.1522C26.2554 14.3552 26.6448 14.7446 26.8478 15.2346C27 15.6022 27 16.0681 27 17M24.2 34H30.8C31.9201 34 32.4802 34 32.908 33.782C33.2843 33.5903 33.5903 33.2843 33.782 32.908C34 32.4802 34 31.9201 34 30.8V24.2C34 23.0799 34 22.5198 33.782 22.092C33.5903 21.7157 33.2843 21.4097 32.908 21.218C32.4802 21 31.9201 21 30.8 21H24.2C23.0799 21 22.5198 21 22.092 21.218C21.7157 21.4097 21.4097 21.7157 21.218 22.092C21 22.5198 21 23.0799 21 24.2V30.8C21 31.9201 21 32.4802 21.218 32.908C21.4097 33.2843 21.7157 33.5903 22.092 33.782C22.5198 34 23.0799 34 24.2 34Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </template>
            <template #title>
              Generate new form link
            </template>
            <div class="p-4">
              <p>
                You can choose between two different URL formats for your form. 
                <span class="font-semibold">Be careful, changing your form URL is not a reversible operation</span>. 
                Make sure to udpate your form URL everywhere where it's used.
              </p>
              <div class="border-t py-4 mt-4">
                <h3 class="text-xl text-gray-700 font-semibold">
                  Human Readable URL
                </h3>
                <p>If your users are going to see this url, you might want to make nice and readable. Example:</p>
                <p class="text-gray-600 p-4 bg-gray-100 rounded-md mt-4">
                  https://opnform.com/forms/contact
                </p>
                <div class="text-center mt-4">
                  <v-button :loading="loadingNewLink" color="outline-blue" @click="regenerateLink('slug')">
                    Generate a Human Readable URL
                  </v-button>
                </div>
              </div>
              <div class="border-t pt-4 mt-4">
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
                  <v-button :loading="loadingNewLink" color="outline-blue" @click="regenerateLink('uuid')">
                    Generate a Random ID URL
                  </v-button>
                </div>
              </div>
            </div>
          </modal>

          <create-template-modal :form="form" :show="showCreateTemplateModal" @close="showCreateTemplateModal=false" />

          <url-form-prefill-modal :form="form" :show="showUrlFormPrefillModal" @close="showUrlFormPrefillModal=false" />

        </div>
      </div>
    </template>
    <div v-else-if="loading" class="text-center w-full p-5">
      <loader class="h-6 w-6 mx-auto" />
    </div>
    <div v-else class="text-center w-full p-5">
      Form not found.
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import store from '~/store'
import Form from 'vform'
import CopyContent from '../../components/open/forms/components/CopyContent'
import Breadcrumb from '../../components/common/Breadcrumb'
import { mapGetters, mapState } from 'vuex'
import ProTag from '../../components/common/ProTag'
import UrlFormPrefillModal from '../../components/pages/forms/UrlFormPrefillModal'
import CreateTemplateModal from '../../components/pages/forms/CreateTemplateModal'
import FormStats from '../../components/open/forms/components/FormStats'
import FormSubmissions from '../../components/open/forms/components/FormSubmissions'
import Dropdown from '../../components/common/Dropdown'

const loadForms = function () {
  store.commit('open/forms/startLoading')
  store.dispatch('open/workspaces/loadIfEmpty').then(() => {
    store.dispatch('open/forms/loadIfEmpty', store.state['open/workspaces'].currentId)
  })
}

export default {
  name: 'EditForm',
  components: { UrlFormPrefillModal, CreateTemplateModal, ProTag, Breadcrumb, Dropdown, CopyContent, FormStats, FormSubmissions },

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
      showUrlFormPrefillModal: false,
      showGenerateFormLinkModal: false,
      showCreateTemplateModal: false,
      tabsList: ['Submissions', 'Analytics', 'Share'],
      currentTab: 1,
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
    },
    embedCode () {
      return '<iframe style="border:none;width:100%;" height="' + this.formHeight + 'px" src="' + this.form.share_url + '"></iframe>'
    },
    formHeight () {
      let height = 200
      if (!this.form.hide_title) {
        height += 60
      }
      height += this.form.properties.filter((property) => {
        return !property.hidden
      }).length * 70

      return height
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
    },
    goBack () {
      this.$router.push({ name: 'home' })
    },
    openEdit () {
      this.$router.push({ name: 'forms.edit', params: { slug: this.form.slug } })
    },
    onTabClick(newTabNo){
      this.currentTab = newTabNo
    }
  }
}
</script>
