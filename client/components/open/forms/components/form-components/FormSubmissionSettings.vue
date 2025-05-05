<template>
  <SettingsSection
    name="Submission Settings"
    icon="i-heroicons-paper-airplane"
    class="w-full"
  >
    <!-- Basic Submission Settings -->
    <h4 class="font-semibold mt-4 border-t pt-4">
      Basic
    </h4>
    <p class="text-gray-500 text-sm mb-4">
      Configure how form submissions are handled.
    </p>
    <text-input
      name="submit_button_text"
      :form="form"
      class="max-w-xs"
      label="Submit button text"
      :required="true"
    />

    <ToggleSwitchInput
      name="auto_save"
      :form="form"
      label="Auto save form response"
      help="Saves form progress, allowing respondents to resume later."
      class="mt-4"
      :disabled="hasPaymentBlock"
    />
    <UAlert
      v-if="hasPaymentBlock"
      color="primary"
      variant="subtle"
      title="Must be enabled with a payment block."
      class="max-w-md"
    />
    
    <flat-select-input
      :form="submissionOptions"
      name="databaseAction"
      class="mt-4 max-w-xs"
      label="Database Submission Action"
      :options="[
        { name: 'Create new record', value: 'create' },
        { name: 'Update existing record', value: 'update' }
      ]"
      :required="true"
    />
    <div
      v-if="submissionOptions.databaseAction == 'update' && filterableFields.length"
      class="bg-gray-50 border rounded-lg px-4 py-2"
    >
      <div
        v-if="submissionOptions.databaseAction == 'update' && filterableFields.length"
        class="w-auto max-w-lg"
      >
        <p class="mb-2 mt-2 text-gray-500 text-sm">
          When matching values are found in the selected column(s), the (first) existing record will be updated instead of creating a new record. If there's no match, a new record will be created.
          <a
            href="#"
            class="text-blue-500 hover:underline"
            @click.prevent="crisp.openHelpdeskArticle('how-to-update-a-record-on-form-submission-1t1jwmn')"
          >
            Learn more.
          </a>
        </p>
        <select-input
          v-if="filterableFields.length"
          :form="form"
          name="database_fields_update"
          label="Properties to check on update"
          :options="filterableFields"
          multiple
          clearable
        />
      </div>
    </div>

    <!-- Advanced Submission Settings -->
    <h4 class="font-semibold mt-4 border-t pt-4">
      Advanced Submission Options <pro-tag />
    </h4>
    <p class="text-gray-500 text-sm mb-4">
      Configure advanced options for form submissions and data collection.
    </p>
    
    <ToggleSwitchInput
      name="enable_partial_submissions"
      :form="form"
      help="Capture incomplete form submissions to analyze user drop-off points and collect partial data even when users don't complete the entire form."
    >
      <template #label>
        <span class="text-sm">
          Collect partial submissions
        </span>
        <ProTag
          class="ml-1"
          upgrade-modal-title="Upgrade to collect partial submissions"
          upgrade-modal-description="Capture valuable data from incomplete form submissions. Analyze where users drop off and collect partial information even when they don't complete the entire form."
        />
      </template>
    </ToggleSwitchInput>

    <!-- Post-Submission Behavior -->
    <h4 class="font-semibold mt-4 border-t pt-4">
      After Submission <pro-tag
        upgrade-modal-title="Upgrade to customize post-submission behavior"
        upgrade-modal-description="Customize post-submission behavior: redirect users, show custom messages, or trigger actions. Upgrade to unlock advanced options for a seamless user experience. We have plenty of other pro features to enhance your form functionality and user engagement."
      />
    </h4>
    <p class="text-gray-500 text-sm mb-4">
      Customize the user experience after form submission.
    </p>
    <div
      class="w-full"
      :class="{'flex flex-wrap gap-x-4':submissionOptions.submissionMode === 'redirect'}"
    >
      <flat-select-input
        :form="submissionOptions"
        name="submissionMode"
        class="w-full max-w-xs"
        label="Action After Form Submission"
        :options="[
          { name: 'Show Success page', value: 'default' },
          { name: 'Redirect', value: 'redirect' }
        ]"
      >
        <template #selected="{ option, optionName }">
          <div class="flex items-center truncate text-sm mr-6">
            {{ optionName }}
            <pro-tag
              v-if="option === 'redirect'"
              class="ml-2"
            />
          </div>
        </template>
        <template #option="{ option, selected }">
          <span class="flex">
            <p class="flex-grow">
              {{ option.name }} <template v-if="option.value === 'redirect'"><pro-tag /></template>
            </p>
            <span
              v-if="selected"
              class="absolute inset-y-0 right-0 flex items-center pr-4"
            >
              <Icon
                name="heroicons:check-20-solid"
                class="h-5 w-5"
              />
            </span>
          </span>
        </template>
      </flat-select-input>
      <template v-if="submissionOptions.submissionMode === 'redirect'">
        <MentionInput
          name="redirect_url"
          :form="form"
          :mentions="form.properties"
          class="w-full max-w-xs"
          label="Redirect URL"
          placeholder="https://www.google.com"
          :required="true"
        />
      </template>
      <template v-else>
        <rich-text-area-input
          enable-mentions
          :mentions="form.properties"
          name="submitted_text"
          class="w-full mt-4"
          :form="form"
          label="Success page text"
          :required="false"
        />
        <div class="flex items-center flex-wrap gap-x-4">
          <toggle-switch-input
            name="re_fillable"
            class="w-full max-w-xs"
            :form="form"
            label="Re-fillable form"
            help="Allows user to fill the form multiple times"
          />
          <text-input
            v-if="form.re_fillable"
            name="re_fill_button_text"
            :form="form"
            label="Text of re-start button"
            :required="true"
          />
        </div>

        <div class="flex items-center flex-wrap gap-x-4">
          <toggle-switch-input
            name="editable_submissions"
            class="w-full max-w-sm"
            help="Allows users to edit submissions via unique URL"
            :form="form"
          >
            <template #label>
              <span class="text-sm">
                Editable submissions
              </span>
              <ProTag
                class="ml-1"
                upgrade-modal-title="Upgrade to use Editable Submissions"
                upgrade-modal-description="On the Free plan, you can try out all paid features only within the form editor. Upgrade your plan to allow users to update their submissions via a unique URL, and much more. Gain full access to all advanced features."
              />
            </template>
          </toggle-switch-input>
          <text-input
            v-if="form.editable_submissions"
            name="editable_submissions_button_text"
            class="w-full max-w-64"
            :form="form"
            label="Edit submission button text"
            :required="true"
          />
        </div>
      </template>
    </div>
  </SettingsSection>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { storeToRefs } from 'pinia'
