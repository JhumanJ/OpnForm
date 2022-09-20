<template>
  <div :class="wrapperClass" :style="inputStyle">
    <label v-if="label" :for="id?id:name"
           :class="[theme.default.label,{'uppercase text-xs':uppercaseLabels, 'text-sm':!uppercaseLabels}]"
    >
      {{ label }}
      <span v-if="required" class="text-red-500 required-dot">*</span>
    </label>

    <div class="stars-outer">
      <div v-for="i in numberOfStars" :key="i"
           class="cursor-pointer inline-block"
           :class="{'text-yellow-400':i<=compVal, 'text-yellow-100 dark:text-yellow-900':i>compVal && i<=hoverRating ,'text-gray-200 dark:text-gray-800':i>compVal && i>hoverRating}"
           role="button" @click="setRating(i)"
           @mouseover="hoverRating = i"
           @mouseleave="hoverRating = null"
      >
        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
          <path
            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
          />
        </svg>
      </div>
    </div>

    <small v-if="help" :class="theme.default.help">
      <slot name="help">{{ help }}</slot>
    </small>
    <has-error v-if="hasValidation" :form="form" :field="name" />
  </div>
</template>

<script>
import inputMixin from '~/mixins/forms/input'

export default {
  name: 'RatingInput',

  mixins: [inputMixin],

  props: {
    numberOfStars: { type: Number, default: 5 }
  },

  data () {
    return {
      hoverRating: null
    }
  },

  updated () {
    if (this.compVal === null) {
      this.compVal = 0
    }
  },

  methods: {
    setRating (val) {
      if (this.compVal === val) {
        this.compVal = 0
      } else {
        this.compVal = val
      }
    }
  }
}
</script>
