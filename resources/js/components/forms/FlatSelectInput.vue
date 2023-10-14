<template>
  <div :class="wrapperClass">
    <label v-if="label" :for="id?id:name"
           :class="[theme.default.label,{'uppercase text-xs':uppercaseLabels, 'text-sm':!uppercaseLabels}]"
    >
      {{ label }}
      <span v-if="required" class="text-red-500 required-dot">*</span>
    </label>
    <small v-if="help && helpPosition=='above_input'" :class="theme.SelectInput.help" class="block mb-1">
      <slot name="help"><span class="field-help" v-html="help" /></slot>
    </small>

    <loader v-if="loading" key="loader" class="h-6 w-6 text-nt-blue mx-auto" />
    <div v-for="(option, index) in options" v-else :key="option[optionKey]" role="button"
         :class="[theme.default.input,'cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-900 flex',{ 'mb-2': index !== options.length,'!ring-red-500 !ring-2': hasValidation && form.errors.has(name), '!cursor-not-allowed !bg-gray-200':disabled }]"
         @click="onSelect(option[optionKey])"
    >
      <p class="flex-grow">
        {{ option[displayKey] }}
      </p>
      <div v-if="isSelected(option[optionKey])" class="flex items-center">
        <svg :color="color" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
      </div>
    </div>

    <small v-if="help && helpPosition=='below_input'" :class="theme.SelectInput.help" class="block">
      <slot name="help"><span class="field-help" v-html="help" /></slot>
    </small>
    <has-error v-if="hasValidation" :form="form" :field="name" />
  </div>
</template>

<script>
import inputMixin from '~/mixins/forms/input.js'

/**
 * Options: {name,value} objects
 */
export default {
  name: 'FlatSelectInput',
  mixins: [inputMixin],

  props: {
    options: { type: Array, required: true },
    optionKey: { type: String, default: 'value' },
    emitKey: { type: String, default: 'value' },
    displayKey: { type: String, default: 'name' },
    loading: { type: Boolean, default: false },
    multiple: { type: Boolean, default: false }
  },
  data () {
    return {}
  },
  computed: {},
  methods: {
    onSelect (value) {
      if(this.disabled){
        return
      }
      
      if (this.multiple) {
        const emitValue = Array.isArray(this.compVal) ? [...this.compVal] : []

        // Already in value, remove it
        if (this.isSelected(value)) {
          this.compVal = emitValue.filter((item) => {
            return item !== value
          })
          return
        }

        // Otherwise add value
        emitValue.push(value)
        this.compVal = emitValue
      } else {
        this.compVal = (this.compVal === value) ? null : value
      }
    },
    isSelected (value) {
      if (!this.compVal) return false

      if (this.multiple) {
        return this.compVal.includes(value)
      }
      return this.compVal === value
    }
  }
}
</script>
