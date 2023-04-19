<template>
  <div :class="wrapperClass">
    <label v-if="label" :for="id?id:name"
           :class="[theme.RichTextAreaInput.label, {'uppercase text-xs':uppercaseLabels, 'text-sm':!uppercaseLabels}]"
    >
      {{ label }}
      <span v-if="required" class="text-red-500 required-dot">*</span>
    </label>
    <div v-if="help && helpPosition=='above_input'" class="flex mb-1">
      <small :class="theme.RichTextAreaInput.help" class="grow">
        <slot name="help"><span class="field-help" v-html="help" /></slot>
      </small>
    </div>
    <vue-editor :id="id?id:name" ref="editor" v-model="compVal" :disabled="disabled"
                :placeholder="placeholder" :class="[{ '!ring-red-500 !ring-2': hasValidation && form.errors.has(name), '!cursor-not-allowed !bg-gray-200':disabled }, theme.RichTextAreaInput.input]"
                :editor-toolbar="editorToolbar" class="rich-editor resize-y"
                :style="inputStyle"
    />

    <small v-if="help && helpPosition=='below_input'" :class="theme.RichTextAreaInput.help">
      <slot name="help"><span class="field-help" v-html="help" /></slot>
    </small>
    <has-error v-if="hasValidation" :form="form" :field="name" />
  </div>
</template>

<script>
import { VueEditor, Quill } from 'vue2-editor'
import inputMixin from '~/mixins/forms/input.js'

Quill.imports['formats/link'].PROTOCOL_WHITELIST.push('notion')

export default {
  name: 'RichTextAreaInput',
  components: { VueEditor },
  mixins: [inputMixin],

  props: {
    editorToolbar: {
      type: Array,
      default: () => {
        return [
          [{ header: 1 }, { header: 2 }],
          ['bold', 'italic', 'underline', 'link'],
          [{ list: 'ordered' }, { list: 'bullet' }],
          [{color: []}]
        ]
      }
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
