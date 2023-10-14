<template>
  <div v-if="form" class="open-complete-form">
    <h1 v-if="!isHideTitle" class="mb-4 px-2" :class="{'mt-4':isEmbedPopup}" v-text="form.title" />

    <div v-if="isPublicFormPage && form.is_password_protected">
      <p class="form-description mb-4 text-gray-700 dark:text-gray-300 px-2">
        This form is protected by a password.
      </p>
      <div class="form-group flex flex-wrap w-full">
        <div class="relative mb-3 w-full px-2">
          <text-input :form="passwordForm" name="password" native-type="password" label="Password" />
        </div>
      </div>
      <div class="flex flex-wrap justify-center w-full text-center">
        <v-button @click="passwordEntered">
          Submit
        </v-button>
      </div>
    </div>

    <v-transition>
      <div v-if="!form.is_password_protected && form.password && !hidePasswordDisabledMsg"
           class="border shadow-sm p-2 my-4 flex items-center rounded-md bg-yellow-100 border-yellow-500"
      >
        <div class="flex flex-grow">
          <p class="mb-0 py-2 px-4 text-yellow-600">
            We disabled the password protection for this form because you are an owner of it.
          </p>
          <v-button color="yellow" @click="hidePasswordDisabledMsg=true">
            OK
          </v-button>
        </div>
      </div>
    </v-transition>

    <div v-if="isPublicFormPage && (form.is_closed || form.visibility=='closed')"
         class="border shadow-sm p-2 my-4 flex items-center rounded-md bg-yellow-100 border-yellow-500"
    >
      <div class="flex-grow">
        <p class="mb-0 py-2 px-4 text-yellow-600" v-html="form.closed_text" />
      </div>
    </div>

    <div v-if="isPublicFormPage && form.max_number_of_submissions_reached"
         class="border shadow-sm p-2 my-4 flex items-center rounded-md bg-yellow-100 border-yellow-500"
    >
      <div class="flex-grow">
        <p class="mb-0 py-2 px-4 text-yellow-600" v-html="form.max_submissions_reached_text" />
      </div>
    </div>

    <form-cleanings v-if="!adminPreview" :hideable="true" class="mb-4 mx-2" :form="form" :specify-form-owner="true" />

    <transition
      v-if="!form.is_password_protected && (!isPublicFormPage || (!form.is_closed && !form.max_number_of_submissions_reached && form.visibility!='closed'))"
      enter-active-class="duration-500 ease-out"
      enter-class="translate-x-full opacity-0"
      enter-to-class="translate-x-0 opacity-100"
      leave-active-class="duration-500 ease-in"
      leave-class="translate-x-0 opacity-100"
      leave-to-class="translate-x-full opacity-0"
      mode="out-in"
    >
      <div v-if="!submitted" key="form">
        <p v-if="form.description && form.description !==''"
           class="form-description mb-4 text-gray-700 dark:text-gray-300 whitespace-pre-wrap px-2"
           v-html="form.description"
        />
        <open-form v-if="form"
                     :form="form"
                     :loading="loading"
                     :fields="form.properties"
                     :theme="theme"
                     :admin-preview="adminPreview"
                     @submit="submitForm"
        >
          <template #submit-btn="{submitForm}">
            <open-form-button :loading="loading" :theme="theme" :color="form.color" class="mt-2 px-8 mx-1"
                                :class="submitButtonClass" @click.prevent="submitForm"
            >
              {{ form.submit_button_text }}
            </open-form-button>
          </template>
        </open-form>
        <p v-if="!form.no_branding" class="text-center w-full mt-2">
          <a href="https://opnform.com?utm_source=form&utm_content=powered_by"
             class="text-gray-400 hover:text-gray-500 dark:text-gray-600 dark:hover:text-gray-500 cursor-pointer hover:underline text-xs"
             target="_blank"
          >Powered by <span class="font-semibold">OpnForm</span></a>
        </p>
      </div>
      <div v-else key="submitted" class="px-2">
        <p class="form-description text-gray-700 dark:text-gray-300 whitespace-pre-wrap" v-html="form.submitted_text " />
        <open-form-button v-if="form.re_fillable" :theme="theme" :color="form.color" class="my-4" @click="restart">
          {{ form.re_fill_button_text }}
        </open-form-button>
        <p v-if="form.editable_submissions && submissionId" class="mt-5">
          <a target="_parent" :href="form.share_url+'?submission_id='+submissionId" class="text-nt-blue hover:underline">
            {{ form.editable_submissions_button_text }}
          </a>
        </p>
        <p v-if="!form.no_branding" class="mt-5">
          <a target="_parent" href="https://opnform.com/?utm_source=form&utm_content=create_form_free" class="text-nt-blue hover:underline">Create your form for free with OpnForm</a>
        </p>
      </div>
    </transition>
  </div>
