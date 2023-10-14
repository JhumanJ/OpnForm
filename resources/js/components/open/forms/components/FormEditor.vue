<template>
  <div v-if="form" id="form-editor" class="relative flex w-full flex-col grow max-h-screen">
    <!--  Navbar  -->
    <div class="w-full border-b p-2 flex items-center justify-between bg-white">
      <a v-if="backButton" href="#" class="ml-2 flex text-blue font-semibold text-sm"
         @click.prevent="$router.back()"
      >
        <svg class="w-3 h-3 text-blue mt-1 mr-1" viewBox="0 0 6 10" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M5 9L1 5L5 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                stroke-linejoin="round"
          />
        </svg>
        Go back
      </a>
      <div class="hidden md:flex items-center ml-3">
        <h3 class="font-semibold text-lg max-w-[14rem] truncate text-gray-500">
          {{ form.title }}
        </h3>
      </div>

      <div class="flex items-center" :class="{'mx-auto md:mx-0':!backButton}">
        <div class="hidden md:block mr-10 relative">
          <a href="#"
             class="text-sm px-3 py-2 hover:bg-gray-50 cursor-pointer rounded-md text-gray-500 px-0 sm:px-3 hover:text-gray-800 cursor-pointer mt-1"
             @click.prevent="openCrisp"
          >
            Help
          </a>
        </div>
        <v-button v-track.save_form_click size="small" class="w-full px-8 md:px-4 py-2"
                  :loading="updateFormLoading" :class="saveButtonClass"
                  @click="saveForm"
        >
          <svg class="w-4 h-4 text-white inline mr-1 -mt-1" viewBox="0 0 24 24" fill="none"
               xmlns="http://www.w3.org/2000/svg"
          >
            <path
              d="M17 21V13H7V21M7 3V8H15M19 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H16L21 8V19C21 19.5304 20.7893 20.0391 20.4142 20.4142C20.0391 20.7893 19.5304 21 19 21Z"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            />
          </svg>
          <template v-if="form.visibility === 'public'">
            Publish Form
          </template>
          <template v-else>
            Save Changes
          </template>
        </v-button>
      </div>
    </div>

    <div class="w-full flex grow overflow-y-scroll relative">
      <div class="relative w-full shrink-0 overflow-y-scroll border-r md:w-1/2 md:max-w-sm lg:w-2/5">
        <div class="border-b bg-blue-50 p-5 text-nt-blue-dark md:hidden">
          Please create this form on a device with a larger screen. That will allow you to preview your form changes.
        </div>

        <form-information/>
        <form-structure/>
        <form-customization/>
        <form-about-submission/>
        <form-notifications/>
        <form-security-privacy/>
        <form-custom-seo />
        <form-custom-code/>
        <form-integrations/>
      </div>

      <form-editor-preview/>

      <form-field-edit-sidebar/>
      <add-form-block-sidebar/>

      <!-- Form Error Modal -->
      <form-error-modal
        :show="showFormErrorModal"
        :validation-error-response="validationErrorResponse"
        @close="showFormErrorModal=false"
      />
    </div>
  </div>
  <div v-else class="flex justify-center items-center">
    <loader class="w-6 h-6"/>
  </div>
</template>

<script>
import {mapGetters} from 'vuex'
import AddFormBlockSidebar from './form-components/AddFormBlockSidebar.vue'
import FormFieldEditSidebar from '../fields/FormFieldEditSidebar.vue'
import FormErrorModal from './form-components/FormErrorModal.vue'
import FormInformation from './form-components/FormInformation.vue'
import FormStructure from './form-components/FormStructure.vue'
import FormCustomization from './form-components/FormCustomization.vue'
import FormCustomCode from './form-components/FormCustomCode.vue'
import FormAboutSubmission from './form-components/FormAboutSubmission.vue'
import FormNotifications from './form-components/FormNotifications.vue'
import FormIntegrations from './form-components/FormIntegrations.vue'
import FormEditorPreview from './form-components/FormEditorPreview.vue'
import FormSecurityPrivacy from './form-components/FormSecurityPrivacy.vue'
import FormCustomSeo from './form-components/FormCustomSeo.vue'
import saveUpdateAlert from '../../../../mixins/forms/saveUpdateAlert.js'
import fieldsLogic from '../../../../mixins/forms/fieldsLogic.js'

