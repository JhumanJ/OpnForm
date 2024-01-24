<template>
  <button :type="nativeType" :disabled="loading?true:null" :class="`py-${sizes['p-y']} px-${sizes['p-x']} text-${sizes['font']} ${theme.Button.body}`" :style="buttonStyle"
          class="btn">
    <template v-if="!loading">
      <slot />
    </template>
    <Loader v-else class="h-6 w-6 text-white mx-auto" />
  </button>
</template>

<script>
import { themes } from '~/lib/forms/form-themes.js'

export default {
  name: 'OpenFormButton',

  props: {
    color: {
      type: String,
      required: true
    },

    size: {
      type: String,
      default: 'medium'
    },

    nativeType: {
      type: String,
      default: 'submit'
    },

    loading: {
      type: Boolean,
      default: false
    },

    theme: { type: Object, default: () => themes.default }
  },

  computed: {
    buttonStyle () {
      return {
        backgroundColor: this.color,
        color: this.getTextColor(this.color),
        '--tw-ring-color': this.color
      }
    },
    sizes () {
      if (this.size === 'small') {
        return {
          font: 'sm',
          'p-y': '1',
          'p-x': '2'
        }
      }
      return {
        font: 'base',
        'p-y': '2',
        'p-x': '4'
      }
    }
  },

  methods: {
    getTextColor (bgColor, lightColor = '#FFFFFF', darkColor = '#000000') {
      if (!bgColor) {
        return darkColor
      }
      const color = (bgColor.charAt(0) === '#') ? bgColor.substring(1, 7) : bgColor
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
      return (L > 0.45) ? darkColor : lightColor
    }
  }
}
</script>
