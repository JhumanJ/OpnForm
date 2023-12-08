<template>
  <transition @leave="(el,done) => motions.collapsible.leave(done)">
    <div
      v-if="modelValue"
      key="dropdown"
      v-motion="'collapsible'"
      v-on-click-outside.bubble="close"
      :variants="motionCollapse"
    >
      <slot />
    </div>
  </transition>
</template>

<script>
import { vOnClickOutside } from '@vueuse/components'
import { useMotions } from '@vueuse/motion'

export default {
  name: 'Collapsible',
  directives: {
    onClickOutside: vOnClickOutside
  },
  props: {
    modelValue: { type: Boolean },
    closeOnClickAway: { type: Boolean, default: true }
  },
  setup () {
    return {
      motions: useMotions()
    }
  },
  computed: {
    motionCollapse () {
      return {
        enter: {
          opacity: 1,
          y: 0,
          height: 'auto',
          transition: { duration: 150, ease: 'easeOut' }
        },
        initial: {
          opacity: 0,
          y: -10,
          height: 0,
          transition: { duration: 75, ease: 'easeIn' }
        }
      }
    }
  },
  methods: {
    close () {
      if (this.closeOnClickAway) {
        this.$emit('update:modelValue', false)
      }
    }
  }
}
</script>
