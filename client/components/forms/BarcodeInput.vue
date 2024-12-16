<template>
  <input-wrapper v-bind="inputWrapperProps">
    <template #label>
      <slot name="label" />
    </template>

    <div
      :class="[
        theme.BarcodeInput.input,
        theme.BarcodeInput.spacing.horizontal,
        theme.BarcodeInput.spacing.vertical,
        theme.BarcodeInput.fontSize,
        theme.BarcodeInput.borderRadius,
        {
          '!ring-red-500 !ring-2 !border-transparent': hasError,
          '!cursor-not-allowed !bg-gray-200 dark:!bg-gray-800': disabled,
        },
      ]"
      class="flex flex-col gap-4"
    >
      <!-- Barcode Preview -->
      <div
        v-if="scannedValue"
        class="flex items-center gap-2"
      >
        <div class="flex-1 break-all">
          {{ scannedValue }}
        </div>
        <button
          v-if="!disabled"
          type="button"
          class="text-gray-400 hover:text-gray-600"
          @click="clearValue"
        >
          <Icon
            name="i-heroicons-x-mark-20-solid"
            class="h-5 w-5"
          />
        </button>
      </div>

      <!-- Scanner Interface -->
      <div
        v-show="!scannedValue && isScanning"
        class="relative aspect-video w-full overflow-hidden rounded-lg bg-gray-100"
      >
        <video
          ref="video"
          class="h-full w-full object-cover"
        />
        <div class="absolute inset-0 border-2 border-dashed border-blue-500/50" />
      </div>

      <!-- Controls -->
      <div
        v-if="!disabled"
        class="flex justify-center"
      >
        <button
          v-if="!isScanning && !scannedValue"
          type="button"
          class="inline-flex items-center gap-2 rounded-md bg-blue-50 px-3 py-2 text-sm font-semibold text-blue-600 hover:bg-blue-100"
          @click="startScanning"
        >
          <Icon
            name="i-heroicons-qr-code-20-solid"
            class="h-5 w-5"
          />
          {{ $t('forms.barcodeInput.startScanning') }}
        </button>
        <button
          v-if="isScanning"
          type="button"
          class="inline-flex items-center gap-2 rounded-md bg-gray-50 px-3 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-100"
          @click="stopScanning"
        >
          <Icon
            name="i-heroicons-stop-20-solid"
            class="h-5 w-5"
          />
          {{ $t('forms.barcodeInput.stopScanning') }}
        </button>
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
import Quagga from 'quagga'

export default {
  name: 'BarcodeInput',
  components: { InputWrapper },

  props: {
    ...inputProps,
  },

  setup(props, context) {
    return {
      ...useFormInput(props, context),
    }
  },

  data: () => ({
    isScanning: false,
    scannedValue: null,
    stream: null
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
    async startScanning() {
      if (!this.$refs.video) return

      try {
        // First get user media directly
        this.stream = await navigator.mediaDevices.getUserMedia({
          video: {
            facingMode: "environment"
          }
        })
        
        // Attach stream to video element
        this.$refs.video.srcObject = this.stream
        this.$refs.video.play()

        this.isScanning = true
        
        // Initialize Quagga
        Quagga.init({
          inputStream: {
            name: "Live",
            type: "LiveStream",
            target: this.$refs.video,
            constraints: {
              facingMode: "environment"
            },
          },
          decoder: {
            readers: [
              "ean_reader",
              "ean_8_reader",
              "code_128_reader",
              "code_39_reader",
              "upc_reader",
              "upc_e_reader"
            ]
          },
          locate: true // Try to detect the barcode location
        }, (err) => {
          if (err) {
            console.error('Quagga initialization failed:', err)
            this.stopScanning()
            return
          }
          
          Quagga.start()
          
          Quagga.onDetected((result) => {
            if (result.codeResult) {
              this.scannedValue = result.codeResult.code
              this.stopScanning()
            }
          })
        })
      } catch (err) {
        console.error('Failed to start camera:', err)
        this.isScanning = false
      }
    },

    stopScanning() {
      if (this.isScanning) {
        Quagga.stop()
        
        // Stop all tracks from the stream
        if (this.stream) {
          this.stream.getTracks().forEach(track => track.stop())
          this.stream = null
        }

        // Clear video source
        if (this.$refs.video) {
          this.$refs.video.srcObject = null
        }

        this.isScanning = false
      }
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