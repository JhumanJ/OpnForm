<template>
  <div :class="wrapperClass">
    <label v-if="label" :for="id ? id : name"
      :class="[theme.default.label, { 'uppercase text-xs': uppercaseLabels, 'text-sm': !uppercaseLabels }]">
      {{ label }}
      <span v-if="required" class="text-red-500 required-dot">*</span>
    </label>
    <div v-if="help && helpPosition == 'above_input'" class="flex mb-1">
      <small :class="theme.default.help" class="flex-grow">
        <slot name="help"><span class="field-help" v-html="help" /></slot>
      </small>
    </div>
    <div>
      <vue-tel-input :id="id ? id : name" :disabled="disabled"
        :class="[theme.default.input, { '!ring-red-500 !ring-2': hasValidation && form.errors.has(name), '!cursor-not-allowed !bg-gray-200': disabled }]"
        :name="name" :style="inputStyle" :placeholder="placeholder" v-model="compVal"
        v-bind="bindProps"></vue-tel-input>
    </div>
    <has-error v-if="hasValidation" :form="form" :field="name" />
  </div>
</template>

<script>
import inputMixin from '~/mixins/forms/input.js';

export default {
  phone: 'PhoneInput',
  mixins: [inputMixin],

  props: {
    nativeType: { type: String, default: 'tel' },
  },

  data() {
    return {
      phone: "",
      bindProps: {
        mode: "international",
        defaultCountry: "US",
        disabledFetchingCountry: false,
        disabledFormatting: false,
        placeholder: "Enter a phone number",
        enabledCountryCode: false,
        enabledFlags: true,
        autocomplete: "off",
        name: "telephone",
        maxLen: 25,
        wrapperClasses: "",
        inputClasses: 'border border-gray-300 dark:bg-notion-dark-light dark:border-gray-600 dark:placeholder-gray-500 dark:text-gray-300 flex-1 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-opacity-100 placeholder-gray-400 px-4 py-2 rounded-lg shadow-sm text-base text-black text-gray-700' + (this.disabled ? ' !cursor-not-allowed !bg-gray-200' : ''),
        inputOptions: {
          showDialCode: true
        },
        validCharactersOnly: true,
      }
    };
  },


  methods: { },
};
</script>
