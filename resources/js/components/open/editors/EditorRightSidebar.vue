<template>
  <transition @leave="(el, done) => motions.leave(done)">
    <div v-if="show" ref="sidebar"
         class="absolute shadow-lg shadow-gray-800/30 top-0 h-[calc(100vh-53px)] right-0 lg:shadow-none lg:relative bg-white w-full md:w-1/2 lg:w-2/5 border-l overflow-y-scroll md:max-w-[20rem] flex-shrink-0 z-50"
    >
      <slot />
    </div>
  </transition>
</template>

<script>
import { useMotion } from '@vueuse/motion'
import { ref } from 'vue'

export default {
  name: 'EditorRightSidebar',
  props: {
    show: {
      type: Boolean,
      default: false
    }
  },
  setup () {
    const sidebar = ref(null)
    const motions = useMotion(sidebar, {
      initial: {
        x: 500,
        transition: {
          duration: 1000,
          ease: 'easeIn'
        }
      },
      enter: {
        x: 0,
        transition: {
          duration: 1000,
          ease: 'easeOut'
        }
      }
    })
    motions.variant = 'initial'

    return {
      motions
    }
  },
  data () {
    return {}
  },
  computed: {}
}
</script>
