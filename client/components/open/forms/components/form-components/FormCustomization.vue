<template>
  <div class="px-4 pb-4">
    <EditorSectionHeader
      icon="heroicons:paint-brush-16-solid"
      title="Basic Appearance"
      :show-line="false"
    />

    <select-input
      name="theme"
      class="mt-4"
      :options="[
        { name: 'Default', value: 'default' },
        { name: 'Notion', value: 'notion' },
        { name: 'Simple (no shadows)', value: 'simple' },
      ]"
      :form="form"
      label="Form Theme"
    />
    <color-input
      name="color"
      :form="form"
    >
      <template #help>
        <InputHelp>
          <span class="text-gray-500">
            Color (for buttons & inputs border) - <a
              class="text-blue-500"
              href="#"
              @click.prevent="form.color = DEFAULT_COLOR"
            >Reset</a>
          </span>
        </InputHelp>
      </template>
    </color-input>
    <select-input
      name="dark_mode"
      :options="[
        { name: 'Auto', value: 'auto' },
        { name: 'Light Mode', value: 'light' },
        { name: 'Dark Mode', value: 'dark' },
      ]"
      :form="form"
      label="Color Mode"
      help="Use Auto to use device system preferences"
    />

    <EditorSectionHeader
      icon="octicon:typography-16"
      title="Typography"
    />
    <template v-if="useFeatureFlag('services.google.fonts')">
      <label class="text-gray-700 font-medium text-sm">Font Style</label>
      <v-button
        color="white"
        class="w-full mb-4"
        size="small"
        @click="showGoogleFontPicker = true"
      >
        <span :style="{ 'font-family': (form.font_family?form.font_family+' !important':null) }">
          {{ form.font_family || 'Default' }}
        </span>
      </v-button>
      <GoogleFontPicker
        :show="showGoogleFontPicker"
        :font="form.font_family || null"
        @close="showGoogleFontPicker=false"
        @apply="onApplyFont"
      />
    </template>
    <toggle-switch-input
      name="uppercase_labels"
      :form="form"
      label="Uppercase Input Labels"
    />

    <select-input
      name="language"
      class="mt-4"
      searchable
      :options="availableLocales"
      :form="form"
      label="Form Language"
    />

    <EditorSectionHeader
      icon="heroicons:rectangle-stack-16-solid"
      title="Layout & Sizing"
    />
    <div class="flex space-x-4 justify-stretch">
      <select-input
        name="size"
        class="flex-grow"
        :options="[
          { name: 'Small', value: 'sm' },
          { name: 'Medium', value: 'md' },
          { name: 'Large', value: 'lg' },
        ]"
        :form="form"
        label="Input Size"
      />

      <select-input
        name="border_radius"
        class="flex-grow"
        :options="[
          { name: 'None', value: 'none' },
          { name: 'Small', value: 'small' },
          { name: 'Full', value: 'full' },
        ]"
        :form="form"
        label="Input Roundness"
      />
    </div>
    <select-input
      name="width"
      :options="[
        { name: 'Centered', value: 'centered' },
        { name: 'Full Width', value: 'full' },
      ]"
      :form="form"
      label="Form Width"
      help="Useful when embedding your form"
    />
    
    <ToggleSwitchInput
      name="layout_rtl"
      :form="form"
      class="mt-4"
      label="Right-to-Left Layout"
      help="Adjusts layout for RTL languages"
    />

    <EditorSectionHeader
      icon="heroicons:tag-16-solid"
      title="Branding & Media"
    />
    <image-input
      name="logo_picture"
      :form="form"
      label="Logo"
      help="Not visible when form is embedded"
      :required="false"
    />
    <image-input
      name="cover_picture"
      :form="form"
      label="Cover image"
      help="Not visible when form is embedded"
    />
    <toggle-switch-input
      name="no_branding"
      :form="form"
      @update:model-value="onChangeNoBranding"
    >
      <template #label>
        <span class="text-sm">
          Remove OpnForm Branding
        </span>
        <pro-tag
          upgrade-modal-title="Upgrade today to remove OpnForm branding"
          class="-mt-1"
        />
      </template>
    </toggle-switch-input>

    <EditorSectionHeader
      icon="heroicons:cog-6-tooth-16-solid"
      title="Advanced Options"
    />
    <toggle-switch-input
      name="show_progress_bar"
      :form="form"
      label="Show progress bar"
      :help="
        form.show_progress_bar
          ? 'The bar is at the top of the page (above navigation in this editor) or below the title when embedded'
          : ''
      "
    />
    <toggle-switch-input
      name="transparent_background"
      :form="form"
      label="Transparent Background"
      help="Only applies when form is embedded"
    />
    <toggle-switch-input
      name="confetti_on_submission"
      :form="form"
      label="Confetti on successful submisison"
      @update:model-value="onChangeConfettiOnSubmission"
    />
    <ToggleSwitchInput
      name="auto_focus"
      :form="form"
      label="Auto focus first input on page"
    />
  </div>
</template>

<script setup>
import EditorSectionHeader from "./EditorSectionHeader.vue"
import { useWorkingFormStore } from "../../../../../stores/working_form"
import GoogleFontPicker from "../../../editors/GoogleFontPicker.vue"
import ProTag from "~/components/global/ProTag.vue"
import { DEFAULT_COLOR } from "@/composables/forms/initForm"


const workingFormStore = useWorkingFormStore()
const subscriptionModalStore = useSubscriptionModalStore()
const authStore = useAuthStore()
const workspacesStore = useWorkspacesStore()
const form = storeToRefs(workingFormStore).content
const isMounted = ref(false)
const confetti = useConfetti()
const showGoogleFontPicker = ref(false)
const { $i18n } = useNuxtApp()

const user = computed(() => authStore.user)
const workspace = computed(() => workspacesStore.getCurrent)

const isPro = computed(() => {
  if (!useFeatureFlag('billing.enabled')) return true
  if (!user.value || !workspace.value) return false
  return workspace.value.is_pro
})

const availableLocales = computed(() => {
  return $i18n.locales?.value.map(locale => ({ name: locale.name, value: locale.code })) ?? []
})

onMounted(() => {
  isMounted.value = true
})

const onChangeConfettiOnSubmission = (val) => {
  if (isMounted.value && val) {
    confetti.play()
  }
}

const onChangeNoBranding = (val) => {
  if (!isPro.value && val) {
    subscriptionModalStore.setModalContent("Upgrade today to remove OpnForm branding")
    subscriptionModalStore.openModal()
    setTimeout(() => {
      form.value.no_branding = false
    }, 300)
  } 
}

const onApplyFont = (val) => {
  form.value.font_family = val
  showGoogleFontPicker.value = false
}
</script>
