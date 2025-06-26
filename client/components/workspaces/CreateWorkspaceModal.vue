<template>
  <UModal
    v-model:open="isOpen"
    :ui="{
      content: 'w-full max-w-md'
    }"
  >
    <template #header>
        <div class="flex items-center gap-3">
            <Icon 
              name="i-heroicons-plus-circle" 
              class="w-8 h-8 text-primary-500" 
            />
            <h3 class="text-lg font-semibold text-gray-900">
              Create Workspace
            </h3>
        </div>

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

const workspacesStore = useWorkspacesStore()
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
const handleSubmit = async () => {
  try {
    loading.value = true
    
    const response = await form.post('/open/workspaces/create')

    // Update store with new workspace
    workspacesStore.save(response.workspace)
    workspacesStore.setCurrentId(response.workspace.id)

    // Show success message
    alert.success('You are now working in your new workspace.', 10000, {
      title: 'Workspace created successfully!'
    })

    // Emit created event and close modal
    emit('created', response.workspace)
    closeModal()
    
  } catch (error) {
    console.error('Error creating workspace:', error)
    alert.error(error.data?.message || 'Something went wrong. Please try again.', 10000, {
      title: 'Error creating workspace'
    })
  } finally {
    loading.value = false
  }
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