<template>
  <Sidebar v-motion-slide-right v-if="show"
    class="absolute shadow-lg shadow-gray-800/30 top-0 h-[calc(100vh-53px)] right-0 lg:shadow-none lg:relative bg-white w-full md:w-1/2 lg:w-2/5 border-l overflow-y-scroll md:max-w-[20rem] flex-shrink-0 z-50"
  >
    <slot />
  </Sidebar>
</template>

<script>
// import posed, { PoseTransition } from 'vue-pose'
// import { MotionPlugin } from '@vueuse/motion'
import { directive as motion } from '@vueuse/motion'

export default {
  name: 'EditorRightSidebar',
  directives: {
    motion: motion(),
  },
  components: {
    // PoseTransition,
    // Sidebar: posed.div({
    //   enter: {
    //     x: 0,
    //     transition: { duration: 100, ease: 'easeOut' }
    //   },
    //   exit: {
    //     x: '100%',
    //     transition: { duration: 100, ease: 'easeIn' }
    //   }
    // })
  },
  props: {
    show: {
      type: Boolean,
      default: false
    },
  },
  data () {
    return {

    }
  },
  computed: {}
}
</script>
