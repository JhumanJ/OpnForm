<template>
  <div :class="wrapperClass">
    <label v-if="label" :for="id?id:name"
           :class="[theme.default.label,{'uppercase text-xs':uppercaseLabels, 'text-sm':!uppercaseLabels}]"
    >
      {{ label }}
      <span v-if="required" class="text-red-500 required-dot">*</span>
    </label>
    <t-datepicker :id="id?id:name" ref="datepicker" v-model="compVal" class="datepicker" :disabled="disabled"
                  :class="{ 'ring-red-500 ring-2': hasValidation && form.errors.has(name), 'cursor-not-allowed bg-gray-200':disabled }"
                  :style="inputStyle" :name="name" :fixed-classes="fixedClasses" :range="dateRange"
                  :placeholder="placeholder" :timepicker="useTime"
                  :date-format="useTime?'Z':'Y-m-d'"
                  :user-format="useTime ? amPm ? 'F j, Y - G:i K' : 'F j, Y - H:i' : 'F j, Y'"
                  :amPm="amPm"
                  :disabled-dates="disabledDates"
    />
    <small v-if="help" :class="theme.default.help">
      <slot name="help">{{ help }}</slot>
    </small>
    <has-error v-if="hasValidation" :form="form" :field="name"/>
  </div>
</template>

<script>
import {fixedClasses} from '../../plugins/config/vue-tailwind/datePicker'
import inputMixin from '~/mixins/forms/input'

import VueTailwind from 'vue-tailwind'
import TDatepicker from 'vue-tailwind/dist/t-datepicker'
import Vue from 'vue'

const settings = {
  't-datepicker': {
    component: TDatepicker,
    props: {
      classes: {
        dropdown: 'origin-top-left absolute rounded shadow bg-white dark:bg-notion-dark dark:border-gray-100 border overflow-hidden mt-1',
        wrapper: 'flex flex-col',
        dropdownWrapper: 'relative z-10',
        enterClass: 'opacity-0 scale-95',
        enterActiveClass: 'transition transform ease-out duration-100',
        enterToClass: 'opacity-100 scale-100',
        leaveClass: 'opacity-100 scale-100',
        leaveActiveClass: 'transition transform ease-in duration-75',
        leaveToClass: 'opacity-0 scale-95',
        inlineWrapper: '',
        inlineViews: 'rounded bg-white border mt-1 inline-flex',
        inputWrapper: '',
        input: 'text-black placeholder-gray-400 border-gray-300',
        clearButton: 'hover:bg-gray-100 rounded transition duration-100 ease-in-out text-gray-600',
        clearButtonIcon: '',
        viewGroup: '',
        view: '',
        navigator: 'pt-2 px-3',
        navigatorViewButton: 'transition ease-in-out duration-100 inline-flex cursor-pointer rounded-full px-2 py-1 -ml-1 hover:bg-gray-100',
        navigatorViewButtonIcon: 'fill-current text-gray-400',
        navigatorViewButtonBackIcon: 'fill-current text-gray-400',
        navigatorViewButtonMonth: 'text-gray-700 font-semibold',
        navigatorViewButtonYear: 'text-gray-500 ml-1',
        navigatorViewButtonYearRange: 'text-gray-500 ml-1',
        navigatorLabel: 'py-1',
        navigatorLabelMonth: 'text-gray-700 font-semibold',
        navigatorLabelYear: 'text-gray-500 ml-1',
        navigatorPrevButton: 'transition ease-in-out duration-100 inline-flex cursor-pointer hover:bg-gray-100 rounded-full p-1 ml-2 ml-auto disabled:opacity-50 disabled:cursor-not-allowed',
        navigatorNextButton: 'transition ease-in-out duration-100 inline-flex cursor-pointer hover:bg-gray-100 rounded-full p-1 -mr-1 disabled:opacity-50 disabled:cursor-not-allowed',
        navigatorPrevButtonIcon: 'text-gray-400',
        navigatorNextButtonIcon: 'text-gray-400',
        calendarWrapper: 'px-3 pt-2',
        calendarHeaderWrapper: '',
        calendarHeaderWeekDay: 'uppercase text-xs text-gray-500 w-8 h-8 flex items-center justify-center',
        calendarDaysWrapper: '',
        calendarDaysDayWrapper: 'w-full h-8 flex flex-shrink-0 items-center',
        otherMonthDay: 'text-sm rounded-full w-8 h-8 mx-auto hover:bg-blue-100 text-gray-400 disabled:opacity-50 disabled:cursor-not-allowed',
        emptyDay: '',
        inRangeFirstDay: 'text-sm bg-blue-500 text-white w-full h-8 rounded-l-full',
        inRangeLastDay: 'text-sm bg-blue-500 text-white w-full h-8 rounded-r-full',
        inRangeDay: 'text-sm bg-blue-200 w-full h-8 disabled:opacity-50 disabled:cursor-not-allowed',
        selectedDay: 'text-sm rounded-full w-8 h-8 mx-auto bg-blue-500 text-white disabled:opacity-50 disabled:cursor-not-allowed',
        activeDay: 'text-sm rounded-full bg-blue-100 w-8 h-8 mx-auto disabled:opacity-50 disabled:cursor-not-allowed',
        highlightedDay: 'text-sm rounded-full bg-blue-200 w-8 h-8 mx-auto disabled:opacity-50 disabled:cursor-not-allowed',
        day: 'text-sm rounded-full w-8 h-8 mx-auto hover:bg-blue-100 disabled:opacity-50 disabled:cursor-not-allowed',
        today: 'text-sm rounded-full w-8 h-8 mx-auto hover:bg-blue-100 disabled:opacity-50 disabled:cursor-not-allowed border border-blue-500',
        monthWrapper: 'px-3 pt-2',
        selectedMonth: 'text-sm rounded w-full h-12 mx-auto bg-blue-500 text-white',
        activeMonth: 'text-sm rounded w-full h-12 mx-auto bg-blue-100',
        month: 'text-sm rounded w-full h-12 mx-auto hover:bg-blue-100',
        yearWrapper: 'px-3 pt-2',
        year: 'text-sm rounded w-full h-12 mx-auto hover:bg-blue-100',
        selectedYear: 'text-sm rounded w-full h-12 mx-auto bg-blue-500 text-white',
        activeYear: 'text-sm rounded w-full h-12 mx-auto bg-blue-100',
        timepickerWrapper: 'flex items-center px-4 py-2 space-x-2',
        timepickerTimeWrapper: 'flex items-center space-x-2',
        timepickerTimeFieldsWrapper: 'bg-gray-100 dark:bg-notion-dark-light rounded-md w-full text-right flex items-center border border-gray-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none focus:ring-opacity-50',
        timepickerOkButton: 'text-blue-600 text-sm uppercase font-semibold transition duration-100 ease-in-out border border-transparent focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none focus:ring-opacity-50 rounded cursor-pointer',
        timepickerInput: 'text-center w-8 border-transparent bg-transparent dark:bg-notion-dark-light p-0 h-6 text-sm transition duration-100 ease-in-out border border-transparent focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none focus:ring-opacity-50 rounded',
        timepickerTimeLabel: 'flex-grow text-sm text-gray-500',
        timepickerAmPmWrapper: 'relative inline-flex flex-shrink-0 transition duration-200 ease-in-out bg-gray-100 dark:bg-notion-dark-light border border-transparent rounded cursor-pointer focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none focus:ring-opacity-50',
        timepickerAmPmWrapperChecked: 'relative inline-flex flex-shrink-0 transition duration-200 ease-in-out bg-gray-100 dark:bg-notion-dark-light border border-transparent rounded cursor-pointer focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none focus:ring-opacity-50',
        timepickerAmPmWrapperDisabled: 'relative inline-flex flex-shrink-0 transition duration-200 ease-in-out opacity-50 cursor-not-allowed',
        timepickerAmPmWrapperCheckedDisabled: 'relative inline-flex flex-shrink-0 transition duration-200 ease-in-out opacity-50 cursor-not-allowed',
        timepickerAmPmButton: 'absolute flex items-center justify-center w-6 h-6 text-xs text-gray-800 transition duration-200 ease-in-out transform translate-x-0 bg-white rounded shadow',
        timepickerAmPmButtonChecked: 'absolute flex items-center justify-center w-6 h-6 text-xs text-gray-800 transition duration-200 ease-in-out transform translate-x-full bg-white rounded shadow',
        timepickerAmPmCheckedPlaceholder: 'flex items-center justify-center w-6 h-6 text-xs text-gray-500 rounded-sm',
        timepickerAmPmUncheckedPlaceholder: 'flex items-center justify-center w-6 h-6 text-xs text-gray-500 rounded-sm'
      }
    }
  }
}
Vue.use(VueTailwind, settings)

