<template>
  <portal to="modals" :order="portalOrder">
    <transition leave-active-class="duration-200" name="fade" appear>
      <div v-if="show" class="fixed z-30 top-0 inset-x-0 px-4 pt-6 sm:px-0 sm:flex sm:items-top sm:justify-center">
        <transition enter-active-class="transition-all delay-75 linear duration-300"
                    enter-class="opacity-0"
                    enter-to-class="opacity-100"
                    leave-active-class="transition-all linear duration-100"
                    leave-class="opacity-100"
                    leave-to-class="opacity-0"
                    appear @after-leave="leaveCallback"
        >
          <div v-if="show" class="fixed inset-0 transform" @click="close">
            <div class="absolute inset-0 bg-gray-500 opacity-75" />
          </div>
        </transition>

        <transition enter-active-class="delay-75 linear duration-300"
                    enter-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    enter-to-class="opacity-100 translate-y-0 sm:scale-100"
                    leave-active-class="linear duration-200" appear
                    leave-class="opacity-100 translate-y-0 sm:scale-100"
                    leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        >
          <div v-if="show" class="modal-content bg-white dark:bg-notion-dark rounded-lg overflow-y-scroll shadow-xl transform transition-all sm:w-full"
               :class="maxWidthClass"
          >
            <div class="bg-white dark:bg-notion-dark px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <div class="sm:flex sm:items-start">
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                  <h3 v-if="$scopedSlots.hasOwnProperty('title')" class="text-lg">
                    <slot name="title" />
                  </h3>
                </div>
              </div>

              <div class="mt-2 w-full">
                <slot />
              </div>
            </div>

            <div v-if="$scopedSlots.hasOwnProperty('footer')" class="px-6 py-4 bg-gray-100 text-right">
              <slot name="footer" />
            </div>
          </div>
        </transition>
      </div>
    </transition>
  </portal>
</template>

<script>
export default {
  name: 'Modal',

  props: {
    show: {
      default: false
    },
    maxWidth: {
      default: '2xl'
    },
    closeable: {
      default: true
    },
    portalOrder: {
      default: 1
    },
    afterLeave: {
      type: Function,
      required: false
    }
  },

  computed: {
    maxWidthClass () {
      return {
        sm: 'sm:max-w-sm',
        md: 'sm:max-w-md',
        lg: 'sm:max-w-lg',
        xl: 'sm:max-w-xl',
        '2xl': 'sm:max-w-2xl'
      }[this.maxWidth]
    }
  },

  watch: {
    show: {
      immediate: true,
      handler: (show) => {
        if (show) {
          document.body.style.overflow = 'hidden'
        } else {
          document.body.style.overflow = null
        }
      }
    }
  },

  created () {
    const closeOnEscape = (e) => {
      if (e.key === 'Escape' && this.show) {
        this.close()
      }
    }

    document.addEventListener('keydown', closeOnEscape)

    this.$once('hook:destroyed', () => {
      document.removeEventListener('keydown', closeOnEscape)
    })
  },

  methods: {
    close () {
      if (this.closeable) {
        this.$emit('close')
      }
    },
    leaveCallback () {
      if (this.afterLeave) {
        this.afterLeave()
      }
    }
  }
}
</script>

<style lang="scss" scoped>
.modal-content {
  max-height: calc(100vh - 40px);
}
</style>
