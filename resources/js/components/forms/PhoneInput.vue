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
        :class="inputClasses"
        :style="inputStyle"
      ></v-select>
      <input
        type="tel"
        v-model="phoneNumber"
        :name="name"
        :class="inputClasses"
        :style="inputStyle"
      />
    </div>

    <small v-if="help && helpPosition === 'below_input'" :class="theme.default.help">
      <slot name="help"><span class="field-help" v-html="help" /></slot>
    </small>
    <has-error v-if="hasValidation" :form="form" :field="name" />
  </div>
</template>

<script>
import inputMixin from '~/mixins/forms/input.js'
import vSelect from './components/VSelect.vue'
import countries from '@/data/countries.json'

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
      return countries;
    },
    inputClasses() {
      return 'border border-gray-300 dark:bg-notion-dark-light dark:border-gray-600 dark:placeholder-gray-500 dark:text-gray-300 flex-1 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-opacity-100 placeholder-gray-400 px-4 py-2 rounded-lg shadow-sm text-base text-black text-gray-700' + (this.disabled ? ' !cursor-not-allowed !bg-gray-200' : '');
    },
    wrapperClass() {
      return 'border-1 border-gray-300 bg-gray-200 px-4 py-2 rounded-lg shadow-sm';
    },
    labelClasses() {
      return 'font-semibold';
    },
    inputStyle() {
      return 'input-style'; 
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
  border: 1px solid #ccc;
  background-color: #f7f7f7;
}
.label-class {
  border: 1px solid #ccc;
  background-color: #f7f7f7;
}
.input-style {
  border: 1px solid #ccc;
  background-color: #f7f7f7;
}
.input-classes {
  border: 1px solid #ccc;
  background-color: #f7f7f7;
}

</style>
