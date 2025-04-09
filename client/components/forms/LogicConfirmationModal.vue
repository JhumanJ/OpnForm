<template>
  <Modal
    :show="isVisible"
    compact-header
    icon-color="red"
    @close="$emit('cancel')"
  >
    <template #title>
      Invalid Form Logic Detected
    </template>
    <template #icon>
      <Icon
        name="heroicons:exclamation-triangle"
        class="h-7 w-7"
      />
    </template>

    <p class="text-sm text-gray-500">
      Some logic rules are incomplete or invalid and will be removed to ensure the form works correctly.
    </p>

    <div class="mt-4 space-y-3">
      <template
        v-for="error in groupedErrors"
        :key="error.fieldId"
      >
        <div class="rounded-lg bg-red-50 p-3">
          <div class="flex items-center">
            <Icon
              name="heroicons:exclamation-triangle"
              class="h-5 w-5 text-red-400"
            />
            <h4 class="ml-2 text-sm font-medium text-red-800">
              Field: {{ error.fieldName }}
            </h4>
          </div>
          <div class="mt-2 text-sm text-red-700">
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

    <template #footer>
      <div class="flex justify-end space-x-3">
        <button
          type="button"
          class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
          @click="$emit('cancel')"
        >
          Cancel
        </button>
        <button
          type="button"
          class="inline-flex justify-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
          @click="$emit('confirm')"
        >
          Save Anyway (Remove Invalid Logic)
        </button>
      </div>
    </template>
  </Modal>
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

defineEmits(['cancel', 'confirm'])

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