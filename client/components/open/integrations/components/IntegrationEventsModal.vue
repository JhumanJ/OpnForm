<template>
  <modal :show="show" @close="emit('close')" compact-header inner-padding="">
    <template #icon>
      <Icon name="heroicons:clock" size="40px"/>
    </template>
    <template #title>
      Past Events
    </template>

    <UTable :loading="integrationEventsLoading" :columns="columns" :rows="integrationEvents">
      <template #status-data="{ row }">
        <Badge :color="(row.status==='Success') ? 'green' : 'red'">
          {{row.status}}
        </Badge>
      </template>
      <template #data-data="{ row }">
        <vue-json-pretty v-if="row.data && Object.keys(row.data).length > 0" :data="row.data" :collapsedNodeLength="0" :showLength="true" :showIcon="true" />
        <span v-else>-</span>
      </template>
    </UTable>

    <template #footer>
      <div class="flex justify-center gap-x-2">
        <v-button color="white" @click.prevent="emit('close')">
          Close
        </v-button>
      </div>
    </template>
  </modal>
</template>

<script setup>
import VueJsonPretty from 'vue-json-pretty'
import 'vue-json-pretty/lib/styles.css'

const props = defineProps({
  show: { type: Boolean, required: true },
  form: {type: Object, required: true},
  formIntegrationId: {type: Number, required: true}
})

const emit = defineEmits(['close'])
const formIntegrationEventEndpoint = '/open/forms/{formid}/integration/{integrationid}/events'
const columns = [
  { key: 'date', label: 'Date', sortable: true },
  { key: 'status', label: 'Status', sortable: true },
  { key: 'data', label: 'Info'}
]
let integrationEvents = ref([])
let integrationEventsLoading = ref(false)

watch(() => props.show, () => {
  fetchEvents()
})

const fetchEvents = () => {
  if (props.show) {
    nextTick(() => {
      integrationEventsLoading.value = true
      integrationEvents.value = []
      opnFetch(formIntegrationEventEndpoint.replace('{formid}', props.form.id).replace('{integrationid}', props.formIntegrationId)).then((data) => {
        integrationEvents.value = data
        integrationEventsLoading.value = false
      })
    })
  }
}
</script>
