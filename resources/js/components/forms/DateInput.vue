<template>
  <div :class="wrapperClass">
    <label v-if="label" :for="id?id:name"
           :class="[theme.default.label,{'uppercase text-xs':uppercaseLabels, 'text-sm':!uppercaseLabels}]"
    >
      {{ label }}
      <span v-if="required" class="text-red-500 required-dot">*</span>
    </label>
    <small v-if="help && helpPosition=='above_input'" :class="theme.default.help" class="flex mb-1">
      <slot name="help"><span class="field-help" v-html="help" /></slot>
    </small>

    <div class="flex" v-if="!dateRange">
      <input :type="useTime ? 'datetime-local' : 'date'" :id="id?id:name" v-model="fromDate" :class="inputClasses" :disabled="disabled"
          :style="inputStyle" :name="name" data-date-format="YYYY-MM-DD"
          :min="setMinDate" :max="setMaxDate"
      />
    </div>
    <div :class="inputClasses" v-else>
      <div class="flex -mx-2">
      <p class="text-gray-900 px-4">From</p>
      <input :type="useTime ? 'datetime-local' : 'date'" :id="id?id:name" v-model="fromDate" :disabled="disabled"
             :style="inputStyle" :name="name" data-date-format="YYYY-MM-DD" class="flex-grow border-transparent focus:outline-none "
             :min="setMinDate" :max="setMaxDate"
      />
      <p class="text-gray-900 px-4">To</p>
      <input v-if="dateRange" :type="useTime ? 'datetime-local' : 'date'" :id="id?id:name" v-model="toDate" :disabled="disabled"
             :style="inputStyle" :name="name" class="flex-grow border-transparent focus:outline-none"
             :min="setMinDate" :max="setMaxDate"
      />
      </div>
    </div>

    <small v-if="help && helpPosition=='below_input'" :class="theme.default.help">
      <slot name="help"><span class="field-help" v-html="help" /></slot>
    </small>
    <has-error v-if="hasValidation" :form="form" :field="name"/>
  </div>
</template>

<script>
import {fixedClasses} from '../../plugins/config/vue-tailwind/datePicker.js'
import inputMixin from '~/mixins/forms/input.js'

export default {
  name: 'DateInput',
  mixins: [inputMixin],

  props: {
    withTime: {type: Boolean, default: false},
    dateRange: {type: Boolean, default: false},
    disablePastDates: {type: Boolean, default: false},
    disableFutureDates: {type: Boolean, default: false}
  },

  data: () => ({
    fixedClasses: fixedClasses,
    fromDate: null,
    toDate: null
  }),

  computed: {
    inputClasses (){
      let str = 'border border-gray-300 dark:bg-notion-dark-light dark:border-gray-600 dark:placeholder-gray-500 dark:text-gray-300 flex-1 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-opacity-100 placeholder-gray-400 px-4 py-2 rounded-lg shadow-sm text-base text-black text-gray-700'
      str += this.dateRange ? ' w-50' : ' w-full'
      str += this.disabled ? ' !cursor-not-allowed !bg-gray-200' : ''
      return str
    },
    useTime() {
      return this.withTime && !this.dateRange
    },
    setMinDate () {
      if (this.disablePastDates) {
        return new Date().toISOString().split('T')[0]
      }
      return false
    },
    setMaxDate () {
      if (this.disableFutureDates) {
        return new Date().toISOString().split('T')[0]
      }
      return false
    }
  },

  watch: {
    color: {
      handler() {
        this.setInputColor()
      },
      immediate: true
    },
    fromDate: {
      handler(val) {
        if(this.dateRange){
          if(!Array.isArray(this.compVal)){
            this.compVal = [];
          }
          this.compVal[0] = this.dateToUTC(val)
        }else{
          this.compVal = this.dateToUTC(val)
        }
      },
      immediate: false
    },
    toDate: {
      handler(val) {
        if(this.dateRange){
          if(!Array.isArray(this.compVal)){
            this.compVal = [null];
          }
          this.compVal[1] = this.dateToUTC(val)
        }else{
          this.compVal = null
        }
      },
      immediate: false
    }
  },

  mounted() {
    if(this.compVal){
      if(Array.isArray(this.compVal)){
        this.fromDate = this.compVal[0] ?? null
        this.toDate = this.compVal[1] ?? null
      }else{
        this.fromDate = this.dateToLocal(this.compVal)
      }
    }

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
    dateToUTC(val){
      if(!val){
        return null
      }
      if(!this.useTime){
        return val
      }
      return new Date(val).toISOString()
    },
    dateToLocal(val){
      if(!val){
        return null
      }
      const dateObj = new Date(val)
      let dateStr = dateObj.getFullYear() + '-' +
                  String(dateObj.getMonth() + 1).padStart(2, '0') + '-' +
                  String(dateObj.getDate()).padStart(2, '0')
      if(this.useTime){
        dateStr += 'T' + String(dateObj.getHours()).padStart(2, '0') + ':' +
        String(dateObj.getMinutes()).padStart(2, '0');
      }
      return dateStr
    }
  }
}
</script>
