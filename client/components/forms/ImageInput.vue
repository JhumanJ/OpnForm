<template>
  <InputWrapper v-bind="inputWrapperProps">
    <template #label>
      <slot name="label" />
    </template>

    <span class="inline-block w-full rounded-md shadow-xs">
      <button
        type="button"
        aria-haspopup="listbox"
        aria-expanded="true"
        aria-labelledby="listbox-label"
        class="cursor-pointer relative w-full"
        :class="[
          theme.default.input, 
          theme.default.spacing.horizontal,
          theme.default.spacing.vertical,
          theme.default.fontSize,
          theme.default.borderRadius,
          { 'ring-red-500 ring-2': hasError }
        ]"
        :style="inputStyle"
        @click.prevent="showUploadModal = true"
      >
        <div
          v-if="currentUrl == null"
          class="text-gray-600 dark:text-gray-400 flex justify-center"
        >
          <Icon
            name="heroicons:cloud-arrow-up"
            class="h-5 w-5"
          />
          <span class="ml-2">
            Upload
          </span>

        </div>
        <div
          v-else
          class=" text-gray-600 dark:text-gray-400 flex"
        >
          <div class="flex-grow">
            <img
              :src="tmpFile ?? currentUrl"
              class="h-5 rounded shadow-md border"
            >
          </div>
          <a
            href="#"
            class="text-gray-500 hover:text-red-500 flex items-center"
            @click.prevent="clearUrl"
          >
            <Icon
              name="heroicons:trash"
              class="h-5 w-5"
            />
          </a>
        </div>
      </button>
    </span>

    <template #help>
      <slot name="help" />
    </template>
    <template #error>
      <slot name="error" />
    </template>

    <!--  Modal  -->
    <modal
      :show="showUploadModal"
      @close="showUploadModal = false"
    >
      <h2 class="text-lg font-semibold">
        Upload an image
      </h2>

      <div class="max-w-3xl mx-auto lg:max-w-none">
        <div
          class="sm:mt-5 sm:grid sm:grid-cols-1 sm:gap-4 sm:items-start sm:pt-5"
        >
          <div class="mt-2 sm:mt-0 sm:col-span-2 mb-5">
            <div
              v-cloak
              class="w-full flex justify-center items-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md h-128"
              @dragover.prevent="onUploadDragoverEvent($event)"
              @drop.prevent="onUploadDropEvent($event)"
            >
              <div
                v-if="loading"
                class="text-gray-600 dark:text-gray-400"
              >
                <loader class="h-5 w-5 mx-auto m-10" />
                <p class="text-center mt-6">
                  Uploading your file...
                </p>
              </div>
              <template v-else>
                <div
                  class="absolute rounded-full bg-gray-100 h-20 w-20 z-10 transition-opacity duration-500 ease-in-out"
                  :class="{
                    'opacity-100': uploadDragoverTracking,
                    'opacity-0': !uploadDragoverTracking,
                  }"
                />
                <div class="relative z-20 text-center">
                  <input
                    ref="actual-input"
                    class="hidden"
                    type="file"
                    :name="name"
                    accept="image/png, image/gif, image/jpeg, image/bmp, image/svg+xml"
                    @change="manualFileUpload"
                  >
                  <Icon
                    name="heroicons:cloud-arrow-up"
                    class="x-auto h-24 w-24 text-gray-200"
                  />
                  <p class="mt-5 text-sm text-gray-600">
                    <button
                      type="button"
                      class="font-semibold text-nt-blue hover:text-nt-blue-dark focus:outline-none focus:underline transition duration-150 ease-in-out"
                      @click="openFileUpload"
                    >
                      Upload your image,
                    </button>
                    use drag and drop or paste it
                  </p>
                  <p class="mt-1 text-xs text-gray-500">
                    .jpg, .jpeg, .png, .bmp, .gif, .svg up to 5mb
                  </p>
                </div>
              </template>
            </div>
          </div>
        </div>
      </div>
    </modal>
  </InputWrapper>
</template>

<script>
import { inputProps, useFormInput } from "./useFormInput.js"
import InputWrapper from "./components/InputWrapper.vue"
import Modal from "../global/Modal.vue"
import { storeFile } from "~/lib/file-uploads.js"

export default {
  name: "ImageInput",
  components: { InputWrapper, Modal },
  mixins: [],
  props: {
    ...inputProps,
  },

  setup(props, context) {
    return {
      ...useFormInput(props, context),
    }
  },

  data: () => ({
    showUploadModal: false,

    file: [],
    uploadDragoverTracking: false,
    uploadDragoverEvent: false,
    loading: false,
  }),

  computed: {
    currentUrl() {
      return this.compVal
    },
  },

  watch: {
    showUploadModal: {
      handler() {
        if (import.meta.server) return
        document.removeEventListener("paste", this.onUploadPasteEvent)
        if (this.showUploadModal) {
          document.addEventListener("paste", this.onUploadPasteEvent)
        }
      },
    },
  },

  methods: {
    clearUrl() {
      this.compVal = null
    },
    onUploadDragoverEvent() {
      this.uploadDragoverEvent = true
      this.uploadDragoverTracking = true
    },
    onUploadDropEvent(e) {
      this.uploadDragoverEvent = false
      this.uploadDragoverTracking = false
      this.droppedFiles(e.dataTransfer.files)
    },
    onUploadPasteEvent(e) {
      if (!this.showUploadModal) return
      this.uploadDragoverEvent = false
      this.uploadDragoverTracking = false
      this.droppedFiles(e.clipboardData.files)
    },
    droppedFiles(droppedFiles) {
      if (!droppedFiles) return

      this.file = droppedFiles[0]
      this.uploadFileToServer()
    },
    openFileUpload() {
      this.$refs["actual-input"].click()
    },
    manualFileUpload(e) {
      this.file = e.target.files[0]
      this.uploadFileToServer()
    },
    uploadFileToServer() {
      this.loading = true
      // Store file in s3
      storeFile(this.file)
        .then((response) => {
          // Move file to permanent storage for form assets
          opnFetch("/open/forms/assets/upload", {
            method: "POST",
            body: {
              url:
                this.file.name.split(".").slice(0, -1).join(".") +
                "_" +
                response.uuid +
                "." +
                response.extension,
            },
          })
            .then((moveFileResponseData) => {
              if (!this.multiple) {
                this.files = []
              }
              this.compVal = moveFileResponseData.url
              this.showUploadModal = false
              this.loading = false
            })
            .catch(() => {
              this.compVal = null
              this.showUploadModal = false
              this.loading = false
            })
        })
        .catch(() => {
          this.compVal = null
          this.showUploadModal = false
          this.loading = false
        })
    },
  },
}
</script>
