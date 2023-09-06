<template>
  <div :class="wrapperClass" :style="inputStyle">
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

<style scoped>
/deep/ .vue-tel-input input {
  background-color: v-bind(inputStyle.backgroundColor);
}
</style>