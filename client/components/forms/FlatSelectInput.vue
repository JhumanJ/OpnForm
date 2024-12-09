<template>
  <input-wrapper v-bind="inputWrapperProps">
    <template #label>
      <slot name="label" />
    </template>

    <Loader
      v-if="loading"
      key="loader"
      class="h-6 w-6 text-nt-blue mx-auto"
    />
    <div
      v-else
      class="relative overflow-hidden"
      :class="[
        theme.default.input,
        theme.default.borderRadius,
        {
          '!ring-red-500 !ring-2 !border-transparent': hasError,
          '!cursor-not-allowed !bg-gray-200 dark:!bg-gray-800': disabled,
        },
      ]"
    >
      <template
        v-if="options && options.length"
      >
        <div
          v-for="(option) in options"
          :key="option[optionKey]"
          :role="multiple?'checkbox':'radio'"
          :aria-checked="isSelected(option[optionKey])"
          :class="[
            theme.FlatSelectInput.spacing.vertical,
            theme.FlatSelectInput.fontSize,
            theme.FlatSelectInput.option,
            {
              '!cursor-not-allowed !bg-gray-200 dark:!bg-gray-800': disableOptions.includes(option[optionKey]),
            },
          ]"
          @click="onSelect(option[optionKey])"
        >
          <template v-if="multiple">
            <CheckboxIcon
              :is-checked="isSelected(option[optionKey])"
              :color="color"
              :theme="theme"
            />
          </template>
          <template v-else>
            <RadioButtonIcon
              :is-checked="isSelected(option[optionKey])"
              :color="color"
              :theme="theme"
            />
          </template>
          <UTooltip
            :text="disableOptionsTooltip"
            :prevent="!disableOptions.includes(option[optionKey])"
            class="w-full"
          >
            <p class="flex-grow">
              {{ option[displayKey] }}
            </p>
          </UTooltip>
        </div>
      </template>
      <div
        v-else
        :class="[
          theme.FlatSelectInput.spacing.horizontal,
          theme.FlatSelectInput.spacing.vertical,
          theme.FlatSelectInput.fontSize,
          theme.FlatSelectInput.option,
          '!text-gray-500 !cursor-not-allowed'
        ]"
      >
        {{ $t('forms.select.noOptionAvailable') }}
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
import {inputProps, useFormInput} from "./useFormInput.js"
import InputWrapper from "./components/InputWrapper.vue"
import RadioButtonIcon from "./components/RadioButtonIcon.vue"
import CheckboxIcon from "./components/CheckboxIcon.vue"

/**
 * Options: {name,value} objects
 */
export default {
  name: "FlatSelectInput",
  components: {InputWrapper, RadioButtonIcon, CheckboxIcon},

  props: {
    ...inputProps,
    options: {type: Array, required: true},
    optionKey: {type: String, default: "value"},
    emitKey: {type: String, default: "value"},
    displayKey: {type: String, default: "name"},
    loading: {type: Boolean, default: false},
    multiple: { type: Boolean, default: false },
    disableOptions: { type: Array, default: () => [] },
    disableOptionsTooltip: { type: String, default: "Not allowed" },
    clearable: { type: Boolean, default: false },
  },
  setup(props, context) {
    return {
      ...useFormInput(props, context),
    }
  },
  data() {
    return {}
  },
  computed: {},
  methods: {
    onSelect(value) {
      if (this.disabled || this.disableOptions.includes(value)) {
        return
      }

      if (this.multiple) {
        const emitValue = Array.isArray(this.compVal) ? [...this.compVal] : []

        // Already in value, remove it only if clearable or not the last item
        if (this.isSelected(value)) {
          if (this.clearable || emitValue.length > 1) {
            this.compVal = emitValue.filter((item) => item !== value)
          }
          return
        }

        // Otherwise add value
        emitValue.push(value)
        this.compVal = emitValue
      } else {
        // For single select, only change value if it's different or clearable
        if (this.compVal !== value || this.clearable) {
          this.compVal = this.compVal === value && this.clearable ? null : value
        }
      }
    },
    isSelected(value) {
      if (!this.compVal) return false

      if (this.multiple) {
        return this.compVal.includes(value)
      }
      return this.compVal === value
    },
  },
}
</script>