import { ref, computed, watch } from 'vue'
import { themes } from '~/lib/forms/form-themes.js'

export const inputProps = {
  id: { type: String, default: null },
  name: { type: String, required: true },
  label: { type: String, required: false },
  form: { type: Object, required: false },
  theme: { type: Object, default: () => themes.default },
  modelValue: { required: false },
  required: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
  placeholder: { type: String, default: null },
  uppercaseLabels: { type: Boolean, default: false },
  hideFieldName: { type: Boolean, default: false },
  help: { type: String, default: null },
  helpPosition: { type: String, default: 'below_input' },
  color: { type: String, default: '#3B82F6' },
  wrapperClass: { type: String, default: 'relative mb-3' }
}

export function useFormInput (props, context, formPrefixKey = null) {
  const content = ref(props.modelValue)

  const inputStyle = computed(() => {
    return {
      '--tw-ring-color': props.color
    }
  })

  const hasValidation = computed(() => {
    return props.form !== null && props.form !== undefined && props.form.hasOwnProperty('errors')
  })

  const hasError = computed(() => {
    return hasValidation && props.form?.errors?.has(props.name)
  })

  const compVal = computed({
    get: () => {
      if (props.form) {
        return props.form[(formPrefixKey || '') + props.name]
      }
      return content.value
    },
    set: (val) => {
      if (props.form) {
        props.form[(formPrefixKey || '') + props.name] = val
      } else {
        content.value = val
      }

      if (hasValidation.value) {
        props.form.errors.clear(props.name)
      }

      context.emit('update:modelValue', compVal.value)
    }
  })

  const inputWrapperProps = computed(() => {
    const wrapperProps = {}
    Object.keys(inputProps).forEach((key) => {
      if (!['modelValue', 'disabled', 'placeholder', 'color'].includes(key)) {
        wrapperProps[key] = props[key]
      }
    })
    return wrapperProps
  })

  // Watch for changes in props.modelValue and update the local content
  watch(
    () => props.modelValue,
    (newValue) => {
      if (content.value !== newValue) {
        content.value = newValue
      }
    }
  )

  return {
    compVal,
    inputStyle,
    hasValidation,
    hasError,
    inputWrapperProps
  }
}
