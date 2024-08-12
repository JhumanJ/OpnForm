<template>
  <input-wrapper v-bind="inputWrapperProps">
    <template #label>
      <slot name="label" />
    </template>

    <v-select
      v-model="compVal"
      :dusk="name"
      :data="finalOptions"
      :label="label"
      :option-key="optionKey"
      :emit-key="emitKey"
      :required="required"
      :multiple="multiple"
      :clearable="clearable"
      :searchable="searchable"
      :loading="loading"
      :color="color"
      :placeholder="placeholder"
      :uppercase-labels="uppercaseLabels"
      :theme="theme"
      :has-error="hasError"
      :allow-creation="allowCreation"
      :disabled="disabled"
      :help="help"
      :help-position="helpPosition"
      :remote="remote"
      :dropdown-class="dropdownClass"
      @update-options="updateOptions"
      @update:model-value="updateModelValue"
    >
      <template #selected="{ option }">
        <template v-if="multiple">
          <div class="flex items-center truncate mr-6">
            <span
              class="truncate"
              :class="[
                theme.SelectInput.fontSize,
              ]"
            >
              {{ getOptionNames(selectedValues).join(', ') }}
            </span>
          </div>
        </template>
        <template v-else>
          <slot
            name="selected"
            :option="option"
            :option-name="getOptionName(option)"
          >
            <div class="flex items-center truncate mr-6">
              <div
                :class="[
                  theme.SelectInput.fontSize,
                ]"
              >
                {{ getOptionName(option) }}
              </div>
            </div>
          </slot>
        </template>
      </template>
      <template #option="{ option, selected }">
        <slot
          name="option"
          :option="option"
          :selected="selected"
        >
          <span class="flex">
            <p
              class="flex-grow"
              :class="[
                theme.SelectInput.fontSize,
              ]"
            >
              {{ getOptionName(option) }}
            </p>
            <span
              v-if="selected"
              class="absolute inset-y-0 right-0 flex items-center pr-4 dark:text-white"
            >
              <Icon
                name="heroicons:check-16-solid"
                class="w-5 h-5"
              />
            </span>
          </span>
        </slot>
      </template>
    </v-select>

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
  name: 'SelectInput',
  components: { InputWrapper },

  props: {
    ...inputProps,
    options: { type: Array, required: true },
    optionKey: { type: String, default: 'value' },
    emitKey: { type: String, default: 'value' },
    displayKey: { type: String, default: 'name' },
    loading: { type: Boolean, default: false },
    multiple: { type: Boolean, default: false },
    searchable: { type: Boolean, default: false },
    clearable: { type: Boolean, default: false },
    allowCreation: { type: Boolean, default: false },
    dropdownClass: { type: String, default: 'w-full' },
    remote: { type: Function, default: null }
  },
  setup(props, context) {
    return {
      ...useFormInput(props, context)
    }
  },
  data() {
    return {
      additionalOptions: [],
      selectedValues: []
    }
  },
  computed: {
    finalOptions() {
      return this.options.concat(this.additionalOptions)
    }
  },
  watch: {
    compVal: {
      handler(newVal, oldVal) {
        if (!oldVal) {
          this.handleCompValChanged()
        }
      },
      immediate: false
    }
  },
  mounted() {
    this.handleCompValChanged()
  },
  methods: {
    getOptionName(val) {
      if (val == null) return ''
      const option = this.finalOptions.find((optionCandidate) => {
        return optionCandidate && optionCandidate[this.optionKey] === val ||
          (typeof val === 'object' && val && optionCandidate && optionCandidate[this.optionKey] === val[this.optionKey])
      })
      if (option && option[this.displayKey] !== undefined) {
        return option[this.displayKey]
      }
      return val.toString() // Convert to string to ensure it's not null
    },
    getOptionNames(values) {
      if (!Array.isArray(values)) return []
      return values.map(val => this.getOptionName(val)).filter(Boolean)
    },
    updateModelValue(newValues) {
      if (newValues === null) newValues = []
      this.selectedValues = newValues
    },
    updateOptions(newItem) {
      if (newItem) {
        this.additionalOptions.push(newItem)
      }
    },
    handleCompValChanged() {
      if (this.compVal) {
        this.selectedValues = this.compVal
      }
    }
  }
}
</script>
