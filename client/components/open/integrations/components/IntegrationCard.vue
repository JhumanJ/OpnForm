<template>
  <div
    class="text-gray-500 border shadow-sm rounded-sm p-5 mt-4 relative flex items-center"
  >
    <div
      class="flex items-center w-full md:max-w-[240px]"
      :class="{'flex-grow': !actionsComponent}"
    >
      <div
        class="mr-4"
        :class="{
          'text-blue-500': integration.status === 'active',
          'text-gray-400': integration.status !== 'active',
        }"
      >
        <Icon
          :name="integrationTypeInfo.icon"
          size="32px"
        />
      </div>
      <div>
        <div class="flex space-x-3 font-semibold mr-2">
          {{ integrationTypeInfo.name }}
        </div>
        <UBadge
          variant="subtle"  
          :color="integration.status === 'active' ? 'success' : 'neutral'"
          :icon="integration.status === 'active' ? 'heroicons:play' : 'heroicons:pause'"
        >
          {{ integration.status === "active" ? "Active" : "Paused" }}
        </UBadge>
      </div>
    </div>

    <div
      class="flex items-center gap-4 pl-4"
      :class="{'grow': actionsComponent}"
    >
      <component
        :is="actionsComponent"
        v-if="actionsComponent"
        :integration="integration"
        :form="form"
      />

      <div
        v-if="loadingDelete"
        class="pr-4 pt-2 ml-auto"
      >
        <Loader class="h-6 w-6 mx-auto" />
      </div>
      <UDropdownMenu
        v-else
        :items="dropdownItems"
        :content="{ side: 'bottom', align: 'end' }"
      >
        <UButton
          color="neutral"
          variant="outline"
          size="lg"
          icon="heroicons:ellipsis-horizontal"
        />
      </UDropdownMenu>
    </div>

    <IntegrationModal
      v-if="form && integration && integrationTypeInfo"
      :form="form"
      :integration="integrationTypeInfo"
      :integration-key="integration.integration_id"
      :form-integration-id="integration.id"
      :show="showIntegrationModal"
      @close="showIntegrationModal = false"
    />

    <IntegrationEventsModal
      v-if="form && integration"
      :form="form"
      :form-integration-id="integration.id"
      :show="showIntegrationEventsModal"
      @close="showIntegrationEventsModal = false"
    />
  </div>
</template>

<script setup>
import { computed } from "vue"

const props = defineProps({
  integration: {
    type: Object,
    required: true,
  },
  form: {
    type: Object,
    required: true,
  },
})

const alert = useAlert()
const formIntegrationsStore = useFormIntegrationsStore()
const integrations = computed(
  () => formIntegrationsStore.availableIntegrations,
)
const integrationTypeInfo = computed(() =>
  integrations.value.get(props.integration.integration_id),
)

const showIntegrationModal = ref(false)
const showIntegrationEventsModal = ref(false)
const loadingDelete = ref(false)

const actionsComponent = computed(() => {
  if(integrationTypeInfo.value?.actions_file_name || false) {
    return resolveComponent(integrationTypeInfo.value.actions_file_name)
  }

  return null
})

const dropdownItems = computed(() => {
  const items = []

  // Edit option
  if (integrationTypeInfo.value?.is_editable !== false) {
    items.push({
      label: 'Edit',
      icon: 'i-heroicons-pencil',
      onClick: () => {
        showIntegrationModal.value = true
      }
    })
  } else if (integrationTypeInfo.value?.url) {
    items.push({
      label: `Edit on ${integrationTypeInfo.value.name}`,
      icon: 'i-heroicons-pencil',
      to: integrationTypeInfo.value.url
    })
  }

  // Past Events option
  items.push({
    label: 'Past Events',
    icon: 'i-heroicons-clock',
    onClick: () => {
      showIntegrationEventsModal.value = true
    }
  })

  // Delete option
  items.push({
    label: 'Delete Integration',
    icon: 'i-heroicons-trash',
    onClick: () => {
      deleteFormIntegration(props.integration.id)
    },
    color: 'error',
  })

  return items
})

const deleteFormIntegration = (integrationid) => {
  alert.confirm("Do you really want to delete this form integration?", () => {
    opnFetch(
      "/open/forms/{formid}/integration/{integrationid}"
        .replace("{formid}", props.form.id)
        .replace("{integrationid}", integrationid),
      { method: "DELETE" },
    )
      .then((data) => {
        if (data.type === "success") {
          alert.success(data.message)
          formIntegrationsStore.remove(integrationid)
        } else {
          alert.error("Something went wrong!")
        }
      })
      .catch((error) => {
        alert.error(error.data.message)
      })
  })
}
</script>
