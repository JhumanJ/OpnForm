<template>
  <div v-if="hasValidLicense" class="p-6 bg-white border shadow-md rounded-md">
    <img src="/img/appsumo/as-taco-white-bg.png" class="max-w-[60px] mx-auto" alt="AppSumo"/>

    <img src="/img/appsumo/as-Select-dark.png" class="max-w-[300px] mx-auto" alt="AppSumo"/>

    <p class="mt-6">
      <span class="text-green-500">We found your AppSumo Lifetime deal license!</span> Just complete the registration form to finalize the activation of
      your license.
    </p>
  </div>
  <div v-else-if="hasLicenseError" class="p-6 bg-white border border-red-500 shadow-md rounded-md">
    <img src="/img/appsumo/as-taco-white-bg.png" class="max-w-[60px] mx-auto" alt="AppSumo"/>

    <img src="/img/appsumo/as-Select-dark.png" class="max-w-[300px] mx-auto" alt="AppSumo"/>

    <p class="mt-6">
      <span class="text-red-600">Invalid AppSumo license</span>. The license was probably already attached to an OpnForm account. Please contact support.
    </p>
  </div>
</template>

<script>
export default {

  name: 'AppSumoRegister',

  data () {
    return {
      hasValidLicense: false,
      hasLicenseError: false
    }
  },

  computed: {},

  watch: {},

  mounted () {
    if (this.$route.query.appsumo_license !== undefined && this.$route.query.appsumo_license) {
      this.hasValidLicense = true
    } else if (this.$route.query.appsumo_error !== undefined && this.$route.query.appsumo_error) {
      this.hasLicenseError = true
    }
  },

  created () {
  },
  destroyed () {
  },

  methods: {}
}
</script>
