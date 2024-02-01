<template>
  <div class="border relative max-w-5xl mx-auto mt-4 lg:mt-10">
    <div class="w-full">
      <div class="rounded-lg bg-gray-50 dark:bg-gray-800 px-6 py-8 sm:p-10 lg:flex lg:items-center">
        <div class="flex-1">
          <h3 class="inline-flex px-4 py-1 rounded-full text-md font-bold tracking-wide uppercase bg-white text-gray-800">
            Custom plan
          </h3>
          <div class="mt-4 text-md text-gray-600 dark:text-gray-400">
            Get a custom file upload limit, enterprise-level support, custom contract, dedicated application instance in a specific region, payment via invoice/PO etc.
          </div>
        </div>
        <div class="mt-6 rounded-md lg:mt-0 lg:ml-10 lg:flex-shrink-0">
          <v-button color="white" class="w-full mt-4" @click.prevent="customPlanClick">
            Contact us
          </v-button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'CustomPlan',
  components: {},
  props: {},

  data: () => ({}),

  computed: {},

  methods: {
    customPlanClick () {
      useCrisp().sendTextMessage('Hi, I would like to discuss about a custom plan')
    }
  }
}
</script>
