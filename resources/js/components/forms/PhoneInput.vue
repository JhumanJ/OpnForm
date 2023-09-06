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
    <div v-on-clickaway="closeDropdown" class="relative">
      <div :id="id ? id : name" :disabled="disabled" :name="name" class="flex">
        <div class="dropdown" :style="inputStyle">
          <div class="selected-option flex items-center justify-center space-x-2" @click="isOpen = !isOpen"
            :class="[theme.default.input, { '!ring-red-500 !ring-2': hasValidation && form.errors.has(name), '!cursor-not-allowed !bg-gray-200': disabled }]"
            :style="inputStyle">
            <country-flag :country="selectedCountryCode.code" />
            <span>{{ selectedCountryCode.dial_code }} </span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transform" :class="{ 'rotate-180': isOpen }"
              fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </div>
          <div class="options rounded-md bg-white dark:bg-notion-dark-light shadow-lg z-10" v-if="isOpen">
            <div
              class="option bg-white dark:bg-notion-dark-light z-10 flex items-center space-x-2 text-gray-900 cursor-default select-none relative py-2 pl-3 pr-9  cursor-pointer group hover:text-white hover:bg-nt-blue focus:outline-none focus:text-white focus:bg-nt-blue"
              v-for="country in countries" :key="country.code" @click="onCountryChange(country)" :style="inputStyle">
              <span><country-flag :country="country.code" /></span>
              <span>{{ country.name }}</span>
              <span>{{ country.code }}</span>
              <span>{{ country.dial_code }}</span>
            </div>
          </div>
        </div>
        <input type="number" class="phone" v-model="inputVal"
          :class="[theme.default.input, { '!ring-red-500 !ring-2': hasValidation && form.errors.has(name), '!cursor-not-allowed !bg-gray-200': disabled }]"
          :placeholder="placeholder" :style="inputStyle">
      </div>

    </div>
  </div>
</template>

<script>
import { directive as onClickaway } from 'vue-clickaway'
import inputMixin from '~/mixins/forms/input.js'
import countryCodes from '../../../data/country_codes.json'

export default {
  phone: 'PhoneInput',
  components: {},
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
      this.compVal = this.selectedCountryCode.dial_code + newVal;
    },
    selectedCountryCode(newVal, oldVal) {
      this.compVal = this.compVal.replace(oldVal.dial_code, newVal.dial_code);
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
  }
}
</script>

<style scoped>
.dropdown {
  position: relative;
  width: 25%;
}

.options {
  z-index: 10;
  position: absolute;
  width: 474px;
  max-height: 200px;
  overflow-y: auto;
}

.option {
  padding: 10px;
  cursor: pointer;
}

.selected-option {
  padding: 10px;
  cursor: pointer;
}

.phone {
  flex-grow: 1;
  margin-left: 10px;
  padding: 10px;
}

</style>