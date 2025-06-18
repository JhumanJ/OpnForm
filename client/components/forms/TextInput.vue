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
      :class="[
        theme.default.input,
        theme.default.borderRadius,
        theme.default.spacing.horizontal,
        theme.default.spacing.vertical,
        theme.default.fontSize,
        {
          '!ring-red-500 !ring-2 !border-transparent': hasError,
          '!cursor-not-allowed !bg-gray-200 dark:!bg-gray-800': disabled,
        },
      ]"
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
      <small :class="theme.default.help">
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
import {inputProps, useFormInput} from "./useFormInput.js"
import InputWrapper from "./components/InputWrapper.vue"

export default {
  name: "TextInput",
  components: {InputWrapper},

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
    const onChange = (event) => {
      if (props.nativeType !== "file") return

      const file = event.target.files[0]
       
      props.form[props.name] = file
    }

    const onEnterPress = (event) => {
      event.preventDefault()
      return false
    }

    return {
      ...useFormInput(
        props,
        context,
        {
          formPrefixKey: props.nativeType === "file" ? "file-" : null
        },
      ),
      onEnterPress,
      onChange
    }
  },
  computed: {
    charCount() {
      return this.compVal ? this.compVal.length : 0
    },
  },
}
</script>
