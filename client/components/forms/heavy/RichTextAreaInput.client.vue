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
        v-if="!isFullscreen"
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

    <!-- Fullscreen Modal -->
  <UModal v-if="allowFullscreen" v-model:open="isFullscreen" fullscreen>
    <template #content>
    <div class="flex flex-col h-full">
      <div class="flex items-center justify-between p-4 border-b">
        <div>
          <h3 class="text-lg font-medium text-neutral-900 dark:text-white">
            {{ label || 'Rich Text Editor' }}
          </h3>
        </div>
        <UButton
          @click="closeFullscreen"
          variant="ghost"
          size="sm"
          icon="i-heroicons-x-mark"
          :ui="{ rounded: 'rounded-md' }"
          title="Exit fullscreen"
        />
      </div>

      <!-- Editor Container for Fullscreen -->
      <div class="flex-1 overflow-hidden">
        <div class="rich-editor h-full" style="resize: none;">
          <MentionDropdown
            v-if="enableMentions && mentionState"
            :mention-state="mentionState"
            :mentions="mentions"
            :content="{ position: 'bottom', align: 'start' }"
          >
            <span class="absolute right-4 top-24" />
          </MentionDropdown>
          
          <ClientOnly>
            <QuillyEditor
              v-model="compVal"
              :options="modalQuillOptions"
              :disabled="disabled"
              :style="{ height: '100%' }"
              @ready="onEditorReady"
            />
            <template #fallback>
              <USkeleton class="w-full h-full" />
            </template>
          </ClientOnly>
        </div>
      </div>
    </div>
    </template>
  </UModal>

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

// Global icon registration - only happens once
if (import.meta.client) {
  const icons = Quill.import("ui/icons")
  if (!icons["fullscreen"]) {
    icons["fullscreen"] = `<svg viewBox="0 0 18 18" width="14" height="14">
      <path fill="none" class="ql-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M2.8 2.8v3.4m0-3.4h3.4m-3.4 0L6.8 6.8M2.8 15.2v-3.4m0 3.4h3.4m-3.4 0L6.8 11.2M15.2 2.8h-3.4m3.4 0v3.4m0-3.4L11.2 6.8m4 8.4h-3.4m3.4 0v-3.4m0 3.4L11.2 11.2"/>
    </svg>`
  }
}

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
  },
  allowFullscreen: {
    type: Boolean,
    default: false
  }
})
const emit = defineEmits(['update:modelValue'])

const { compVal, inputStyle, hasError, inputWrapperProps } = useFormInput(props, { emit })
const editor = ref(null)
const mentionState = ref(null)

// Fullscreen functionality
const isFullscreen = ref(false)

const openFullscreen = () => {
  isFullscreen.value = true
}

const closeFullscreen = () => {
  isFullscreen.value = false
}

// Handle keyboard shortcuts
defineShortcuts({
  escape: {
    usingInput: true,
    handler: () => {
      if (isFullscreen.value) {
        closeFullscreen()
      }
    }
  }
})

// Add this watch to clean up empty HTML content
watch(compVal, (val) => {
  if (val && val.replace(/<[^>]*>/g, '').trim() === '') {
    compVal.value = null
  }
}, { immediate: true })

// Initialize mention extension and fullscreen icon
if (import.meta.client) {
  if (props.enableMentions) {
    // Register the mention extension with Quill
    mentionState.value = registerMentionExtension(Quill)
  }
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

  // Limit the number of characters
  if (props.maxCharLimit) {
    quillInstance.on('text-change', function() {
      if (quillInstance.getLength() > props.maxCharLimit) {
        quillInstance.deleteText(props.maxCharLimit, quillInstance.getLength())
      }
    })
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
  
  // Add fullscreen button to toolbar if enabled
  if (props.allowFullscreen) {
    mergedOptions.modules.toolbar.push(['fullscreen'])
    
    // Set up toolbar with handlers
    mergedOptions.modules.toolbar = {
      container: mergedOptions.modules.toolbar,
      handlers: {
        fullscreen: () => {
          openFullscreen()
        }
      }
    }
  }
  
  if (props.enableMentions) {
    mergedOptions.modules.mention = true
    if (!mergedOptions.modules.toolbar.container) {
      mergedOptions.modules.toolbar.container = mergedOptions.modules.toolbar
    }
    mergedOptions.modules.toolbar.container.push(['mention'])
  }
  
  return mergedOptions
})

const modalQuillOptions = computed(() => {
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
  
  // NOTE: No fullscreen button for modal editor
  
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

const showCharLimit = computed(() => {
  return props.maxCharLimit && props.maxCharLimit > 0
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

/* Fullscreen editor styles */
.rich-editor.h-full .ql-container {
  height: calc(100% - 42px) !important; /* Subtract toolbar height */
}

.rich-editor.h-full .ql-editor {
  height: 100% !important;
  min-height: 100% !important;
}
</style>