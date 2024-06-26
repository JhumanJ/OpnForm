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
import { inputProps, useFormInput } from "./useFormInput.js"
import VSwitch from "./components/VSwitch.vue"
import InputWrapper from "./components/InputWrapper.vue"
export default {
  name: "ToggleSwitchInput",

  components: { InputWrapper, VSwitch },
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
