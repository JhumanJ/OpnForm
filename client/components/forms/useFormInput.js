import {ref, computed, watch} from "vue"
import {default as _get} from "lodash/get"
import {default as _set} from "lodash/set"
import {default as _has} from "lodash/has"
import CachedDefaultTheme from "~/lib/forms/themes/CachedDefaultTheme.js"

export const inputProps = {
  id: {type: String, default: null},
  name: {type: String, required: true},
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
  modelValue: {required: false},
  required: {type: Boolean, default: false},
  disabled: {type: Boolean, default: false},
  placeholder: {type: String, default: null},
  uppercaseLabels: {type: Boolean, default: false},
  hideFieldName: {type: Boolean, default: false},
  showCharLimit: {type: Boolean, default: false},
  help: {type: String, default: null},
  helpPosition: {type: String, default: "below_input"},
  color: {type: String, default: "#3B82F6"},
  wrapperClass: { type: String, default: "" },
  locale: { type: String, default: "en" },
}

export function useFormInput(props, context, options = {}) {
  const composableOptions = {
    formPrefixKey: null,
    ...options
  }
  const content = ref(props.modelValue)

  const inputStyle = computed(() => {
    return {
      "--tw-ring-color": props.color,
    }
  })

  const hasValidation = computed(() => {
    return (
      props.form !== null &&
      props.form !== undefined &&
      _has(props.form, "errors")
    )
  })

  const hasError = computed(() => {
    return hasValidation.value && props.form?.errors?.has(props.name)
  })

  const compVal = computed({
    get: () => {
      if (props.form) {
        return _get(props.form, (composableOptions.formPrefixKey || "") + props.name)
      }
      return content.value
    },
    set: (val) => {
      if (props.form) {
        _set(props.form, (composableOptions.formPrefixKey || "") + props.name, val)
      } else {
        content.value = val
      }

      if (hasValidation.value) {
        props.form.errors.clear(props.name)
      }

      context.emit("update:modelValue", compVal.value)
    },
  })

  const inputWrapperProps = computed(() => {
    const wrapperProps = {}
    Object.keys(inputProps).forEach((key) => {
      if (!["modelValue", "disabled", "placeholder", "color"].includes(key)) {
        wrapperProps[key] = props[key]
      }
    })
    return wrapperProps
  })

  const onFocus = (event) => {
    context.emit('focus', event)
  }

  const onBlur = (event) => {
    context.emit('blur', event)
  }

  // Watch for changes in props.modelValue and update the local content
  watch(
    () => props.modelValue,
    (newValue) => {
      if (content.value !== newValue) {
        content.value = newValue
      }
    },
  )

  return {
    compVal,
    inputStyle,
    hasValidation,
    hasError,
    inputWrapperProps,
    onFocus,
    onBlur,
  }
}
