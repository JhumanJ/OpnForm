<template>
  <!-- Backdrop -->
  <div
    v-if="isExpanded"
    class="fixed inset-0 z-40 bg-white/30 dark:bg-gray-900/30 backdrop-blur-sm"
    @click="toggleExpand"
  />

  <!--   Form Preview (desktop only)   -->
  <div
    ref="parent"
    :class="{
      'fixed inset-8 z-50 !flex': isExpanded,
      'bg-gray-50 dark:bg-notion-dark-light hidden md:flex flex-grow p-4 flex-col items-center overflow-y-scroll shadow-inner': !isExpanded
    }"
  >
    <div 
      class="border rounded-lg bg-white dark:bg-notion-dark w-full block shadow-sm transition-all flex flex-col"
      :class="{ 'max-w-5xl': !isExpanded, 'h-full': isExpanded }"
    >
      <div class="w-full bg-white dark:bg-gray-950 border-b border-gray-300 dark:border-blue-900 dark:border-gray-700 rounded-t-lg p-1.5 px-4 flex items-center gap-x-1.5">
        <div class="bg-red-500 rounded-full w-2.5 h-2.5" />
        <div class="bg-yellow-500 rounded-full w-2.5 h-2.5" />
        <div class="bg-green-500 rounded-full w-2.5 h-2.5" />
        <p class="text-sm text-gray-500/70 text-sm ml-4 select-none">
          Form Preview
        </p>
        <div class="flex-grow" />
        <UButton
          v-if="previewFormSubmitted"
          icon="i-heroicons-arrow-path-rounded-square"
          color="white"
          size="xs"
          @click="restartForm"
        >
          Re-start
        </UButton>
        <UTooltip :text="isExpanded ? 'Collapse' : 'Expand'">
          <UButton
            v-track.form_editor_toggle_expand="{toggle: !isExpanded}"
            :icon="isExpanded ? 'i-heroicons-arrows-pointing-in' : 'i-heroicons-arrows-pointing-out'"
            color="white"
            size="xs"
            @click="toggleExpand"
          />
        </UTooltip>
      </div>
      <div class="flex-grow overflow-y-auto">
        <transition
          enter-active-class="linear duration-100 overflow-hidden"
          enter-from-class="max-h-0"
          enter-to-class="max-h-56"
          leave-active-class="linear duration-100 overflow-hidden"
          leave-from-class="max-h-56"
          leave-to-class="max-h-0"
        >
          <div v-if="(form.logo_picture || form.cover_picture)">
            <div v-if="form.cover_picture">
              <div
                id="cover-picture"
                class="h-[30vh] w-full overflow-hidden flex items-center justify-center"
              >
                <img
                  alt="Form Cover Picture"
                  :src="coverPictureSrc(form.cover_picture)"
                  class="object-cover w-full h-[30vh] object-center"
                >
              </div>
            </div>
            <div
              v-if="form.logo_picture"
              class="w-full mx-auto py-5 relative"
              :class="{'pt-20':!form.cover_picture, 'max-w-lg': form && (form.width === 'centered'),'px-7': !isExpanded, 'px-3': isExpanded}"
              :style="{ 'direction': form?.layout_rtl ? 'rtl' : 'ltr' }"
            >
              <img
                alt="Logo Picture"
                :src="coverPictureSrc(form.logo_picture)"
                :class="{'top-5':!form.cover_picture, '-top-10':form.cover_picture}"
                class="max-w-60 h-20 object-contain absolute transition-all"
              >
            </div>
          </div>
        </transition>
        <div v-if="recordLoading">
          <p class="text-center p-4">
            <loader class="h-6 w-6 text-nt-blue mx-auto" />
          </p>
        </div>
        <open-complete-form
          v-show="!recordLoading"
          ref="formPreview"
          class="w-full mx-auto py-5"
          :class="{'max-w-lg': form && (form.width === 'centered'),'px-7': !isExpanded, 'px-3': isExpanded}"
          :form="form"
          :dark-mode="darkMode"
          :mode="formMode"
          @restarted="previewFormSubmitted=false"
          @submitted="previewFormSubmitted=true"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, watch, onMounted } from 'vue'
import OpenCompleteForm from '../../OpenCompleteForm.vue'
import {handleDarkMode, useDarkMode} from "~/lib/forms/public-page.js"
import { useRecordsStore } from '~/stores/records'
import { useWorkingFormStore } from '~/stores/working_form'
import { storeToRefs } from 'pinia'
import { FormMode } from "~/lib/forms/FormModeStrategy.js"

const recordsStore = useRecordsStore()
const workingFormStore = useWorkingFormStore()

const parent = ref(null)
const formPreview = ref(null)
const previewFormSubmitted = ref(false)
const isExpanded = ref(false)

const { content: form } = storeToRefs(workingFormStore)
const recordLoading = computed(() => recordsStore.loading)
const darkMode = useDarkMode(parent)

// Use PREVIEW mode when not expanded, TEST mode when expanded
const formMode = computed(() => isExpanded.value ? FormMode.TEST : FormMode.PREVIEW)

defineShortcuts({
  escape: {
    usingInput: true,
    handler: () => {
      if (isExpanded.value) {
        isExpanded.value = false
      }
    }
  }
})

watch(() => form.value.dark_mode, () => {
  handleDarkModeChange()
})

onMounted(() => {
  handleDarkModeChange()
})

function coverPictureSrc(val) {
  try {
    // Is valid url
    new URL(val)
  } catch {
    // Is file
    return URL.createObjectURL(val)
  }
  return val
}

function handleDarkModeChange() {
  handleDarkMode(form.value.dark_mode, parent.value)
}

function restartForm() {
  previewFormSubmitted.value = false
  formPreview.value.restart()
}

function toggleExpand() {
  isExpanded.value = !isExpanded.value
}
</script>

<style scoped>
.fixed {
  transition: all 0.3s ease-in-out;
}
</style>