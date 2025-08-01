<template>
  <InputWrapper v-bind="props">
    <template #label>
      <slot name="label" />
    </template>

    <UPopover
      v-model:open="pickerOpen"
      arrow
      :disabled="props.disabled"
      :content="{ side: 'bottom', align: 'center' }"
    >
      <button
        ref="datepicker"
        class="cursor-pointer overflow-hidden"
        :class="inputClasses"
        :disabled="props.disabled"
      >
        <div class="flex items-stretch min-w-0">
          <div
            class="grow min-w-0 flex items-center gap-x-2"
            :class="[
              props.theme.DateInput.spacing.horizontal,
              props.theme.DateInput.spacing.vertical,
              props.theme.DateInput.fontSize,
            ]"
          >
            <Icon
              name="heroicons:calendar-20-solid"
              class="w-4 h-4 shrink-0"
              dynamic
            />
            <div class="grow truncate overflow-hidden flex items-center">
              <p
                v-if="formattedDatePreview"
                class="grow truncate"
              >
                {{ formattedDatePreview }}
              </p>
              <p
                v-else
                class="text-transparent"
              >
                -
              </p>
            </div>
          </div>
          <button
            v-if="fromDate && !props.disabled"
            class="hover:bg-neutral-50 dark:hover:bg-neutral-900 ltr:border-l rtl:border-r px-2 flex items-center"
            @click.prevent="clear()"
          >
            <Icon
              name="heroicons:x-mark-20-solid"
              class="w-5 h-5 text-neutral-500"
              width="2em"
              dynamic
            />
          </button>
        </div>
      </button>

      <template #content>
        <DatePicker
          class="rounded-md"
          v-if="props.dateRange"
          v-model.range="modeledValue"
          :mode="props.withTime ? 'dateTime' : 'date'"
          :is24hr="props.timeFormat == '24'"
          is-required
          borderless
          :min-date="minDate"
          :max-date="maxDate"
          :is-dark="props.isDark"
          color="form"
          :locale="props.locale"
          @update:model-value="updateModelValue"
          :style="datePickerStyles"
        />
        <DatePicker
          class="rounded-md"
          v-else
          v-model="modeledValue"
          :mode="props.withTime ? 'dateTime' : 'date'"
          :is24hr="props.timeFormat == '24'"
          is-required
          borderless
          :min-date="minDate"
          :max-date="maxDate"
          :is-dark="props.isDark"
          color="form"
          :locale="props.locale"
          @update:model-value="updateModelValue"
          :style="datePickerStyles"
        />
      </template>
    </UPopover>

    <template #help>
      <slot name="help" />
    </template>
    <template #error>
      <slot name="error" />
    </template>
  </InputWrapper>
</template>

<script setup>
import { inputProps, useFormInput } from '../useFormInput.js'
import { getCurrentInstance } from 'vue'
import { DatePicker } from 'v-calendar'
import 'v-calendar/dist/style.css'
import { format, startOfDay, endOfDay } from 'date-fns'
import { tailwindcssPaletteGenerator } from '~/lib/colors.js'

const props = defineProps({
  ...inputProps,
  withTime: { type: Boolean, default: false },
  dateRange: { type: Boolean, default: false },
  disablePastDates: { type: Boolean, default: false },
  disableFutureDates: { type: Boolean, default: false },
  dateFormat: { type: String, default: 'dd/MM/yyyy' },
  timeFormat: { type: String, default: '24' },
  outputDateFormat: { type: String, default: 'yyyy-MM-dd\'T\'HH:mm:ssXXX' },
  isDark: { type: Boolean, default: false }
})

const input = useFormInput(props, getCurrentInstance())
const fromDate = ref(null)
const toDate = ref(null)
const datepicker = ref(null)
const pickerOpen = ref(false)

const twColors = computed(() => {
  return tailwindcssPaletteGenerator(props.color).primary
})

const datePickerStyles = computed(() => {
  if (!twColors.value) {
    return {}
  }
  const styles = {}
  for (const shade in twColors.value) {
    styles[`--vc-accent-${shade}`] = twColors.value[shade]
  }
  return styles
})

const modeledValue = computed({
  get () {
    return props.dateRange ? { start: fromDate.value, end: toDate.value } : fromDate.value
  },
  set (value) {
    if (props.dateRange) {
      fromDate.value = format(value.start, props.outputDateFormat)
      toDate.value = format(value.end, props.outputDateFormat)
    } else {
      fromDate.value = format(value, props.outputDateFormat)
    }
  }
})

const updateModelValue = () => {
  if (!props.withTime && !props.dateRange && modeledValue.value) {
    pickerOpen.value = false
  }
}

const inputClasses = computed(() => {
  const classes = [props.theme.DateInput.input, props.theme.DateInput.borderRadius]
  if (props.disabled) {
    classes.push('!cursor-not-allowed bg-neutral-200! dark:bg-neutral-800!')
  }
  if (input.hasError.value) {
    classes.push('ring-red-500! ring-2! border-transparent!')
  }
  if (!props.disabled && !input.hasError.value && pickerOpen.value) {
    classes.push('ring-2 ring-form/100 border-transparent')
  }
  return classes.join(' ')
})

const minDate = computed(() => {
  if (props.disablePastDates) {
    return startOfDay(new Date())
  }
  return undefined
})
const maxDate = computed(() => {
  if (props.disableFutureDates) {
    return endOfDay(new Date())
  }
  return undefined
})
const handleCompValChange = () => {
  if (input.compVal.value) {
    if (Array.isArray(input.compVal.value)) {
      fromDate.value = input.compVal.value[0] ?? null
      toDate.value = input.compVal.value[1] ?? null
    } else {
      fromDate.value = input.compVal.value
    }
  }
}

const setInputColor = () => {
  if (datepicker.value) {
    datepicker.value.style.setProperty('--tw-ring-color', props.color)
  }
}

const clear = () => {
  fromDate.value = null
  toDate.value = null
  pickerOpen.value = false
}

const formattedDate = (value) => {
  if (props.withTime) {
    try {
      return format(new Date(value), props.dateFormat + (props.timeFormat == 12 ? ' p':' HH:mm'))
    } catch {
      console.error('Error formatting date')
      return ''
    }
  }
  try {
    return format(new Date(value), props.dateFormat)
  } catch {
    return ''
  }
}

const formattedDatePreview = computed(() => {
  if (!fromDate.value) return ''
  if (props.dateRange) {
    if (!toDate.value) return formattedDate(fromDate.value)
    return `${formattedDate(fromDate.value)} - ${formattedDate(toDate.value)}`
  }
  return formattedDate(fromDate.value)
})

watch(() => props.color, () => {
  setInputColor()
}, { immediate: true })

watch(() => props.dateRange, () => {
  fromDate.value = null
  toDate.value = null
}, { immediate: true })

watch(() => fromDate.value, (val) => {
  if (props.dateRange) {
    if (!Array.isArray(input.compVal.value)) input.compVal.value = []
    input.compVal.value[0] = val
  } else {
    input.compVal.value = val
  }
}, { immediate: false })

watch(() => toDate.value, (val) => {
  if (props.dateRange) {
    if (!Array.isArray(input.compVal.value)) input.compVal.value = [null]
    input.compVal.value[1] = val
  } else {
    input.compVal.value = null
  }
}, { immediate: false })

watch(() => input.compVal.value, (val, oldVal) => {
  if (!oldVal) handleCompValChange()
}, { immediate: false })

onMounted(() => {
  handleCompValChange()
  setInputColor()
})
</script>