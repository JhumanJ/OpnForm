<template>
  <transition
    :name="transitionName"
    @enter="enter"
    @leave="leave"
    @after-enter="resetStyles"
    @after-leave="resetStyles"
  >
    <slot />
  </transition>
</template>

<script setup>
import { computed, ref, watch } from 'vue'

const props = defineProps({
  direction: {
    type: String,
    default: 'horizontal',
    validator: (value) => ['vertical', 'horizontal'].includes(value)
  },
  step: {
    type: Number,
    default: 1
  }
})

const previousStep = ref(props.step)
const transitionName = computed(() => {
  const baseTransition = props.direction === 'vertical' ? 'slide-vertical' : 'slide-horizontal'
  return `${baseTransition}-${props.step > previousStep.value ? 'forward' : 'backward'}`
})

watch(() => props.step, (newStep, oldStep) => {
  previousStep.value = oldStep
})

const enter = (el, done) => {
  const { height } = el.getBoundingClientRect()
  el.style.height = '0'
  el.offsetHeight // force reflow
  el.style.height = `${height}px`
  el.addEventListener('transitionend', done, { once: true })
}

const leave = (el, done) => {
  const { height } = el.getBoundingClientRect()
  el.style.height = `${height}px`
  el.offsetHeight // force reflow
  el.style.height = '0'
  el.addEventListener('transitionend', done, { once: true })
}

const resetStyles = (el) => {
  el.style.height = ''
}
</script>

<style scoped>
.slide-horizontal-forward-enter-active,
.slide-horizontal-forward-leave-active,
.slide-horizontal-backward-enter-active,
.slide-horizontal-backward-leave-active,
.slide-vertical-forward-enter-active,
.slide-vertical-forward-leave-active,
.slide-vertical-backward-enter-active,
.slide-vertical-backward-leave-active {
  transition: all 0.3s ease-out;
  overflow: hidden;
}

.slide-horizontal-forward-enter-from,
.slide-horizontal-backward-leave-to {
  opacity: 0;
  transform: translateX(30px);
}

.slide-horizontal-forward-leave-to,
.slide-horizontal-backward-enter-from {
  opacity: 0;
  transform: translateX(-30px);
}

.slide-vertical-forward-enter-from,
.slide-vertical-backward-leave-to {
  opacity: 0;
  transform: translateY(30px);
}

.slide-vertical-forward-leave-to,
.slide-vertical-backward-enter-from {
  opacity: 0;
  transform: translateY(-30px);
}
</style>