export default {
  name: 'DateInput',
  mixins: [inputMixin],

  props: {
    withTime: {type: Boolean, default: false},
    dateRange: {type: Boolean, default: false},
    amPm: {type: Boolean, default: false},
    disablePastDates: {type: Boolean, default: false},
    disableFutureDates: {type: Boolean, default: false}
  },

  data: () => ({
    fixedClasses: fixedClasses
  }),

  computed: {
    useTime() {
      return this.withTime && !this.dateRange
    }
  },

  watch: {
    color: {
      handler() {
        this.setInputColor()
      },
      immediate: true
    }
  },

  mounted() {
    fixedClasses.input = this.theme.default.input
    this.setInputColor()
  },

  methods: {
    /**
     * Pressing enter won't submit form
     * @param event
     * @returns {boolean}
     */
    onEnterPress(event) {
      event.preventDefault()
      return false
    },
    setInputColor() {
      if (this.$refs.datepicker) {
        const dateInput = this.$refs.datepicker.$el.getElementsByTagName('input')[0]
        dateInput.style.setProperty('--tw-ring-color', this.color)
      }
    },
    disabledDates(date) {
      const today = new Date()
      if (this.disablePastDates) {
        return new Date(date.getFullYear(), date.getMonth(), date.getDate()) < new Date(today.getFullYear(), today.getMonth(), today.getDate())
      } else if (this.disableFutureDates) {
        return new Date(date.getFullYear(), date.getMonth(), date.getDate()) > new Date(today.getFullYear(), today.getMonth(), today.getDate())
      }
      return false;
    }
  }
}
</script>
