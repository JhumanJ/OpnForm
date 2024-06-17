<template>
  <div class="flex">
    <v-button
      v-if="canUndo"
      size="small"
      color="white"
      class="flex-glow"
      @click="undo"
    >
      <Icon
        name="material-symbols:undo"
        class="w-5 h-5"
      /> Undo
    </v-button>
    <v-button
      v-if="canRedo"
      size="small"
      color="white"
      class="ml-2"
      @click="redo"
    >
      <Icon
        name="material-symbols:redo"
        class="w-5 h-5"
      /> Redo
    </v-button>
  </div>
</template>

<script setup>
const workingFormStore = useWorkingFormStore()

const { undo, redo, clear } = workingFormStore
const { canUndo, canRedo } = storeToRefs(workingFormStore)

onMounted(() => {
  setTimeout(() => { clear() }, 500)
})
</script>
