<template>
    <slot v-if="!error" />
    <slot
      v-else
      name="error"
      :error="error"
      :clear-error="clearError"
    />
  </template>
  
<script setup>
    const error = ref()
    const emit = defineEmits(['on-error'])
    function clearError() {
        error.value = undefined
    }
    onErrorCaptured(err => {
        error.value = err
        emit('on-error', err)
        return false
    })
    const route = useRoute()
    watch(
        () => route.fullPath,
        () => {
            error.value = undefined
        }
    )
    defineExpose({
        clearError
    })
</script>
  