<template>
  <div
    v-if="form"
    class="open-complete-form"
    :dir="form?.layout_rtl ? 'rtl' : 'ltr'"
    :style="{ '--font-family': form.font_family, 'direction': form?.layout_rtl ? 'rtl' : 'ltr' }"
  >
    <link
      v-if="formModeStrategy.display.showFontLink && form.font_family"
      rel="stylesheet"
      :href="getFontUrl"
    >

    <div v-if="isPublicFormPage && form.is_password_protected">
      <p class="form-description mb-4 text-gray-700 dark:text-gray-300 px-2">
        {{ $t('forms.password_protected') }}
      </p>
      <div class="form-group flex flex-wrap w-full">
        <div class="relative mb-3 w-full px-2">
          <text-input
            :theme="theme"
            :form="passwordForm"
            name="password"
            native-type="password"
            label="Password"
          />
        </div>
      </div>
      <div class="flex flex-wrap justify-center w-full text-center">
        <open-form-button
          :theme="theme"
          :color="form.color"
          class="my-4"
          @click="passwordEntered"
        >
          {{ $t('forms.submit') }}
        </open-form-button>
      </div>
    </div>

    <v-transition name="fade">
      <div
        v-if="!form.is_password_protected && form.password && !hidePasswordDisabledMsg"
        class="m-2 my-4 flex flex-grow items-end p-4 rounded-md dark:text-yellow-500 bg-yellow-50 dark:bg-yellow-600/20 dark:border-yellow-500"
      >
        <p class="mb-0 text-yellow-600 dark:text-yellow-600 text-sm">
          We disabled the password protection for this form because you are an owner of it.
        </p>
        <UButton
          color="yellow"
          size="xs"
          @click="hidePasswordDisabledMsg = true"
        >
          Close
        </ubutton>
      </div>
    </v-transition>

    <div
      v-if="isPublicFormPage && (form.is_closed || form.visibility=='closed')"
      class="border shadow-sm p-2 my-4 flex items-center rounded-md bg-yellow-100 dark:bg-yellow-600/20 border-yellow-500 dark:border-yellow-500/20"
    >
      <div class="flex-grow">
        <div
          class="mb-0 py-2 px-4 text-yellow-600"
          v-html="form.closed_text"
        />
      </div>
    </div>

    <div
      v-if="isPublicFormPage && form.max_number_of_submissions_reached"
      class="border shadow-sm p-2 my-4 flex items-center rounded-md bg-yellow-100 dark:bg-yellow-600/20 border-yellow-500 dark:border-yellow-500/20"
    >
      <div class="flex-grow">
        <div
          class="mb-0 py-2 px-4 text-yellow-600 dark:text-yellow-600"
          v-html="form.max_submissions_reached_text"
        />
      </div>
    </div>

    <form-cleanings
      v-if="formModeStrategy.display.showFormCleanings"
      :hideable="true"
      class="mb-4 mx-2"
      :form="form"
      :specify-form-owner="true"
    />

    <v-transition name="fade">
      <div
        v-if="!submitted"
        key="form"
      >
        <open-form
          v-if="form && !form.is_closed"
          :form="form"
          :loading="loading"
          :fields="form.properties"
          :theme="theme"
          :dark-mode="darkMode"
          :mode="mode"
          @submit="submitForm"
        >
          <template #submit-btn="{submitForm: handleSubmit}">
            <open-form-button
              :loading="loading"
              :theme="theme"
              :color="form.color"
              class="mt-2 px-8 mx-1"
              :class="submitButtonClass"
              @click.prevent="handleSubmit"
            >
              {{ form.submit_button_text }}
            </open-form-button>
          </template>
        </open-form>
        <p
          v-if="!form.no_branding"
          class="text-center w-full mt-2"
        >
          <a
            href="https://opnform.com?utm_source=form&utm_content=powered_by"
            class="text-gray-400 hover:text-gray-500 dark:text-gray-600 dark:hover:text-gray-500 cursor-pointer hover:underline text-xs"
            target="_blank"
          >
            {{ $t('forms.powered_by') }} <span class="font-semibold">{{ $t('app.name') }}</span>
          </a>
        </p>
      </div>
      <div
        v-else
        key="submitted"
        class="px-2"
      >
        <TextBlock
          v-if="form.submitted_text"
          class="form-description text-gray-700 dark:text-gray-300 whitespace-pre-wrap"
          :content="form.submitted_text"
          :mentions-allowed="true"
          :form="form"
          :form-data="submittedData"
        />
        <open-form-button
          v-if="form.re_fillable"
          :theme="theme"
          :color="form.color"
          class="my-4"
          @click="restart"
        >
          {{ form.re_fill_button_text }}
        </open-form-button>
        <p
          v-if="form.editable_submissions && submissionId"
          class="mt-5"
        >
          <a
            target="_parent"
            :href="form.share_url+'?submission_id='+submissionId"
            class="text-nt-blue hover:underline"
          >
            {{ form.editable_submissions_button_text }}
          </a>
        </p>
        <p
          v-if="!form.no_branding"
          class="mt-5"
        >
          <a
            target="_parent"
            href="https://opnform.com/?utm_source=form&utm_content=create_form_free"
            class="text-nt-blue hover:underline"
          >
            {{ $t('forms.create_form_free') }}
          </a>
        </p>
      </div>
    </v-transition>
    <FirstSubmissionModal
      :show="showFirstSubmissionModal"
      :form="form"
      @close="showFirstSubmissionModal=false"
    />
  </div>
