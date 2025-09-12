<template>
  <input-wrapper v-bind="inputWrapperProps">
    <template #label>
      <slot name="label" />
    </template>

    <textarea
      :id="id ? id : name"
      v-model="compVal"
      :disabled="disabled ? true : null"
      :class="variantSlots.textarea()"
      :name="name"
      :style="inputStyle"
      :placeholder="placeholder"
      :maxlength="maxCharLimit"
    />

    <template
      v-if="$slots.help"
      #help
    >
      <slot name="help" />
    </template>

    <template
      v-if="maxCharLimit && showCharLimit"
      #bottom_after_help
    >
      <small :class="variantSlots.help()">
        {{ charCount }}/{{ maxCharLimit }}
      </small>
    </template>

    <template
      v-if="$slots.error"
      #error
    >
      <slot name="error" />
    </template>
  </input-wrapper>
</template>

<script>
import { computed } from "vue"
import {inputProps, useFormInput} from "../useFormInput.js"
import { tv } from "tailwind-variants"
import { textAreaInputTheme } from "~/lib/forms/themes/text-area-input.theme.js"

export default {
  name: "TextAreaInput",
  components: {},
  mixins: [],

  props: {
    ...inputProps,
    maxCharLimit: {type: Number, required: false, default: null},
  },

  setup(props, context) {
    const formInput = useFormInput(props, context)
    const textAreaVariants = computed(() => tv(textAreaInputTheme, props.ui))
    const variantSlots = computed(() => textAreaVariants.value({
      themeName: formInput.resolvedThemeName.value,
      size: formInput.resolvedSize.value,
      borderRadius: formInput.resolvedBorderRadius.value,
      hasError: formInput.hasError.value,
      disabled: props.disabled
    }))

    return {
      ...formInput,
      variantSlots,
    }
  },

  computed: {
    charCount() {
      return this.compVal ? this.compVal.length : 0
    },
  },
}
</script>
