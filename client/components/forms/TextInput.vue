<template>
  <input-wrapper v-bind="inputWrapperProps">
    <template #label>
      <slot name="label" />
    </template>

    <input
      ref="inputRef"
      :id="id ? id : name"
      v-model="displayValue"
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
      :placeholder="effectivePlaceholder"
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
    pattern: { type: String, default: null },
    mask: { type: String, default: null }
  },

  setup(props, context) {
    const { formatValue, isComplete, getMaskPlaceholder, isValidMask } = useInputMask(() => props.mask)

    const { compVal, inputWrapperProps } = useFormInput(
      props,
      context,
      {
        formPrefixKey: props.nativeType === "file" ? "file-" : null
      },
    )

    const maskedValue = ref('')
    const inputRef = ref(null)

    const displayValue = computed({
      get() {
        if (props.mask && isValidMask.value) {
          return maskedValue.value
        } else {
          return compVal.value
        }
      },
      set(newValue) {
        if (props.mask && isValidMask.value) {
          handleInput({ target: { value: newValue } })
        } else {
          compVal.value = newValue
        }
      }
    })

    const handleInput = (event) => {
      const inputValue = event.target.value

      if (!props.mask || !isValidMask.value) {
        // No mask or invalid mask - behave as normal input
        compVal.value = inputValue
        return
      }

      const formatted = formatValue(inputValue)

      // Auto-clear if incomplete and user is backspacing/clearing
      if (!isComplete(formatted) && inputValue.length > formatted.length) {
        maskedValue.value = ''
        compVal.value = ''
        nextTick(() => {
          if (inputRef.value) {
            inputRef.value.value = ''
          }
        })
        return
      }

      maskedValue.value = formatted
      compVal.value = formatted

      // Update input display value
      nextTick(() => {
        if (inputRef.value && inputRef.value.value !== formatted) {
          const cursorPos = inputRef.value.selectionStart
          inputRef.value.value = formatted
          // Maintain cursor position logic here
          if (inputRef.value.setSelectionRange) {
            inputRef.value.setSelectionRange(cursorPos, cursorPos)
          }
        }
      })
    }

    const effectivePlaceholder = computed(() => {
      if (props.placeholder) return props.placeholder
      if (props.mask && isValidMask.value) return getMaskPlaceholder.value
      return null
    })

    // Watch for mask changes (form editor support)
    watch(() => props.mask, (newMask) => {
      if (!newMask) {
        maskedValue.value = compVal.value || ''
      } else if (compVal.value) {
        // Reformat existing value with new mask
        maskedValue.value = formatValue(compVal.value)
      }
    })

    // Watch for compVal changes from parent
    watch(compVal, (newVal) => {
      if (props.mask && isValidMask.value && newVal) {
        maskedValue.value = formatValue(newVal)
      } else {
        maskedValue.value = newVal || ''
      }
    }, { immediate: true })


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
      onChange,
      handleInput,
      maskedValue,
      effectivePlaceholder,
      inputRef,
      isValidMask,
      displayValue
    }
  },
  computed: {
    charCount() {
      return this.compVal ? this.compVal.length : 0
    },
  },
}
</script>
