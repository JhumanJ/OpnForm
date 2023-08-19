<template>
  <div :class="wrapperClass">
    <label v-if="label" :for="id ? id : name" :class="labelClasses">
      {{ label }}
      <span v-if="required" class="text-red-500 required-dot">*</span>
    </label>
    <small v-if="help && helpPosition === 'above_input'" :class="theme.default.help" class="flex mb-1">
      <slot name="help"><span class="field-help" v-html="help" /></slot>
    </small>

    <div class="flex">
      <v-select
        v-model="selectedCountry"
        :options="countries"
        label="name"
        :clearable="false"
        @input="handleCountryChange"
      ></v-select>
      <input
        type="tel"
        v-model="phoneNumber"
        :class="inputClasses"
        :style="inputStyle"
        :name="name"
      />
    </div>

    <small v-if="help && helpPosition === 'below_input'" :class="theme.default.help">
      <slot name="help"><span class="field-help" v-html="help" /></slot>
    </small>
    <has-error v-if="hasValidation" :form="form" :field="name" />
  </div>
</template>

<script>
import { fixedClasses } from '../../plugins/config/vue-tailwind/phoneInput.js';
import inputMixin from '~/mixins/forms/input.js';
import vSelect from './VSelect.vue'; 

export default {
  name: 'PhoneInput',
  mixins: [inputMixin],
  components: {
    vSelect,
  },

  data() {
    return {
      selectedCountry: null,
      phoneNumber: '',
    };
  },

  computed: {
    countries() {
      // Load countries from resources/data or your data source
      return require('../resources/data/countries.json');
    },
    inputClasses() {
      let classes = 'border border-gray-300 dark:bg-notion-dark-light dark:border-gray-600 dark:placeholder-gray-500 dark:text-gray-300 flex-1 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-opacity-100 placeholder-gray-400 px-4 py-2 rounded-lg shadow-sm text-base text-black text-gray-700';
      classes += this.disabled ? ' !cursor-not-allowed !bg-gray-200' : '';
      return classes;
    },
  },

  methods: {
    handleCountryChange(country) {
      this.selectedCountry = country;
      if (country.clashPattern) {
        this.phoneNumber = this.phoneNumber.replace(country.clashPattern, '');
      }
    },
  },
};
</script>

<style scoped>
.wrapper-class {
  // Add wrapper styles if needed
}

.label-classes {
  // Add label styles if needed
}

.input-classes {
  border: 1px solid #ccc;
  background-color: #f7f7f7;
  // Add other input styles
}

.input-style {
  // Add input style if needed
}

/* Adjust styles based on your needs */
</style>
