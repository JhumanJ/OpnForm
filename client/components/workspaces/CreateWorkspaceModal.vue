<template>
  <UModal
    v-model:open="isOpen"
    :ui="{
      content: 'w-full max-w-md'
    }"
  >
    <template #header>
      <div class="flex items-center w-full gap-4 px-2">
        <h2 class="font-semibold">
          Create Workspace
        </h2>
      </div>
      <UButton
        color="neutral"
        variant="outline"
        icon="i-heroicons-question-mark-circle"
        size="sm"
        @click="crisp.openHelpdeskArticle('how-many-workspaces-can-i-create-r4dvt6')"
      >
        Help
      </UButton>
    </template>
    
    <template #body>
        <VForm size="sm">
          <form
            @submit.prevent="handleSubmit"
            @keydown="form.onKeydown($event)"
          >
            <text-input
              name="name"
              :form="form"
              :required="true"
              :disabled="loading"
              label="Workspace Name"
              placeholder="My Workspace"
            />
            <text-input
              name="emoji"
              class="mt-4"
              :form="form"
              :required="false"
              :disabled="loading"
              label="Emoji (optional)"
              placeholder="🚀"
              help="Choose an emoji to represent your workspace"
            />
          </form>
        </VForm>
    </template>
    <template #footer>
      <div class="flex gap-2 w-full">
        <UButton color="neutral" variant="outline" @click="closeModal">Cancel</UButton>
        <UButton block type="submit" :loading="loading" @click="handleSubmit">Create Workspace</UButton>
      </div>
    </template>
  </UModal>
</template>

<script setup>
const emit = defineEmits(['created', 'close'])

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  }
})

const { create } = useWorkspaces()
const appStore = useAppStore()
const crisp = useCrisp()
const alert = useAlert()

// Modal state
const isOpen = computed({
  get: () => props.modelValue,
  set: (value) => emit('close', value)
})

// Form state
const loading = ref(false)
const form = useForm({
  name: '',
  emoji: ''
})

// Handle form submission
const createMutation = create()

const handleSubmit = () => {
  loading.value = true
  
  createMutation.mutateAsync(form.data()).then((newWorkspace) => {
    appStore.setCurrentId(newWorkspace.id)

    // Show success message
    alert.success('You are now working in your new workspace.', 10000, {
      title: 'Workspace created successfully!'
    })

    // Emit created event and close modal
    emit('created', newWorkspace)
    closeModal()
    loading.value = false
  }).catch((error) => {
    console.error('Error creating workspace:', error)
    alert.error(error.data?.message || 'Something went wrong. Please try again.', 10000, {
      title: 'Error creating workspace'
    })
    loading.value = false
  })
}

// Close modal and reset form
const closeModal = () => {
  form.reset()
  isOpen.value = false
}

// Reset form when modal opens
watch(isOpen, (newValue) => {
  if (newValue) {
    form.reset()
  }
})
</script> 