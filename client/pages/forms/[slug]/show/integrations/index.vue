<template>
  <div class="p-4">
    <div class="w-full max-w-4xl mx-auto">
      <h1 class="font-semibold text-xl">
        Integrations
      </h1>

      <div class="text-sm text-gray-500">
        Read, update and create data with dozens of 3rd-party integrations
      </div>

      <div
        v-if="isIntegrationsLoading"
        class="my-6 space-y-4"
      >
        <USkeleton class="h-20 w-full" />
        <USkeleton class="h-20 w-full" />
        <USkeleton class="h-20 w-full" />
      </div>
      <div
        v-else-if="formIntegrationsList.length"
        class="my-6"
      >
        <IntegrationCard
          v-for="row in formIntegrationsList"
          :key="row.id"
          :integration="row"
          :form="form"
        />
      </div>
      <div
        v-else
        class="text-gray-500 border shadow-sm rounded-sm p-5 mt-4"
      >
        No integration yet for this form.
      </div>

      <h1 class="font-semibold mt-8 text-xl">
        Add a new integration
      </h1>
      <div
        v-for="(section, sectionName) in sectionsList"
        :key="sectionName"
        class="mb-8"
      >
        <h3 class="text-gray-500">
          {{ sectionName }}
        </h3>
        <div class="flex flex-wrap mt-2 gap-4">
          <IntegrationListOption
            v-for="(sectionItem, sectionItemKey) in section"
            :key="sectionItemKey"
            :integration="sectionItem"
            @select="openIntegration"
          />
        </div>
      </div>
      <IntegrationModal
        v-if="form && selectedIntegrationKey && selectedIntegration"
        :form="form"
        :integration="selectedIntegration"
        :integration-key="selectedIntegrationKey"
        :show="showIntegrationModal"
        @close="closeIntegrationModal"
      />
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue"
import IntegrationModal from "~/components/open/integrations/components/IntegrationModal.vue"

const props = defineProps({
  form: { type: Object, required: true },
})

definePageMeta({
  middleware: "auth",
})

useOpnSeoMeta({
  title: computed(() => props.form 
    ? `Form Integrations - ${props.form.title}`
    : "Form Integrations"
  ),
})

const alert = useAlert()

const { list, availableIntegrations, integrationsBySection } = useFormIntegrations()

// Reactive form ID for proper dependency tracking
const formId = computed(() => props.form?.id)

const { 
  data: formIntegrationsData, 
  isLoading: isIntegrationsLoading,
} = list(formId)

// Get available integrations and sections from the composable
const integrations = availableIntegrations
const sectionsList = integrationsBySection

// Get form integrations list from the query data
const formIntegrationsList = computed(() => formIntegrationsData.value || [])

const showIntegrationModal = ref(false)
const selectedIntegrationKey = ref(null)
const selectedIntegration = ref(null)

const openIntegration = (itemKey) => {
  if (!itemKey || !integrations.value.has(itemKey)) {
    return alert.error("Integration not found")
  }

  const integration = integrations.value.get(itemKey)

  if (integration.coming_soon) {
    return alert.warning("This integration is not available yet")
  }

  if (integration.is_external && integration.url) {
    window.open(integration.url, '_blank')
    return
  }

  selectedIntegrationKey.value = itemKey
  selectedIntegration.value = integrations.value.get(selectedIntegrationKey.value)
  showIntegrationModal.value = true
}

const closeIntegrationModal = () => {
  showIntegrationModal.value = false
  nextTick(() => {
    selectedIntegrationKey.value = null
    selectedIntegration.value = null
  })
}
</script>
