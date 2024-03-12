<template>
  <transition @leave="onLeave">
    <div
      ref="collapsible"
      v-if="modelValue"
      v-on-click-outside.bubble="onClickAway"
    >
      <slot/>
    </div>
  </transition>
</template>

<script setup>
import {vOnClickOutside} from '@vueuse/components'

const props = defineProps({
  modelValue: {type: Boolean},
  maxHeight: {type: Number, default: 200},
})
const emit = defineEmits(['click-away'])

const motion = ref(null)
const collapsible = ref(null)
const variants = {
  initial: {
    opacity: 0,
    y: -10,
    transition: {duration: 75, ease: 'easeIn'}
  },
  enter: {
    opacity: 1,
    y: 0,
    transition: {duration: 150, ease: 'easeOut'}
  }
}

watch(() => props.modelValue, (newValue) => {
  if (newValue) {
    nextTick(() => {
      motion.value = useMotion(collapsible.value, variants)
    })
  }
})

const onLeave = (el, done) => {
  motion.value.leave(done)
}

const onClickAway = (event) => {
  emit('click-away', event)
}
</script>
