<template>
  <input-wrapper
    v-bind="$props"
  >
    <template #label>
      <slot name="label" />
    </template>
    <template v-if="helpPosition==='above_input'" #help>
      <slot name="help" />
    </template>

    <input :id="id?id:name" v-model="compVal" :disabled="disabled"
           :type="nativeType"
           :pattern="pattern"
           :style="inputStyle"
           :class="[theme.default.input, { '!ring-red-500 !ring-2': hasError, '!cursor-not-allowed !bg-gray-200': disabled }]"
           :name="name" :accept="accept"
           :placeholder="placeholder" :min="min" :max="max" :maxlength="maxCharLimit"
           @change="onChange" @keydown.enter.prevent="onEnterPress"
    >

    <!--  TODO: fix this in the case of below input there's something off  -->
    <!--    <input-help v-if="helpPosition==='below_input' || showCharLimit" :help="help" :theme="theme">-->
    <!--      <template #help>-->
    <!--        <slot name="help" />-->
    <!--      </template>-->
    <!--      <template v-if="showCharLimit" #after-help>-->
    <!--        <small v-if="showCharLimit && maxCharLimit" :class="theme.default.help">-->
    <!--          {{ charCount }}/{{ maxCharLimit }}-->
    <!--        </small>-->
    <!--      </template>-->
    <!--    </input-help>-->

    <template #error>
      <slot name="error" />
    </template>
  </input-wrapper>
</template>

<script>
import { inputProps, useFormInput } from './useFormInput.js'
import InputLabel from './components/InputLabel.vue'
import InputHelp from './components/InputHelp.vue'
import InputWrapper from './components/InputWrapper.vue'

export default {
  name: 'TextInput',
  components: { InputWrapper, InputHelp, InputLabel },

  props: {
    ...inputProps,
    nativeType: { type: String, default: 'text' },
    accept: { type: String, default: null },
    min: { type: Number, required: false, default: null },
    max: { type: Number, required: false, default: null },
    maxCharLimit: { type: Number, required: false, default: null },
    showCharLimit: { type: Boolean, required: false, default: false },
    pattern: { type: String, default: null }
  },

  setup (props, context) {
    const {
      compVal,
      inputStyle,
      hasValidation,
      hasError
    } = useFormInput(props, context, props.nativeType === 'file' ? 'file-' : null)

    const onChange = (event) => {
      if (props.nativeType !== 'file') return

      const file = event.target.files[0]
      // eslint-disable-next-line vue/no-mutating-props
      props.form[props.name] = file
    }

    const onEnterPress = (event) => {
      event.preventDefault()
      return false
    }

    return {
      compVal,
      inputStyle,
      hasValidation,
      hasError
    }
  },
  computed: {
    charCount () {
      return (this.compVal) ? this.compVal.length : 0
    }
  }
}
</script>
