<template>
  <transition enter-active-class="linear duration-500 overflow-hidden"
              enter-class="max-h-0 opacity-0"
              enter-to-class="max-h-screen opacity-100"
              leave-active-class="linear duration-500 overflow-hidden"
              leave-class="max-h-screen opacity-100"
              leave-to-class="max-h-0 opacity-0"
  >
    <div :class="alertClasses" class="border shadow-sm p-2 flex items-center rounded-md">
      <div class="flex-grow">
        <p class="mb-0 py-2 px-4" :class="textClasses" v-html="message"/>
      </div>

      <div class="justify-end">
        <v-button v-if="type == 'error'" color="red" shade="light" @click="close">
          Close
        </v-button>
        <v-button v-if="type == 'success'" color="green" shade="light" @click="close">
          OK
        </v-button>
        <v-button v-if="type == 'warning'" color="yellow" shade="light" @click="close">
          OK
        </v-button>
        <v-button v-if="type == 'confirmation'" class="mr-1 mb-1" @click="confirm">
          Yes
        </v-button>
        <v-button v-if="type == 'confirmation'" color="gray" shade="light" @click="cancel">
          No, cancel
        </v-button>
      </div>
    </div>
  </transition>
</template>

<script>
export default {
  name: 'Alert',
  props: ['type', 'message', 'autoClose', 'confirmationProceed', 'confirmationCancel'],

  data () {
    return {
      timeout: null
    }
  },

  computed: {
    alertClasses () {
      if (this.type === 'error') return 'bg-red-100 border-red-500'
      if (this.type === 'success') return 'bg-green-100 border-green-500'
      if (this.type === 'warning') return 'bg-yellow-100 border-yellow-500'
      return 'bg-blue-50 border-nt-blue-light'
    },
    textClasses () {
      if (this.type === 'error') return 'text-red-600'
      if (this.type === 'success') return 'text-green-600'
      if (this.type === 'warning') return 'text-yellow-600'
      return 'text-nt-blue'
    }
  },

  mounted () {
    if (this.autoClose) {
      this.timeout = setTimeout(() => {
        this.close()
      }, this.autoClose)
    }
  },

  methods: {
    /**
     * Close the modal.
     */
    close () {
      clearTimeout(this.timeout)
      this.$emit('close')
    },
    /**
     * Confirm and close the modal.
     */
    confirm () {
      this.confirmationProceed()
      this.close()
    },
    /**
     * Cancel and close the modal.
     */
    cancel () {
      if (this.confirmationCancel) {
        this.confirmationCancel()
      }
      this.close()
    }
  }
}
</script>
