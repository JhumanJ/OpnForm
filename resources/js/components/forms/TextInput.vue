<template>
  <div :class="wrapperClass" :style="inputStyle">
    <slot name="label">
      <label v-if="label" :for="id?id:name"
             :class="[theme.default.label,{'uppercase text-xs':uppercaseLabels, 'text-sm':!uppercaseLabels}]"
      >
        {{ label }}
        <span v-if="required" class="text-red-500 required-dot">*</span>
      </label>
    </slot>
    <div v-if="help && helpPosition=='above_input'" class="flex mb-1">
      <small :class="theme.default.help" class="grow">
        <slot name="help"><span class="field-help" v-html="help" /></slot>
      </small>
    </div>
    <input :id="id?id:name" v-model="compVal" :disabled="disabled"
           :type="nativeType"
           :pattern="pattern"
           :style="inputStyle"
           :class="[theme.default.input,{ '!ring-red-500 !ring-2': hasValidation && form.errors.has(name), '!cursor-not-allowed !bg-gray-200':disabled }]"
           :name="name" :accept="accept"
           :placeholder="placeholder" :min="min" :max="max" :maxlength="maxCharLimit"
           @change="onChange" @keydown.enter.prevent="onEnterPress"
    >
    <div v-if="(help && helpPosition=='below_input') || showCharLimit" class="flex">
      <small v-if="help && helpPosition=='below_input'" :class="theme.default.help" class="flex-grow">
        <slot name="help"><span class="field-help" v-html="help" /></slot>
      </small>
      <small v-else class="flex-grow"></small>
      <small v-if="showCharLimit && maxCharLimit" :class="theme.default.help">
      {{ charCount }}/{{ maxCharLimit }}
      </small>
    </div>
    <has-error v-if="hasValidation" :form="form" :field="name" />
  </div>
</template>

<script>
import inputMixin from '~/mixins/forms/input.js'
export default {
  name: 'TextInput',

  mixins: [inputMixin],

  props: {
    nativeType: { type: String, default: 'text' },
    accept: { type: String, default: null },
    min: { type: Number, required: false, default: null },
    max: { type: Number, required: false, default: null },
    maxCharLimit: { type: Number, required: false, default: null },
    showCharLimit: { type: Boolean, required: false, default: false },
    pattern: { type: String, default: null }
  },

  data: () => ({}),

  computed: {
    compVal: {
      set (val) {
        if (this.form) {
          this.$set(this.form, this.nativeType !== 'file' ? this.name : 'file-' + this.name, val)
        } else {
          this.content = val
        }
        if (this.hasValidation) {
          this.form.errors.clear(this.name)
        }
        this.$emit('input', val)
      },
      get () {
        if (this.form) {
          return this.form[this.nativeType !== 'file' ? this.name : 'file-' + this.name]
        }
        return this.content
      }
    },
    charCount() {
      return (this.compVal) ? this.compVal.length : 0
    }
  },

  watch: {},

  created () {},

  methods: {
    onChange (event) {
      if (this.nativeType !== 'file') return

      const file = event.target.files[0]
      this.$set(this.form, this.name, file)
    },
    /**
     * Pressing enter won't submit form
     * @param event
     * @returns {boolean}
     */
    onEnterPress (event) {
      event.preventDefault()
      return false
    }
  }
}
</script>
