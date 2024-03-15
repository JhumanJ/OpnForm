<template>
  <input-wrapper
    v-bind="inputWrapperProps"
  >
    <template #label>
      <slot name="label" />
    </template>

    <Loader v-if="loading" key="loader" class="h-6 w-6 text-nt-blue mx-auto" />
    <div v-for="(option, index) in options" v-else :key="option[optionKey]" role="button"
         :class="[theme.default.input,'cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-900 flex',{ 'mb-2': index !== options.length,'!ring-red-500 !ring-2 !border-transparent': hasError, '!cursor-not-allowed !bg-gray-200':disabled }]"
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

/**
 * Options: {name,value} objects
 */
export default {
  name: 'FlatSelectInput',
  components: { InputWrapper },

  props: {
    ...inputProps,
    options: { type: Array, required: true },
    optionKey: { type: String, default: 'value' },
    emitKey: { type: String, default: 'value' },
    displayKey: { type: String, default: 'name' },
    loading: { type: Boolean, default: false },
    multiple: { type: Boolean, default: false }
  },
  setup (props, context) {
    return {
      ...useFormInput(props, context)
    }
  },
  data () {
    return {}
  },
  computed: {},
  methods: {
    onSelect (value) {
      if (this.disabled) {
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
