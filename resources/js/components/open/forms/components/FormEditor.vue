<template>
  <div v-if="form" id="form-editor" class="w-full flex border-t flex-grow relative overflow-x-hidden">
    <!-- Form fields selection -->
    <v-tour name="tutorial" :steps="steps" />
    <div class="w-full md:w-1/2 lg:w-2/5 border-r relative overflow-y-scroll md:max-w-sm flex-shrink-0 p-4">
      <div class="p-5 bg-blue-50 border-b text-nt-blue-dark md:hidden">
        We suggest you create this form on a device with a larger screen such as computed. That will allow you
        to preview your form changes.
      </div>
      
      <a href="javascript:;" @click="$router.back()" class="flex text-blue mb-5">
        <svg class="w-4 h-4 text-blue mt-1" viewBox="0 0 6 10" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M5 9L1 5L5 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Go back
      </a>
      <h3 class="font-semibold text-lg">{{form.title}}</h3>
      <small>Edited 2 months ago</small>
      <v-button v-track.save_form_click class="hidden md:block w-full mt-2 mb-8" :loading="updateFormLoading" @click="saveForm">
        <svg class="w-4 h-4 text-white inline mr-1" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M14.6667 1L5.49999 10.1667L1.33333 6" stroke="currentColor" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Save changes
      </v-button>

      <form-information />
      <form-structure />
      <form-customization />
      <form-about-submission />
      <form-notifications />
      <form-security-privacy />
      <form-custom-code />
      <form-integrations />
    </div>

    <form-editor-preview />

    <!-- Form Error Modal -->
    <form-error-modal :show="showFormErrorModal"
                      :validation-error-response="validationErrorResponse"
                      @close="showFormErrorModal=false"
    />
  </div>
  <div v-else class="flex justify-center items-center">
    <loader class="w-6 h-6" />
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import FormErrorModal from './form-components/FormErrorModal'
import FormInformation from './form-components/FormInformation'
import FormStructure from './form-components/FormStructure'
import FormCustomization from './form-components/FormCustomization'
import FormCustomCode from './form-components/FormCustomCode'
import FormAboutSubmission from './form-components/FormAboutSubmission'
import FormNotifications from './form-components/FormNotifications'
import FormIntegrations from './form-components/FormIntegrations'
import FormEditorPreview from './form-components/FormEditorPreview'
import FormSecurityPrivacy from './form-components/FormSecurityPrivacy'

export default {
  name: 'FormEditor',
  components: {
    FormEditorPreview,
    FormIntegrations,
    FormNotifications,
    FormAboutSubmission,
    FormCustomCode,
    FormCustomization,
    FormStructure,
    FormInformation,
    FormErrorModal,
    FormSecurityPrivacy
  },
  props: {
    validationErrorResponse: {
      required: false,
      type: Object
    },
  },

  data () {
    return {
      showFormErrorModal: false,
      updateFormLoading: false
    }
  },

  computed: {
    ...mapGetters({
      user: 'auth/user'
    }),
    form: {
      get () {
        return this.$store.state['open/working_form'].content
      },
      /* We add a setter */
      set (value) {
        this.$store.commit('open/working_form/set', value)
      }
    },
    steps () {
      return [
        {
          target: '#v-step-0',
          header: {
            title: 'Welcome to the OpenForm Editor!'
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

  mounted () {
    this.$emit('mounted')
    this.startTour()
  },

  methods: {
    startTour () {
      if (!this.user.has_forms) {
        this.$tours.tutorial.start()
      }
    },
    showValidationErrors () {
      this.showFormErrorModal = true
    },
    saveForm () {
      alert("Comming Soon...")
    },
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
