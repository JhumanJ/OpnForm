<template>
  <InputWrapper
    v-bind="inputWrapperProps"
    wrapper-class="not-draggable"
  >
    <template #label>
      <slot name="label" />
    </template>

    <div
      class="rich-editor resize-y notranslate relative"
      :class="[
        {
          'ring-red-500! ring-2! border-transparent!': hasError,
          '!cursor-not-allowed bg-neutral-200! dark:bg-neutral-800!': disabled,
          'focus-within:ring-2 focus-within:ring-form/100 focus-within:border-transparent': !hasError && !disabled
        },
        theme.RichTextAreaInput.input,
        theme.RichTextAreaInput.borderRadius,
        theme.default.fontSize,
      ]"
      :style="{
        '--font-size': theme.default.fontSize,
        ...inputStyle
      }"
    >
      <MentionDropdown
        v-if="enableMentions && mentionState"
        :mention-state="mentionState"
        :mentions="mentions"
        :content="{ position: 'bottom', align: 'start' }"
      >
        <span class="absolute left-4 bottom-2" />
      </MentionDropdown>
      
      <ClientOnly>
      <QuillyEditor
        :id="id ? id : name"
        ref="editor"
        v-model="compVal"
        :options="quillOptions"
        :disabled="disabled"
        :style="inputStyle"
        @ready="onEditorReady"
      />
        <template #fallback>
          <USkeleton class="w-full h-10" />
        </template>
      </ClientOnly>
    </div>

    <template
      v-if="$slots.help"
      #help
    >
      <slot name="help" />
    </template>

    <template
      v-if="maxCharLimit && showCharLimit"
      #bottom_after_help
    >
      <small :class="theme.default.help">
        {{ charCount }}/{{ maxCharLimit }}
      </small>
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
import Quill from 'quill'
import { inputProps, useFormInput } from '../useFormInput.js'
import QuillyEditor from './components/QuillyEditor.vue'
import MentionDropdown from './components/MentionDropdown.vue'
import registerMentionExtension from '~/lib/quill/quillMentionExtension.js'

const props = defineProps({
  ...inputProps,
  maxCharLimit: { type: Number, required: false, default: null },
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

// Add this watch to clean up empty HTML content
watch(compVal, (val) => {
  if (val && val.replace(/<[^>]*>/g, '').trim() === '') {
    compVal.value = null
  }
}, { immediate: true })

// Initialize mention extension
if (import.meta.client && props.enableMentions) {
  // Register the mention extension with Quill
  mentionState.value = registerMentionExtension(Quill)
}

// Handle editor ready event
const onEditorReady = (quillInstance) => {
  // If we have a mention module, get its state
  if (props.enableMentions && quillInstance) {
    const mentionModule = quillInstance.getModule('mention')
    if (mentionModule && mentionModule.state) {
      mentionState.value = mentionModule.state
    }
  }
}

const quillOptions = computed(() => {
  const defaultOptions = {
    placeholder: props.placeholder || '',
    theme: 'snow',
    modules: {
      toolbar: [
        [{ 'header': 1 }, { 'header': 2 }],
        ['bold', 'italic', 'underline', 'strike'],
        ['link'],
        [{ list: 'ordered' }, { list: 'bullet' }],
        [{ color: [] }],
      ],
      keyboard: {
        bindings: {
          tab: {
            key: 9,
            handler (range) {
              this.quill.insertText(range.index, '    ', 'user')
            }
          }
        }
      }
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

const charCount = computed(() => {
  return compVal.value ? compVal.value.replace(/<[^>]*>/g, '').trim().length : 0
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
  border-bottom: 1px solid var(--color-neutral-300);
  :where(.dark) & {
    border-bottom: 1px solid var(--color-neutral-600);
  }
  }

  .ql-header {
    @apply rounded-md;
  }

  .ql-editor.ql-blank:before {
    @apply text-neutral-400 dark:text-neutral-500 not-italic;
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
    @apply text-blue-500;
  }
}

.ql-mention {
  padding-top: 0px !important;
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