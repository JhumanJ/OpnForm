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
    />
    <small v-if="help" :class="theme.default.help">
      <slot name="help">{{ help }}</slot>
    </small>
    <has-error v-if="hasValidation" :form="form" :field="name" />
  </div>
</template>

<script>
import { fixedClasses } from '../../plugins/config/vue-tailwind/datePicker'
import inputMixin from '~/mixins/forms/input'

export default {
  name: 'DateInput',
  mixins: [inputMixin],

  props: {
    withTime: { type: Boolean, default: false },
    dateRange: { type: Boolean, default: false },
    amPm: { type: Boolean, default: false }
  },

  data: () => ({
    fixedClasses: fixedClasses
  }),

  computed: {
    useTime () {
      return this.withTime && !this.dateRange
    }
  },

  watch: {
    color: {
      handler () {
        this.setInputColor()
      },
      immediate: true
    }
  },

  mounted () {
    fixedClasses.input = this.theme.default.input
    this.setInputColor()
  },

  methods: {
    /**
     * Pressing enter won't submit form
     * @param event
     * @returns {boolean}
     */
    onEnterPress (event) {
      event.preventDefault()
      return false
    },
    setInputColor () {
      if (this.$refs.datepicker) {
        const dateInput = this.$refs.datepicker.$el.getElementsByTagName('input')[0]
        dateInput.style.setProperty('--tw-ring-color', this.color)
      }
    }
  }
}
</script>
