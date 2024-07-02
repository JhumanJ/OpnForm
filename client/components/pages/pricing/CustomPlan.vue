<template>
  <div class="border rounded-lg relative max-w-5xl mx-auto mt-4 lg:mt-10">
    <div class="w-full">
      <div
        class="rounded-lg bg-gray-50 dark:bg-gray-800 px-6 py-8 sm:p-10 lg:flex lg:items-center"
      >
        <div class="flex-1">
          <h3
            class="inline-flex px-4 py-1 rounded-full text-md font-semibold tracking-wide bg-blue-500 text-white"
          >
            Custom Plan
          </h3>
          <div class="mt-4 text-md text-gray-600 dark:text-gray-400 max-w-2xl">
            Get a custom file upload limit, enterprise-level support, custom
            contract, dedicated application instance in a specific region,
            payment via invoice/PO etc.
          </div>
        </div>
        <div class="mt-6 rounded-md lg:mt-0 lg:ml-10 lg:flex-shrink-0">
          <UButton
            size="xl"
            color="white"
            class="w-full"
            icon="i-heroicons-chat-bubble-left"
            @click.prevent="customPlanClick"
          >
            Contact us
          </UButton>
        </div>
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
