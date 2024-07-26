<template>
  <div
    :class="[ twMerge(theme.default.wrapper,wrapperClass)]"
    :style="inputStyle"
  >
    <slot name="label">
      <InputLabel
        v-if="label && !hideFieldName"
        :label="label"
        :theme="theme"
        :required="required"
        :native-for="id ? id : name"
        :uppercase-labels="uppercaseLabels"
      />
    </slot>
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
    <slot />

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
    <slot name="error">
      <has-error
        v-if="hasValidation && form"
        :form="form"
        :field="name"
      />
    </slot>
  </div>
</template>

<script setup>
import InputLabel from "./InputLabel.vue"
import InputHelp from "./InputHelp.vue"
import {twMerge} from "tailwind-merge"
import CachedDefaultTheme from "~/lib/forms/themes/CachedDefaultTheme.js"

defineProps({
  id: {type: String, required: false},
  name: {type: String, required: false},
  label: {type: String, required: false},
  form: {type: Object, required: false},
  theme: {
    type: Object, default: () => {
      const theme = inject("theme", null)
      if (theme) {
        return theme.value
      }
      return CachedDefaultTheme.getInstance()
    }
  },
  wrapperClass: {type: String, required: false},
  inputStyle: {type: Object, required: false},
  help: {type: String, required: false},
  helpPosition: {type: String, default: "below_input"},
  uppercaseLabels: {type: Boolean, default: true},
  hideFieldName: {type: Boolean, default: true},
  required: {type: Boolean, default: false},
  hasValidation: {type: Boolean, default: true},
})
</script>