</template>

<script>
import OpenForm from './OpenForm.vue'
import OpenFormButton from './OpenFormButton.vue'
import FormCleanings from '../../pages/forms/show/FormCleanings.vue'
import VTransition from '~/components/global/transitions/VTransition.vue'
import { pendingSubmission } from "~/composables/forms/pendingSubmission.js"
import { usePartialSubmission } from "~/composables/forms/usePartialSubmission.js"
import clonedeep from "clone-deep"
import ThemeBuilder from "~/lib/forms/themes/ThemeBuilder.js"
import FirstSubmissionModal from '~/components/open/forms/components/FirstSubmissionModal.vue'
import { FormMode, createFormModeStrategy } from "~/lib/forms/FormModeStrategy.js"

export default {
  components: { VTransition, OpenFormButton, OpenForm, FormCleanings, FirstSubmissionModal },

  props: {
    form: { type: Object, required: true },
    mode: {
      type: String,
      default: FormMode.LIVE,
      validator: (value) => Object.values(FormMode).includes(value)
    },
    submitButtonClass: { type: String, default: '' },
    darkMode: {
      type: Boolean,
      default: false
    }
  },

  emits: ['submitted', 'password-entered', 'restarted'],

  setup(props) {
    const { setLocale } = useI18n()
    const authStore = useAuthStore()
    
    return {
      setLocale,
      authStore,
      authenticated: computed(() => authStore.check),
      isIframe: useIsIframe(),
      pendingSubmission: pendingSubmission(props.form),
      partialSubmission: usePartialSubmission(props.form),
      confetti: useConfetti()
    }
  },

  data () {
    return {
      loading: false,
      submitted: false,
      passwordForm: useForm({
        password: null
      }),
      hidePasswordDisabledMsg: false,
      submissionId: false,
      submittedData: null,
      showFirstSubmissionModal: false
    }
  },

  computed: {
    /**
     * Gets the comprehensive strategy based on the form mode
     */
    formModeStrategy() {
      return createFormModeStrategy(this.mode)
    },
    isEmbedPopup () {
      return import.meta.client && window.location.href.includes('popup=true')
    },
    theme () {
      return new ThemeBuilder(this.form.theme, {
        size: this.form.size,
        borderRadius: this.form.border_radius
      }).getAllComponents()
    },
    isPublicFormPage () {
      return this.$route.name === 'forms-slug'
    },
    getFontUrl() {
      if(!this.form || !this.form.font_family) return null
      const family = this.form?.font_family.replace(/ /g, '+')
      return `https://fonts.googleapis.com/css?family=${family}:wght@400,500,700,800,900&display=swap`
    },
    isFormOwner() {
      return this.authenticated && this.form && this.form.creator_id === this.authStore.user.id
    }
  },
  watch: {
    'form.language': {
      handler(newLanguage) {
        if (newLanguage && typeof newLanguage === 'string') {
          this.setLocale(newLanguage)
        } else {
          this.setLocale('en')  // Default to English if invalid locale
        }
      },
      immediate: true
    }
  },
  beforeUnmount() {
    this.setLocale('en')
  },

  methods: {
    submitForm (form, onFailure) {
      // Check if we should perform actual submission based on the mode
      if (!this.formModeStrategy.validation.performActualSubmission) {
        this.submitted = true
        this.$emit('submitted', true)
        return
      }

      if (form.busy) return
      this.loading = true

      if (this.form?.enable_partial_submissions) {
        this.partialSubmission.stopSync()
      }

      form.post('/forms/' + this.form.slug + '/answer').then((data) => {
        this.submittedData = form.data()
        useAmplitude().logEvent('form_submission', {
          workspace_id: this.form.workspace_id,
          form_id: this.form.id
        })
    
        const payload = clonedeep({
          type: 'form-submitted',
          form: {
            slug: this.form.slug,
            id: this.form.id,
            redirect_target_url: (this.form.is_pro && data.redirect && data.redirect_url) ? data.redirect_url : null
          },
          submission_data: form.data(),
          completion_time: form['completion_time']
        })

        if (this.isIframe) {
          window.parent.postMessage(payload, '*')
        }
        window.postMessage(payload, '*')
        this.pendingSubmission.remove()
        this.pendingSubmission.removeTimer()

        if (data.redirect && data.redirect_url) {
          window.location.href = data.redirect_url
        }

        if (data.submission_id) {
          this.submissionId = data.submission_id
        }
        if (this.isFormOwner && !this.isIframe && data?.is_first_submission) {
          this.showFirstSubmissionModal = true
        }
        this.loading = false
        this.submitted = true
        this.$emit('submitted', true)

        // If enabled display confetti
        if (this.form.confetti_on_submission) {
          this.confetti.play()
        }
      }).catch((error) => {
        if (this.form?.enable_partial_submissions) {
          this.partialSubmission.startSync()
        }
      
        console.error(error)
        if (error.response && error.data) {
          useAlert().formValidationError(error.data)
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
        this.addPasswordError(this.$t('forms.password_required'))
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
  * {
    font-family: var(--font-family) !important;
  }
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
