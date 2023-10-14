<template>
  <div>
    <div class="w-full relative">
      <div class="cursor-pointer" @click="trigger">
        <slot name="title" />
      </div>
      <div class="text-gray-400 hover:text-gray-600 absolute -right-2 -top-1 cursor-pointer p-2" @click="trigger">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition transform duration-500"
             :class="{'rotate-180':showContent}" viewBox="0 0 20 20" fill="currentColor"
        >
          <path fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z"
                clip-rule="evenodd"
          />
        </svg>
      </div>
    </div>
    <v-transition>
      <div v-if="showContent" class="w-full">
        <slot />
      </div>
    </v-transition>
  </div>
</template>

<script>
import VTransition from './transitions/VTransition.vue'

export default {
  name: 'Collapse',
  components: { VTransition },
  props: {
    defaultValue: { type: Boolean, default: false },
    value: { type: Boolean, default: null }
  },
  data () {
    return {
      showContent: this.value ?? this.defaultValue
    }
  },
  methods: {
    trigger () {
      this.showContent = !this.showContent
      this.$emit('input', this.showContent)
    }
  }
}
</script>
