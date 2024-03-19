<template>
  <input-wrapper
    v-bind="inputWrapperProps"
  >
    <template #label>
      <slot name="label" />
    </template>

    <div v-if="!dateRange" class="flex">
      <input :id="id?id:name" v-model="fromDate" :type="useTime ? 'datetime-local' : 'date'" :class="inputClasses"
             :disabled="disabled?true:null"
             :style="inputStyle" :name="name" data-date-format="YYYY-MM-DD"
             :min="setMinDate" :max="setMaxDate"
      >
    </div>
    <div v-else :class="inputClasses">
      <div class="flex -mx-2">
        <p class="text-gray-900 px-4">
          From
        </p>
        <input :id="id?id:name" v-model="fromDate" :type="useTime ? 'datetime-local' : 'date'" :disabled="disabled?true:null"
               :style="inputStyle" :name="name" data-date-format="YYYY-MM-DD"
               class="flex-grow border-transparent focus:outline-none "
               :min="setMinDate" :max="setMaxDate"
        >
        <p class="text-gray-900 px-4">
          To
        </p>
        <input v-if="dateRange" :id="id?id:name" v-model="toDate" :type="useTime ? 'datetime-local' : 'date'"
               :disabled="disabled?true:null"
               :style="inputStyle" :name="name" class="flex-grow border-transparent focus:outline-none"
               :min="setMinDate" :max="setMaxDate"
        >
      </div>
    </div>

    <template #help>
      <slot name="help" />
    </template>
    <template #error>
      <slot name="error" />
    </template>
  </input-wrapper>
</template>

<script setup>
import { inputProps, useFormInput } from './useFormInput.js'
import InputWrapper from './components/InputWrapper.vue'


const props = defineProps({
  ...inputProps,
  withTime: { type: Boolean, default: false },
  dateRange: { type: Boolean, default: false },
  disablePastDates: { type: Boolean, default: false },
  disableFutureDates: { type: Boolean, default: false }
})

const {
  compVal,
  inputStyle,
  hasValidation,
  hasError,
  inputWrapperProps
} = useFormInput(props, getCurrentInstance(), null, (val) => setValue(val))

const fromDate = ref(null)
const toDate = ref(null)

const inputClasses = computed(() => {
  let str = 'border border-gray-300 dark:bg-notion-dark-light dark:border-gray-600 dark:placeholder-gray-500 dark:text-gray-300 flex-1 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-opacity-100 placeholder-gray-400 px-4 py-2 rounded-lg shadow-sm text-base text-black text-gray-700'
  str += this.dateRange ? ' w-50' : ' w-full'
  str += this.disabled ? ' !cursor-not-allowed !bg-gray-200' : ''
  return str
})
const useTime = computed(() => props.withTime)
const setMinDate = computed(() => props.disablePastDates ? new Date().toISOString().split('T')[0] : false)
const setMaxDate = computed(() => props.disableFutureDates ? new Date().toISOString().split('T')[0] : false)

const onEnterPress = (event) => {
  event.preventDefault()
  return false
}

const setInputColor = () => {
  if (this.$refs.datepicker) {
    const dateInput = this.$refs.datepicker.$el.getElementsByTagName('input')[0]
    dateInput.style.setProperty('--tw-ring-color', this.color)
  }
}
const setValue = (val) => {
  if (Array.isArray(val)) {
    fromDate.value = dateToLocal(val) ?? null
    toDate.value = dateToLocal(val) ?? null
  } else {
    fromDate.value = dateToLocal(val)
  }
}

const dateToUTC = (val) => {
  if (!val) {
    return null
  }
  if (!this.useTime) {
    return val
  }
  return new Date(val).toISOString()
}
const dateToLocal = (val) => {
  if (!val) {
    return null
  }
  const dateObj = new Date(val)
  let dateStr = dateObj.getFullYear() + '-' +
    String(dateObj.getMonth() + 1).padStart(2, '0') + '-' +
    String(dateObj.getDate()).padStart(2, '0')
  if (this.useTime) {
    dateStr += 'T' + String(dateObj.getHours()).padStart(2, '0') + ':' +
      String(dateObj.getMinutes()).padStart(2, '0')
  }
  return dateStr
}

watch(() => color.value, () => {
  setInputColor()
})

watch(() => fromDate.value, (val) => {
  if (props.dateRange) {
    if (!Array.isArray(compVal.value)) {
      compVal.value = []
    }
    compVal.value[0] = dateToUTC(val)
  } else {
    compVal.value = dateToUTC(val)
  }
})

watch(() => toDate.value, (val) => {
  if (props.dateRange) {
    if (!Array.isArray(compVal.value)) {
      compVal.value = [null]
    }
    compVal.value[1] = dateToUTC(val)
  } else {
    compVal.value = null
  }
})
onMounted(() => {
  if (compVal.value) {
    setValue(compVal.value)
  }
  setInputColor()

})
</script>
