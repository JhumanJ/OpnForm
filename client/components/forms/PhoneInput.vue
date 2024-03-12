<template>
  <input-wrapper
    v-bind="inputWrapperProps"
  >
    <template #label>
      <slot name="label"/>
    </template>

    <div :id="id ? id : name" :name="name" :style="inputStyle" class="flex items-start">
      <v-select v-model="selectedCountryCode" class="w-[130px]" dropdown-class="w-[300px]" input-class="rounded-r-none"
                :data="countries"
                :disabled="(disabled || countries.length===1)?true:null" :searchable="true" :search-keys="['name']"
                :option-key="'code'" :color="color"
                :has-error="hasError"
                :placeholder="'Select a country'" :uppercase-labels="true" :theme="theme"
                @update:model-value="onChangeCountryCode"
      >
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
      <input v-model="inputVal" type="text" class="inline-flex-grow !border-l-0 !rounded-l-none"
             :disabled="disabled?true:null"
             :class="[theme.default.input, { '!ring-red-500 !ring-2': hasError, '!cursor-not-allowed !bg-gray-200': disabled }]"
             :placeholder="placeholder" :style="inputStyle" @input="onInput"
      >
    </div>

    <template #help>
      <slot name="help"/>
    </template>

    <template #error>
      <slot name="error"/>
    </template>
  </input-wrapper>
</template>

<script>
import {inputProps, useFormInput} from './useFormInput.js'
import InputWrapper from './components/InputWrapper.vue'
import countryCodes from '~/data/country_codes.json'
import CountryFlag from 'vue-country-flag-next'
import parsePhoneNumber from 'libphonenumber-js'

export default {
  phone: 'PhoneInput',
  components: {InputWrapper, CountryFlag},
  props: {
    ...inputProps,
    canOnlyCountry: {type: Boolean, default: false},
    unavailableCountries: {type: Array, default: () => []}
  },

  setup(props, context) {
    return {
      ...useFormInput(props, context)
    }
  },

  data() {
    return {
      selectedCountryCode: null,
      inputVal: null
    }
  },

  computed: {
    countries() {
      return countryCodes.filter((item) => {
        return !this.unavailableCountries.includes(item.code)
      })
    }
  },

  watch: {
    inputVal: {
      handler(val) {
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
    compVal(newVal, oldVal) {
      this.initState()
    },
    selectedCountryCode(newVal, oldVal) {
      if (this.compVal && newVal && oldVal) {
        this.compVal = this.compVal.replace(oldVal.code + oldVal.dial_code, newVal.code + newVal.dial_code)
      }
    }
  },

  mounted() {
    if (this.compVal) {
      this.initState()
    }
    if (!this.selectedCountryCode) {
      this.selectedCountryCode = this.getCountryBy()
    }
    if (!this.selectedCountryCode || this.countries.length === 1) {
      this.selectedCountryCode = this.countries[0]
    }
  },

  methods: {
    getCountryBy(code = 'US', type = 'code') {
      if (!code) code = 'US' // Default US
      return this.countries.find((item) => {
        return item[type] === code
      }) ?? null
    },
    onInput(event) {
      this.inputVal = event?.target?.value.replace(/[^0-9]/g, '')

    },
    onChangeCountryCode() {
      if (!this.selectedCountryCode && this.countries.length > 0) {
        this.selectedCountryCode = this.countries[0]
      }
      if (this.canOnlyCountry && (this.inputVal === null || this.inputVal === '' || !this.inputVal)) {
        this.compVal = this.selectedCountryCode.code + this.selectedCountryCode.dial_code
      }
    },
    initState() {
      if (this.compVal === null) {
        return;
      }
      if (!this.compVal?.startsWith('+')) {
        this.selectedCountryCode = this.getCountryBy(this.compVal.substring(2, 0))
      }

      const phoneObj = parsePhoneNumber(this.compVal)
      if (phoneObj !== undefined && phoneObj) {
        if (!this.selectedCountryCode && phoneObj.country !== undefined && phoneObj.country) {
          this.selectedCountryCode = this.getCountryBy(phoneObj.country)
        }
        this.inputVal = phoneObj.nationalNumber
      }
    }
  }
}
</script>
