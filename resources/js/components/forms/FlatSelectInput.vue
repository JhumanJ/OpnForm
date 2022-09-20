<template>
    <div :class="wrapperClass">
        <label v-if="label" :for="id?id:name"
                :class="[theme.default.label,{'uppercase text-xs':uppercaseLabels, 'text-sm':!uppercaseLabels}]"
            >
            {{ label }}
            <span v-if="required" class="text-red-500 required-dot">*</span>
        </label>

        <loader v-if="loading" key="loader" class="h-6 w-6 text-nt-blue mx-auto" />
        <div v-else v-for="option in options" :key="option[optionKey]" class="flex border mb-4 p-3 cursor-pointer rounded-2xl" @click="onSelect(option[optionKey])">
            <p class="flex-grow">
              {{ option[displayKey] }}
            </p>
            <span v-if="isSelected(option[optionKey])" class="float-right">
              <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                      clip-rule="evenodd"
                />
              </svg>
            </span>
        </div>

        <small v-if="help" :class="theme.SelectInput.help">
            <slot name="help">{{ help }}</slot>
        </small>
        <has-error v-if="hasValidation" :form="form" :field="name" />
    </div>
  </template>

  <script>
  import inputMixin from '~/mixins/forms/input'

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
      multiple: { type: Boolean, default: false },
    },
    data () {
      return {
      }
    },
    computed: {
    },
    methods: {
      onSelect (value) {
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
        if(!this.compVal) return false

        if (this.multiple) {
          return this.compVal.includes(value)
        }
        return this.compVal === value
      }
    }
  }
  </script>
