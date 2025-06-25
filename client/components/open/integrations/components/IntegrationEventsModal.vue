<template>
  <modal
    :show="show"
    compact-header
    inner-padding=""
    @close="emit('close')"
  >
    <template #icon>
      <Icon
        name="heroicons:clock"
        size="40px"
      />
    </template>
    <template #title>
      Past Events
    </template>

    <UTable
      :loading="integrationEventsLoading"
      :columns="columns"
      :data="integrationEvents"
    >
      <template #status-cell="{ row }">
        <Badge :color="row.original.status === 'Success' ? 'green' : 'red'">
          {{ row.original.status }}
        </Badge>
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

    <template #footer>
      <div class="flex justify-center gap-x-2">
        <v-button
          color="white"
          @click.prevent="emit('close')"
        >
          Close
        </v-button>
      </div>
    </template>
  </modal>
</template>

<script setup>
import VueJsonPretty from "vue-json-pretty"
import "vue-json-pretty/lib/styles.css"

const props = defineProps({
  show: { type: Boolean, required: true },
  form: { type: Object, required: true },
  formIntegrationId: { type: Number, required: true },
})

const emit = defineEmits(["close"])
const formIntegrationEventEndpoint =
  "/open/forms/{formid}/integration/{integrationid}/events"
const columns = [
  { accessorKey: "date", header: "Date", sortable: true },
  { accessorKey: "status", header: "Status", sortable: true },
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
      opnFetch(
        formIntegrationEventEndpoint
          .replace("{formid}", props.form.id)
          .replace("{integrationid}", props.formIntegrationId),
      ).then((data) => {
        integrationEvents.value = data
        integrationEventsLoading.value = false
      })
    })
  }
}
</script>
