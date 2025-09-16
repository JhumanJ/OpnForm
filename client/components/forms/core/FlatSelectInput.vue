<template>
  <input-wrapper v-bind="inputWrapperProps">
    <template #label>
      <slot name="label" />
    </template>

    <Loader
      v-if="loading"
      key="loader"
      class="h-6 w-6 text-blue-500 mx-auto"
    />
    <div
      v-else
      class="relative overflow-hidden"
      :class="ui.container()"
      :role="multiple ? 'group' : 'radiogroup'"
      :aria-label="label || `Select ${multiple ? 'options' : 'option'}`"
    >
      <template
        v-if="options && options.length"
      >
        <div
          v-for="(option) in options"
          :key="option[optionKey]"
          :role="multiple?'checkbox':'radio'"
          :aria-checked="isSelected(option[optionKey])"
          class="relative"
          :class="[
            ui.option(),
            ui.hover(),
            { '!cursor-not-allowed !bg-neutral-200 dark:!bg-neutral-800': disableOptions.includes(option[optionKey]) }
          ]"
          @click="onSelect(option[optionKey])"
        >
          <template v-if="multiple">
            <CheckboxIcon
              :is-checked="isSelected(option[optionKey])"
              :color="color"
              :theme="resolvedTheme"
            />
          </template>
          <template v-else>
            <RadioButtonIcon
              :is-checked="isSelected(option[optionKey])"
              :color="color"
              :theme="resolvedTheme"
            />
          </template>
          <UTooltip
            :text="disableOptionsTooltip"
            :disabled="!disableOptions.includes(option[optionKey])"
            class="w-full"
          >
            <slot
              name="option"
              :option="option"
              :selected="isSelected(option[optionKey])"
            >
              <p class="flex-grow">
                {{ option[displayKey] }}
              </p>
            </slot>
          </UTooltip>
        </div>
      </template>
      <div
        v-else
        :class="[ui.option(), '!text-neutral-500 !cursor-not-allowed']"
      >
        {{ $t('forms.select.noOptionAvailable') }}
      </div>
    </div>

    <template #help>
      <slot name="help" />
    </template>

    <template
      v-if="multiple && (minSelection || maxSelection) && selectedCount > 0"
      #bottom_after_help
    >
      <small :class="ui.help()">
        <span v-if="minSelection && maxSelection">
          {{ selectedCount }} of {{ minSelection }}-{{ maxSelection }}
        </span>
        <span v-else-if="minSelection">
          {{ selectedCount }} selected (min {{ minSelection }})
        </span>
        <span v-else-if="maxSelection">
          {{ selectedCount }}/{{ maxSelection }} selected
        </span>
      </small>
    </template>

    <template #error>
      <slot name="error" />
    </template>
  </input-wrapper>
</template>

<script>
import {inputProps, useFormInput} from "../useFormInput.js"
import RadioButtonIcon from "./components/RadioButtonIcon.vue"
import CheckboxIcon from "./components/CheckboxIcon.vue"
import { flatSelectInputTheme } from "~/lib/forms/themes/flat-select-input.theme.js"

/**
 * Options: {name,value} objects
 */
export default {
  name: "FlatSelectInput",
  components: {RadioButtonIcon, CheckboxIcon},

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
    minSelection: { type: Number, default: null },
    maxSelection: { type: Number, default: null }
  },
  setup(props, context) {
    const formInput = useFormInput(props, context, {
      variants: flatSelectInputTheme,
      additionalVariants: {
        loading: props.loading,
        multiple: props.multiple
      }
    })
    return {
      ...formInput
    }
  },
  data() {
    return {}
  },
  computed: {
    selectedOptions() {
      if (!this.compVal) return []
      
      if (this.multiple) {
        return this.options.filter(option => this.compVal.includes(option[this.optionKey]))
      }
      
      return this.options.find(option => option[this.optionKey] === this.compVal) || null
    },
    selectedCount() {
      if (!this.multiple || !Array.isArray(this.compVal)) return 0
      return this.compVal.length
    },
    maxSelectionReached() {
      if (!this.multiple || !this.maxSelection) return false
      return this.selectedCount >= this.maxSelection
    },
  },
  methods: {
    onSelect(value) {
      if (this.disabled || this.disableOptions.includes(value) || this.isOptionDisabled(value)) {
        return
      }

      if (this.multiple) {
        const emitValue = Array.isArray(this.compVal) ? [...this.compVal] : []

        // Already in value, remove it only if clearable or not the last item
        if (this.isSelected(value)) {
          const nextLen = emitValue.length - 1
          if (this.minSelection && nextLen < this.minSelection) return
          if (this.clearable || nextLen >= 1) {
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
    isOptionDisabled(value) {
      if (!this.multiple || !this.maxSelection) return false
      // Allow deselection of already selected options
      const isSelected = this.isSelected(value)
      return !isSelected && this.maxSelectionReached
    },
    getOptionName(option) {
      return option ? option[this.displayKey] : ''
    },
    getSelectedOptionsNames() {
      if (!this.compVal) return []
      
      if (this.multiple) {
        return this.selectedOptions.map(option => option[this.displayKey])
      }
      
      return [this.getOptionName(this.selectedOptions)]
    },
  },
}
</script>
