<template>
  <portal to="modals" :order="portalOrder">
    <transition @leave="(el,done) => motions.backdrop.leave(done)">
      <div v-if="show" v-motion="'backdrop'" :variants="motionFadeIn"
           class="fixed z-30 top-0 inset-0 px-4 sm:px-0 flex items-top justify-center bg-gray-700/75 w-full h-screen overflow-y-scroll"
           :class="{'backdrop-blur-sm':backdropBlur}"
           @click.self="close"
      >
        <div ref="content" v-motion="'body'" :variants="motionSlideBottom"
             class="self-start bg-white dark:bg-notion-dark w-full relative p-4 md:p-6 my-6 rounded-xl shadow-xl"
             :class="maxWidthClass"
        >
          <div v-if="closeable" class="absolute top-4 right-4">
            <button class="text-gray-500 hover:text-gray-900 cursor-pointer" @click.prevent="close">
              <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                      stroke-linejoin="round"
                />
              </svg>
            </button>
          </div>
          <div class="sm:flex sm:flex-col sm:items-start">
            <div v-if="$scopedSlots.hasOwnProperty('icon')" class="flex w-full justify-center mb-4">
              <div class="w-14 h-14 rounded-full flex justify-center items-center"
                   :class="'bg-'+iconColor+'-100 text-'+iconColor+'-600'"
              >
                <slot name="icon"/>
              </div>
            </div>
            <div class="mt-3 text-center sm:mt-0 w-full">
              <h2 v-if="$scopedSlots.hasOwnProperty('title')"
                  class="text-2xl font-semibold text-center text-gray-900"
              >
                <slot name="title"/>
              </h2>
            </div>
          </div>

          <div class="w-full">
            <slot/>
          </div>

          <div v-if="$scopedSlots.hasOwnProperty('footer')" class="px-6 py-4 bg-gray-100 text-right">
            <slot name="footer"/>
          </div>
        </div>
      </div>
    </transition>
  </portal>
</template>

<script>
import {useMotions} from '@vueuse/motion'

export default {
  name: 'Modal',

  props: {
    show: {
      default: false
    },
    backdropBlur: {
      type: Boolean,
      default: false
    },
    iconColor: {
      default: 'blue'
    },
    maxWidth: {
      default: '2xl'
    },
    closeable: {
      default: true
    },
    portalOrder: {
      default: 1
    }
  },

  setup(props) {
    useHead({
      bodyAttrs: {
        class: {
          'overflow-hidden': props.show
        }
      }
    })

    const closeOnEscape = (e) => {
      if (e.key === 'Escape' && this.show) {
        this.close()
      }
    }

    onMounted(() => {
      if (process.server) return
      document.addEventListener('keydown', closeOnEscape)
    })

    onBeforeUnmount(() => {
      if (process.server) return
      document.removeEventListener('keydown', closeOnEscape)
    })

    return {
      motions: useMotions(),
    }
  },

  computed: {
    maxWidthClass() {
      return {
        sm: 'sm:max-w-sm',
        md: 'sm:max-w-md',
        lg: 'sm:max-w-lg',
        xl: 'sm:max-w-xl',
        '2xl': 'sm:max-w-2xl'
      }[this.maxWidth]
    },
    motionFadeIn() {
      return {
        initial: {
          opacity: 0,
          transition: {
            delay: 100,
            duration: 200,
            ease: 'easeIn'
          }
        },
        enter: {
          opacity: 1,
          transition: {
            duration: 200
          }
        }
      }
    },
    motionSlideBottom() {
      return {
        initial: {
          y: 150,
          opacity: 0,
          transition: {
            ease: 'easeIn',
            duration: 200
          }
        },
        enter: {
          y: 0,
          opacity: 1,
          transition: {
            duration: 250,
            ease: 'easeOut',
            delay: 100
          }
        }
      }
    }
  },

  watch: {
    show(newVal, oldVal) {
      if (newVal !== oldVal) {
        if (!newVal) {
          this.motions.body.apply('initial')
          this.motions.backdrop.apply('initial')
        }
      }
    }
  },

  methods: {
    close() {
      if (this.closeable) {
        this.$emit('close')
      }
    }
  }
}
</script>
