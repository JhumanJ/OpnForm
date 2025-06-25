<template>
  <div>
    <div class="p-2 border-b sticky top-0 z-10 bg-white">
      <UButton
        v-if="!field"
        size="sm"
        color="neutral"
        icon="i-heroicons-x-mark-20-solid"
        variant="ghost"
        @click="closeSidebar"
      />
      <template v-else>
        <div class="flex">
          <UButton
            size="sm"
            color="neutral"
            icon="i-heroicons-x-mark-20-solid"
            variant="ghost"
            @click="closeSidebar"
          />
          <div class="ml-2 flex flex-grow items-center space-between min-w-0 gap-x-3">
            <div class="flex-grow" />
            <BlockTypeIcon
              :type="field.type"
            />

            <p
              v-if="blocksTypes[field.type]"
              class="text-sm text-gray-500"
            >
              {{ blocksTypes[field.type].title }}
            </p>
            
            <UDropdownMenu
              :items="dropdownItems"
              :popper="{ placement: 'bottom-start' }"
            >
              <UButton
                color="neutral"
                variant="outline"
                icon="i-heroicons-ellipsis-vertical"
              />

              <template
                v-if="typeCanBeChanged"
                #changetype
              >
                <ChangeFieldType
                  v-if="!isBlockField"
                  :field="field"
                  @change-type="onChangeType"
                />
              </template>
            </UDropdownMenu>
          </div>
        </div>
      </template>
    </div>

    <template v-if="field">
      <div class="bg-gray-100 border-b">
        <UTabs
          v-model="activeTab"
          :items="tabItems"
          color="primary"
          :content="false"
          class="w-full"
        />
      </div>
      <div v-if="activeTab === 'options'">
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
      <div v-else-if="activeTab === 'logic'">
        <FormBlockLogicEditor
          class="py-2 px-4"
          :form="form"
          :field="field"
        />
      </div>
      <div v-else-if="activeTab === 'validation'">
        <custom-field-validation
          class="py-2 px-4"
          :form="form"
          :field="field"
        />
      </div>
    </template>
    <div
      v-else
      class="text-center p-10 text-sm text-gray-500"
    >
      Click on field to edit it.
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

// Only set the page once when the component is mounted
// This prevents page jumps when editing field properties
onMounted(() => {
  if (selectedFieldIndex.value !== null) {
    if (workingFormStore.structureService) {
      workingFormStore.structureService.setPageForField(selectedFieldIndex.value)
    }
  }
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
  // Explicitly clear the selected field index to prevent issues with subsequent block additions
  workingFormStore.selectedFieldIndex = null
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
      onclick: () => {
        navigator.clipboard.writeText(field.value.id)
        useAlert().success('Field ID copied to clipboard')
      }
    }],
    [{
      label: 'Duplicate',
      icon: 'i-heroicons-document-duplicate-20-solid',
      onclick: () => {
        const newField = clonedeep(field.value)
        newField.id = generateUUID()
        newField.name = 'Copy of ' + newField.name
        const newFields = [...form.value.properties]
        newFields.splice(selectedFieldIndex.value + 1, 0, newField)
        form.value.properties = newFields
      }
    }],
    ... (typeCanBeChanged.value ? [[{
      label: 'Change type',
      icon: 'i-heroicons-document-duplicate-20-solid',
      slot: 'changetype',
    }]] : []),
    [{
      label: 'Remove',
      icon: 'i-heroicons-trash-20-solid',
      class: 'group/remove hover:text-red-800',
      iconClass: 'group-hover/remove:text-red-900',
      onclick: removeBlock
    }]
  ]
})


const activeTab = ref('options')

const tabItems = computed(() => {
  const commonTabs = [
    { label: 'Options', value: 'options' },
    { label: 'Logic', value: 'logic' },
  ]

  if (isBlockField.value) {
    return commonTabs
  } else {
    return [
      ...commonTabs,
      { label: 'Validation', value: 'validation' },
    ]
  }
})

</script>
