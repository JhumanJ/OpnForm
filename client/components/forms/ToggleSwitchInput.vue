<template>
  <input-wrapper v-bind="inputWrapperProps">
    <template #label>
      <span />
    </template>

    <div class="flex space-x-2 items-center">
      <v-switch
        :id="id ? id : name"
        v-model="compVal"
        :disabled="disabled ? true : null"
        :color="color"
        :theme="theme"
      />
      <div>
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
        <slot name="help">
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
  </input-wrapper>
</template>

<script>
import {inputProps, useFormInput} from "./useFormInput.js"
import VSwitch from "./components/VSwitch.vue"
import InputWrapper from "./components/InputWrapper.vue"
import InputHelp from "~/components/forms/components/InputHelp.vue"

export default {
  name: "ToggleSwitchInput",

  components: {InputHelp, InputWrapper, VSwitch},
  props: {
    ...inputProps,
  },

  setup(props, context) {
    return {
      ...useFormInput(props, context),
    }
  },

  mounted() {
    this.compVal = !!this.compVal
  },
}
</script>
