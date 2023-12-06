<template>
  <transition @leave="(el,done) => motions.slide.leave(done)">
    <div v-if="show" v-motion-slide-right="'slide'"
         class="absolute shadow-lg shadow-gray-800/30 top-0 h-[calc(100vh-53px)] right-0 lg:shadow-none lg:relative bg-white w-full md:w-1/2 lg:w-2/5 border-l overflow-y-scroll md:max-w-[20rem] flex-shrink-0 z-50"
    >
      <slot />
    </div>
  </transition>
</template>

<script>
import { useMotions } from '@vueuse/motion'

export default {
  name: 'EditorRightSidebar',
  props: {
    show: {
      type: Boolean,
      default: false
    }
  },
  setup (props) {
    return {
      motions: useMotions()
    }
  }
}
</script>
