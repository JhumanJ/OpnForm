<template>
  <input-wrapper v-bind="inputWrapperProps">
    <template #label>
      <slot name="label" />
    </template>

    <div
      v-if="isScanning"
      class="relative w-full"
    >
      <ClientOnly>
      <CameraUpload
        :is-barcode-mode="true"
        :decoders="decoders"
        @stop-webcam="stopScanning"
        @barcode-detected="handleBarcodeDetected"
      />
      </ClientOnly>
    </div>

    <div
      v-else-if="scannedValue"
      class="flex items-center justify-between border border-gray-300 dark:border-gray-600 w-full bg-white text-gray-700 dark:bg-notion-dark-light dark:text-gray-300 rounded-lg px-4 py-2"
    >
      <div class="flex-1 break-all">
        {{ scannedValue }}
      </div>
      <button
        v-if="!disabled"
        type="button"
        class="pt-1 text-gray-400 hover:text-gray-600"
        @click="clearValue"
      >
        <Icon
          name="i-heroicons-x-mark-20-solid"
          class="h-5 w-5"
        />
      </button>
    </div>
          
    <div
      v-else
      :style="inputStyle"
      class="flex flex-col w-full items-center justify-center transition-colors duration-40"
      :class="[
        {'!cursor-not-allowed':disabled, 'cursor-pointer':!disabled},
        theme.fileInput.input,
        theme.fileInput.borderRadius,
        theme.fileInput.spacing.horizontal,
        theme.fileInput.spacing.vertical,
        theme.fileInput.fontSize,
        theme.fileInput.minHeight,
        {'border-red-500 border-2':hasError},
        'focus:outline-none focus:ring-2'
      ]"
      tabindex="0"
      role="button"
      aria-label="Click to open a camera"
      @click="startScanning"
      @keydown.enter.prevent="startScanning"
    >
      <div class="flex w-full items-center justify-center">
        <div class="text-center">
          <template v-if="!scannedValue && !isScanning">
            <div class="text-gray-500 w-full flex justify-center">
              <Icon
                name="i-material-symbols-barcode-scanner-rounded"
                class="w-6 h-6"
              />
            </div>

            <p class="mt-2 text-sm text-gray-500 font-medium select-none">
              {{ $t('forms.barcodeInput.clickToOpenCamera') }}
            </p>
          </template>
        </div>
      </div>
    </div>
    
    <template #error>
      <slot name="error" />
    </template>
  </input-wrapper>
</template>

<script>
import { inputProps, useFormInput } from './useFormInput.js'
import InputWrapper from './components/InputWrapper.vue'
import CameraUpload from './components/CameraUpload.vue'

export default {
  name: 'BarcodeInput',
  components: { InputWrapper, CameraUpload },

  props: {
    ...inputProps,
    decoders: {
      type: Array,
      default: () => []
    }
  },

  setup(props, context) {
    return {
      ...useFormInput(props, context),
    }
  },

  data: () => ({
    isScanning: false,
    scannedValue: null
  }),

  watch: {
    scannedValue: {
      handler(value) {
        this.compVal = value
      },
      immediate: true
    }
  },

  beforeUnmount() {
    this.stopScanning()
  },

  methods: {
    startScanning() {
      if (this.disabled) return
      this.isScanning = true
    },

    stopScanning() {
      this.isScanning = false
    },

    handleBarcodeDetected(code) {
      this.scannedValue = code
      this.stopScanning()
    },

    clearValue() {
      this.scannedValue = null
    }
  }
}
</script>

<style scoped>
video {
  /* Ensure the video displays properly */
  max-width: 100%;
  height: auto;
}
</style> 