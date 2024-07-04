<template>
  <div class="border lg:rounded-xl bg-gray-50 dark:bg-gray-800 relative max-w-5xl mx-auto mt-10">
    <div
      class=" p-6 lg:flex lg:items-center"
    >
      <div class="flex-1">
        <h3
          class="inline-flex px-4 py-1 rounded-full text-md font-semibold tracking-wide bg-blue-500 text-white"
        >
          Custom Plan
        </h3>
        <div class="mt-4 text-gray-600 dark:text-gray-400 max-w-2xl">
          Get a custom file upload limit, enterprise-level support, custom
          contract, dedicated application instance in a specific region,
          payment via invoice/PO etc.
        </div>
      </div>
      <div class="mt-6 lg:mt-0 lg:ml-10 lg:flex-shrink-0">
        <UButton
          size="xl"
          color="white"
          class="w-auto"
          icon="i-heroicons-chat-bubble-left"
          @click.prevent="customPlanClick"
        >
          Contact us
        </UButton>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "CustomPlan",
  components: {},
  props: {},

  data: () => ({}),

  computed: {},

  methods: {
    customPlanClick() {
      useCrisp().sendTextMessage(
        "Hi, I would like to discuss about a custom plan",
      )
    },
  },
}
</script>
