<template>
  <SettingsSection
    name="Security & Access"
    icon="i-heroicons-shield-check"
  >
    <h4 class="font-semibold mt-4 border-t pt-4">
      Access
    </h4>
    <p class="text-gray-500 text-sm">
      Manage who can access your form and when.
    </p>

    <text-input
      name="password"
      :form="form"
      class="mt-4 max-w-xs"
      label="Form Password"
      placeholder="********"
      help="Leave empty to disable password protection"
    />
    <date-input
      :with-time="true"
      name="closes_at"
      class="mt-4 max-w-xs"
      :form="form"
      label="Closing date"
      help="Leave empty to keep the form open indefinitely"
      :required="false"
    />
    <div
      v-if="form.closes_at || form.visibility == 'closed'"
      class="bg-gray-50 border rounded-lg px-4 py-2"
    >
      <rich-text-area-input
        name="closed_text"
        :form="form"
        label="Closed form text"
        help="This message will be shown when the form will be closed"
        :required="false"
        wrapper-class="mb-0"
      />
    </div>
    <text-input
      name="max_submissions_count"
      native-type="number"
      :min="1"
      :form="form"
      label="Limit number of submissions"
      placeholder="Max submissions"
      class="mt-4 max-w-xs"
      help="Leave empty for unlimited submissions"
      :required="false"
    />
    <div
      v-if="form.max_submissions_count && form.max_submissions_count > 0"
      class="bg-gray-50 border rounded-lg px-4 py-2"
    >
      <rich-text-area-input
        wrapper-class="mb-0"
        name="max_submissions_reached_text"
        :form="form"
        label="Max Submissions reached text"
        help="This message will be shown when the form will have the maximum number of submissions"
        :required="false"
      />
    </div>


    <h4 class="font-semibold mt-4 border-t pt-4">
      Security
    </h4>
    <p class="text-gray-500 text-sm">
      Protect your form, and your sensitive files.
    </p>
    <ToggleSwitchInput
      name="use_captcha"
      :form="form"
      class="mt-4"
      label="Bot Protection"
      help="Protects your form from spam and abuse with a captcha"
    />
  </SettingsSection>
</template>

<script>
import { useWorkingFormStore } from '../../../../../stores/working_form'

export default {
  components: { },
  props: {},
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
