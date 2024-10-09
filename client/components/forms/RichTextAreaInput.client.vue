<template>
  <InputWrapper v-bind="inputWrapperProps">
    <template #label>
      <slot name="label" />
    </template>

    <VueEditor
      :id="id ? id : name"
      ref="editor"
      v-model="compVal"
      :disabled="disabled ? true : null"
      :placeholder="placeholder"
      :class="[
        {
          '!ring-red-500 !ring-2 !border-transparent': hasError,
          '!cursor-not-allowed !bg-gray-200': disabled,
        },
        theme.RichTextAreaInput.input,
        theme.RichTextAreaInput.borderRadius,
      ]"
      :editor-options="editorOptions"
      :editor-toolbar="editorToolbar"
      class="rich-editor resize-y"
      :style="inputStyle"
    />

    <InputHelp
      v-if="enableMentions"
      help="Click @ to use form variables"
      :help-classes="theme.default.help"
    />

    <template #help>
      <slot name="help" />
    </template>
    <template #error>
      <slot name="error" />
    </template>

    <MentionDropdown
      v-if="enableMentions"
      :state="mentionState"
      :mentions="mentions"
    />

  </InputWrapper>
</template>

<script>
import { Quill, VueEditor } from 'vue3-editor'
import { inputProps, useFormInput } from './useFormInput.js'
import InputWrapper from './components/InputWrapper.vue'
import MentionDropdown from './components/MentionDropdown.vue'
import registerMentionExtension from '~/lib/quill/quillMentionExtension.js'

Quill.imports['formats/link'].PROTOCOL_WHITELIST.push('notion')

export default {
  name: 'RichTextAreaInput',
  components: { InputWrapper, VueEditor, MentionDropdown },

  props: {
    ...inputProps,
    editorOptions: {
      type: Object,
      default: () => ({
        formats: [
          'bold',
          'color',
          'font',
          'italic',
          'link',
          'underline',
          'header',
          'indent',
          'list',
          'mention'
        ],
        modules: {
          mention: {
            mentions: [] // This will be populated with form fields
          }
        }
      })
    },
    editorToolbar: {
      type: Array,
      default: () => [
        [{ header: 1 }, { header: 2 }],
        ['bold', 'italic', 'underline', 'link'],
        [{ list: 'ordered' }, { list: 'bullet' }],
        [{ color: [] }]
      ]
    },
    mentions: {
      type: Array,
      default: () => []
    },
    enableMentions: {
      type: Boolean,
      default: false
    }
  },

  setup(props, context) {
    const editorOptions = {
      ...props.editorOptions,
      modules: { 
        ...props.editorOptions.modules,
        mention: props.enableMentions ? { mentions: props.mentions } : undefined
      }
    }
    const editorToolbar = props.enableMentions 
      ? [...props.editorToolbar, ['mention']]
      : props.editorToolbar
    return {
      ...useFormInput(props, context),
      editorOptions,
      editorToolbar,
      mentionState: registerMentionExtension(Quill)
    }
  }
}
</script>

<style lang="scss">
.rich-editor {
  .ql-container {
    border-bottom: 0px !important;
    border-right: 0px !important;
    border-left: 0px !important;

    .ql-editor {
      min-height: 100px !important;
    }
  }

  .ql-toolbar {
    border-top: 0px !important;
    border-right: 0px !important;
    border-left: 0px !important;
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

.ql-mention::after {
  content: '@';
  font-size: 18px;
}
span[mention] {
  @apply max-w-[150px] truncate overflow-hidden bg-blue-100 text-blue-800 border border-blue-200 rounded-md px-1 inline-flex items-center align-baseline leading-tight text-sm relative;
}
</style>
