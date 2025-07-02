<template>
  <div>
    <UButton
      v-track.share_embed_form_popup_click="{
        form_id: form.id,
        form_slug: form.slug,
      }"
      block
      size="lg"
      icon="mdi:code-braces"
      variant="outline"
      label="Embed as popup"
      @click="showEmbedFormAsPopupModal = true"
    />

    <UModal
      v-model:open="isModalOpen"
      :ui="{ content: 'sm:max-w-2xl' }"
      title="Add the popup to your website"
    >
      <template #body>
        <h3 class="text-xl font-semibold mb-2">
          Demo
        </h3>
        <p class="pb-6">
          A live preview of your form popup was just added to this page.
          <span class="font-semibold text-blue-800">Click on the button on the bottom
            {{ advancedOptions.position }} corner to try it</span>.
        </p>

        <h3 class="border-t text-xl font-semibold mb-2 pt-6">
          How does it work?
        </h3>
        <p>
          Paste the following code snippet in the <b>&lt;head&gt;</b> section of
          your website.
        </p>

        <div class="border border-blue-300 bg-blue-50 dark:bg-notion-dark-light rounded-md p-4 mb-5 w-full mx-auto mt-4 select-all">
          <div class="flex items-center">
            <p class="select-all text-blue-500 flex-grow break-all">
              {{ embedPopupCode }}
            </p>
            <div
              class="hover:bg-blue-100 rounded-sm transition-colors cursor-pointer"
              @click="copyToClipboard"
            >
                <Icon 
                  name="heroicons:clipboard-document" 
                  class="h-6 w-6 text-blue-500" 
                />
            </div>
          </div>
        </div>

        <collapse
          class="py-5 w-full border rounded-md px-4"
          :model-value="true"
        >
          <template #title>
            <div class="flex">
              <h3 class="font-semibold block text-lg">
                Advanced options
              </h3>
            </div>
          </template>
          <div class="border-t mt-4 -mx-4" />
          <ColorInput
            v-model="advancedOptions.bgcolor"
            name="bgcolor"
            class="mt-4"
            label="Circle Background Color"
          />
          <TextInput
            v-model="advancedOptions.emoji"
            name="emoji"
            class="mt-4"
            label="Emoji"
            :max-char-limit="2"
          />
          <FlatSelectInput
            v-model="advancedOptions.position"
            name="position"
            class="mt-4"
            label="Position"
            :options="[
              { name: 'Bottom Right', value: 'right' },
              { name: 'Bottom Left', value: 'left' },
            ]"
          />
          <TextInput
            v-model="advancedOptions.width"
            name="width"
            class="mt-4"
            label="Form pop max width (px)"
            native-type="number"
          />
        </collapse>
      </template>
    </UModal>
  </div>
</template>

<script setup>
import { ref, defineProps, computed } from "vue"
import { appUrl } from "~/lib/utils.js"

const { copy } = useClipboard()
const crisp = useCrisp()
const props = defineProps({
  form: { type: Object, required: true },
})

const embedScriptUrl = "/widgets/embed-min.js"
const showEmbedFormAsPopupModal = ref(false)

// Modal state
const isModalOpen = computed({
  get() {
    return showEmbedFormAsPopupModal.value
  },
  set(value) {
    if (!value) {
      onClose()
    }
  }
})

const advancedOptions = ref({
  emoji: "💬",
  position: "right",
  bgcolor: "#3B82F6",
  width: "500",
})

const shareUrl = computed(() => {
  return props.form.share_url
})
const embedPopupCode = computed(() => {
  const nfData = {
    formurl: shareUrl.value,
    emoji: advancedOptions.value.emoji,
    position: advancedOptions.value.position,
    bgcolor: advancedOptions.value.bgcolor,
    width: advancedOptions.value.width,
  }
  previewPopup(nfData)
  return (
    "<script async data-nf='" +
    JSON.stringify(nfData) +
    "' src='" +
    appUrl(embedScriptUrl) +
    "'></scrip" +
    "t>"
  )
})

onMounted(() => {
  advancedOptions.value.bgcolor = props.form.color
})

const onClose = () => {
  removePreview()
  crisp.showChat()
  showEmbedFormAsPopupModal.value = false
}
const copyToClipboard = () => {
  if (import.meta.server) return
  copy(embedPopupCode.value)
  useAlert().success("Copied!")
}
const removePreview = () => {
  if (import.meta.server) return
  const oldP = document.head.querySelector("#nf-popup-preview")
  if (oldP) {
    oldP.remove()
  }
  const oldM = document.body.querySelector(".nf-main")
  if (oldM) {
    oldM.remove()
  }
}
const previewPopup = (nfData) => {
  if (import.meta.server) return
  if (!showEmbedFormAsPopupModal.value) {
    return
  }

  // Remove old preview, if there
  removePreview()

  // Hide crisp
  crisp.hideChat()

  // Add new preview
  const el = document.createElement("script")
  el.id = "nf-popup-preview"
  el.async = true
  el.src = embedScriptUrl
  el.setAttribute("data-nf", JSON.stringify(nfData))
  document.head.appendChild(el)
}
</script>
