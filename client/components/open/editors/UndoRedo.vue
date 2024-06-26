<template>
  <UButtonGroup
    size="sm"
    orientation="horizontal"
  >
    <UButton
      :disabled="!canUndo"
      color="white"
      icon="i-material-symbols-undo"
      class="disabled:text-gray-500"
      @click="undo"
    />
    <UButton
      :disabled="!canRedo"
      icon="i-material-symbols-redo"
      color="white"
      class="disabled:text-gray-500"
      @click="redo"
    />
  </UButtonGroup>
</template>

<script setup>
const workingFormStore = useWorkingFormStore()

const { undo, redo, clearHistory } = workingFormStore
const { canUndo, canRedo } = storeToRefs(workingFormStore)

onMounted(() => {
  setTimeout(() => { clearHistory() }, 500)
})
</script>
