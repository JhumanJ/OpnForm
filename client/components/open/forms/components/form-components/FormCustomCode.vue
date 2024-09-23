<template>
  <SettingsSection
    name="Custom Code"
    icon="i-heroicons-code-bracket"
  >
    <ProTag
      class="mb-2 block"
      upgrade-modal-title="Upgrade to Unlock Custom Code Capabilities"
      upgrade-modal-description="On the Free plan, you can explore custom code features within the form editor. Upgrade your plan to implement custom scripts, styles, and advanced tracking in live forms. Elevate your form's functionality and design with unlimited customization options."
    />
    <p class="text-sm text-gray-500">
      The code will be injected in the <b>head</b> section of your form page.
      <a
        href="#"
        class="text-gray-700"
        @click.prevent="
          crisp.openHelpdeskArticle(
            'how-do-i-add-custom-code-to-my-form-1amadj3',
          )
        "
      >Click here to get an example CSS code.</a>
    </p>
    
    <code-input
      name="custom_code"
      class="mt-4"
      :form="form"
      help="Saves changes and visit the actual form page to test."
      label="Custom Code"
      placeholder="<script>console.log('Hello World!')</script>"
    />
  </SettingsSection>
</template>

<script>
import ProTag from '~/components/global/ProTag.vue'
import { useWorkingFormStore } from '../../../../../stores/working_form'

export default {
  components: {
    ProTag
  },
  setup () {
    const workingFormStore = useWorkingFormStore()
    return {
      workingFormStore,
      form: storeToRefs(workingFormStore).content,
      crisp: useCrisp()
    }
  }
}
</script>
