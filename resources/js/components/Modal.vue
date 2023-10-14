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
            <div class="absolute inset-0 bg-gray-500 opacity-75"/>
          </div>
        </transition>

        <transition enter-active-class="delay-75 linear duration-300"
                    enter-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    enter-to-class="opacity-100 translate-y-0 sm:scale-100"
                    leave-active-class="linear duration-200" appear
                    leave-class="opacity-100 translate-y-0 sm:scale-100"
                    leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        >
          <div v-if="show"
               class="modal-content bg-white dark:bg-notion-dark rounded-lg overflow-y-auto shadow-xl transform transition-all sm:w-full"
               :class="maxWidthClass"
          >
            <div class="bg-white relative dark:bg-notion-dark p-4 md:p-6">
              <div class="absolute top-4 right-4" v-if="closeable">
                <button class="text-gray-500 hover:text-gray-900 cursor-pointer" @click.prevent="close">
                  <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                          stroke-linejoin="round"/>
                  </svg>
                </button>
              </div>
              <div class="sm:flex sm:flex-col sm:items-start">
                <div v-if="$scopedSlots.hasOwnProperty('icon')" class="flex w-full justify-center mb-4">
                  <div class="w-14 h-14 rounded-full flex justify-center items-center"
                       :class="'bg-'+iconColor+'-100 text-'+iconColor+'-600'">
                    <slot name="icon"/>
                  </div>
                </div>
                <div class="mt-3 text-center sm:mt-0 w-full">
                  <h2 v-if="$scopedSlots.hasOwnProperty('title')" class="text-2xl font-semibold text-center text-gray-900">
                    <slot name="title"/>
                  </h2>
                </div>
              </div>

              <div class="mt-2 w-full">
                <slot/>
              </div>
            </div>

            <div v-if="$scopedSlots.hasOwnProperty('footer')" class="px-6 py-4 bg-gray-100 text-right">
              <slot name="footer"/>
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
    },
    afterLeave: {
      type: Function,
      required: false
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

  created() {
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
    close() {
      if (this.closeable) {
        this.$emit('close')
      }
    },
    leaveCallback() {
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
