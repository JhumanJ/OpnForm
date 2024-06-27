<template>
  <input-wrapper v-bind="inputWrapperProps">
    <template #label>
      <slot name="label" />
    </template>

    <div class="stars-outer">
      <div
        v-for="i in starsCount"
        :key="i"
        class="cursor-pointer inline-block text-gray-200 dark:text-gray-800"
        :class="{
          '!text-yellow-400 active-star': i <= compVal,
          '!text-yellow-200 !dark:text-yellow-800 hover-star':
            i > compVal && i <= hoverRating,
          '!cursor-not-allowed': disabled,
        }"
        role="button"
        @click="setRating(i)"
        @mouseenter="onMouseHover(i)"
        @mouseleave="hoverRating = -1"
      >
        <Icon
          name="heroicons:star-20-solid"
          :class="theme.RatingInput.size"
        />
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
import { inputProps, useFormInput } from "./useFormInput.js"
import InputWrapper from "./components/InputWrapper.vue"

export default {
  name: "RatingInput",
  components: { InputWrapper },

  props: {
    ...inputProps,
    numberOfStars: { type: Number, default: 5 },
  },

  setup(props, context) {
    return {
      ...useFormInput(props, context),
    }
  },

  data() {
    return {
      hoverRating: -1,
    }
  },

  computed: {
    starsCount() {
      if (!this.numberOfStars || this.numberOfStars < 1) {
        return 5
      }
      return this.numberOfStars
    },
  },

  mounted() {
    if (!this.compVal) this.compVal = 0
  },

  updated() {
    if (!this.compVal) {
      this.compVal = 0
    }
  },

  methods: {
    onMouseHover(i) {
      this.hoverRating = this.disabled ? -1 : i
    },
    setRating(val) {
      if (this.disabled) {
        return
      }
      if (this.compVal === val) {
        this.compVal = 0
      } else {
        this.compVal = val
      }
    },
  },
}
</script>
