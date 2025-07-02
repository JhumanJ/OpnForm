<template>
  <div>
    <UButton
      v-track.regenerate_form_link_click="{
        form_id: form.id,
        form_slug: form.slug,
      }"
      block
      size="lg"
      icon="heroicons:link"
      variant="outline"
      @click="showGenerateFormLinkModal = true"
      label="Regenerate link"
    />

    <!--  Regenerate form link modal  -->
    <UModal
      v-model:open="isModalOpen"
      :ui="{ content: 'sm:max-w-2xl' }"
      title="Generate new form link"
    >
      <template #body>
        <p>
          You can choose between two different URL formats for your form.
          <span class="font-semibold">Be careful, changing your form URL is not a reversible
            operation</span>. Make sure to update your form URL everywhere where it's used.
        </p>
        <div class="border-t py-4 mt-4">
          <h3 class="text-xl text-gray-700 font-semibold">
            Human Readable URL
          </h3>
          <p>
            If your users are going to see this url, you might want to make nice
            and readable. Example:
          </p>
          <p class="text-gray-600 border p-4 bg-gray-50 rounded-md mt-4">
            https://opnform.com/forms/contact
          </p>
          <div class="text-center mt-4">
            <UButton
              :loading="loadingNewLink"
              color="primary"
              @click="regenerateLink('slug')"
              label="Generate a Human Readable URL"
            />
          </div>
        </div>
        <div class="border-t pt-4 mt-4">
          <h3 class="text-xl text-gray-700 font-semibold">
            Random ID URL
          </h3>
          <p>
            If your user are not going to see your form url (if it's embedded),
            and if you prefer to have a random non-guessable URL. Example:
          </p>
          <p class="text-gray-600 p-4 border bg-gray-50 rounded-md mt-4">
            https://opnform.com/forms/b4417f9c-34ae-4421-8006-832ee47786e7
          </p>
          <div class="text-center mt-4">
            <UButton
              :loading="loadingNewLink"
              color="primary"
              @click="regenerateLink('uuid')"
              label="Generate a Random ID URL"
            />
          </div>
        </div>
      </template>
    </UModal>
  </div>
</template>

<script setup>
const props = defineProps({
  form: { type: Object, required: true },
})

const formsStore = useFormsStore()
const router = useRouter()

const loadingNewLink = ref(false)
const showGenerateFormLinkModal = ref(false)

// Modal state
const isModalOpen = computed({
  get() {
    return showGenerateFormLinkModal.value
  },
  set(value) {
    showGenerateFormLinkModal.value = value
  }
})

const formEndpoint = computed(() => "/open/forms/{id}")

const regenerateLink = (option) => {
  if (loadingNewLink.value) return
  loadingNewLink.value = true
  opnFetch(
    formEndpoint.value.replace("{id}", props.form.id) +
      "/regenerate-link/" +
      option,
    { method: "PUT" },
  )
    .then((data) => {
      formsStore.save(data.form)
      router.push({
        name: "forms-slug-show-share",
        params: { slug: data.form.slug },
      })
      useAlert().success(data.message)
      loadingNewLink.value = false
    })
    .finally(() => {
      showGenerateFormLinkModal.value = false
    })
}
</script>
