<template>
  <UModal
    v-model:open="isModalOpen"
    :ui="{ content: 'sm:max-w-2xl' }"
    title="🎉 Your first submission! Congratulations!"
  >
    <template #body>
      <div class="text-sm text-neutral-500">
        Congratulations on creating your form and receiving your first submission! Your form is now live and ready for action. You can now <span class="font-semibold">share your form</span> with others, or <span class="font-semibold">open your Form submission page</span> to view the submitted data.
      </div>

      <div class="flex gap-2 items-center max-w-full">
        <p class="text-sm w-48 text-neutral-500">
          Share form URL:
        </p>
        <ShareFormUrl
          class="flex-grow my-4"
          :form="form"
        />
      </div>
      <div class="flex py-2 items-center max-w-full">
        <p class="text-sm w-48 text-neutral-500">
          Check your submissions:
        </p>
        <UButton
          color="neutral"
          variant="outline"
          icon="i-heroicons-document"
          target="_blank"
          @click="trackOpenDbClick"
          label="See Submissions"
          trailing-icon=""
        />
      </div>

      <p class="text-neutral-500 font-medium text-sm text-center my-4">
        What's next?
      </p>
      <div class="grid grid-cols-2 gap-2">
        <div
          v-for="(item, i) in helpLinks"
          :key="i"
          role="button"
          class="bg-white shadow-sm border border-neutral-200 rounded-lg p-4 pb-2 items-center justify-center flex flex-col relative hover:bg-neutral-50 dark:hover:bg-neutral-800 group transition-colors"
          @click="item.action"
        >
          <div class="flex justify-center">
            <div class="h-8 w-8 text-neutral-600 group-hover:text-neutral-800 dark:group-hover:text-white transition-colors flex items-center">
              <Icon
                :name="item.icon"
                class=""
                size="40px"
              />
            </div>
          </div>

          <p class="text-neutral-500 font-medium text-xs text-center my-2">
            {{ item.label }}
          </p>
        </div>
      </div>
    </template>
  </UModal>
</template>

<script setup>
import ShareFormUrl from '~/components/open/forms/components/ShareFormUrl.vue'

const props = defineProps({
  show: { type: Boolean, required: true },
  form: { type: Object, required: true }
})

const emit = defineEmits(['close'])

// Modal state
const isModalOpen = computed({
  get() {
    return props.show
  },
  set(value) {
    if (!value) {
      emit("close")
    }
  }
})

const confetti = useConfetti()
const crisp = useCrisp()
const amplitude = useAmplitude()

watch(() => props.show, () => {
  if (props.show) {
    confetti.play()
    useAmplitude().logEvent('form_first_submission_modal_viewed')
  }
})

const helpLinks = computed(() => {
  return [
    {
      'label': 'Help Center',
      'icon': 'heroicons:book-open',
      'action': () => crisp.openHelpdesk()
    },
    {
      'label': 'Contact Us',
      'icon': 'heroicons:chat-bubble-left-right',
      'action': () => { crisp.openAndShowChat() }
    },
  ]
})

const trackOpenDbClick = () => {
  const submissionsUrl = props.form.submissions_url
  window.open(submissionsUrl, '_blank')
  amplitude.logEvent('form_first_submission_modal_open_db_click')
}
</script>