import { useWorkingFormStore } from '../../../../../stores/working_form'
import ProTag from '~/components/global/ProTag.vue'
import ToggleSwitchInput from '../../../../forms/ToggleSwitchInput.vue'
import { useCrisp } from '~/composables/useCrisp'

const workingFormStore = useWorkingFormStore()
const { content: form } = storeToRefs(workingFormStore)
const crisp = useCrisp()

const submissionOptions = ref({})

const filterableFields = computed(() => {
  if (submissionOptions.value.databaseAction !== "update") return []
  return form.value.properties
    .filter((field) => {
      return (
        !field.hidden &&
        !["files", "signature", "multi_select"].includes(field.type)
      )
    })
    .map((field) => {
      return {
        name: field.name,
        value: field.id,
      }
    })
})

watch(form, () => {
  if (form.value) {
    submissionOptions.value = {
      submissionMode: form.value.redirect_url ? 'redirect' : 'default',
      databaseAction: form.value.database_fields_update ? 'update' : 'create'
    }
  }
}, { deep: true })

watch(submissionOptions, (val) => {
  if (val.submissionMode === 'default') form.value.redirect_url = null
  if (val.databaseAction === 'create') form.value.database_fields_update = null
}, { deep: true })

const hasPaymentBlock = computed(() => {
  return form.value.properties.some(property => property.type === 'payment')
})
</script>
