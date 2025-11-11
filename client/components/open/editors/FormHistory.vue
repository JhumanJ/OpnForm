<template>
  <UTooltip 
    text="Form History" 
    :content="{ side: 'left' }" 
    arrow
  >
    <UButton
      :disabled="!versions.length"
      size="sm"
      color="neutral"
      variant="outline"
      class="disabled:text-neutral-500 shadow-none"
      icon="i-material-symbols-history"
      @click="isHistoryModalOpen=true"
    />
  </UTooltip>

  <UModal
    v-model:open="isHistoryModalOpen"
    title="Form History"
    description="View the history of changes to your form"
    :ui="{ content: 'sm:max-w-2xl' }"
    @close="isHistoryModalOpen = false"
  >
    <template #body>
      <div class="space-y-4">
        <div
          v-for="version in versions"
          :key="version.id"
          class="flex items-start gap-3 p-3 border rounded-lg"
        >
          <img
            :src="version.user.photo_url"
            :alt="version.user.name"
            class="w-8 h-8 rounded-full"
          />
          <div class="min-w-0 flex-1">
            <div class="flex items-center justify-between gap-2">
              <div class="truncate">
                <div class="text-sm font-medium text-neutral-900">
                  {{ version.user.name }} - {{ version.id }}
                </div>
                <div class="text-xs text-neutral-500">
                  {{ formatDate(version.created_at) }}
                </div>
              </div>
              <UButton
                variant="outline"
                label="Restore"
                icon="i-heroicons-arrow-path"
                @click="onRestore(version)"
              />
            </div>

            <div class="mt-2 text-sm text-neutral-800">
              Edited form
            </div>

            <div class="mt-2 flex flex-wrap gap-2">
              <UBadge
                v-for="tag in getTags(version)"
                :key="tag.key"
                size="sm"
                variant="subtle"
                color="neutral"
              >
                {{ tag.label }}
              </UBadge>
            </div>
          </div>
        </div>
      </div>
    </template>
  </UModal>
</template>

<script setup>
import { versionsApi } from '~/api/versions'
import { format } from 'date-fns'

const alert = useAlert()
const { openSubscriptionModal } = useAppModals()
const workingFormStore = useWorkingFormStore()

const { content: form } = storeToRefs(workingFormStore)
const isHistoryModalOpen = ref(false)
const versions = ref([])

onMounted(() => {
  if (form.value) {
    fetchVersions()
  }
})

const fetchVersions = async () => {
  const response = await versionsApi.list('form', form.value.id)
  versions.value = response || []
}

const formatDate = (val) => {
  try {
    return format(new Date(val), 'MMM dd h:mm a')
  } catch {
    return ''
  }
}

const getTags = (version) => {
  const diff = version?.diff || {}
  const tags = []
  const entries = Object.entries(diff)
  if (!entries.length) {
    tags.push({ key: 'no-changes', label: 'No visible changes' })
    return tags
  }

  for (const [key, change] of entries) {
    const label = humanizeKey(key, change)
    tags.push({ key, label })
  }

  return tags
}

const humanizeKey = (key, change) => {
  const words = String(key).replace(/[_-]+/g, ' ').trim().toLowerCase()
  const capitalized = words.charAt(0).toUpperCase() + words.slice(1)
  if (typeof change?.new === 'boolean' || typeof change?.old === 'boolean') {
    return `${capitalized} ${change?.new ? 'enabled' : 'disabled'}`
  }
  return `${capitalized} changed`
}

const onRestore = async (version) => {
  if(!form.value.is_pro) {
    openSubscriptionModal({ modal_title: 'Upgrade to restore form history' })
    return
  }

  alert.confirm('Are you sure you want to restore this version?', () => restoreVersion(version))
}

const restoreVersion = async (version) => {
  await versionsApi.restore(version.id).then((response) => {
    useAlert().success(response.message)
    fetchVersions()
  })
  .catch((error) => {
    console.error(error)
    alert.error(error.data?.message || 'Failed to restore version')
  })
}
</script>
