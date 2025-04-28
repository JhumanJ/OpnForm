<template>
  <div
    v-if="form"
    id="form-editor"
    class="relative flex w-full flex-col grow max-h-screen"
  >
    <!-- Loading overlay -->
    <div
      v-if="updateFormLoading"
      class="absolute inset-0 bg-white bg-opacity-70 z-50 flex items-center justify-center"
    >
      <loader class="h-6 w-6 text-blue-500" />
    </div>
    <div
      class="border-b bg-white md:hidden fixed inset-0 w-full z-50 flex flex-col items-center justify-center"
    >
      <Icon
        name="heroicons:exclamation-circle"
        class="w-10 h-10 text-nt-blue-dark"
      />
      <div class="p-5 text-nt-blue-dark text-center">
        OpnForm is not optimized for mobile devices. Please open this page on a device with a larger screen.
      </div>
      <div>
        <UButton
          color="white"
          size="lg"
          class="w-full"
          :to="{ name: 'home' }"
        >
          Back to dashboard
        </UButton>
      </div>
    </div>

    <FormEditorNavbar
      :back-button="backButton"
      :update-form-loading="updateFormLoading"
      :save-button-class="saveButtonClass"
      @go-back="goBack"
      @save-form="saveForm"
    >
      <template #before-save>
        <slot name="before-save" />
      </template>
    </FormEditorNavbar>

    <FormEditorErrorHandler>
      <div
        v-show="activeTab !== 2"
        class="w-full flex grow overflow-y-scroll relative bg-white"
      >
        <div
          class="relative w-full shrink-0 overflow-y-scroll border-r md:w-1/2 md:max-w-xs lg:w-2/5"
        >
          <VForm
            size="sm"
            @submit.prevent=""
          >
            <div
              v-show="activeTab === 0"
            >
              <FormFieldsEditor />
            </div>
            <div
              v-show="activeTab === 1"
            >
              <FormCustomization />
            </div>
          </VForm>
        </div>

        <FormEditorPreview />

        <FormEditorSidebar />
      </div>
    </FormEditorErrorHandler>

    <FormSettings v-show="activeTab === 2" />

    <!-- Form Error Modal -->
    <FormErrorModal
      :show="showFormErrorModal"
      :validation-error-response="validationErrorResponse"
      @close="showFormErrorModal = false"
    />

    <!-- Logic Confirmation Modal -->
    <LogicConfirmationModal
      :is-visible="showLogicConfirmationModal"
      :errors="logicErrors"
      @cancel="handleLogicConfirmationCancel"
      @confirm="handleLogicConfirmationConfirm"
    />
  </div>
  <div
    v-else
    class="flex justify-center items-center p-8"
  >
    <Loader class="w-6 h-6" />
  </div>
</template>

<script>
import FormEditorNavbar from './FormEditorNavbar.vue'
import FormEditorSidebar from "./form-components/FormEditorSidebar.vue"
import FormErrorModal from "./form-components/FormErrorModal.vue"
import FormFieldsEditor from './FormFieldsEditor.vue'
import FormCustomization from "./form-components/FormCustomization.vue"
import FormEditorPreview from "./form-components/FormEditorPreview.vue"
import { useFormLogic } from "~/composables/forms/useFormLogic.js"
import opnformConfig from "~/opnform.config.js"
import { captureException } from "@sentry/core"
import FormSettings from './form-components/FormSettings.vue'
import FormEditorErrorHandler from '~/components/open/forms/components/FormEditorErrorHandler.vue'
import { setFormDefaults } from '~/composables/forms/initForm.js'
import { breakpointsTailwind, useBreakpoints } from '@vueuse/core'
import LogicConfirmationModal from '~/components/forms/LogicConfirmationModal.vue'

