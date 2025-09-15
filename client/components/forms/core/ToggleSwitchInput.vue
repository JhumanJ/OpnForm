<template>
  <input-wrapper v-bind="inputWrapperProps">
    <template #label>
      <span />
    </template>

    <div class="flex space-x-2 items-center">
      <USwitch
        :id="id ? id : name"
        v-model="compVal"
        :disabled="disabled ? true : null"
        :ui="{
          active: 'bg-[var(--form-color,#3B82F6)]',
          ring: 'focus:ring-[var(--form-color,#3B82F6)]/50'
        }"
      />
  
      <div>
        <slot
          v-if="helpPosition === 'above_input'"
          name="help"
        >
          <InputHelp
            :help="help"
            :help-classes="ui.help()"
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
            :class="ui.label()"
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
            :help-classes="ui.help()"
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
import {inputProps, useFormInput} from "../useFormInput.js"
import InputHelp from "~/components/forms/core/components/InputHelp.vue"
import { toggleSwitchInputTheme } from "~/lib/forms/themes/toggle-switch-input.theme.js"

export default {
  name: "ToggleSwitchInput",

  components: {InputHelp},
  props: {
    ...inputProps,
  },

  setup(props, context) {
    const formInput = useFormInput(props, context, {
      variants: toggleSwitchInputTheme
    })
    return {
      ...formInput
    }
  },

  mounted() {
    this.compVal = !!this.compVal
  },
}
</script>
