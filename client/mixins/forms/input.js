import { themes } from '~/lib/forms/form-themes.js'
import { default as _has } from 'lodash/has'

export default {
  props: {
    id: { type: String, default: null },
    name: { type: String, required: true },
    label: { type: String, required: false },
    form: { type: Object, required: false },
    value: { required: false },
    required: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
    placeholder: { type: String, default: null },
    uppercaseLabels: { type: Boolean, default: false },
    help: { type: String, default: null },
    helpPosition: { type: String, default: 'below_input' },
    theme: { type: Object, default: () => themes.default },
    color: { type: String, default: '#3B82F6' },
    wrapperClass: { type: String, default: 'relative mb-3' }
  },

  data () {
    return {
      content: this.value
    }
  },

  computed: {
    inputStyle () {
      return {
        '--tw-ring-color': this.color
      }
    },
    hasValidation () {
      return this.form !== null && this.form !== undefined && _has(this.form, 'errors')
    },
    compVal: {
      set (val) {
        if (this.form) {
          this.form[this.name] = val
        } else {
          this.content = val
        }
        if (this.hasValidation) {
          this.form.errors.clear(this.name)
        }
        this.$emit('input', this.compVal)
      },
      get () {
        if (this.form) {
          return this.form[this.name]
        }
        return this.content
      }
    }
  },

  watch: {
    value (val) {
      if (val !== this.compVal) {
        this.compVal = val
      }
    }
  }
}
