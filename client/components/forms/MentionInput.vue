<template>
  <InputWrapper v-bind="inputWrapperProps">
    <template #label>
      <slot name="label" />
    </template>

    <MentionDropdown
      :mention-state="mentionState"
      :mentions="mentions"
    />

    <div class="relative">
      <div
        ref="editableDiv"
        :contenteditable="!disabled"
        class="mention-input"
        :style="inputStyle"
        :class="[
          theme.default.input,
          theme.default.borderRadius,
          theme.default.spacing.horizontal,
          theme.default.spacing.vertical,
          theme.default.fontSize,
          {
            '!ring-red-500 !ring-2 !border-transparent': hasError,
            '!cursor-not-allowed !bg-gray-200 dark:!bg-gray-800': disabled,
          },
          'pr-12'
        ]"
        :placeholder="placeholder"
        @input="onInput"
      />
      <UButton
        type="button"
        color="white"
        class="absolute right-2 top-1/2 transform -translate-y-1/2 p-1 px-2"
        icon="i-heroicons-at-symbol-16-solid"
        @click="openMentionDropdown"
      />
    </div>

    <template
      v-if="$slots.help"
      #help
    >
      <slot name="help" />
    </template>

    <template
      v-if="$slots.error"
      #error
    >
      <slot name="error" />
    </template>
  </InputWrapper>
</template>

<script setup>
import { ref, onMounted, watch, reactive } from 'vue'
import { inputProps, useFormInput } from './useFormInput.js'
import InputWrapper from './components/InputWrapper.vue'
import MentionDropdown from './components/MentionDropdown.vue'

const props = defineProps({
  ...inputProps,
  mentions: { type: Array, default: () => [] },
  disableMention: { type: Boolean, default: false },
})

const emit = defineEmits(['update:modelValue'])

const { compVal, inputStyle, hasError, inputWrapperProps } = useFormInput(props, { emit })
const editableDiv = ref(null)
const savedRange = ref(null)
const subscriptionModalStore = useSubscriptionModalStore()

// Create a reactive state object for the mention dropdown
const mentionState = reactive({
  open: false,
  onInsert: (mention) => {
    insertMention(mention)
    mentionState.open = false
  },
  onCancel: () => {
    mentionState.open = false
    restoreSelection()
  },
})

const createMentionSpan = (mention) => {
  const mentionSpan = document.createElement('span')
  mentionSpan.setAttribute('mention', 'true')
  mentionSpan.setAttribute('mention-field-id', mention.field.id)
  mentionSpan.setAttribute('mention-field-name', mention.field.name)
  mentionSpan.setAttribute('mention-fallback', mention.fallback || '')
  mentionSpan.setAttribute('contenteditable', 'false')
  mentionSpan.setAttribute('class', 'mention-item')
  mentionSpan.textContent = mention.field.name.length > 25 ? `${mention.field.name.slice(0, 25)}...` : mention.field.name
  return mentionSpan
}

const insertMention = (mention) => {
  const mentionSpan = createMentionSpan(mention)

  restoreSelection()

  const range = window.getSelection().getRangeAt(0)

  // Insert the mention span
  range.insertNode(mentionSpan)
  
  // Move the cursor after the inserted mention
  range.setStartAfter(mentionSpan)
  range.collapse(true)

  // Apply the new selection
  const selection = window.getSelection()
  selection.removeAllRanges()
  selection.addRange(range)

  // Ensure the editableDiv is focused
  editableDiv.value.focus()

  updateCompVal()
}

const openMentionDropdown = () => {
  if (props.disableMention) {
    subscriptionModalStore.setModalContent('Upgrade to Pro', 'Upgrade to Pro to use mentions')
    subscriptionModalStore.openModal()
    return
  }

  saveSelection()
  if (!savedRange.value) {
    // If no previous selection, move cursor to the end
    const range = document.createRange()
    range.selectNodeContents(editableDiv.value)
    range.collapse(false)
    const selection = window.getSelection()
    selection.removeAllRanges()
    selection.addRange(range)
    savedRange.value = range
  }
  mentionState.open = true
}

const saveSelection = () => {
  const selection = window.getSelection()
  if (selection.rangeCount > 0) {
    savedRange.value = selection.getRangeAt(0)
  }
}

const restoreSelection = () => {
  if (savedRange.value) {
    const selection = window.getSelection()
    selection.removeAllRanges()
    selection.addRange(savedRange.value)
    editableDiv.value.focus()
  }
}

const updateCompVal = () => {
  compVal.value = editableDiv.value.innerHTML
}

const onInput = () => {
  updateCompVal()
}

onMounted(() => {
  if (compVal.value) {
    editableDiv.value.innerHTML = compVal.value
  }
})

watch(compVal, (newVal) => {
  if (editableDiv.value && editableDiv.value.innerHTML !== newVal) {
    editableDiv.value.innerHTML = newVal
  }
})

defineExpose({
  editableDiv,
  compVal,
  mentionState,
  openMentionDropdown,
  onInput,
})
</script>

<style scoped>
.mention-input {
  min-height: 1.5rem;
  white-space: pre-wrap;
  word-break: break-word;
}

.mention-input:empty::before {
  content: attr(placeholder);
  color: #9ca3af;
}

.mention-input span[mention] {
  max-width: 150px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  display: inline-flex;
  align-items: center;
  background-color: #dbeafe;
  color: #1e40af;
  border: 1px solid #bfdbfe;
  border-radius: 0.25rem;
  padding: 0 0.25rem;
  font-size: 0.875rem;
  line-height: 1.25rem;
  position: relative;
  vertical-align: baseline;
}
</style>