</template>

<script>
import Form from 'vform'
import OpenForm from './OpenForm.vue'
import OpenFormButton from './OpenFormButton.vue'
import { themes } from '~/config/form-themes.js'
import VButton from '../../common/Button.vue'
import VTransition from '../../common/transitions/VTransition.vue'
import FormPendingSubmissionKey from '../../../mixins/forms/form-pending-submission-key.js'
import FormCleanings from '../../pages/forms/show/FormCleanings.vue'

export default {
  components: { VTransition, VButton, OpenFormButton, OpenForm, FormCleanings },

  props: {
    form: { type: Object, required: true },
    creating: { type: Boolean, default: false }, // If true, fake form submit
    adminPreview: { type: Boolean, default: false }, // If used in FormEditorPreview
    submitButtonClass: { type: String, default: '' }
  },

  mixins: [FormPendingSubmissionKey],

  data () {
    return {
      loading: false,
      submitted: false,
      themes: themes,
      passwordForm: new Form({
        password: null
      }),
      hidePasswordDisabledMsg: false,
      submissionId: false
    }
  },

  computed: {
    isIframe () {
      return window.location !== window.parent.location || window.frameElement
    },
    isEmbedPopup () {
      return window.location.href.includes('popup=true')
    },
    theme () {
      return this.themes[this.themes.hasOwnProperty(this.form.theme) ? this.form.theme : 'default']
    },
    isPublicFormPage () {
      return this.$route.name === 'forms.show_public'
    },
    isHideTitle () {
      return this.form.hide_title || window.location.href.includes('hide_title=true')
    }
  },

  mounted () {
  },

  methods: {
    submitForm (form, onFailure) {
      if (this.creating) {
        this.submitted = true
        this.$emit('submitted', true)
        return
      }

      this.loading = true
      this.closeAlert()
      form.post('/api/forms/' + this.form.slug + '/answer').then((response) => {
        this.$logEvent('form_submission', {
          workspace_id: this.form.workspace_id,
          form_id: this.form.id
        })

        if (this.isIframe) {
          window.parent.postMessage({
            type: 'form-submitted',
            form: {
              slug: this.form.slug,
              id: this.form.id
            },
            submission_data: form.data()
          }, '*')
        }
        window.postMessage({
          type: 'form-submitted',
          form: {
            slug: this.form.slug,
            id: this.form.id
          },
          submission_data: form.data()
        }, '*')


        try {
          window.localStorage.removeItem(this.formPendingSubmissionKey)
        } catch (e) {}

        if (response.data.redirect && response.data.redirect_url) {
          window.location.href = response.data.redirect_url
        }

        if (response.data.submission_id) {
          this.submissionId = response.data.submission_id
        }

        this.loading = false
        this.submitted = true
        this.$emit('submitted', true)

        // If enabled display confetti
        if(this.form.confetti_on_submission){
          this.playConfetti()
        }

      }).catch((error) => {
        if (error.response.data && error.response.data.message) {
          this.alertError(error.response.data.message)
        }
        this.loading = false
        onFailure()
      })
    },
    restart () {
      this.submitted = false
      this.$emit('restarted', true)
    },
    passwordEntered () {
      if (this.passwordForm.password !== '' && this.passwordForm.password !== null) {
        this.$emit('password-entered', this.passwordForm.password)
      } else {
        this.addPasswordError('The Password field is required.')
      }
    },
    addPasswordError (msg) {
      this.passwordForm.errors.set('password', msg)
    }
  }
}
</script>

<style lang="scss">
.open-complete-form {
  .form-description, .nf-text {
    ol {
      @apply list-decimal list-inside;
      margin-left: 10px;
    }

    ul {
      @apply list-disc list-inside;
      margin-left: 10px;
    }
  }
}
</style>
