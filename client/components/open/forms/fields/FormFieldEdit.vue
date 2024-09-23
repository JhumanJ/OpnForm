<template>
  <div>
    <div class="px-4 py-2 border-b sticky top-0 z-10 bg-white">
      <button
        v-if="!field"
        class="text-gray-500 hover:text-gray-900 cursor-pointer"
        @click.prevent="closeSidebar"
      >
        <Icon
          name="heroicons:x-mark-solid"
          class="h-6 w-6"
        />
      </button>
      <template v-else>
        <div class="flex">
          <button
            class="text-gray-500 hover:text-gray-900 cursor-pointer"
            @click.prevent="closeSidebar"
          >
            <Icon
              name="heroicons:x-mark-solid"
              class="h-6 w-6"
            />
          </button>
          <div class="ml-2 flex flex-grow items-center space-between min-w-0 gap-x-3">
            <div class="flex-grow" />
            <BlockTypeIcon
              :type="field.type"
            />

            <p class="text-sm text-gray-500">
              {{ blocksTypes[field.type].title }}
            </p>
            
            <UDropdown
              :items="dropdownItems"
              :popper="{ placement: 'bottom-start' }"
            >
              <UButton
                color="white"
                icon="i-heroicons-ellipsis-vertical"
              />
            </UDropdown>

            <ChangeFieldType
              v-if="!isBlockField"
              btn-classes="rounded-l-none text-xs"
              :field="field"
              @change-type="onChangeType"
            />
          </div>
        </div>
      </template>
    </div>

    <template v-if="field">
      <div class="bg-gray-100 border-b">
        <UTabs
          v-model="activeTab"
          :items="tabItems"
          class="w-full"
        />
      </div>
      <div v-if="activeTab ===0">
        <FieldOptions
          v-if="!isBlockField"
          :form="form"
          :field="field"
        />
        <BlockOptions
          v-else
          :form="form"
          :field="field"
        />
      </div>
      <div v-else-if="activeTab === 1">
        <FormBlockLogicEditor
          class="py-2 px-4"
          :form="form"
          :field="field"
        />
      </div>
      <div v-else-if="activeTab === 2">
        <custom-field-validation
          class="py-2 px-4"
          :form="form"
          :field="field"
        />
      </div>
    </template>
    <div
      v-else
      class="text-center p-10"
    >
      Click on field's setting icon in your form to modify it
    </div>
  </div>
</template>


<script setup>
import { storeToRefs } from 'pinia'
import clonedeep from 'clone-deep'
import FieldOptions from './components/FieldOptions.vue'
import BlockOptions from './components/BlockOptions.vue'
import BlockTypeIcon from '../components/BlockTypeIcon.vue'
import ChangeFieldType from "./components/ChangeFieldType.vue"
import blocksTypes from '~/data/blocks_types.json'
import FormBlockLogicEditor from '../components/form-logic-components/FormBlockLogicEditor.vue'
import CustomFieldValidation from '../components/CustomFieldValidation.vue'
import { generateUUID } from '~/lib/utils'

const workingFormStore = useWorkingFormStore()
const { content: form } = storeToRefs(workingFormStore)

const selectedFieldIndex = computed(() => workingFormStore.selectedFieldIndex)

const field = computed(() => {
  return form.value && selectedFieldIndex.value !== null
    ? form.value.properties[selectedFieldIndex.value]
    : null
})

const isBlockField = computed(() => {
  return field.value && field.value.type.startsWith('nf')
})

const typeCanBeChanged = computed(() => {
  return [
    "text",
    "email",
    "phone_number",
    "number",
    "select",
    "multi_select",
    "rating",
    "scale",
    "slider",
  ].includes(field.value.type)
})

function removeBlock() {
  workingFormStore.removeField(field.value)
}

function closeSidebar() {
  workingFormStore.closeEditFieldSidebar()
}

function onChangeType(newType) {
  if (["select", "multi_select"].includes(field.value.type)) {
    field.value[newType] = field.value[field.value.type] // Set new options with new type
    delete field.value[field.value.type] // remove old type options
  }
  field.value.type = newType
}

const dropdownItems = computed(() => {
  return [
    [{
      label: 'Copy field ID',
      icon: 'i-heroicons-clipboard-20-solid',
      click: () => {
        navigator.clipboard.writeText(field.value.id)
        useAlert().success('Field ID copied to clipboard')
      }
    }],
    [{
      label: 'Duplicate',
      icon: 'i-heroicons-document-duplicate-20-solid',
      click: () => {
        const newField = clonedeep(field.value)
        newField.id = generateUUID()
        newField.name = 'Copy of ' + newField.name
        const newFields = [...form.value.properties]
        newFields.splice(selectedFieldIndex.value + 1, 0, newField)
        form.value.properties = newFields
      }
    }],
    [{
      label: 'Remove',
      icon: 'i-heroicons-trash-20-solid',
      class: 'group/remove hover:text-red-800',
      iconClass: 'group-hover/remove:text-red-900',
      click: removeBlock
    }]
  ]
})


const activeTab = ref(0)
const tabItems = computed(() => {
  const commonTabs = [
    { label: 'Options'},
    { label: 'Logic' },
  ]

  if (isBlockField.value) {
    return commonTabs
  } else {
    return [
      ...commonTabs,
      { label: 'Validation'},
    ]
  }
})

</script>
