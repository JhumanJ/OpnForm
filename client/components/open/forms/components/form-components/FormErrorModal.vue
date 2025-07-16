<template>
  <UModal
    v-model:open="isOpen"
    :ui="{ content: 'sm:max-w-lg' }"
    title="We couldn't save your form"
  >
    <template #body>
      <div
        v-if="validationErrorResponse"
        class="p-4 border border-red-200 rounded-lg bg-red-50"
      >
        <p
          v-if="validationErrorResponse.message"
          class="text-red-800 mb-3"
          v-text="validationErrorResponse.message"
        />
        <ul class="list-disc list-inside text-red-700 space-y-1">
          <li
            v-for="(err, key) in validationErrorResponse.errors"
            :key="key"
          >
            {{ Array.isArray(err) ? err[0] : err }}
          </li>
        </ul>
      </div>
    </template>

    <template #footer>
      <div class="flex justify-end">
        <UButton
          color="neutral"
          variant="outline"
          @click="closeModal"
        >
          Close
        </UButton>
      </div>
    </template>
  </UModal>
</template>

<script setup>
const props = defineProps({
  show: { type: Boolean, required: true },
  validationErrorResponse: { type: Object, required: false },
})

const emit = defineEmits(['close'])

// Modal state
const isOpen = computed({
  get: () => props.show,
  set: (value) => {
    if (!value) {
      emit('close')
    }
  }
})

// Methods
const closeModal = () => {
  isOpen.value = false
}
</script>
