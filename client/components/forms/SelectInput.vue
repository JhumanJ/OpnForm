<template>
  <input-wrapper v-bind="inputWrapperProps">
    <template #label>
      <slot name="label" />
    </template>
    <v-select v-model="compVal" :data="finalOptions" :label="label" :option-key="optionKey" :emit-key="emitKey"
      :required="required" :multiple="multiple" :searchable="searchable" :loading="loading" :color="color"
      :placeholder="placeholder" :uppercase-labels="uppercaseLabels" :theme="theme" :has-error="hasError"
      :allow-creation="allowCreation" :disabled="disabled ? true : null" :help="help" :help-position="helpPosition"
      @update-options="updateOptions" @update:model-value="updateModelValue">
      <template #selected="{ option }">
        <slot name="selected" :option="option" :optionName="getOptionName(option)">
          <template v-if="multiple">
            <div class="flex items-center truncate mr-6">
              <span class="truncate">
                {{ getOptionNames(selectedValues).join(', ') }}
              </span>
            </div>
          </template>
          <template v-else>
            <div class="flex items-center truncate mr-6">
              <div>{{ getOptionName(option) }}</div>
            </div>
          </template>
        </slot>
      </template>
      <template #option="{ option, selected }">
        <slot name="option" :option="option" :selected="selected">
          <span class="flex group-hover:text-white">
            <p class="flex-grow group-hover:text-white">
              {{ option.name }}
            </p>
            <span v-if="selected" class="absolute inset-y-0 right-0 flex items-center pr-4 dark:text-white">
              <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                  d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                  clip-rule="evenodd" />
              </svg>
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
    allowCreation: { type: Boolean, default: false }
  },

  setup(props, context) {
    return {
      ...useFormInput(props, context)
    }
  },

  data() {
    return {
      additionalOptions: [],
      selectedValues: [],
    }
  },

  computed: {
    finalOptions() {
      return this.options.concat(this.additionalOptions)
    }
  },
  methods: {
    getOptionName(val) {
      const option = this.finalOptions.find((optionCandidate) => {
        return optionCandidate[this.optionKey] === val
      })
      if (option) return option[this.displayKey]
      return null
    },
    getOptionNames(values) {
      return values.map(val => {
        return this.getOptionName(val)
      })
    },
    updateModelValue(newValues) {
      if (newValues === null) newValues = []
      this.selectedValues = newValues
    },
    updateOptions(newItem) {
      if (newItem) {
        this.additionalOptions.push(newItem)
      }
    }
  }
}
</script>
