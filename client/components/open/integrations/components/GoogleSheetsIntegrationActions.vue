<template>
  <div class="flex flex-1 items-center">
    <div
      v-if="integration.provider"
      class="space-y-1"
    >
      <div
        class="font-medium mr-2"
      >
        {{ integration.provider.user.name }}
      </div>
      <div class="text-sm">
        {{ integration.provider.user.email }}
      </div>
    </div>

    <div
      v-if="integration.data"
      class="ml-auto"
    >
      <v-button
        :href="integration.data.url"
        target="_blank"
        color="white"
      >
        Open spreadsheet
      </v-button>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  integration: {
    type: Object,
    required: true,
  },
  form: {
    type: Object,
    required: true,
  }
})

const formIntegrationsStore = useFormIntegrationsStore()
let interval = null

onMounted(() => {
  if (!props.integration.data) {
    interval = setInterval(() => formIntegrationsStore.fetchFormIntegrations(props.form.id), 3000)
    setTimeout(() => { clearInterval(interval) }, 30000)
  }
})

onBeforeUnmount(() => {
  if (interval) {
    clearInterval(interval)
  }
})
</script>
