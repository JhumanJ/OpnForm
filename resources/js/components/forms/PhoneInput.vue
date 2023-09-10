<template>
  <div :class="wrapperClass" :style="inputStyle">
    <slot name="label">
      <label v-if="label" :for="id ? id : name"
        :class="[theme.default.label, { 'uppercase text-xs': uppercaseLabels, 'text-sm': !uppercaseLabels }]">
        {{ label }}
        <span v-if="required" class="text-red-500 required-dot">*</span>
      </label>
    </slot>
    <div v-if="help && helpPosition == 'above_input'" class="flex mb-1">
      <small :class="theme.default.help" class="grow">
        <slot name="help"><span class="field-help" v-html="help" /></slot>
      </small>
    </div>
    <div :id="id ? id : name" :disabled="disabled" :name="name" :style="inputStyle" class="flex items-center">
      <v-select class="w-1/4" :data="countries" :value="selectedCountryCode" :inner-style="{ width: '474px' }"
        :searchable="true" :search-keys="['name']" :option-key="'code'" :color="'#3B82F6'"
        :placeholder="'Select a country'" :uppercase-labels="true" :theme="theme" @input="onCountryChange">
        <template #option="props">
          <div class="flex items-center space-x-2">
            <country-flag :country="props.option.code" />
            <span>{{ props.option.name }}</span>
            <span>{{ props.option.code }}</span>
            <span>{{ props.option.dial_code }}</span>
          </div>
        </template>
        <template #selected="props">
          <div class="flex items-center space-x-2 justify-start">
            <country-flag :country="props.option.code" :style="{
              'margin-top': '-0.7em',
              'margin-left': '-0.9em',
              'margin-right': '-0.6em',
              'margin-bottom': '-0.7em'
            }" />
            <span>{{ props.option.dial_code }}</span>
          </div>
        </template>
      </v-select>
      <input v-model="inputVal" type="text" class="inline-flex-grow ml-5"
        :class="[theme.default.input, { '!ring-red-500 !ring-2': hasValidation && form.errors.has(name), '!cursor-not-allowed !bg-gray-200': disabled }]"
        :placeholder="placeholder" :style="inputStyle" @input="onInput">
    </div>
  </div>
</template>

<script>
import { directive as onClickaway } from 'vue-clickaway'
import inputMixin from '~/mixins/forms/input.js'
import countryCodes from '../../../data/country_codes.json'
import CountryFlag from 'vue-country-flag'
import VSelect from './components/VSelect.vue'

export default {
  phone: 'PhoneInput',
  components: {
    CountryFlag, VSelect
  },
  directives: {
    onClickaway: onClickaway
  },
  mixins: [inputMixin],

  data() {
    return {
      selectedCountryCode: countryCodes[234],
      countries: countryCodes,
      isOpen: false,
      inputVal: ''
    }
  },
  watch: {
    inputVal(newVal, oldVal) {
      if (newVal.startsWith('0')) {
        newVal = newVal.replace(/^0+/, '')
      }
      this.compVal = this.selectedCountryCode.dial_code + ' ' + newVal
    },
    selectedCountryCode(newVal, oldVal) {
      if (this.compVal) {
        this.compVal = this.compVal.replace(oldVal.dial_code, newVal.dial_code)
      }
    }
  },
  methods: {
    onCountryChange(country) {
      this.selectedCountryCode = country
      this.closeDropdown()
    },
    closeDropdown() {
      this.isOpen = false
    },
    onInput(event) {
      const input = event.target.value
      const digitsOnly = input.replace(/[^0-9]/g, '')
      this.inputVal = digitsOnly
    },
  }
}
</script>

<style scoped></style>