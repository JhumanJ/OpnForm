<template>
  <div v-if="form" id="form-editor" class="w-full flex border-t flex-grow relative overflow-x-hidden">
    <!-- Form fields selection -->
    <v-tour name="tutorial" :steps="steps" />
    <div class="w-full md:w-1/2 lg:w-2/5 border-r relative overflow-y-scroll md:max-w-sm flex-shrink-0">
      <div class="p-5 bg-blue-50 border-b text-nt-blue-dark md:hidden">
        We suggest you create this form on a device with a larger screen such as computed. That will allow you
        to preview your form changes.
      </div>
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
      showFormErrorModal: false
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
