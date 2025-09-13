<template>
  <input-wrapper v-bind="inputWrapperProps">
    <template #label>
      <slot name="label" />
    </template>

    <input
      :id="id ? id : name"
      v-model="compVal"
      :disabled="disabled ? true : null"
      :type="nativeType"
      :autocomplete="autocomplete"
      :pattern="pattern"
      :style="inputStyle"
      :class="variantSlots.input()"
      :name="name"
      :accept="accept"
      :placeholder="placeholder"
      :min="min"
      :max="max"
      :maxlength="maxCharLimit"
      @change="onChange"
      @keydown.enter.prevent="onEnterPress"
      @focus="onFocus"
      @blur="onBlur"
    >

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
import {inputProps, useFormInput} from "../useFormInput.js"
import { tv } from "tailwind-variants"
import { textInputTheme } from "~/lib/forms/themes/text-input.theme.js"

export default {
  name: "TextInput",
  components: {},

  props: {
    ...inputProps,
    nativeType: {type: String, default: "text"},
    accept: {type: String, default: null},
    min: {type: Number, required: false, default: null},
    max: {type: Number, required: false, default: null},
    autocomplete: {type: [Boolean, String, Object], default: null},
    maxCharLimit: {type: Number, required: false, default: null},
    pattern: {type: String, default: null},
  },

  setup(props, context) {
    // Create textInput variants with UI prop merging as computed property
    const textInputVariants = computed(() => tv(textInputTheme, props.ui))

    const formInput = useFormInput(
      props,
      context,
      {
        formPrefixKey: props.nativeType === "file" ? "file-" : null
      },
    )

    const onChange = (event) => {
      if (props.nativeType !== "file") return

      const file = event.target.files[0]
       
      props.form[props.name] = file
    }

    const onEnterPress = (event) => {
      event.preventDefault()
      return false
    }

    // Single variant computation - fully reactive
    const variantSlots = computed(() => {
      return textInputVariants.value({
        themeName: formInput.resolvedThemeName.value,
        size: formInput.resolvedSize.value,
        borderRadius: formInput.resolvedBorderRadius.value,
        hasError: formInput.hasError.value,
        disabled: props.disabled
      })
    })

    return {
      ...formInput,
      onEnterPress,
      onChange,
      variantSlots
    }
  },
  computed: {
    charCount() {
      return this.compVal ? this.compVal.length : 0
    },
  },
}
</script>
