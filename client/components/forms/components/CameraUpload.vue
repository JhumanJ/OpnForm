<template>
  <div class="relative border">
    <video
      id="webcam"
      autoplay
      playsinline
      :class="[{ hidden: !isCapturing }, theme.fileInput.minHeight, theme.fileInput.borderRadius]"
      width="1280"
      height="720"
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
        Allow Camera Permission
      </p>
      <p class="text-xs">
        You need to allow camera permission before you can take pictures. Go to
        browser settings to enable camera permission on this page.
      </p>
      <UButton
        color="white"
        @click.stop="cancelCamera"
      >
        Got it!
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
        Camera Device Error
      </p>
      <p class="text-xs">
        An unknown error occurred when trying to start Webcam device.
      </p>
      <UButton
        color="white"
        @click.stop="cancelCamera"
      >
        Go back
      </UButton>
    </div>
  </div>
</template>

<script>
import Webcam from "webcam-easy"
import CachedDefaultTheme from "~/lib/forms/themes/CachedDefaultTheme.js"

export default {
  name: "FileInput",
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
  },
  emits: ['stopWebcam', 'uploadImage'],
  data: () => ({
    webcam: null,
    isCapturing: false,
    capturedImage: null,
    cameraPermissionStatus: "loading",
  }),
  computed: {
    videoDisplay() {
      return this.isCapturing ? "" : "hidden"
    },
    canvasDisplay() {
      return !this.isCapturing && this.capturedImage ? "" : "hidden"
    },
  },
  mounted() {
    const webcamElement = document.getElementById("webcam")
    const canvasElement = document.getElementById("canvas")
    this.webcam = new Webcam(webcamElement, "user", canvasElement)
    this.openCameraUpload()
  },

  methods: {
    openCameraUpload() {
      this.isCapturing = true
      this.capturedImage = null
      this.webcam
        .start()
        .then(() => {
          this.cameraPermissionStatus = "allowed"
        })
        .catch((err) => {
          console.error(err)
          if (err.toString() === "NotAllowedError: Permission denied") {
            this.cameraPermissionStatus = "blocked"
            return
          }
          this.cameraPermissionStatus = "unknown"
        })
    },
    cancelCamera() {
      this.isCapturing = false
      this.capturedImage = null
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

      // Create Blob from binary data
      const blob = new Blob(byteArrays, { type: "image/png" })
      const filename = Date.now()
      // Create a File object from the Blob
      const file = new File([blob], `${filename}.png`, { type: "image/png" })
      this.$emit("uploadImage", file)
    },
  },
}
</script>
