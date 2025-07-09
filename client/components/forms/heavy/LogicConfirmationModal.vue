<template>
  <UModal
    v-model:open="isOpen"
    :ui="{ content: 'sm:max-w-lg' }"
    title="Incomplete Form Logic"
  >
    <template #body>
      <p class="text-neutral-700">
        Some logic rules are incomplete or invalid and will be cleaned up to ensure that the form works correctly.
      </p>

      <div class="mt-4 space-y-3">
        <template
          v-for="error in groupedErrors"
          :key="error.fieldId"
        >
          <div class="rounded-lg bg-yellow-50 p-3">
            <div class="flex items-center">
              <Icon
                name="heroicons:exclamation-triangle"
                class="h-5 w-5 text-yellow-400"
              />
              <h4 class="ml-2 text-sm font-medium text-yellow-800">
                Field: {{ error.fieldName }}
              </h4>
            </div>
            <div class="mt-2 text-sm text-yellow-700">
              <ul class="list-disc pl-5 space-y-1">
                <li
                  v-for="(message, index) in error.messages"
                  :key="index"
                >
                  {{ message }}
                </li>
              </ul>
            </div>
          </div>
        </template>
      </div>
    </template>

    <template #footer>
      <div class="flex justify-end space-x-3">
        <UButton
          variant="outline"
          @click="closeModal"
        >
          Cancel
        </UButton>
        <UButton
          color="primary"
          @click="$emit('confirm')"
        >
          Save Anyway (Remove Invalid Logic)
        </UButton>
      </div>
    </template>
  </UModal>
</template>

<script setup>
const props = defineProps({
  isVisible: {
    type: Boolean,
    required: true
  },
  errors: {
    type: Array,
    required: true
  }
})

// Modal state
const isOpen = computed({
  get: () => props.isVisible,
  set: (value) => emit('cancel', value)
})

// Methods
const closeModal = () => {
  isOpen.value = false
}

const emit = defineEmits(['cancel', 'confirm'])

// Group errors by field and format messages
const groupedErrors = computed(() => {
  const errorMap = new Map()

  props.errors.forEach(error => {
    if (!errorMap.has(error.fieldId)) {
      errorMap.set(error.fieldId, {
        fieldId: error.fieldId,
        fieldName: error.fieldName,
        messages: []
      })
    }
    
    const group = errorMap.get(error.fieldId)
    group.messages.push(error.message)
  })

  return Array.from(errorMap.values())
})
</script> 