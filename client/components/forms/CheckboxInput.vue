<template>
  <InputWrapper v-bind="inputWrapperProps">
    <template #label>
      <span />
    </template>

    <div class="flex space-x-2 items-center">
      <VCheckbox
        :id="id ? id : name"
        v-model="compVal"
        :value="value"
        :disabled="disabled ? true : null"
        :name="name"
        :color="color"
        :theme="theme"
      />
      <div>
        <slot
          v-if="helpPosition === 'above_input'"
          name="help"
        >
          <InputHelp
            :help="help"
            :help-classes="theme.default.help"
          >
            <template #after-help>
              <slot name="bottom_after_help" />
            </template>
          </InputHelp>
        </slot>
        <slot name="label">
          <label
            :aria-label="id ? id : name"
            :for="id ? id : name"
            :class="theme.default.fontSize"
          >
            {{ label }}
            <span
              v-if="required"
              class="text-red-500 required-dot"
            >*</span>
          </label>
        </slot>
        <slot
          v-if="helpPosition === 'below_input'"
          name="help"
        >
          <InputHelp
            :help="help"
            :help-classes="theme.default.help"
          >
            <template #after-help>
              <slot name="bottom_after_help" />
            </template>
          </InputHelp>
        </slot>
      </div>
    </div>

    <template #help>
      <span class="hidden" />
    </template>

    <template #error>
      <slot name="error" />
    </template>
  </InputWrapper>
</template>

<script>
import { inputProps, useFormInput } from './useFormInput.js'
import VCheckbox from './components/VCheckbox.vue'
import InputWrapper from './components/InputWrapper.vue'

export default {
  name: 'CheckboxInput',

  components: { InputWrapper, VCheckbox },
  props: {
    ...inputProps,
    value: { type: [Boolean, String, Number, Object], required: false },
  },

  setup(props, context) {
    return {
      ...useFormInput(props, context),
    }
  },

  mounted() {
    if (!this.compVal)
      this.compVal = false
  },
}
</script>
