<template>
  <div :class="wrapperClass">
    <label v-if="label" :for="id?id:name"
           :class="[theme.default.label,{'uppercase text-xs':uppercaseLabels, 'text-sm':!uppercaseLabels}]"
    >
      {{ label }}
      <span v-if="required" class="text-red-500 required-dot">*</span>
    </label>
    <input :id="id?id:name" v-model="compVal" v-cleave="cleaveOptions" :disabled="disabled"
                  :class="[theme.default.input,{ 'ring-red-500 ring-2': hasValidation && form.errors.has(name), 'cursor-not-allowed bg-gray-200':disabled }]"
                  :style="inputStyle" :name="name"
                  :placeholder="placeholder"
    />
    <small v-if="help" :class="theme.default.help">
    <slot name="help"><span class="field-help" v-html="help" /></slot>
    </small>
    <has-error v-if="hasValidation" :form="form" :field="name" />
  </div>
</template>

<script>
import inputMixin from '~/mixins/forms/input'
import Cleave from 'cleave.js';

export default {
  name: 'SimpleDateInput',
  mixins: [inputMixin],

  props: {
    dateFormat: { type: String, default: 'DD/MM/YYYY' },
  },

  directives: {
    cleave: {
      inserted: (el, binding) => {
        el.cleave = new Cleave(el, binding.value || {})
      },
      update: (el) => {
        const event = new Event('input', {bubbles: true});
        setTimeout(function () {
            el.value = el.cleave.properties.result
            el.dispatchEvent(event)
        }, 100);
      }
    }
  },

  data: () => ({

  }),

  computed: {
    cleaveOptions () {
      let datePattern = ['d','m','Y']
      if(this.dateFormat == 'MM/DD/YYYY'){
        datePattern = ['m','d','Y']
      }else if(this.dateFormat == 'YYYY/MM/DD'){
        datePattern = ['Y','m','d']
      }
      return {
        date: true,
        delimiter: '/',
        datePattern: datePattern
      }
    }
  },

  watch: {
    color: {
      handler () {
        this.setInputColor()
      },
      immediate: true
    }
  },

  mounted () {
    this.setInputColor()
  },

  methods: {
    /**
     * Pressing enter won't submit form
     * @param event
     * @returns {boolean}
     */
    onEnterPress (event) {
      event.preventDefault()
      return false
    },
    setInputColor () {
      if (this.$refs.datepicker) {
        const dateInput = this.$refs.datepicker.$el.getElementsByTagName('input')[0]
        dateInput.style.setProperty('--tw-ring-color', this.color)
      }
    }
  }
}
</script>
