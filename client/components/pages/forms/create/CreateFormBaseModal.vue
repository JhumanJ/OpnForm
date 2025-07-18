<template>
  <UModal
    v-model:open="isOpen"
    :ui="{ content: state === 'default' ? 'sm:max-w-3xl' : 'sm:max-w-2xl' }"
    :title="state === 'default' ? 'Choose a base for your form' : 'AI-powered form generator'"
    :dismissible="!aiForm.busy"
  >
    <template #body>
      <div
        v-if="state === 'default'"
        class="grid grid-cols-1 sm:grid-cols-3 gap-4"
      >
        <div
          v-track.select_form_base="{ base: 'contact-form' }"
          role="button"
          class="rounded-md border p-6 flex flex-col items-center cursor-pointer hover:bg-neutral-50"
          @click="$emit('close')"
        >
          <div class="p-4">
            <UIcon
              name="i-heroicons-envelope"
              class="w-8 h-8 text-blue-500"
            />
          </div>
          <p class="font-medium">
            Simple contact form
          </p>
        </div>
        <div
          v-if="useFeatureFlag('ai_features')"
          v-track.select_form_base="{ base: 'ai' }"
          class="rounded-md border p-6 flex flex-col items-center cursor-pointer hover:bg-neutral-50"
          role="button"
          @click="state = 'ai'"
        >
          <div class="p-4">
            <UIcon
              name="i-heroicons-bolt"
              class="w-8 h-8 text-blue-500"
            />
          </div>
          <p class="font-medium text-blue-700">
            AI Form Generator
          </p>
        </div>
        <div
          class="rounded-md border p-6 flex flex-col items-center cursor-pointer hover:bg-neutral-50 relative"
        >
          <div class="p-4">
            <UIcon
              name="i-heroicons-squares-2x2"
              class="w-8 h-8 text-blue-500"
            />
          </div>
          <p class="font-medium">
            Browse templates <Icon name="heroicons:arrow-top-right-on-square-20-solid" class="w-3 h-3 text-neutral-500" />
          </p>
          <NuxtLink
            v-track.select_form_base="{ base: 'template' }"
            :to="{ name: 'templates' }"
            class="absolute inset-0"
          />
        </div>
      </div>
      <div v-else-if="state === 'ai'">
        <text-area-input
          label="Form Description"
          :disabled="loading ? true : null"
          :form="aiForm"
          name="form_prompt"
          help="Give us a description of the form you want to build (the more details the better)"
          placeholder="A simple contact form, with a name, email and message field"
        />
        <UButton
          class="mt-4"
          block
          :loading="loading"
          @click="generateForm"
          label="Generate a form"
        />
        <p class="text-neutral-500 text-xs text-center mt-1">
          ~30 sec
        </p>
        <div
          v-if="loading"
          class="my-4"
        >
          <AIFormLoadingMessages />
        </div>
        <div class="flex gap-2 mt-4">
          <UButton
            variant="ghost"
            color="neutral"
            icon="i-heroicons-arrow-left"
            @click="state = 'default'"
            label="Back to form types"
          />
        </div>
      </div>
    </template>
  </UModal>
</template>

<script setup>
import AIFormLoadingMessages from "~/components/open/forms/components/AIFormLoadingMessages.vue"
import { formsApi } from "~/api/forms"

const props = defineProps({
  show: { type: Boolean, required: true },
})

const emit = defineEmits(["close", "form-generated"])

// Modal state
const isOpen = computed({
  get() {
    return props.show
  },
  set(value) {
    if (!value) {
      emit("close")
    }
  }
})

const state = ref("default")
const aiForm = useForm({
  form_prompt: "",
})
const loading = ref(false)

const generateForm = () => {
  if (loading.value) return

  loading.value = true
  aiForm
    .post("/forms/ai/generate")
    .then((response) => {
      useAlert().success(response.message)
      fetchGeneratedForm(response.ai_form_completion_id)
    })
    .catch((error) => {
      console.error(error)
      loading.value = false
      state.value = "ai"
    })
}

const fetchGeneratedForm = (generationId) => {
  // check every 4 seconds if form is generated
  setTimeout(() => {
    formsApi.ai.get(generationId)
      .then((data) => {
        if (data.ai_form_completion.status === "completed") {
          useAlert().success(data.message)
          emit(
            "form-generated",
            JSON.parse(data.ai_form_completion.result),
          )
          emit("close")
        } else if (data.ai_form_completion.status === "failed") {
          useAlert().error("Something went wrong, please try again.")
          state.value = "default"
          loading.value = false
        } else {
          fetchGeneratedForm(generationId)
        }
      })
      .catch((error) => {
        if (error?.data?.message) {
          useAlert().error(error.data.message)
        }
        state.value = "default"
        loading.value = false
      })
  }, 4000)
}
</script>
