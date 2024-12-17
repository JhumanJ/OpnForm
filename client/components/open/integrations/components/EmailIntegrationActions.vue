<template>
  <div class="flex flex-1 items-center">
    <div
      v-if="integration"
      class="hidden md:block space-y-1"
    >
      <UBadge
        :label="mentionAsText(integration.data.subject)"
        color="gray"
        size="xs"
        class="max-w-[300px] block truncate"
      />
      <div class="flex items-center gap-1">
        <UBadge
          :label="firstEmail"
          color="white"
          size="xs"
          class="max-w-[300px] block truncate"
        />
        <UBadge
          v-if="additionalEmailsCount > 0"
          :label="`+${additionalEmailsCount}`"
          color="white"
          size="xs"
        />
      </div>
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
    interval = setInterval(() => formIntegrationsStore.fetchFormIntegrations(props.form.id), 3000)
    setTimeout(() => { clearInterval(interval) }, 30000)
  }
})

onBeforeUnmount(() => {
  if (interval) {
    clearInterval(interval)
  }
})

const firstEmail = computed(() => {
  const emails = mentionAsText(props.integration.data.send_to).split('\n').filter(Boolean)
  return emails[0] || ''
})

const additionalEmailsCount = computed(() => {
  const emails = mentionAsText(props.integration.data.send_to).split('\n').filter(Boolean)
  return Math.max(0, emails.length - 1)
})
</script>