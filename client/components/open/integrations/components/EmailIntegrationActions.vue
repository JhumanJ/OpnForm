<template>
  <div class="flex flex-1 items-center">
    <div
      v-if="integration"
      class="hidden md:block space-y-1"
    >
      <UBadge
        :label="mentionAsText(integration.data.subject)"
        color="neutral"
        variant="subtle"
        size="sm"
        class="max-w-[300px] block truncate"
      />
      <div class="flex items-center gap-1">
        <UBadge
          :label="firstEmail"
          color="neutral"
          variant="outline"
          size="sm"
          class="max-w-[300px] block truncate"
        />
        <UBadge
          v-if="additionalEmailsCount > 0"
          :label="`+${additionalEmailsCount}`"
          color="neutral"
          variant="outline"
          size="sm"
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

const { invalidateIntegrations } = useFormIntegrations()
let interval = null

onMounted(() => {
  if (!props.integration.data || props.integration.data.length === 0) {
    interval = setInterval(() => invalidateIntegrations(props.form.id), 3000)
    setTimeout(() => { clearInterval(interval) }, 30000)
  }
})

onBeforeUnmount(() => {
  if (interval) {
    clearInterval(interval)
  }
})

const sendToEmails = computed(() => {
  if (!props.integration.data.send_to) {
    return []
  }
  return mentionAsText(props.integration.data.send_to).split(/[,;\s\n]+/).filter(Boolean)
})

const firstEmail = computed(() => {
  return sendToEmails.value[0] || ''
})

const additionalEmailsCount = computed(() => {
  return Math.max(0, sendToEmails.value.length - 1)
})
</script>
