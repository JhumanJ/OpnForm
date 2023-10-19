<template>
  <div :class="wrapperClass" :style="inputStyle">
    <slot name="label">
      <input-label v-if="label"
                   :label="label"
                   :theme="theme"
                   :required="true"
                   :native-for="id?id:name"
                   :uppercase-labels="uppercaseLabels"
      />
    </slot>
    <input-help v-if="help && helpPosition=='above_input'" :help="help" :theme="theme">
      <template #help>
        <slot name="help" />
      </template>
    </input-help>
    <input :id="id?id:name" v-model="compVal" :disabled="disabled"
           :type="nativeType"
           :pattern="pattern"
           :style="inputStyle"
           :class="[theme.default.input, { '!ring-red-500 !ring-2': hasError, '!cursor-not-allowed !bg-gray-200': disabled }]"
           :name="name" :accept="accept"
           :placeholder="placeholder" :min="min" :max="max" :maxlength="maxCharLimit"
           @change="onChange" @keydown.enter.prevent="onEnterPress"
    >
    <!--    <input-help v-if="(help && helpPosition=='below_input') || showCharLimit" :help="help" :theme="theme">-->
    <!--      <template v-if="showCharLimit" #after-help>-->
    <!--        <small v-if="showCharLimit && maxCharLimit" :class="theme.default.help">-->
    <!--          {{ charCount }}/{{ maxCharLimit }}-->
    <!--        </small>-->
    <!--      </template>-->
    <!--    </input-help>-->
    <has-error v-if="hasValidation" :form="form" :field="name" />
  </div>
</template>

<script>
import { inputProps, useFormInput } from './useFormInput.js'
import InputLabel from './components/InputLabel.vue'
import InputHelp from './components/InputHelp.vue'

export default {
  name: 'TextInput',
  components: { InputHelp, InputLabel },

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

  setup (props) {
    const { compVal, inputStyle, hasValidation, hasError } = useFormInput(props)

    const onChange = (event) => {
      console.log(props)
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
  }
}
</script>
