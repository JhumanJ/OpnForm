<template>
  <div class="flex flex-1 items-center">
    <div
      v-if="integration.provider"
      class="hidden md:block space-y-1"
    >
      <UBadge
        :label="mentionAsText(integration.provider.email)"
        color="gray"
        size="xs"
        class="max-w-[300px] truncate"
      />
    </div>

    <div
      v-if="integration.data"
      class="ml-auto"
    >
      <v-button
        :href="integration.data.url"
        target="_blank"
        color="white"
        class="block"
      >
        <Icon
          name="mdi:google-spreadsheet"
          size="20px"
        />
        Open
        <Icon
          class="ml-1"
          name="heroicons:arrow-top-right-on-square-20-solid"
        />
      </v-button>
    </div>
  </div>
</template>

<script setup>
import { mentionAsText } from '~/lib/utils.js'

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
  if (!props.integration.data || props.integration.data.length === 0) {
    interval = setInterval(() => formIntegrationsStore.fetchFormIntegrations(props.form.id, false), 3000)
    setTimeout(() => { clearInterval(interval) }, 30000)
  }
})

onBeforeUnmount(() => {
  if (interval) {
    clearInterval(interval)
  }
})
</script>
