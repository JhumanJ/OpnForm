<template>
  <VForm
    class="flex-grow flex flex-col"
    size="sm"
    @submit.prevent=""
  >
    <SettingsWrapper class="flex-grow">
      <FormInformation />
      <FormSubmissionSettings />
      <FormSecurityAccess />
      <FormCustomSeo />
      <FormCustomCode /> 
    </SettingsWrapper>
  </VForm>
</template>

<script>
import { useWorkingFormStore } from '../../../../../stores/working_form'
import FormInformation from './FormInformation.vue'
import FormSubmissionSettings from './FormSubmissionSettings.vue'
import FormSecurityAccess from './FormSecurityAccess.vue'
import FormCustomSeo from './FormCustomSeo.vue'
import FormCustomCode from './FormCustomCode.vue'

export default {
  components: {
    FormInformation,
    FormSubmissionSettings,
    FormSecurityAccess,
    FormCustomSeo,
    FormCustomCode,
  },
  setup() {
    const workingFormStore = useWorkingFormStore()
    return {
      form: storeToRefs(workingFormStore).content
    }
  }
}
</script>