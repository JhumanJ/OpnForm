<template>
  <transition @leave="(el,done)=>motions.collapsible.leave(done)">
    <div
      ref="collapsible"
      v-if="modelValue"
      v-motion="'collapsible'"
      :variants="variants"
      v-on-click-outside.bubble="close"
    >
      <slot/>
    </div>
  </transition>
</template>

<script setup>
import {vOnClickOutside} from '@vueuse/components'

const props = defineProps({
  modelValue: {type: Boolean},
  closeOnClickAway: {type: Boolean, default: true},
  maxHeight: {type: Number, default: 200},
})
const emits = defineEmits(['update:modelValue'])

const motions = useMotions()
const variants = ref({
  enter: {
    opacity: 1,
    y: 0,
    transition: {duration: 150, ease: 'easeOut'}
  },
  initial: {
    opacity: 0,
    y: -10,
    transition: {duration: 75, ease: 'easeIn'}
  },
})
const close = () => {
  if (props.closeOnClickAway) {
    emits('update:modelValue', false)
  }
}
</script>
