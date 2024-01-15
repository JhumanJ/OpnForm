<template>
  <transition v-if="name=='slideInUp'"
              enter-active-class="linear duration-300 overflow-hidden"
              enter-from-class="max-h-0"
              enter-to-class="max-h-screen"
              leave-active-class="linear duration-300 overflow-hidden"
              leave-from-class="max-h-screen"
              leave-to-class="max-h-0"
  >
    <slot />
  </transition>
</template>

<script>
export default {
  name: 'VTransition',
  props: { name: { default: 'slideInUp' } }
}
</script>
