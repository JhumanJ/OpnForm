<template>
  <div
    v-if="form"
    class="open-complete-form"
    :dir="form?.layout_rtl ? 'rtl' : 'ltr'"
    :style="{ '--font-family': form.font_family, 'direction': form?.layout_rtl ? 'rtl' : 'ltr',  '--form-color': form.color }"
  >
    <ClientOnly>
      <Teleport to="head">
        <link
          v-if="showFontLink && form.font_family"
          :key="form.font_family"
          :href="getFontUrl"
          rel="stylesheet"
          crossorigin="anonymous"
          referrerpolicy="no-referrer"
        >
      </Teleport>
    </ClientOnly>

    <v-transition name="fade" mode="out-in">
      <div v-if="isAutoSubmit" key="auto-submit" class="text-center p-6">
        <Loader class="h-6 w-6 text-nt-blue mx-auto" />
      </div>

      <div v-else key="form-content">
        <div v-if="isPublicFormPage && form.is_password_protected">
          <p class="form-description text-gray-700 dark:text-gray-300 px-2">
            {{ t('forms.password_protected') }}
          </p>
          <div class="form-group flex flex-wrap w-full">
            <div class="relative w-full px-2">
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

        <div v-if="!form.is_password_protected && form.password && !hidePasswordDisabledMsg" 
          class="m-2 my-4">
          <UAlert
            :close-button="{ icon: 'i-heroicons-x-mark-20-solid', color: 'gray', variant: 'link', padded: false }"
            color="yellow"
            variant="subtle"
            icon="i-material-symbols-info-outline"
            @close="hidePasswordDisabledMsg = true"
            title="Password protection has been disabled since you are the owner of this form."
          />
        </div>


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
          v-else-if="isPublicFormPage && form.max_number_of_submissions_reached"
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

        <v-transition name="fade" v-if="form && !form.is_password_protected">
          <div
            v-if="!isFormSubmitted"
            key="form"
          >
            <open-form
              v-if="formManager && form && shouldDisplayForm"
              :form-manager="formManager"
              :theme="theme"
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
    </v-transition>
  </div>
</template>

<script setup>
import { useFormManager } from '~/lib/forms/composables/useFormManager'
import { FormMode } from "~/lib/forms/FormModeStrategy.js"
import ThemeBuilder from "~/lib/forms/themes/ThemeBuilder.js"
import OpenForm from './OpenForm.vue'
import OpenFormButton from './OpenFormButton.vue'
import FormCleanings from '../../pages/forms/show/FormCleanings.vue'
import VTransition from '~/components/global/transitions/VTransition.vue'
import FirstSubmissionModal from '~/components/open/forms/components/FirstSubmissionModal.vue'
import TextBlock from '~/components/forms/TextBlock.vue'
import { useForm } from '~/composables/useForm'
import { useAlert } from '~/composables/useAlert'
import { useI18n } from 'vue-i18n'
import { useIsIframe } from '~/composables/useIsIframe'
import Loader from '~/components/global/Loader.vue'

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
const workingFormStore = useWorkingFormStore()

const passwordForm = useForm({ password: null })
const hidePasswordDisabledMsg = ref(false)
const submissionId = ref(route.query.submission_id || null)
const submittedData = ref(null)
const showFirstSubmissionModal = ref(false)

// Check for auto_submit parameter during setup
const isAutoSubmit = ref(import.meta.client && window.location.href.includes('auto_submit=true'))

// Create a reactive reference directly from the prop
const darkModeRef = toRef(props, 'darkMode')

// Add back the local theme computation
const theme = computed(() => {
  return new ThemeBuilder(props.form.theme, {
    size: props.form.size,
    borderRadius: props.form.border_radius
  }).getAllComponents()
})

let formManager = null
if (props.form) {
  formManager = useFormManager(props.form, props.mode, {
    darkMode: darkModeRef
  })
  formManager.initialize({
    submissionId: submissionId,
    urlParams: import.meta.client ? new URLSearchParams(window.location.search) : null,
  })
}

// Share the structure service with the working form store only when in admin edit context
watch(() => formManager?.strategy?.value?.admin?.showAdminControls, (showAdminControls) => {
  if (workingFormStore && formManager?.structure && showAdminControls) {
    workingFormStore.setStructureService(formManager.structure)
  }
}, { immediate: true }) 

// Add a watcher to update formManager's darkMode whenever darkModeRef changes
watch(darkModeRef, (newDarkMode) => {
  if (formManager) {
    formManager.setDarkMode(newDarkMode)
  }
})

// If auto_submit is true, trigger submit after component is mounted
onMounted(() => {
  if (isAutoSubmit.value && formManager) {
    // Using nextTick to ensure form is fully rendered and initialized
    nextTick(() => {
      triggerSubmit()
    })
  }
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

const isFormSubmitted = computed(() => formManager?.state.isSubmitted ?? false)
const isProcessing = computed(() => formManager?.state.isProcessing ?? false)
const showFormCleanings = computed(() => formManager?.strategy.value.display.showFormCleanings ?? false)
const showFontLink = computed(() => formManager?.strategy.value.display.showFontLink ?? false)
const shouldDisplayForm = computed(() => {
  return (!props.form.is_closed && !props.form.max_number_of_submissions_reached) || formManager?.strategy?.value.admin?.showAdminControls
})

watch(() => props.form.language, (newLanguage) => {
  if (newLanguage && typeof newLanguage === 'string') {
    setLocale(newLanguage)
  } else {
    setLocale('en')
  }
}, { immediate: true })

onBeforeUnmount(() => {
  setLocale('en')
})

const handleScrollToError = () => {
  if (import.meta.server) return

  nextTick(() => {
      const firstErrorElement = document.querySelector('.form-group [error], .form-group .has-error')
      if (firstErrorElement) {
        const headerOffset = 60 // Offset for fixed headers, adjust as needed
        const elementPosition = firstErrorElement.getBoundingClientRect().top
        const offsetPosition = elementPosition + window.scrollY - headerOffset

        window.scrollTo({
            top: offsetPosition,
            behavior: 'smooth'
        })
      } 
  })
}

const triggerSubmit = async () => {
  if (!formManager || isProcessing.value) return

  formManager.submit({
    submissionId: submissionId.value
  }).then(result => {
      if (result) {
        submittedData.value = result || {}
        
        if (result?.submission_id) {
          submissionId.value = result.submission_id
        }

        if (isFormOwner.value && !useIsIframe() && result?.is_first_submission) {
          showFirstSubmissionModal.value = true
        }
        
        emit('submitted', true)
              } else {
        console.warn('Form submission failed via composable, but no error thrown?')
        alert.error(t('forms.submission_error'))
      }
    })
    .catch(error => {
      if (error.response && error.response.status === 422 && error.data) {
        useAlert().formValidationError(error.data)
      } else if (error.message) {
        useAlert().error(error.message)
      }
      handleScrollToError()
    }).finally(() => {
      isAutoSubmit.value = false
    })
}

const restart = async () => {
  if (!formManager) return
  await formManager.restart()
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

defineExpose({
  addPasswordError
})

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
