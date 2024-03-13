<template>
  <input-wrapper
    v-bind="inputWrapperProps"
  >
    <template #label>
      <slot name="label" />
    </template>

    <vue-editor :id="id?id:name" ref="editor" v-model="compVal" :disabled="disabled?true:null"
                :placeholder="placeholder" :class="[{ '!ring-red-500 !ring-2 !border-transparent': hasError, '!cursor-not-allowed !bg-gray-200':disabled }, theme.RichTextAreaInput.input]"
                :editor-toolbar="editorToolbar" class="rich-editor resize-y"
                :style="inputStyle"
    />

    <template #help>
      <slot name="help" />
    </template>
    <template #error>
      <slot name="error" />
    </template>
  </input-wrapper>
</template>

<script>
import { inputProps, useFormInput } from './useFormInput.js'
import InputWrapper from './components/InputWrapper.vue'
import { VueEditor, Quill } from 'vue3-editor'

Quill.imports['formats/link'].PROTOCOL_WHITELIST.push('notion')

export default {
  name: 'RichTextAreaInput',
  components: { InputWrapper, VueEditor },

  props: {
    ...inputProps,
    editorToolbar: {
      type: Array,
      default: () => {
        return [
          [{ header: 1 }, { header: 2 }],
          ['bold', 'italic', 'underline', 'link'],
          [{ list: 'ordered' }, { list: 'bullet' }],
          [{ color: [] }]
        ]
      }
    }
  },

  setup (props, context) {
    return {
      ...useFormInput(props, context)
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

  .ql-snow .ql-toolbar .ql-picker-item.ql-selected, .ql-snow .ql-toolbar .ql-picker-item:hover, .ql-snow .ql-toolbar .ql-picker-label.ql-active, .ql-snow .ql-toolbar .ql-picker-label:hover, .ql-snow .ql-toolbar button.ql-active, .ql-snow .ql-toolbar button:focus, .ql-snow .ql-toolbar button:hover, .ql-snow.ql-toolbar .ql-picker-item.ql-selected, .ql-snow.ql-toolbar .ql-picker-item:hover, .ql-snow.ql-toolbar .ql-picker-label.ql-active, .ql-snow.ql-toolbar .ql-picker-label:hover, .ql-snow.ql-toolbar button.ql-active, .ql-snow.ql-toolbar button:focus, .ql-snow.ql-toolbar button:hover {
    @apply text-nt-blue;
  }
}
</style>
