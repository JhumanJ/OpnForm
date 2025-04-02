<template>
  <div
    class="relative overflow-hidden"
    :class="[theme.fileInput.borderRadius]"
  >
    <video
      id="webcam"
      autoplay
      playsinline
      muted
      :class="[
        { hidden: !isCapturing }, 
        theme.fileInput.minHeight, 
        theme.fileInput.borderRadius,
        'w-full h-full object-cover border border-gray-400/30'
      ]"
      webkit-playsinline
    />
    <canvas
      id="canvas"
      :class="[
        { hidden: !capturedImage },
        theme.fileInput.borderRadius,
        theme.fileInput.minHeight,
        'w-full h-full object-cover border border-gray-400/30'
      ]"
    />
    
    <!-- Barcode scanning overlay -->
    <div 
      v-if="isCapturing && isBarcodeMode" 
      class="absolute inset-0 pointer-events-none"
    >
      <!-- Semi-transparent overlay -->
      <div class="absolute inset-0 bg-black/30" />
      
      <!-- Scanning area (transparent window) -->
      <div
        class="absolute inset-0 flex items-center justify-center"
        style="padding-bottom: 60px;"
      >
        <div class="relative w-4/5 h-3/5">
          <!-- Transparent window -->
          <div class="absolute inset-0 bg-transparent border-0" />
          
          <!-- Corner indicators -->
          <div class="absolute top-0 left-0 w-8 h-8 border-t-2 border-l-2 border-white" />
          <div class="absolute top-0 right-0 w-8 h-8 border-t-2 border-r-2 border-white" />
          <div class="absolute bottom-0 left-0 w-8 h-8 border-b-2 border-l-2 border-white" />
          <div class="absolute bottom-0 right-0 w-8 h-8 border-b-2 border-r-2 border-white" />
        </div>
      </div>
    </div>
    
    <div
      v-if="cameraPermissionStatus === 'allowed'"
      class="absolute inset-x-0 grid place-content-center bottom-2"
    >
      <div
        v-if="isCapturing"
        class="p-2 px-4 flex items-center justify-center text-xs space-x-3"
      >
        <span
          v-if="!isBarcodeMode"
          class="cursor-pointer rounded-full w-12 h-12 border-2 grid place-content-center bg-white/20 backdrop-blur-sm shadow-md"
          @click="processCapturedImage"
        >
          <span
            class="cursor-pointer bg-white rounded-full w-8 h-8 grid place-content-center"
          />
        </span>
        <span
          class="text-white cursor-pointer bg-black/40 rounded-full backdrop-blur-sm shadow-md w-12 h-12 grid place-content-center"
          @click="cancelCamera"
        >
          <Icon
            name="heroicons:x-mark"
            class="w-6 h-6"
          />
        </span>
        <span
          v-if="isMobileDevice"
          class="text-white cursor-pointer bg-black/40 rounded-full backdrop-blur-sm shadow-md w-12 h-12 grid place-content-center"
          @click="switchCamera"
        >
          <Icon
            name="heroicons:arrow-path"
            class="w-6 h-6"
          />
        </span>
      </div>
    </div>
    <div
      v-else-if="cameraPermissionStatus === 'blocked'"
      class="absolute p-5 top-0 inset-x-0 flex flex-col items-center justify-center space-y-4 text-center border border-gray-400/30 h-full"
      :class="[theme.fileInput.borderRadius]"
      @click="openCameraUpload"
    >
      <Icon
        :name="isBarcodeMode ? 'i-material-symbols-barcode-scanner-rounded' : 'i-heroicons-camera'"
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
      class="absolute p-5 top-0 inset-x-0 flex flex-col items-center justify-center space-y-4 text-center border border-gray-400/30 h-full"
      :class="[theme.fileInput.borderRadius]"
    >
      <div class="w-6 h-6">
        <Loader />
      </div>
    </div>
    <div
      v-else
      class="absolute p-5 top-0 inset-x-0 flex flex-col items-center justify-center space-y-4 text-center border border-gray-400/30 h-full"
      :class="[theme.fileInput.borderRadius]"
      @click="openCameraUpload"
    >
      <Icon
        :name="isBarcodeMode ? 'i-material-symbols-barcode-scanner-rounded' : 'heroicons:video-camera-slash'"
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

  beforeUnmount() {
    this.cleanupCurrentStream()
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
        const tracks = webcamElement.srcObject.getTracks()
        tracks.forEach(track => track.stop())
        webcamElement.srcObject = null
      }
    },

    async switchCamera() {
      try {
        // Stop current camera and clean up resources
        this.cleanupCurrentStream()

        // Toggle facing mode
        this.currentFacingMode = this.currentFacingMode === 'user' ? 'environment' : 'user'

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

        // Determine the facing mode to use
        let facingMode = this.currentFacingMode
        if (this.isBarcodeMode && this.currentFacingMode === 'user') {
          // Force environment mode for barcode scanning
          facingMode = 'environment'
        }

        // Create constraints based on device capabilities
        const constraints = {
          audio: false,
          video: {
            width: { ideal: 1280 },
            height: { ideal: 720 }
          }
        }

        // Use exact constraints for mobile devices to ensure proper camera selection
        if (this.isMobileDevice) {
          constraints.video.facingMode = { exact: facingMode }
        } else {
          constraints.video.facingMode = facingMode
        }

        // Try to get the stream with the specified constraints
        let stream
        try {
          stream = await navigator.mediaDevices.getUserMedia(constraints)
        } catch (err) {
          // If exact constraint fails, fall back to preference
          if (this.isMobileDevice && err.name === 'OverconstrainedError') {
            constraints.video.facingMode = facingMode
            stream = await navigator.mediaDevices.getUserMedia(constraints)
          } else {
            throw err
          }
        }

        this.mediaStream = stream  // Store the stream reference
        webcamElement.srcObject = stream
        
        this.webcam = new Webcam(
          webcamElement,
          facingMode,
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
              facingMode: this.currentFacingMode,
              width: { min: 640 },
              height: { min: 480 },
              aspectRatio: { min: 1, max: 2 }
            },
          },
          locator: {
            patchSize: "medium",
            halfSample: true
          },
          numOfWorkers: navigator.hardwareConcurrency || 4,
          frequency: 10,
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
      this.cleanupCurrentStream()  // Use the cleanup method
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

