<template>
  <input-wrapper
    v-bind="inputWrapperProps"
  >
    <template #label>
      <slot name="label" />
    </template>

    <div class="stars-outer">
      <div v-for="i in starsCount" :key="i"
           class="cursor-pointer inline-block text-gray-200 dark:text-gray-800"
           :class="{'!text-yellow-400 active-star':i<=compVal, '!text-yellow-200 !dark:text-yellow-800 hover-star':i>compVal && i<=hoverRating, '!cursor-not-allowed':disabled}"
           role="button" @click="setRating(i)"
           @mouseenter="onMouseHover(i)"
           @mouseleave="hoverRating = -1"
      >
        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
          <path
            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
          />
        </svg>
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
  name: 'RatingInput',
  components: { InputWrapper },

  props: {
    ...inputProps,
    numberOfStars: { type: Number, default: 5 }
  },

  setup (props, context) {
    return {
      ...useFormInput(props, context)
    }
  },

  data () {
    return {
      hoverRating: -1
    }
  },

  mounted () {
    if (!this.compVal) this.compVal = 0
  },

  updated () {
    if (!this.compVal) {
      this.compVal = 0
    }
  },

  computed: {
    starsCount() {
      if (!this.numberOfStars || this.numberOfStars < 1) {
        return 5
      }
      return this.numberOfStars
    }
  },

  methods: {
    onMouseHover (i) {
      this.hoverRating = (this.disabled) ? -1 : i
    },
    setRating (val) {
      if (this.disabled) {
        return
      }
      if (this.compVal === val) {
        this.compVal = 0
      } else {
        this.compVal = val
      }
    }
  }
}
</script>
