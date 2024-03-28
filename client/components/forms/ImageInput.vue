<template>
  <input-wrapper
    v-bind="inputWrapperProps"
  >
    <template #label>
      <slot name="label" />
    </template>

    <span class="inline-block w-full rounded-md shadow-sm">
      <button type="button" aria-haspopup="listbox" aria-expanded="true" aria-labelledby="listbox-label"
              class="cursor-pointer relative w-full" :class="[theme.default.input,{'ring-red-500 ring-2': hasError}]"
              :style="inputStyle" @click.prevent="showUploadModal=true"
      >
        <div v-if="currentUrl==null" class="h-6 text-gray-600 dark:text-gray-400">
          Upload image <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 inline" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
            />
          </svg>
        </div>
        <div v-else class="h-6 text-gray-600 dark:text-gray-400 flex">
          <div class="flex-grow">
            <img :src="currentUrl" class="h-6 rounded shadow-md"/>
          </div>
          <a href="#" class="hover:text-nt-blue flex" @click.prevent="clearUrl">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
              />
            </svg></a>
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
    <modal :show="showUploadModal" @close="showUploadModal=false">
      <h2 class="text-lg font-semibold">
        Upload an image
      </h2>

      <div class="max-w-3xl mx-auto lg:max-w-none">
        <div class="sm:mt-5 sm:grid sm:grid-cols-1 sm:gap-4 sm:items-start sm:pt-5">
          <div class="mt-2 sm:mt-0 sm:col-span-2 mb-5">
            <div
              v-cloak
              class="w-full flex justify-center items-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md h-128"
              @dragover.prevent="onUploadDragoverEvent($event)"
              @drop.prevent="onUploadDropEvent($event)"
            >
              <div v-if="loading" class="text-gray-600 dark:text-gray-400">
                <Loader class="h-6 w-6 mx-auto m-10" />
                <p class="text-center mt-6">
                  Uploading your file...
                </p>
              </div>
              <template v-else>
                <div
                  class="absolute rounded-full bg-gray-100 h-20 w-20 z-10 transition-opacity duration-500 ease-in-out"
                  :class="{
                    'opacity-100': uploadDragoverTracking,
                    'opacity-0': !uploadDragoverTracking
                  }"
                />
                <div class="relative z-20 text-center">
                  <input ref="actual-input" class="hidden" type="file" :name="name"
                         accept="image/png, image/gif, image/jpeg, image/bmp, image/svg+xml" @change="manualFileUpload"
                  >
                  <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-24 w-24 text-gray-200" fill="none"
                       viewBox="0 0 24 24" stroke="currentColor"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
                    />
                  </svg>
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
  </input-wrapper>
</template>

<script>
import { inputProps, useFormInput } from './useFormInput.js'
import InputWrapper from './components/InputWrapper.vue'
import Modal from '../global/Modal.vue'
import {storeFile} from "~/lib/file-uploads.js"

export default {
  name: 'ImageInput',
  components: { InputWrapper, Modal },
  mixins: [],
  props: {
    ...inputProps
  },

  setup (props, context) {
    return {
      ...useFormInput(props, context)
    }
  },

  data: () => ({
    showUploadModal: false,

    file: [],
    uploadDragoverTracking: false,
    uploadDragoverEvent: false,
    loading: false
  }),

  computed: {
    currentUrl () {
      return this.compVal
    }
  },

  watch: {
    showUploadModal: {
      handler (val) {
        if (import.meta.server) return
        document.removeEventListener('paste', this.onUploadPasteEvent)
        if (this.showUploadModal) {
          document.addEventListener('paste', this.onUploadPasteEvent)
        }
      }
    }
  },

  methods: {
    clearUrl () {
      this.form[this.name] = null
    },
    onUploadDragoverEvent (e) {
      this.uploadDragoverEvent = true
      this.uploadDragoverTracking = true
    },
    onUploadDropEvent (e) {
      this.uploadDragoverEvent = false
      this.uploadDragoverTracking = false
      this.droppedFiles(e.dataTransfer.files)
    },
    onUploadPasteEvent (e) {
      if (!this.showUploadModal) return
      this.uploadDragoverEvent = false
      this.uploadDragoverTracking = false
      this.droppedFiles(e.clipboardData.files)
    },
    droppedFiles (droppedFiles) {
      if (!droppedFiles) return

      this.file = droppedFiles[0]
      this.uploadFileToServer()
    },
    openFileUpload () {
      this.$refs['actual-input'].click()
    },
    manualFileUpload (e) {
      this.file = e.target.files[0]
      this.uploadFileToServer()
    },
    uploadFileToServer () {
      this.loading = true
      // Store file in s3
      storeFile(this.file).then(response => {
        // Move file to permanent storage for form assets
        opnFetch('/open/forms/assets/upload', {
          method: 'POST',
          body: {
            url: this.file.name.split('.').slice(0, -1).join('.') + '_' + response.uuid + '.' + response.extension
          }
        }).then(moveFileResponseData => {
          if (!this.multiple) {
            this.files = []
          }
          this.compVal = moveFileResponseData.url
          this.showUploadModal = false
          this.loading = false
        }).catch((error) => {
          this.compVal = null
          this.showUploadModal = false
          this.loading = false
        })
      }).catch((error) => {
        this.compVal = null
        this.showUploadModal = false
        this.loading = false
      })
    }
  }
}
</script>
