<template>
  <UModal
    v-model:open="isOpen"
    :ui="{ content: 'sm:max-w-4xl' }"
    title="Past Events"
  >
    <template #body>
      <UTable
        :loading="integrationEventsLoading"
        :columns="columns"
        :data="integrationEvents"
      >
        <template #status-cell="{ row }">
          <UBadge
            variant="subtle"
            :color="row.original.status === 'Success' ? 'success' : 'error'"
            :label="row.original.status"
          />
        </template>
        <template #data-cell="{ row }">
          <vue-json-pretty
            v-if="row.original.data && Object.keys(row.original.data).length > 0"
            :data="row.original.data"
            :collapsed-node-length="0"
            :show-length="true"
            :show-icon="true"
          />
          <span v-else>-</span>
        </template>
      </UTable>
    </template>

    <template #footer>
      <UButton
        color="neutral"
        variant="outline"
        @click="close"
        label="Close"
      />
    </template>
  </UModal>
</template>

<script setup>
import VueJsonPretty from "vue-json-pretty"
import "vue-json-pretty/lib/styles.css"
import { formsApi } from "~/api/forms"

const props = defineProps({
  show: { type: Boolean, required: true },
  form: { type: Object, required: true },
  formIntegrationId: { type: Number, required: true },
})

const emit = defineEmits(["close"])

// Modal state
const isOpen = computed({
  get() {
    return props.show
  },
  set(value) {
    if (!value) {
      close()
    }
  }
})
const columns = [
  { accessorKey: "date", header: "Date" },
  { accessorKey: "status", header: "Status" },
  { accessorKey: "data", header: "Info" },
]
const integrationEvents = ref([])
const integrationEventsLoading = ref(false)

watch(
  () => props.show,
  () => {
    fetchEvents()
  },
)

const fetchEvents = () => {
  if (props.show) {
    nextTick(() => {
      integrationEventsLoading.value = true
      integrationEvents.value = []
      formsApi.integrations.events(props.form.id, props.formIntegrationId).then((data) => {
        integrationEvents.value = data
        integrationEventsLoading.value = false
      })
    })
  }
}

const close = () => {
  emit("close")
}
</script>
