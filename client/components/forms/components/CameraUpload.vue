<template>
  <div class="relative border">
    <video
      id="webcam"
      autoplay
      playsinline
      muted
      :class="[
        { hidden: !isCapturing }, 
        theme.fileInput.minHeight, 
        theme.fileInput.borderRadius,
        'w-full h-full object-cover'
      ]"
      webkit-playsinline
    />
    <canvas
      id="canvas"
      :class="{ hidden: !capturedImage }"
    />
    <div
      v-if="cameraPermissionStatus === 'allowed'"
      class="absolute inset-x-0 grid place-content-center bottom-2"
    >
      <div
        v-if="isCapturing"
        class="p-2 px-4 flex items-center justify-center text-xs space-x-2"
      >
        <span
          v-if="!isBarcodeMode"
          class="cursor-pointer rounded-full w-14 h-14 border-2 grid place-content-center"
          @click="processCapturedImage"
        >
          <span
            class="cursor-pointer bg-gray-100 rounded-full w-10 h-10 grid place-content-center"
          />
        </span>
        <span
          class="text-white cursor-pointer"
          @click="cancelCamera"
        >
          <Icon
            name="heroicons:x-mark"
            class="w-8 h-8"
          />
        </span>
        <span
          v-if="isMobileDevice"
          class="text-white cursor-pointer"
          @click="switchCamera"
        >
          <Icon
            name="heroicons:arrow-path"
            class="w-8 h-8"
          />
        </span>
      </div>
    </div>
    <div
      v-else-if="cameraPermissionStatus === 'blocked'"
      class="absolute p-5 top-0 inset-x-0 flex flex-col items-center justify-center space-y-4 text-center rounded border border-gray-400/30 h-full"
      @click="openCameraUpload"
    >
      <Icon
        name="heroicons:camera"
        class="w-6 h-6"
      />
      <p class="text-center font-bold">
        {{ $t('forms.cameraUpload.allowCameraPermission') }}
      </p>
      <p class="text-xs">
        {{ $t('forms.cameraUpload.allowCameraPermissionDescription') }}
      </p>
      <UButton
        color="white"
        @click.stop="cancelCamera"
      >
        {{ $t('forms.cameraUpload.gotIt') }}
      </UButton>
    </div>

    <div
      v-else-if="cameraPermissionStatus === 'loading'"
      class="absolute p-5 top-0 inset-x-0 flex flex-col items-center justify-center space-y-4 text-center rounded border border-gray-400/30 h-full"
    >
      <div class="w-6 h-6">
        <Loader />
      </div>
    </div>
    <div
      v-else
      class="absolute p-5 top-0 inset-x-0 flex flex-col items-center justify-center space-y-4 text-center rounded border border-gray-400/30 h-full"
      @click="openCameraUpload"
    >
      <Icon
        name="heroicons:video-camera-slash"
        class="w-6 h-6"
      />
      <p class="text-center font-bold">
        {{ $t('forms.cameraUpload.cameraDeviceError') }}
      </p>
      <p class="text-xs">
        {{ $t('forms.cameraUpload.cameraDeviceErrorDescription') }}
      </p>
      <UButton
        color="white"
        @click.stop="cancelCamera"
      >
        {{ $t('forms.cameraUpload.goBack') }}
      </UButton>
    </div>
  </div>
</template>

<script>
import Webcam from "webcam-easy"
import CachedDefaultTheme from "~/lib/forms/themes/CachedDefaultTheme.js"
import Quagga from 'quagga'

