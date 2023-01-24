<template>
  <div class="main-layout min-h-screen flex flex-col">
    <navbar />

    <div class="w-full md:w-4/5 lg:w-3/5 md:mx-auto md:max-w-4xl px-4">
      <alert v-if="alert.type"
             class="my-4"
             :message="alert.message"
             :type="alert.type"
             :auto-close="alert.autoClose"
             :confirmation-proceed="alert.confirmationProceed"
             :confirmation-cancel="alert.confirmationCancel"
             @close="closeAlert"
      />
    </div>

    <child class="flex-grow" />
  </div>
</template>

<script>
import Navbar from '~/components/Navbar.vue'
import Alert from '../components/common/Alert.vue'

export default {
  name: 'MainLayout',

  components: {
    Navbar, Alert
  },

  computed: {
    alert () {
      return this.$root.alert
    }
  },

  methods: {
    closeAlert () {
      this.$root.alert = {
        type: null,
        autoClose: 0,
        message: '',
        confirmationProceed: null,
        confirmationCancel: null
      }
    }
  }
}
</script>
