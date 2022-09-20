<template>
  <div :class="wrapperClass">
    <label v-if="label" :for="id?id:name"
           :class="[theme.CodeInput.label,{'uppercase text-xs':uppercaseLabels, 'text-sm':!uppercaseLabels}]"
    >
      {{ label }}
      <span v-if="required" class="text-red-500 required-dot">*</span>
    </label>

    <prism-editor :id="id?id:name" v-model="compVal" :disabled="disabled"
                  class="code-editor"
                  :class="[theme.CodeInput.input,{ 'ring-red-500 ring-2': hasValidation && form.errors.has(name), 'cursor-not-allowed bg-gray-200':disabled }]"
                  :style="inputStyle" :name="name"
                  :placeholder="placeholder"
                  :highlight="highlighter" @change="onChange"
    />

    <small v-if="help" :class="theme.CodeInput.help">
      <slot name="help">{{ help }}</slot>
    </small>
    <has-error v-if="hasValidation" :form="form" :field="name" />
  </div>
</template>

<script>
// import Prism Editor
import { PrismEditor } from 'vue-prism-editor'
import 'vue-prism-editor/dist/prismeditor.min.css' // import the styles somewhere
// import highlighting library (you can use any library you want just return html string)

import { highlight, languages } from 'prismjs/components/prism-core'
import 'prismjs/components/prism-clike'
import 'prismjs/components/prism-markup'
import 'prismjs/themes/prism-tomorrow.css' // import syntax highlighting styles
import inputMixin from '~/mixins/forms/input'

export default {
  name: 'CodeInput',

  components: { PrismEditor },
  mixins: [inputMixin],

  methods: {
    onChange (event) {
      const file = event.target.files[0]
      this.$set(this.form, this.name, file)
    },
    highlighter (code) {
      return highlight(code, languages.markup) // languages.<insert language> to return html with markup
    }
  }
}
</script>
