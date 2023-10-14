<template>
  <div :class="wrapperClass">
    <label v-if="label" :for="id?id:name"
           :class="[theme.CodeInput.label,{'uppercase text-xs':uppercaseLabels, 'text-sm':!uppercaseLabels}]"
    >
      {{ label }}
      <span v-if="required" class="text-red-500 required-dot">*</span>
    </label>

    <div :class="[theme.CodeInput.input,{ '!ring-red-500 !ring-2': hasValidation && form.errors.has(name), '!cursor-not-allowed !bg-gray-200':disabled }]">
      <codemirror :id="id?id:name" v-model="compVal" :disabled="disabled"
                  :options="cmOptions"
                  :style="inputStyle" :name="name"
                  :placeholder="placeholder"
      />
    </div>

    <small v-if="help" :class="theme.CodeInput.help">
      <slot name="help"><span class="field-help" v-html="help" /></slot>
    </small>
    <has-error v-if="hasValidation" :form="form" :field="name" />
  </div>
</template>

<script>
import { codemirror } from 'vue-codemirror'
import 'codemirror/lib/codemirror.css'

import 'codemirror/mode/htmlmixed/htmlmixed.js'

import inputMixin from '~/mixins/forms/input.js'

export default {
  name: 'CodeInput',

  components: { codemirror },
  mixins: [inputMixin],

  data () {
    return {
      cmOptions: {
        // codemirror options
        tabSize: 4,
        mode: 'text/html',
        theme: 'default',
        lineNumbers: true,
        line: true
      }
    }
  },

  methods: {}
}
</script>
