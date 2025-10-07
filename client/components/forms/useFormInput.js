import {ref, computed, watch, inject} from "vue"
import {default as _get} from "lodash/get"
import {default as _set} from "lodash/set"
import {default as _has} from "lodash/has"
import { tv } from "tailwind-variants"

export const inputProps = {
  id: {type: String, default: null},
  name: {type: String, required: true},
  label: {type: String, required: false},
  form: {type: Object, required: false},
  // Theme configuration as strings for tailwind-variants
  theme: {type: String, default: null},
  size: {type: String, default: null}, 
  borderRadius: {type: String, default: null},
  ui: {type: Object, default: () => ({})},
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
  media: { type: Object, default: null },
  isDark: { type: Boolean, default: false },
  locale: { type: String, default: "en" },
  isAdminPreview: { type: Boolean, default: false }
}

export function useFormInput(props, context, options = {}) {
  const composableOptions = {
    formPrefixKey: null,
    variants: null, // Tailwind-variants configuration object
    additionalVariants: {}, // Component-specific variants
    ...options
  }
  const content = ref(props.modelValue)

  // Inject theme values at composable level - centralized for all form inputs
  const injectedTheme = inject('formTheme', null)
  const injectedSize = inject('formSize', null)
  const injectedBorderRadius = inject('formBorderRadius', null)

  // Resolve theme values with proper reactivity
  const resolvedTheme = computed(() => {
    return props.theme || injectedTheme?.value || 'default'
  })

  const resolvedSize = computed(() => {
    return props.size || injectedSize?.value || 'md'
  })

  const resolvedBorderRadius = computed(() => {
    return props.borderRadius || injectedBorderRadius?.value || 'small'
  })

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
      if (!["modelValue", "disabled", "placeholder", "color", "theme", "size", "borderRadius", "ui"].includes(key)) {
        wrapperProps[key] = props[key]
      }
    })
    // Add resolved theme to wrapper props
    wrapperProps.theme = resolvedTheme.value
    return wrapperProps
  })

  // CENTRALIZED VARIANTS: Single computed property for all tailwind-variants
  // Following Nuxt UI pattern - only computed when variants config is provided
  const ui = computed(() => {
    if (!composableOptions.variants) return {}
    
    return tv(composableOptions.variants, props.ui)({
      theme: resolvedTheme.value,        // props.theme resolved with injection
      size: resolvedSize.value,
      borderRadius: resolvedBorderRadius.value,
      hasError: hasError.value,
      disabled: props.disabled,
      // Component-specific variants (e.g., loading, multiple, etc.)
      ...composableOptions.additionalVariants
    })
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
    // Resolved theme values - available to all form input components
    resolvedTheme,
    resolvedSize,
    resolvedBorderRadius,
    // Centralized UI variants - ready to use in templates
    ui,
  }
}

