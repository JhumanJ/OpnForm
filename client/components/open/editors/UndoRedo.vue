<template>
  <UButtonGroup
    size="sm"
    orientation="horizontal"
  >
    <UTooltip text="Undo" :shortcuts="undoShortcut" :popper="{ placement: 'left' }">
      <UButton
        :disabled="!canUndo"
        color="white"
        icon="i-material-symbols-undo"
        class="disabled:text-gray-500"
        @click="undo"
      />
    </UTooltip>
    <UTooltip text="Redo" :shortcuts="redoShortcut" :popper="{ placement: 'right' }">
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

const undoShortcut = computed(() => {
  return getOS() == 'macOS' ? ['⌘', 'Z'] : ['Ctrl', 'Z']
})

const redoShortcut = computed(() => {
  return getOS() == 'macOS' ? ['⌘', 'Shift', 'Z'] : ['Ctrl', 'Shift', 'Z']
})

onMounted(() => {
  setTimeout(() => { clearHistory() }, 500)
})

const  getOS = ()=> {
  if (navigator.userAgentData) {
    // Modern method
    return navigator.userAgentData.platform;
  } else {
    // Fallback for older browsers
    const userAgent = navigator.userAgent.toLowerCase();
    if (userAgent.indexOf("mac") > -1) return "macOS";
    if (userAgent.indexOf("win") > -1) return "Windows";
    if (userAgent.indexOf("linux") > -1) return "Linux";
    return "Unknown";
  }
}
</script>