export default {
  name: "FormEditor",
  components: {
    FormEditorNavbar,
    FormEditorErrorHandler,
    FormEditorSidebar,
    FormEditorPreview,
    FormCustomization,
    FormFieldsEditor,
    FormErrorModal,
    FormSettings,
    LogicConfirmationModal
  },
  props: {
    isEdit: {
      required: false,
      type: Boolean,
      default: false,
    },
    isGuest: {
      required: false,
      type: Boolean,
      default: false,
    },
    backButton: {
      required: false,
      type: Boolean,
      default: true,
    },
    saveButtonClass: {
      required: false,
      type: String,
      default: "",
    },
  },

  emits: ['mounted', 'on-save', 'openRegister', 'go-back', 'save-form'],

  setup() {
    // Check if the editor is visible on smaller screens then send an email
    const breakpoints = useBreakpoints(breakpointsTailwind)
    const isVisible = ref(breakpoints.smaller("md"))
    watch(isVisible, (newValue) => {
      if (newValue && form?.value && form?.value?.id) {
        opnFetch('/open/forms/' + form.value.id + '/mobile-editor-email')
      }
    })
    

    const { user } = storeToRefs(useAuthStore())
    const formsStore = useFormsStore()
    const { content: form } = storeToRefs(useWorkingFormStore())
    const { getCurrent: workspace } = storeToRefs(useWorkspacesStore())
    const workingFormStore = useWorkingFormStore()
    
    return {
      appStore: useAppStore(),
      crisp: useCrisp(),
      amplitude: useAmplitude(),
      opnformConfig,
      workspace,
      formsStore,
      form,
      user,
      workingFormStore,
      activeTab: computed(() => workingFormStore.activeTab)
    }
  },

  data() {
    return {
      showFormErrorModal: false,
      showLogicConfirmationModal: false,
      validationErrorResponse: null,
      updateFormLoading: false,
      createdFormSlug: null,
      logicErrors: [],
    }
  },

  computed: {
    createdForm() {
      return this.formsStore.getByKey(this.createdFormSlug)
    }
  },

  mounted() {
    this.$emit("mounted")
    this.workingFormStore.activeTab = 0
    useAmplitude().logEvent('form_editor_viewed')
    this.appStore.hideNavbar()
    if (!this.isEdit) {
      this.$nextTick(() => {
        this.workingFormStore.openAddFieldSidebar()
      })
    }
  },

  beforeUnmount() {
    this.appStore.showNavbar()
  },

  methods: {
    goBack() {
      if (this.isEdit) {
        useRouter().push({ name: 'forms-slug-show-submissions', params: {slug:this.form.slug} })
      } else {
        useRouter().push({ name: 'home' })
      }
    },
    displayFormModificationAlert(responseData) {
      const alert = useAlert()
      if (
        responseData.form &&
        responseData.form.cleanings &&
        Object.keys(responseData.form.cleanings).length > 0
      ) {
        alert.warning(responseData.message)
      } else if (responseData.message) {
        alert.success(responseData.message)
      }
    },
    openCrisp() {
      this.crisp.openChat()
    },
    showValidationErrors() {
      this.showFormErrorModal = true
    },
    saveForm() {
      // Apply defaults to the form
      const defaultedData = setFormDefaults(this.form.data())
      this.form.fill(defaultedData)
  
      // Check for logic errors
      const { getLogicErrors } = useFormLogic()
      this.logicErrors = getLogicErrors(this.form.properties)
      
      if (this.logicErrors.length > 0) {
        this.showLogicConfirmationModal = true
        return
      }
      
      this.proceedWithSave()
    },
    proceedWithSave() {
      if (this.logicErrors.length > 0) {
        // Clean invalid logic before saving using the comprehensive validator
        const { validatePropertiesLogic } = useFormLogic()
        this.form.properties = validatePropertiesLogic(this.form.properties)
      }

      if (this.isGuest) {
        this.saveFormGuest()
      } else if (this.isEdit) {
        this.saveFormEdit()
      } else {
        this.saveFormCreate()
      }
    },
    handleLogicConfirmationCancel() {
      this.showLogicConfirmationModal = false
    },
    handleLogicConfirmationConfirm() {
      this.showLogicConfirmationModal = false
      this.proceedWithSave()
    },
    saveFormEdit() {
      if (this.updateFormLoading) return

      this.updateFormLoading = true
      this.validationErrorResponse = null
      this.form
        .put("/open/forms/{id}/".replace("{id}", this.form.id))
        .then((data) => {
          this.formsStore.save(data.form)
          this.$emit("on-save")
          this.$router.push({
            name: "forms-slug-show-share",
            params: { slug: this.form.slug },
          })
          this.amplitude.logEvent("form_saved", {
            form_id: this.form.id,
            form_slug: this.form.slug,
          })
          this.displayFormModificationAlert(data)
        })
        .catch((error) => {
          if (error?.response?.status === 422) {
            this.validationErrorResponse = error.data
            this.showValidationErrors()
          } else {
            useAlert().error(
              "An error occurred while saving the form, please try again.",
            )
            captureException(error)
          }
        })
        .finally(() => {
          this.updateFormLoading = false
        })
    },
    saveFormCreate() {
      if (this.updateFormLoading) return
      this.form.workspace_id = this.workspace.id
      this.validationErrorResponse = null

      this.updateFormLoading = true
      this.form
        .post("/open/forms")
        .then((response) => {
          this.formsStore.save(response.form)
          this.$emit("on-save")
          this.createdFormSlug = response.form.slug

          this.amplitude.logEvent("form_created", {
            form_id: response.form.id,
            form_slug: response.form.slug,
          })
          this.crisp.pushEvent("form_created", {
            form_id: response.form.id,
            form_slug: response.form.slug,
          })
          this.displayFormModificationAlert(response)
          useRouter().push({
            name: "forms-slug-show-share",
            params: {
              slug: this.createdFormSlug,
              new_form: response.users_first_form,
            },
          }).then(() => {
            this.updateFormLoading = false
          })
        })
        .catch((error) => {
          if (error?.response?.status === 422) {
            this.validationErrorResponse = error.data
            this.showValidationErrors()
          } else {
            useAlert().error(
              "An error occurred while saving the form, please try again.",
            )
            captureException(error)
          }
          this.updateFormLoading = false
        })
    },
    saveFormGuest() {
      this.$emit("openRegister")
    },
  },
}
</script>

<style lang="scss">
.v-step {
  color: white;

  .v-step__header,
  .v-step__content {
    color: white;

    div {
      color: white;
    }
  }
}
</style>
