<template>
  <input-wrapper
    v-bind="inputWrapperProps"
  >
    <template #label>
      <slot name="label" />
    </template>

    <div class="rectangle-outer grid grid-cols-5 gap-2">
      <div v-for="i in scaleList" :key="i"
           :class="[{'font-semibold':compVal===i},theme.ScaleInput.button, compVal!==i ? unselectedButtonClass: '']"
           :style="btnStyle(i===compVal)"
           role="button" @click="setScale(i)"
      >
        {{ i }}
      </div>
    </div>

    <template #help>
      <slot name="help" />
    </template>
    <template #error>
      <slot name="error" />
    </template>
  </input-wrapper>
</template>

<script>
import { inputProps, useFormInput } from './useFormInput.js'
import InputWrapper from './components/InputWrapper.vue'

export default {
  name: 'ScaleInput',
  components: { InputWrapper },

  props: {
    ...inputProps,
    minScale: { type: Number, default: 1 },
    maxScale: { type: Number, default: 5 },
    stepScale: { type: Number, default: 1 }
  },

  setup (props, context) {
    return {
      ...useFormInput(props, context)
    }
  },

  data () {
    return {}
  },

  computed: {
    scaleList () {
      const list = []
      for (let i = this.minScale; i <= this.maxScale; i += this.stepScale) {
        list.push(i)
      }
      return list
    },
    unselectedButtonClass () {
      return this.theme.ScaleInput.unselectedButton
    },
    textColor () {
      const color = (this.color.charAt(0) === '#') ? this.color.substring(1, 7) : this.color
      const r = parseInt(color.substring(0, 2), 16) // hexToR
      const g = parseInt(color.substring(2, 4), 16) // hexToG
      const b = parseInt(color.substring(4, 6), 16) // hexToB
      const uicolors = [r / 255, g / 255, b / 255]
      const c = uicolors.map((col) => {
        if (col <= 0.03928) {
          return col / 12.92
        }
        return Math.pow((col + 0.055) / 1.055, 2.4)
      })
      const L = (0.2126 * c[0]) + (0.7152 * c[1]) + (0.0722 * c[2])
      return (L > 0.55) ? '#000000' : '#FFFFFF'
    }
  },

  mounted () {
    if (this.compVal && typeof this.compVal === 'string') {
      this.compVal = parseInt(this.compVal)
    }
  },

  methods: {
    btnStyle (isSelected) {
      if (!isSelected) return {}
      return {
        color: this.textColor,
        backgroundColor: this.color
      }
    },
    setScale (val) {
      if (this.disabled) {
        return
      }
      if (this.compVal === val) {
        this.compVal = null
      } else {
        this.compVal = val
      }
    }
  }
}
</script>
