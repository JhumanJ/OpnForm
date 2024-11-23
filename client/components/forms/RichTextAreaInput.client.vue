<template>
  <InputWrapper v-bind="inputWrapperProps">
    <template #label>
      <slot name="label" />
    </template>

    <div
      class="rich-editor resize-y"
      :class="[
        {
          '!ring-red-500 !ring-2 !border-transparent': hasError,
          '!cursor-not-allowed !bg-gray-200 dark:!bg-gray-800': disabled,
        },
        theme.RichTextAreaInput.input,
        theme.RichTextAreaInput.borderRadius,
        theme.default.fontSize,
      ]"
      :style="{
        '--font-size': theme.default.fontSize
      }"
    >
      <QuillyEditor
        :id="id ? id : name"
        ref="editor"
        :key="id+placeholder"
        v-model="compVal"
        :options="quillOptions"
        :disabled="disabled"
        :placeholder="placeholder"
        :style="inputStyle"
      />
    </div>

    <template #help>
      <slot name="help" />
    </template>
    <template #error>
      <slot name="error" />
    </template>

    <MentionDropdown
      v-if="enableMentions && mentionState"
      :state="mentionState"
      :mentions="mentions"
    />
  </InputWrapper>
</template>

<script setup>
import Quill from 'quill'
import { inputProps, useFormInput } from './useFormInput.js'
import InputWrapper from './components/InputWrapper.vue'
import QuillyEditor from './components/QuillyEditor.vue'
import MentionDropdown from './components/MentionDropdown.vue'
import registerMentionExtension from '~/lib/quill/quillMentionExtension.js'
const props = defineProps({
  ...inputProps,
  editorOptions: {
    type: Object,
    default: () => ({})
  },
  enableMentions: {
    type: Boolean,
    default: false
  },
  mentions: {
    type: Array,
    default: () => []
  }
})
const emit = defineEmits(['update:modelValue'])
const { compVal, inputStyle, hasError, inputWrapperProps } = useFormInput(props, { emit })
const editor = ref(null)
const mentionState = ref(null)
// Move the mention extension registration to onMounted

if (props.enableMentions && !Quill.imports['blots/mention']) {
  mentionState.value = registerMentionExtension(Quill)
}

const quillOptions = computed(() => {
  const defaultOptions = {
    theme: 'snow',
    modules: {
      toolbar: [
        [{ 'header': 1 }, { 'header': 2 }],
        ['bold', 'italic', 'underline', 'strike'],
        ['link'],
        [{ list: 'ordered' }, { list: 'bullet' }],
        [{ color: [] }],
      ]
    }
  }
  const mergedOptions = { ...defaultOptions, ...props.editorOptions, modules: { ...defaultOptions.modules, ...props.editorOptions.modules } }
  
  if (props.enableMentions) {
    mergedOptions.modules.mention = true
    if (!mergedOptions.modules.toolbar) {
      mergedOptions.modules.toolbar = []
    }
    mergedOptions.modules.toolbar.push(['mention'])
  }
  
  return mergedOptions
})
</script>

<style lang="scss">
.rich-editor {
  .ql-container {
    border-bottom: 0px !important;
    border-right: 0px !important;
    border-left: 0px !important;
    font-size: var(--font-size);
    .ql-editor {
      min-height: 100px !important;
    }
  }
  .ql-toolbar {
    border-top: 0px !important;
    border-right: 0px !important;
    border-left: 0px !important;
  }
  .ql-header {
    @apply rounded-md;
  }
  .ql-editor.ql-blank:before {
    @apply text-gray-400 dark:text-gray-500 not-italic;
  }
  .ql-snow .ql-toolbar .ql-picker-item.ql-selected,
  .ql-snow .ql-toolbar .ql-picker-item:hover,
  .ql-snow .ql-toolbar .ql-picker-label.ql-active,
  .ql-snow .ql-toolbar .ql-picker-label:hover,
  .ql-snow .ql-toolbar button.ql-active,
  .ql-snow .ql-toolbar button:focus,
  .ql-snow .ql-toolbar button:hover,
  .ql-snow.ql-toolbar .ql-picker-item.ql-selected,
  .ql-snow.ql-toolbar .ql-picker-item:hover,
  .ql-snow.ql-toolbar .ql-picker-label.ql-active,
  .ql-snow.ql-toolbar .ql-picker-label:hover,
  .ql-snow.ql-toolbar button.ql-active,
  .ql-snow.ql-toolbar button:focus,
  .ql-snow.ql-toolbar button:hover {
    @apply text-nt-blue;
  }
}
.ql-mention {
  padding-top: 0px !important;
  margin-top: -5px !important;
}
.ql-mention::after {
  content: '@';
  font-size: 16px;
}
.rich-editor, .mention-input {
  span[mention] {
    @apply inline-flex items-center align-baseline leading-tight text-sm relative bg-blue-100 text-blue-800 border border-blue-200 rounded-md px-1 py-0.5 mx-0.5;
    max-width: 200px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
}
</style>