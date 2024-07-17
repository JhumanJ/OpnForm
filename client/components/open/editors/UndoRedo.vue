<template>
  <UButtonGroup
    size="sm"
    orientation="horizontal"
  >
    <UTooltip 
      text="Undo" 
      :shortcuts="[metaSymbol,'Z']"
      :popper="{ placement: 'left' }"
    >
      <UButton
        :disabled="!canUndo"
        color="white"
        icon="i-material-symbols-undo"
        class="disabled:text-gray-500"
        @click="undo"
      />
    </UTooltip>
    <UTooltip 
      text="Redo" 
      :shortcuts="[metaSymbol,'Shift','Z']"
      :popper="{ placement: 'right' }"
    >
      <UButton
        :disabled="!canRedo"
        icon="i-material-symbols-redo"
        color="white"
        class="disabled:text-gray-500"
        @click="redo"
      />
    </UTooltip>
  </UButtonGroup>
</template>

<script setup>
const workingFormStore = useWorkingFormStore()

const { undo, redo, clearHistory } = workingFormStore
const { canUndo, canRedo } = storeToRefs(workingFormStore)

defineShortcuts({
  meta_z: {
    whenever: [canUndo],
    handler: () => {
      undo()
    }
  },
  meta_shift_z: {
    whenever: [canRedo],
    handler: () => {
      redo()
    }
  }
})
const { metaSymbol } = useShortcuts()

onMounted(() => {
  setTimeout(() => { clearHistory() }, 500)
})

</script>