export default {
  name: "CameraUpload",
  props: {
    theme: {
      type: Object, default: () => {
        const theme = inject("theme", null)
        if (theme) {
          return theme.value
        }
        return CachedDefaultTheme.getInstance()
      }
    },
    isBarcodeMode: {
      type: Boolean,
      default: false
    },
    decoders: {
      type: Array,
      default: () => []
    }
  },
  emits: ['stopWebcam', 'uploadImage', 'barcodeDetected'],
  data: () => ({
    webcam: null,
    isCapturing: false,
    capturedImage: null,
    cameraPermissionStatus: "loading",
    quaggaInitialized: false,
    currentFacingMode: 'user',
    mediaStream: null
  }),
  computed: {
    videoDisplay() {
      return this.isCapturing ? "" : "hidden"
    },
    canvasDisplay() {
      return !this.isCapturing && this.capturedImage ? "" : "hidden"
    },
    isMobileDevice() {
      return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)
    }
  },
  mounted() {
    const webcamElement = document.getElementById("webcam")
    const canvasElement = document.getElementById("canvas")
    this.webcam = new Webcam(webcamElement, "user", canvasElement)
    this.openCameraUpload()
  },

  methods: {
    async cleanupCurrentStream() {
      if (this.quaggaInitialized) {
        Quagga.stop()
        this.quaggaInitialized = false
      }

      if (this.mediaStream) {
        this.mediaStream.getTracks().forEach(track => track.stop())
        this.mediaStream = null
      }

      if (this.webcam) {
        this.webcam.stop()
      }

      const webcamElement = document.getElementById("webcam")
      if (webcamElement && webcamElement.srcObject) {
        webcamElement.srcObject = null
      }
    },

    async switchCamera() {
      try {
        // Stop current camera
        if (this.quaggaInitialized) {
          Quagga.stop()
          this.quaggaInitialized = false
        }
        this.webcam.stop()

        // Toggle facing mode considering barcode mode
        this.currentFacingMode = this.isBarcodeMode ? 'environment' : 
          (this.currentFacingMode === 'user' ? 'environment' : 'user')

        // Restart camera
        await this.openCameraUpload()
      } catch (error) {
        console.error('Error switching camera:', error)
        this.cameraPermissionStatus = "unknown"
      }
    },

    async openCameraUpload() {
      this.isCapturing = true
      this.capturedImage = null

      try {
        const webcamElement = document.getElementById("webcam")
        const canvasElement = document.getElementById("canvas")

        const constraints = {
          audio: false,
          video: {
            facingMode: this.isBarcodeMode ? 'environment' : this.currentFacingMode,
            width: { ideal: 1280 },
            height: { ideal: 720 }
          }
        }

        const stream = await navigator.mediaDevices.getUserMedia(constraints)
        webcamElement.srcObject = stream
        
        this.webcam = new Webcam(
          webcamElement,
          this.isBarcodeMode ? 'environment' : this.currentFacingMode,
          canvasElement
        )
        
        await new Promise((resolve) => {
          webcamElement.onloadedmetadata = () => {
            webcamElement.play()
            resolve()
          }
        })

        this.cameraPermissionStatus = "allowed"
        if (this.isBarcodeMode) {
          this.initQuagga()
        }
      } catch (err) {
        console.error('Camera error:', err)
        if (err.name === 'NotAllowedError' || err.toString().includes('Permission denied')) {
          this.cameraPermissionStatus = "blocked"
        } else {
          this.cameraPermissionStatus = "unknown"
        }
      }
    },
    initQuagga() {
      if (!this.quaggaInitialized) {
        Quagga.init({
          inputStream: {
            name: "Live",
            type: "LiveStream",
            target: document.getElementById("webcam"),
            constraints: {
              facingMode: "environment"
            },
          },
          decoder: {
            readers: this.decoders || []
          },
          locate: true
        }, (err) => {
          if (err) {
            console.error('Quagga initialization failed:', err)
            return
          }
          
          this.quaggaInitialized = true
          Quagga.start()
          
          Quagga.onDetected((result) => {
            if (result.codeResult) {
              this.$emit('barcodeDetected', result.codeResult.code)
              this.cancelCamera()
            }
          })
        })
      }
    },
    cancelCamera() {
      this.isCapturing = false
      this.capturedImage = null
      if (this.quaggaInitialized) {
        Quagga.stop()
        this.quaggaInitialized = false
      }
      this.webcam.stop()
      this.$emit("stopWebcam")
    },
    processCapturedImage() {
      this.capturedImage = this.webcam.snap()
      this.isCapturing = false
      this.webcam.stop()
      const byteCharacters = atob(this.capturedImage.split(",")[1])
      const byteArrays = []
      for (let offset = 0; offset < byteCharacters.length; offset += 512) {
        const slice = byteCharacters.slice(offset, offset + 512)

        const byteNumbers = new Array(slice.length)
        for (let i = 0; i < slice.length; i++) {
          byteNumbers[i] = slice.charCodeAt(i)
        }

        const byteArray = new Uint8Array(byteNumbers)
        byteArrays.push(byteArray)
      }

      const blob = new Blob(byteArrays, { type: "image/png" })
      const filename = Date.now()
      const file = new File([blob], `${filename}.png`, { type: "image/png" })
      this.$emit("uploadImage", file)
    },
  },
}
</script>
