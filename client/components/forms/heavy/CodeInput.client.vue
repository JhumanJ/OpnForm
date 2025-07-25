<template>
  <input-wrapper v-bind="inputWrapperProps">
    <template #label>
      <slot name="label" />
    </template>

    <template #help>
      <slot name="help" />
    </template>

    <div
      :class="[
        'h-40 max-h-96 overflow-y-auto relative',
        theme.CodeInput.input,
        theme.CodeInput.borderRadius,
        {
          '!ring-red-500 !ring-2 !border-transparent': hasError,
          '!cursor-not-allowed !bg-neutral-200 dark:!bg-neutral-800': disabled,
        },
      ]"
    >
      <!-- Fullscreen button -->
      <UTooltip text="Open in fullscreen" :popper="{ placement: 'left' }">
        <UButton
          v-if="allowFullscreen"
          @click="openFullscreen"
          :disabled="disabled"
          variant="ghost"
          color="neutral"
          size="xs"
          icon="i-heroicons-arrows-pointing-out"
          class="absolute top-2 right-2 z-10 !bg-white/80 dark:!bg-neutral-800/80 hover:!bg-white dark:hover:!bg-neutral-800 !opacity-70 hover:!opacity-100 disabled:!opacity-30"
          :ui="{ rounded: 'rounded-md' }"
        />
      </UTooltip>

      <ClientOnly>
        <codemirror
          :id="id ? id : name"
          v-model="compVal"
          :disabled="disabled ? true : null"
          :extensions="extensions"
          :style="inputStyle"
          :name="name"
          :tab-size="4"
          :placeholder="placeholder"
        />
        <template #fallback>
          <USkeleton class="w-full h-10" />
        </template>
      </ClientOnly>

      <UModal v-if="allowFullscreen" v-model:open="isFullscreen" fullscreen>
        <template #content>
        <div class="flex flex-col h-full">
          <div class="flex items-center justify-between p-4 border-b">
            <div>
              <h3 class="text-lg font-medium text-neutral-900 dark:text-white">
                {{ label || 'Code Editor' }}
              </h3>
            </div>
            <UButton
              @click="closeFullscreen"
              variant="outline"
              color="neutral"
              size="sm"
              icon="i-heroicons-x-mark"
              :ui="{ rounded: 'rounded-md' }"
              label="Exit fullscreen"
            />
          </div>

          <!-- Editor -->
          <div class="flex-1 overflow-hidden">
            <ClientOnly>
              <codemirror
                v-model="compVal"
                :disabled="disabled ? true : null"
                :extensions="extensions"
                :style="{ height: '100%' }"
                :name="name"
                :tab-size="4"
                :placeholder="placeholder"
              />
              <template #fallback>
                <USkeleton class="w-full h-full" />
              </template>
            </ClientOnly>
          </div>
        </div>
        </template>
      </UModal>
    </div>

    <template #error>
      <slot name="error" />
    </template>
  </input-wrapper>
</template>

<script setup>
import { Codemirror } from "vue-codemirror"
import { html } from "@codemirror/lang-html"
import { inputProps, useFormInput } from "../useFormInput.js"

const props = defineProps({
  ...inputProps,
  allowFullscreen: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['update:modelValue', 'focus', 'blur'])

const extensions = [html()]
const isFullscreen = ref(false)

const openFullscreen = () => {
  isFullscreen.value = true
}

const closeFullscreen = () => {
  isFullscreen.value = false
}

// Handle keyboard shortcuts
defineShortcuts({
  escape: {
    usingInput: true,
    handler: () => {
      if (isFullscreen.value) {
        closeFullscreen()
      }
    }
  }
})

// Get form input composable
const { compVal, inputWrapperProps, hasError, disabled, inputStyle, id, name } = useFormInput(props, { emit })
</script>

<style>
.v-codemirror, .cm-editor {
  height: 100%;
}
</style>
