<template>
  <div v-if="form" class="open-complete-form">
    <h1 v-if="!form.hide_title" class="mb-4 px-2" v-text="form.title" />

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

    <div v-if="isPublicFormPage && form.is_closed"
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

    <div v-if="getFormCleaningsMsg"
         class="border shadow-sm p-2 my-4 flex items-center rounded-md bg-yellow-100 border-yellow-500"
    >
      <div class="flex-grow">
        <p class="mb-0 py-2 px-4 text-yellow-600">
          You're seeing this because you are an owner of this form. <br>
          All your Pro features are de-activated when sharing this form: <br>

          <span v-html="getFormCleaningsMsg" />
        </p>
      </div>
      <div class="text-right">
        <v-button color="yellow" shade="light" @click="form.cleanings=false">
          Close
        </v-button>
      </div>
    </div>

    <transition
      v-if="!form.is_password_protected && (!isPublicFormPage || (!form.is_closed && !form.max_number_of_submissions_reached))"
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
                     @submit="submitForm"
        >
          <template #submit-btn="{submitForm}">
            <open-form-button :loading="loading" :theme="theme" :color="form.color" class="mt-2 px-8 mx-1"
                                @click.prevent="submitForm"
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
        <p v-if="!form.no_branding" class="mt-5">
          <a target="_parent" href="https://opnform.com/?utm_source=form&utm_content=create_form_free" class="text-nt-blue hover:underline">Create your form for free with OpnForm</a>
        </p>
      </div>
    </transition>
  </div>
</template>

<script>
import Form from 'vform'
import OpenForm from './OpenForm'
import OpenFormButton from './OpenFormButton'
import { themes } from '~/config/form-themes'
import VButton from '../../common/Button'
import VTransition from '../../common/transitions/VTransition'
import FormPendingSubmissionKey from '../../../mixins/forms/form-pending-submission-key'

export default {
  components: { VTransition, VButton, OpenFormButton, OpenForm },

  props: {
    form: { type: Object, required: true },
    creating: { type: Boolean, default: false } // If true, fake form submit
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
      hidePasswordDisabledMsg: false
    }
  },

  computed: {
    isIframe () {
      return window.location !== window.parent.location || window.frameElement
    },
    theme () {
      return this.themes[this.themes.hasOwnProperty(this.form.theme) ? this.form.theme : 'default']
    },
    getFormCleaningsMsg () {
      if (this.form.cleanings && Object.keys(this.form.cleanings).length > 0) {
        let message = ''
        Object.keys(this.form.cleanings).forEach((key) => {
          const fieldName = key.charAt(0).toUpperCase() + key.slice(1)
          let fieldInfo = '<br/>' + fieldName + "<br/><ul class='list-disc list-inside'>"
          this.form.cleanings[key].forEach((msg) => {
            fieldInfo = fieldInfo + '<li>' + msg + '</li>'
          })
          message = message + fieldInfo + '<ul/>'
        })

        return message
      }
      return false
    },
    isPublicFormPage () {
      return this.$route.name === 'forms.show_public'
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

        try {
          window.localStorage.removeItem(this.formPendingSubmissionKey)
        } catch (e) {}

        if (response.data.redirect && response.data.redirect_url) {
          window.location.href = response.data.redirect_url
        }

        this.loading = false
        this.submitted = true
        this.$emit('submitted', true)
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
  .form-description {
    ol {
      @apply list-decimal list-inside;
    }

    ul {
      @apply list-disc list-inside;
    }
  }
}
</style>
