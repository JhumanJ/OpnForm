<template>
  <div class="main-layout min-h-screen flex flex-col">
    <navbar />

    <child class="flex-grow" />
  </div>
</template>

<script>
import Navbar from '~/components/Navbar.vue'

export default {
  name: 'MainLayout',

  components: {
    Navbar
  },

  computed: {}
}
</script>
