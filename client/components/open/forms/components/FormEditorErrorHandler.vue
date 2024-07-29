<template>
    <ErrorBoundary @on-error="onFormEditorError">
  
      <template #error="{ error, clearError }">
        <div class="flex-grow w-full flex items-center justify-center flex-col gap-4">
          <h1 class="text-blue-800 text-2xl font-medium">Oops! Something went wrong.</h1>
          <p class="text-gray-500 max-w-lg text-center">It looks like your last action caused an issue on our side. We
            apologize for
            the
            inconvenience.</p>
          <div class="flex gap-2 mt-4">
            <UButton icon="i-material-symbols-undo" @click="clearEditorError(error, clearError)">Go back one step
            </UButton>
            <UButton variant="outline" icon="i-heroicons-chat-bubble-left-right-16-solid"
                     @click="onErrorContact(error)">
              Report this error
            </UButton>
          </div>
        </div>
      </template>
  
      <slot />
    </ErrorBoundary>
  </template>
  
  <script setup>
  import { computed } from 'vue'
  const crisp = useCrisp()
  const workingFormStore = useWorkingFormStore()
  const authStore = useAuthStore()
  const form = storeToRefs(workingFormStore).content
  const user = computed(() => authStore.user)
  // Clear error and go back 1 step in history
  const clearEditorError = (error, clearError) => {
    crisp.enableChatbot()
    workingFormStore.undo()
    clearError()
  }
  const onFormEditorError = (error) => {
    console.error('Form Editor Error Handled', error)
    crisp.pauseChatBot()
    const eventData = {
      message: error.message,
      // take first 200 characters
      stack: error.stack.substring(0, 100)
    }
    try {
      crisp.pushEvent('form-editor-error', eventData)
    } catch (e) {
      console.error('Failed to send event to crisp', e, eventData)
    }
  }
  const onErrorContact = (error) => {
    console.log('Contacting via crisp for an error', error)
    crisp.pauseChatBot()
    let errorReport = 'Hi there, I have a technical issue with the form editor.'
    if (form.value.slug) {
      errorReport += ` The form I am editing is: \`${form.value.slug}\`.`
    }
    errorReport += ` And here are technical details about the error: \`\`\`${error.stack}\`\`\``
    try {
      crisp.openAndShowChat(errorReport)
      crisp.showMessage(`Hi there, we're very sorry to hear you experienced an issue with NoteForms.
          We'll be in touch about it very soon! In the meantime, I recommend that you try going back one step, and save your changes.`, 2000)
    } catch (e) {
      console.error('Crisp error', e)
    }
  }
  </script>
  