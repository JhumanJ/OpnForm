<template>
  <input-wrapper v-bind="inputWrapperProps">
    <template #label>
      <slot name="label" />
    </template>

    <textarea
      :id="id ? id : name"
      v-model="compVal"
      :disabled="disabled ? true : null"
      :class="ui.input()"
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
      <small :class="ui.help()">
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
import {inputProps, useFormInput} from "../useFormInput.js"
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
    const formInput = useFormInput(props, context, {
      variants: textAreaInputTheme
    })

    return {
      ...formInput
    }
  },

  computed: {
    charCount() {
      return this.compVal ? this.compVal.length : 0
    },
  },
}
</script>
