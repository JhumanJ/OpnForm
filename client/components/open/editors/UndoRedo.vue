<template>
  <UButtonGroup
    v-if="canRedo || canUndo"
    size="sm"
    orientation="horizontal"
  >
    <UButton
      :disabled="!canUndo"
      color="white"
      icon="i-material-symbols-undo"
      @click="undo"
    />
    <UButton
      :disabled="!canRedo"
      icon="i-material-symbols-redo"
      color="white"
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
