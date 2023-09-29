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
        <slot name="help"><span class="field-help" v-html="help"/></slot>
      </small>
    </div>
    <div :id="id ? id : name" :name="name" :style="inputStyle" class="flex items-center">
      <v-select class="w-[110px]" dropdown-class="w-[350px]" input-class="rounded-r-none" :data="countries"
                v-model="selectedCountryCode"
                :has-error="hasValidation && form.errors.has(name)"
                :disabled="disabled" :searchable="true" :search-keys="['name']" :option-key="'code'" :color="color"
                :placeholder="'Select a country'" :uppercase-labels="true" :theme="theme" @input="onChangeCountryCode">
        <template #option="props">
          <div class="flex items-center space-x-2 hover:text-white">
            <country-flag size="normal" class="!-mt-[9px]" :country="props.option.code"/>
            <span class="grow">{{ props.option.name }}</span>
            <span>{{ props.option.dial_code }}</span>
          </div>
        </template>
        <template #selected="props">
          <div class="flex items-center space-x-2 justify-center overflow-hidden">
            <country-flag size="normal" class="!-mt-[9px]" :country="props.option.code"/>
            <span>{{ props.option.dial_code }}</span>
          </div>
        </template>
      </v-select>
      <input v-model="inputVal" type="text" class="inline-flex-grow !border-l-0 !rounded-l-none" :disabled="disabled"
             :class="[theme.default.input, { '!ring-red-500 !ring-2': hasValidation && form.errors.has(name), '!cursor-not-allowed !bg-gray-200': disabled }]"
             :placeholder="placeholder" :style="inputStyle" @input="onInput">
    </div>
    <div v-if="help && helpPosition=='below_input'" class="flex">
      <small :class="theme.default.help" class="grow">
        <slot name="help"><span class="field-help" v-html="help"/></slot>
      </small>
    </div>
    <has-error v-if="hasValidation" :form="form" :field="name"/>
  </div>
</template>

<script>
import {directive as onClickaway} from 'vue-clickaway'
import inputMixin from '~/mixins/forms/input.js'
import countryCodes from '../../../data/country_codes.json'
import CountryFlag from 'vue-country-flag'
import parsePhoneNumber from 'libphonenumber-js'

export default {
  phone: 'PhoneInput',
  components: { CountryFlag },
  directives: {
    onClickaway: onClickaway
  },
  mixins: [inputMixin],
  props: {
    canOnlyCountry: { type: Boolean, default: false }
  },

  data () {
    return {
      selectedCountryCode: null,
      countries: countryCodes,
      inputVal: null
    }
  },

  watch: {
    inputVal: {
      handler (val) {
        if (val && val.startsWith('0')) {
          val = val.substring(1)
        }
        if (this.canOnlyCountry) {
          this.compVal = (val) ? this.selectedCountryCode.code + this.selectedCountryCode.dial_code + val : this.selectedCountryCode.code + this.selectedCountryCode.dial_code  
        } else {
          this.compVal = (val) ? this.selectedCountryCode.code + this.selectedCountryCode.dial_code + val : null
        }
      }
    },
    selectedCountryCode (newVal, oldVal) {
      if (this.compVal && newVal && oldVal) {
        this.compVal = this.compVal.replace(oldVal.code + oldVal.dial_code, newVal.code + newVal.dial_code)
      }
    }
  },

  mounted () {
    if (this.compVal) {
      if(!this.compVal.startsWith('+')){
        this.selectedCountryCode = this.getCountryBy(this.compVal.substring(2, 0))
      }

      const phoneObj = parsePhoneNumber(this.compVal.startsWith('+') ? this.compVal : this.compVal.substring(2, 0))
      if (phoneObj !== undefined && phoneObj) {
        if (!this.selectedCountryCode && phoneObj.country !== undefined && phoneObj.country) {
          this.selectedCountryCode = this.getCountryBy(phoneObj.country)
        }
        this.inputVal = phoneObj.nationalNumber
      }
    }
    if(!this.selectedCountryCode){
      this.selectedCountryCode = this.getCountryBy()
    }
  },
  methods: {
    getCountryBy (code = 'US', type = 'code') {
      if(!code) code = 'US' // Default US
      return countryCodes.find((item) => {
        return item[type] === code
      })
    },
    onInput (event) {
      this.inputVal = event.target.value.replace(/[^0-9]/g, '')
    },
    onChangeCountryCode () {
      if (this.canOnlyCountry && (this.inputVal === null || this.inputVal === '' || !this.inputVal)) {
        this.compVal = this.selectedCountryCode.code + this.selectedCountryCode.dial_code
      }
    }
  }
}
</script>
