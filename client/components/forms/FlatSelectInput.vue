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
          'mb-2': index !== options.length,
          '!ring-red-500 !ring-2 !border-transparent': hasError,
          '!cursor-not-allowed !bg-gray-200': disabled,
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
        ]"
        @click="onSelect(option[optionKey])"
      >
        <template v-if="multiple">
          <Icon
            v-if="isSelected(option[optionKey])"
            name="material-symbols:check-box"
            class="text-inherit"
            :color="color"
            :class="[theme.FlatSelectInput.icon]"
          />
          <Icon
            v-else
            name="material-symbols:check-box-outline-blank"
            :class="[theme.FlatSelectInput.icon,theme.FlatSelectInput.unselectedIcon]"
          />
        </template>
        <template v-else>
          <Icon
            v-if="isSelected(option[optionKey])"
            name="material-symbols:radio-button-checked-outline"
            class="text-inherit"
            :color="color"
            :class="[theme.FlatSelectInput.icon]"
          />
          <Icon
            v-else
            name="material-symbols:radio-button-unchecked"
            :class="[theme.FlatSelectInput.icon,theme.FlatSelectInput.unselectedIcon]"
          />
        </template>
        <p class="flex-grow">
          {{ option[displayKey] }}
        </p>
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
        No options available.
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

/**
 * Options: {name,value} objects
 */
export default {
  name: "FlatSelectInput",
  components: {InputWrapper},

  props: {
    ...inputProps,
    options: {type: Array, required: true},
    optionKey: {type: String, default: "value"},
    emitKey: {type: String, default: "value"},
    displayKey: {type: String, default: "name"},
    loading: {type: Boolean, default: false},
    multiple: {type: Boolean, default: false},
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
        this.compVal = this.compVal === value ? null : value
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
