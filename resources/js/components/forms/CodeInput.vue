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
import Vue from 'vue'
// import Prism Editor
import { PrismEditor } from 'vue-prism-editor'
import 'vue-prism-editor/dist/prismeditor.min.css' // import the styles somewhere
// import highlighting library (you can use any library you want just return html string)

import { highlight, languages } from 'prismjs/components/prism-core'
import 'prismjs/components/prism-clike'
import 'prismjs/components/prism-markup'
import 'prismjs/themes/prism-tomorrow.css' // import syntax highlighting styles
import inputMixin from '~/mixins/forms/input'

import VSanitize from 'v-sanitize'

const defaultOptions = {
  allowedTags: ['a', 'div', 'img','article','iframe','input','label','li','ul','ol','map','p','span','strong','table','textarea',
  'td','th','tr','area','script'
  ],
  allowedAttributes: {
    'a': [ 'href','rel','type','target','download','style','name'],
    'div':['id','class', 'style','name'],
    'img' : ['id','class','width','height','src','crossorigin','alt','style','name'],
    'article':['id','class','style','name'],
    'iframe':['id','class','style','loading','allowfullscreen','width','height','name'],
    'input':['id','class','style','name','placeholder','readonly','required'],
    'li':['id','class','style','name'],
    'ul':['id','class','style','name'],
    'ol':['id','class','style','name'],
    'map':['id','class','style','name'],
    'p':['id','class','style','name'],
    'span':['id','class','style','name'],
    'strong':['id','class','style','name'],
    'table':['id','class','style','name'],
    'textarea':['id','class','style','name','autofocus','cols','name','placeholder','readonly','required','rows'],
    'td':['id','class','style','name','rowspan','colspan','headers'],
    'th':['id','class','style','name','rowspan','colspan','headers'],
    'tr':['id','class','style','name','align','bgcolor'],
    'area':['id','class','style','name','shape','coords','alt','href']
  }
}
Vue.use(VSanitize, defaultOptions)

export default {
  name: 'CodeInput',

  components: { PrismEditor },
  mixins: [inputMixin],

  methods: {
    onChange (event) {
      const file = event.target.files[0]
      this.form.content = this.$sanitize(this.form.content)
      this.$set(this.form, this.name, file)
    },
    highlighter (code) {
      return highlight(code, languages.markup) // languages.<insert language> to return html with markup
    }
  }
}
</script>
