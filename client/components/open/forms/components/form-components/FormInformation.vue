<template>
  <SettingsSection
    name="General"
    icon="i-heroicons-information-circle"
  >
    <p class="text-gray-500 text-sm">
      Basic information about your form.
    </p>

    <text-input
      :form="form"
      name="title"
      class="mt-4 max-w-xs"
      label="Form Name"
      placeholder="My form"
    />
    <select-input
      name="tags"
      label="Tags"
      clearable
      :form="form"
      help="To organize your forms"
      placeholder="Select Tag(s)"
      class="max-w-xs"
      :multiple="true"
      :allow-creation="true"
      :options="allTagsOptions"
    />
    <flat-select-input
      name="visibility"
      label="Form Visibility"
      class="max-w-xs"
      :form="form"
      placeholder="Select Visibility"
      :options="visibilityOptions"
    />
    <div
      v-if="isFormClosingOrClosed"
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

    <UButton
      v-if="copyFormOptions.length > 0"
      color="white"
      class="mt-4"
      icon="i-heroicons-document-duplicate"
      @click.prevent="showCopyFormSettingsModal = true"
    >
      Copy another form's settings
    </UButton>
  </SettingsSection>
    
  <modal
    :show="showCopyFormSettingsModal"
    max-width="md"
    @close="showCopyFormSettingsModal = false"
  >
    <template #icon>
      <svg
        class="w-10 h-10 text-blue"
        viewBox="0 0 48 48"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
      >
        <path
          d="M17 27C16.0681 27 15.6022 27 15.2346 26.8478C14.7446 26.6448 14.3552 26.2554 14.1522 25.7654C14 25.3978 14 24.9319 14 24V17.2C14 16.0799 14 15.5198 14.218 15.092C14.4097 14.7157 14.7157 14.4097 15.092 14.218C15.5198 14 16.0799 14 17.2 14H24C24.9319 14 25.3978 14 25.7654 14.1522C26.2554 14.3552 26.6448 14.7446 26.8478 15.2346C27 15.6022 27 16.0681 27 17M24.2 34H30.8C31.9201 34 32.4802 34 32.908 33.782C33.2843 33.5903 33.5903 33.2843 33.782 32.908C34 32.4802 34 31.9201 34 30.8V24.2C34 23.0799 34 22.5198 33.782 22.092C33.5903 21.7157 33.2843 21.4097 32.908 21.218C32.4802 21 31.9201 21 30.8 21H24.2C23.0799 21 22.5198 21 22.092 21.218C21.7157 21.4097 21.4097 21.7157 21.218 22.092C21 22.5198 21 23.0799 21 24.2V30.8C21 31.9201 21 32.4802 21.218 32.908C21.4097 33.2843 21.7157 33.5903 22.092 33.782C22.5198 34 23.0799 34 24.2 34Z"
          stroke="currentColor"
          stroke-width="2"
          stroke-linecap="round"
          stroke-linejoin="round"
        />
      </svg>
    </template>
    <template #title>
      Import Settings from another form
    </template>
    <div>
      <select-input
        v-model="copyFormId"
        name="copy_form_id"
        label="Copy Settings From"
        placeholder="Choose a form"
        :searchable="copyFormOptions.length > 5"
        :options="copyFormOptions"
      />
      <div class="flex">
        <v-button
          color="white"
          class="w-full mr-2"
          @click="showCopyFormSettingsModal = false"
        >
          Cancel
        </v-button>
        <v-button
          color="blue"
          class="w-full"
          @click="copySettings"
        >
          Confirm & Copy
        </v-button>
      </div>
    </div>
  </modal>
</template>

<script setup>
import clonedeep from 'clone-deep'
import { default as _has } from 'lodash/has'

// Store setup
const formsStore = useFormsStore()
const workingFormStore = useWorkingFormStore()
const { content: form } = storeToRefs(workingFormStore)
const forms = computed(() => formsStore.getAll)

// Reactive state
const showCopyFormSettingsModal = ref(false)
const copyFormId = ref(null)

// Computed properties
const visibilityOptions = [
  {
    name: 'Published',
    value: 'public',
  },
  {
    name: 'Draft - not publicly accessible',
    value: 'draft',
  },
  {
    name: 'Closed - won\'t accept new submissions',
    value: 'closed',
  },
]

const copyFormOptions = computed(() => {
  return forms.value
    .filter((formItem) => {
      return form.value.id !== formItem.id
    })
    .map((formItem) => {
      return {
        name: formItem.title,
        value: formItem.id,
      }
    })
})

const allTagsOptions = computed(() => {
  return formsStore.allTags.map((tagname) => {
    return {
      name: tagname,
      value: tagname,
    }
  })
})

// New computed property for v-if condition
const isFormClosingOrClosed = computed(() => {
  return form.value.closes_at || form.value.visibility === 'closed'
})

// Methods
const copySettings = () => {
  if (copyFormId.value == null)
    return
  const copyForm = clonedeep(
    forms.value.find(form => form.id === copyFormId.value),
  )
  if (!copyForm)
    return;

  // Clean copy from form
  [
    "title",
    "properties",
    "cleanings",
    "views_count",
    "submissions_count",
    "workspace",
    "workspace_id",
    "updated_at",
    "share_url",
    "slug",
    "notion_database_url",
    "id",
    "database_id",
    "database_fields_update",
    "creator",
    "created_at",
    "deleted_at",
    "last_edited_human",
  ].forEach((property) => {
    if (_has(copyForm, property))
      delete copyForm[property]
  })

  // Apply changes
  Object.keys(copyForm).forEach((property) => {
    form.value[property] = copyForm[property]
  })
  showCopyFormSettingsModal.value = false
  useAlert().success('Form settings copied.')
}
</script>