export default {
  name: 'FormEditor',
  components: {
    AddFormBlockSidebar,
    FormFieldEditSidebar,
    FormEditorPreview,
    FormIntegrations,
    FormNotifications,
    FormAboutSubmission,
    FormCustomCode,
    FormCustomization,
    FormStructure,
    FormInformation,
    FormErrorModal,
    FormSecurityPrivacy,
    FormCustomSeo
  },
  mixins: [saveUpdateAlert, fieldsLogic],
  props: {
    isEdit: {
      required: false,
      type: Boolean,
      default: false
    },
    isGuest: {
      required: false,
      type: Boolean,
      default: false
    },
    backButton: {
      required: false,
      type: Boolean,
      default: true
    },
    saveButtonClass: {
      required: false,
      type: String,
      default: ''
    }
  },

  data() {
    return {
      showFormErrorModal: false,
      validationErrorResponse: null,
      updateFormLoading: false,
      createdFormId: null
    }
  },

  computed: {
    ...mapGetters({
      user: 'auth/user'
    }),
    form: {
      get() {
        return this.$store.state['open/working_form'].content
      },
      /* We add a setter */
      set(value) {
        this.$store.commit('open/working_form/set', value)
      }
    },
    createdForm() {
      return this.$store.getters['open/forms/getById'](this.createdFormId)
    },
    workspace() {
      return this.$store.getters['open/workspaces/getCurrent']()
    },
    steps() {
      return [
        {
          target: '#v-step-0',
          header: {
            title: 'Welcome to the OpnForm Editor!'
          },
          content: 'Discover <strong>your form Editor</strong>!'
        },
        {
          target: '#v-step-1',
          header: {
            title: 'Change your form fields'
          },
          content: 'Here you can decide which field to include or not, but also the ' +
            'order you want your fields to be and so on. You also have custom options available for each field, just ' +
            'click the blue cog.'
        },
        {
          target: '#v-step-2',
          header: {
            title: 'Notifications, Customizations and more!'
          },
          content: 'Many more options are available: change colors, texts and receive a ' +
            'notifications whenever someones submits your form.'
        },
        {
          target: '.v-last-step',
          header: {
            title: 'Create your form'
          },
          content: 'Click this button when you\'re done to save your form!'
        }
      ]
    },
    helpUrl: () => window.config.links.help
  },

  watch: {},

  mounted() {
    this.$emit('mounted')
    this.$root.hideNavbar()
  },

  beforeDestroy () {
    this.$root.hideNavbar(false)
  },

  methods: {
    openCrisp () {
      window.$crisp.push(['do', 'chat:show'])
      window.$crisp.push(['do', 'chat:open'])
    },
    showValidationErrors() {
      this.showFormErrorModal = true
    },
    saveForm() {
      this.form.properties = this.validateFieldsLogic(this.form.properties)
      if(this.isGuest) {
        this.saveFormGuest()
      } else if (this.isEdit) {
        this.saveFormEdit()
      } else {
        this.saveFormCreate()
      }
    },
    saveFormEdit() {
      if (this.updateFormLoading) return

      this.updateFormLoading = true
      this.validationErrorResponse = null
      this.form.put('/api/open/forms/{id}/'.replace('{id}', this.form.id)).then((response) => {
        const data = response.data
        this.$store.commit('open/forms/addOrUpdate', data.form)
        this.$emit('on-save')
        this.$router.push({name: 'forms.show', params: {slug: this.form.slug}})
        this.$logEvent('form_saved', {form_id: this.form.id, form_slug: this.form.slug})
        this.displayFormModificationAlert(data)
      }).catch((error) => {
        if (error.response.status === 422) {
          this.validationErrorResponse = error.response.data
          this.showValidationErrors()
        }
      }).finally(() => {
        this.updateFormLoading = false
      })
    },
    saveFormCreate() {
      if (this.updateFormLoading) return
      this.form.workspace_id = this.workspace.id
      this.validationErrorResponse = null

      this.updateFormLoading = true
      this.form.post('/api/open/forms').then((response) => {
        this.$store.commit('open/forms/addOrUpdate', response.data.form)
        this.$emit('on-save')
        this.createdFormId = response.data.form.id

        this.$logEvent('form_created', {form_id: response.data.form.id, form_slug: response.data.form.slug})
        this.$crisp.push(['set', 'session:event', [[['form_created', {
          form_id: response.data.form.id,
          form_slug: response.data.form.slug
        }, 'blue']]]])
        this.displayFormModificationAlert(response.data)
        this.$router.push({
          name: 'forms.show',
          params: {
            slug: this.createdForm.slug,
            new_form: response.data.users_first_form
          }
        })
      }).catch((error) => {
        if (error.response && error.response.status === 422) {
          this.validationErrorResponse = error.response.data
          this.showValidationErrors()
        }
      }).finally(() => {
        this.updateFormLoading = false
      })
    },
    saveFormGuest() {
      this.$emit('openRegister')
    }
  }
}
</script>

<style lang="scss">
.v-step {
  color: white;

  .v-step__header, .v-step__content {
    color: white;

    div {
      color: white;
    }
  }

}
</style>
