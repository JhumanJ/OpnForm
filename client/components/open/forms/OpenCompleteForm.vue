<template>
  <div
    v-if="form"
    class="open-complete-form"
    :dir="form?.layout_rtl ? 'rtl' : 'ltr'"
    :style="{ '--font-family': form.font_family, 'direction': form?.layout_rtl ? 'rtl' : 'ltr' }"
  >
    <link
      v-if="showFontLink && form.font_family"
      rel="stylesheet"
      :href="getFontUrl"
    >

    <div v-if="isPublicFormPage && form.is_password_protected">
      <p class="form-description mb-4 text-gray-700 dark:text-gray-300 px-2">
        {{ t('forms.password_protected') }}
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
          {{ t('forms.submit') }}
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
      v-if="showFormCleanings"
      :hideable="true"
      class="mb-4 mx-2"
      :form="form"
      :specify-form-owner="true"
    />

    <v-transition name="fade">
      <div
        v-if="!isFormSubmitted"
        key="form"
      >
        <open-form
          v-if="formManager && form && !form.is_closed"
          :form-manager="formManager"
          :theme="theme"
          :dark-mode="darkMode"
          :mode="mode"
          :form-properties="form.properties"
          @submit="triggerSubmit"
        >
          <template #submit-btn="{loading}">
            <open-form-button
              :loading="loading || isProcessing"
              :theme="theme"
              :color="form.color"
              class="mt-2 px-8 mx-1"
              :class="submitButtonClass"
              @click.prevent="triggerSubmit"
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
            {{ t('forms.powered_by') }} <span class="font-semibold">{{ t('app.name') }}</span>
          </a>
        </p>
      </div>
      <div
        v-else-if="isFormSubmitted"
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
            {{ t('forms.create_form_free') }}
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

<script setup>
import { FormManager } from '~/lib/forms/FormManager'
import { FormMode } from "~/lib/forms/FormModeStrategy.js"
import ThemeBuilder from "~/lib/forms/themes/ThemeBuilder.js"
import OpenForm from './OpenForm.vue'
import OpenFormButton from './OpenFormButton.vue'
import FormCleanings from '../../pages/forms/show/FormCleanings.vue'
import VTransition from '~/components/global/transitions/VTransition.vue'
import FirstSubmissionModal from '~/components/open/forms/components/FirstSubmissionModal.vue'
import TextBlock from '~/components/forms/TextBlock.vue'

const props = defineProps({
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
})

const emit = defineEmits(['submitted', 'password-entered', 'restarted'])

const { t, setLocale } = useI18n()
const route = useRoute()
const authStore = useAuthStore()
const alert = useAlert()

const formManager = ref(null)
const passwordForm = useForm({ password: null })
const hidePasswordDisabledMsg = ref(false)
const submissionId = ref(route.query.submission_id || null)
const submittedData = ref(null)
const showFirstSubmissionModal = ref(false)

const theme = computed(() => {
  return new ThemeBuilder(props.form.theme, {
    size: props.form.size,
    borderRadius: props.form.border_radius
  }).getAllComponents()
})

const isPublicFormPage = computed(() => {
  return route.name === 'forms-slug'
})

const getFontUrl = computed(() => {
  if(!props.form?.font_family) return null
  const family = props.form.font_family.replace(/ /g, '+')
  return `https://fonts.googleapis.com/css?family=${family}:wght@400,500,700,800,900&display=swap`
})

const isFormOwner = computed(() => {
  return authStore.check && props.form && props.form.creator_id === authStore.user.id
})

const isFormSubmitted = computed(() => formManager.value?.state.isSubmitted ?? false)
const isProcessing = computed(() => formManager.value?.state.isProcessing ?? false)
const showFormCleanings = computed(() => formManager.value?.strategy.display.showFormCleanings ?? false)
const showFontLink = computed(() => formManager.value?.strategy.display.showFontLink ?? false)

watch(() => props.form.language, (newLanguage) => {
  if (newLanguage && typeof newLanguage === 'string') {
    setLocale(newLanguage)
  } else {
    setLocale('en')
  }
}, { immediate: true })

onMounted(async () => {
  if (props.form) {
    formManager.value = new FormManager(props.form, props.mode)
    try {
      console.log("Initializing manager in OpenCompleteForm...")
      await formManager.value.initialize({
        submissionId: route.query.submission_id,
        urlParams: import.meta.client ? new URLSearchParams(window.location.search) : null,
      })
      console.log("Manager initialized.")
    } catch (initError) {
      console.error("Failed to initialize FormManager:", initError)
      alert.error('Could not initialize the form. Please try again.')
    }
  }
})

onBeforeUnmount(() => {
  setLocale('en')
})

const triggerSubmit = async () => {
  if (!formManager.value || isProcessing.value) return

  console.log('Submit triggered in OpenCompleteForm.')

  try {
    const result = await formManager.value.submit()
    
    if (result.success) {
      console.log('Form submission successful via manager.', result)
      submittedData.value = result.submissionData
      
      if (result.backendResponse?.submission_id) {
        submissionId.value = result.backendResponse.submission_id
      }

      if (isFormOwner.value && !useIsIframe() && result.backendResponse?.is_first_submission) {
        showFirstSubmissionModal.value = true
      }
      
      emit('submitted', true)
      
    } else {
      console.warn('Form submission failed via manager, but no error thrown?', result)
      alert.error(t('forms.submission_error'))
    }

  } catch (error) {
    console.error('Form submission error caught in OpenCompleteForm:', error)
    if (error && typeof error === 'object' && !error.errors) {
      alert.error(error.message || t('forms.submission_error'))
    } else if (!error) {
      alert.error(t('forms.submission_error'))
    }
  }
}

const restart = async () => {
  if (!formManager.value) return
  await formManager.value.restart()
  submittedData.value = null
  submissionId.value = null
  emit('restarted', true)
}

const passwordEntered = () => {
  if (passwordForm.password) {
    emit('password-entered', passwordForm.password)
  } else {
    addPasswordError(t('forms.password_required'))
  }
}

const addPasswordError = (msg) => {
  passwordForm.errors.set('password', msg)
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
