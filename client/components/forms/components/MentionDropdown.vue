<template>
  <UPopover
    ref="popover"
    v-model:open="mentionState.open"
    class="h-0"
    @close="cancel"
  >
    <span class="hidden" />
    <template #panel>
      <div class="p-2 max-h-[300px] flex flex-col">
        <div class="flex items-center border-b -mx-2 px-2">
          <div class="font-semibold w-1/2 mb-2 flex-grow">
            Insert Mention
          </div>
          <input
            v-model="fallbackValue"
            class="p-1 mb-2 text-sm w-1/2 border rounded-md hover:bg-gray-50"
            placeholder="Fallback value"
          >
        </div>
        <div class="overflow-scroll pt-2">
          <div class="w-full max-w-xs mb-2">
            <div class="text-sm text-gray-500 mb-1">
              Select a field
            </div>
            <div class="space-y-1">
              <div
                v-for="field in filteredMentions"
                :key="field.id"
                class="flex items-center p-2 rounded-md cursor-pointer hover:bg-gray-100"
                :class="{ 'bg-blue-50 border border-blue-100 inset-0': selectedField?.id === field.id, 'border border-transparent': selectedField?.id !== field.id }"
                @click="selectField(field)"
                @dblclick="selectField(field, true)"
              >
                <BlockTypeIcon
                  :type="field.type"
                  class="mr-2"
                />
                <p class="text-sm text-gray-700 truncate">
                  {{ field.name }}
                </p>
              </div>
            </div>
          </div>
        </div>
          
        <div class="flex border-t pt-2 -mx-2 px-2 justify-end space-x-2">
          <UButton
            size="sm"
            color="primary"
            class="px-6"
            :disabled="!selectedField"
            @click="insertMention" 
          >
            Insert
          </UButton>
          <UButton
            size="sm"
            color="gray"
            @click="cancel"
          >
            Cancel
          </UButton>
        </div>
      </div>
    </template>
  </UPopover>
</template>
    
<script setup>
import { ref, computed, watch } from 'vue'
import BlockTypeIcon from '~/components/open/forms/components/BlockTypeIcon.vue'
import blocksTypes from '~/data/blocks_types.json'

const props = defineProps({
  mentionState: {
    type: Object,
    required: true
  },
  mentions: {
    type: Array,
    required: true
  }
})

defineShortcuts({
  escape: () => {
    props.mentionState.open = false
  }
})

const selectedField = ref(null)
const fallbackValue = ref('')

const filteredMentions = computed(() => {
  return props.mentions.filter(mention => blocksTypes[mention.type]?.is_input ?? false)
})

function selectField(field, insert = false) {
  selectedField.value = {...field}
  if (insert) {
    insertMention()
  }
}

watch(() => props.mentionState.open, (newValue) => {
  if (newValue) {
    selectedField.value = null
    fallbackValue.value = ''
  }
})

const insertMention = () => {
  if (selectedField.value && props.mentionState.onInsert) {
    props.mentionState.onInsert({
      field: selectedField.value,
      fallback: fallbackValue.value
    })
  }
}

const cancel = () => {
  if (props.mentionState.onCancel) {
    props.mentionState.onCancel()
  }
}
</script>