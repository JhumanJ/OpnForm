<template>
  <modal
    :show="show"
    max-width="md"
    @close="emit('close')"
  >
    <template #title>
      <div class="flex items-center justify-center mb-4">
        <Icon
          :name="service.icon"
          class="h-8 w-8 text-gray-500"
        />
        <span class="ml-2 text-gray-700 font-medium">{{ service.title }}</span>
      </div>
    </template>

    <div class="p-4 flex items-center justify-center">
      <div
        ref="widgetContainer"
        v-html="widgetHtml"
      />
    </div>
  </modal>
</template>

<script setup>
const props = defineProps({
  show: Boolean,
  service: Object
})

const emit = defineEmits(['close'])
const widgetContainer = ref(null)
const widgetHtml = ref('')
const providersStore = useOAuthProvidersStore()

onMounted(() => {
  fetchWidget()
})

const fetchWidget = () => {
  providersStore.connect(props.service.name).then((response) => {
    widgetHtml.value = response.url
  })
}
</script>