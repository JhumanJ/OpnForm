<template>
  <input-wrapper
    v-bind="inputWrapperProps"
  >
    <template #label>
      <slot name="label" />
    </template>

    <div class="flex w-full items-center justify-center transition-colors duration-40"
         :class="[{'!cursor-not-allowed':disabled, 'cursor-pointer':!disabled,
                   [theme.fileInput.inputHover.light + ' dark:'+theme.fileInput.inputHover.dark]: uploadDragoverEvent,
                   ['hover:'+theme.fileInput.inputHover.light +' dark:hover:'+theme.fileInput.inputHover.dark]: !loading}, theme.fileInput.input]"
         @dragover.prevent="uploadDragoverEvent=true"
         @dragleave.prevent="uploadDragoverEvent=false"
         @drop.prevent="onUploadDropEvent"
         @click="openFileUpload"
    >
      <div
        v-if="loading"
        class="text-gray-600 dark:text-gray-400"
      >
        <Loader class="mx-auto h-6 w-6" />
        <p class="mt-2 text-center text-sm text-gray-500">
          Uploading your file...
        </p>
      </div>
      <template v-else>
        <div class="text-center">
          <input
            ref="actual-input"
            class="hidden"
            :multiple="multiple"
            type="file"
            :name="name"
            :accept="acceptExtensions"
            @change="manualFileUpload"
          >
          <div v-if="files.length" class="flex flex-wrap items-center justify-center gap-4">
            <uploaded-file
              v-for="file in files"
              :key="file.url"
              :file="file"
              :theme="theme"
              @remove="clearFile(file)"
            />
          </div>
          <template v-else>
            <div class="text-gray-500 w-full flex justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                   stroke="currentColor" class="w-6 h-6"
              >
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"
                />
              </svg>
            </div>

            <p class="mt-2 text-sm text-gray-500 font-semibold select-none">
              Click to choose {{ multiple ? 'file(s)' : 'a file' }} or drag here
            </p>
            <p class="mt-1 text-xs text-gray-400 dark:text-gray-600 select-none">
              Size limit: {{ mbLimit }}MB per file
            </p>
          </template>
        </div>
      </template>
    </div>

    <template #help>
      <slot name="help" />
    </template>
    <template #error>
      <slot name="error" />
    </template>
  </input-wrapper>
</template>

<script>
import { inputProps, useFormInput } from './useFormInput.js'
import InputWrapper from './components/InputWrapper.vue'
import UploadedFile from './components/UploadedFile.vue'
import {storeFile} from "~/lib/file-uploads.js"

export default {
  name: 'FileInput',
  components: { InputWrapper, UploadedFile },
  mixins: [],
  props: {
    ...inputProps,
    multiple: { type: Boolean, default: true },
    mbLimit: { type: Number, default: 5 },
    accept: { type: String, default: '' },
    moveToFormAssets: { type: Boolean, default: false }
  },

  setup (props, context) {
    return {
      ...useFormInput(props, context)
    }
  },

  data: () => ({
    files: [],
    uploadDragoverEvent: false,
    loading: false
  }),

  computed: {
    currentUrl () {
      return this.form[this.name]
    },
    acceptExtensions () {
      if (!this.accept) {
        return null
      }
      return this.accept
        .split(',')
        .map((i) => {
          if (!i) return null
          return '.' + i.trim()
        })
        .join(',')
    }
  },

  watch: {
    files: {
      deep: true,
      handler (files) {
        this.compVal = files.map((file) => file.url)
      }
    }
  },

  async created () {
    if (typeof this.compVal === 'string' || this.compVal instanceof String) {
      await this.getFileFromUrl(this.compVal).then((fileObj) => {
        this.files = [{
          file: fileObj,
          url: this.compVal,
          src: this.getFileSrc(fileObj)
        }]
      })
    } else if (this.compVal && this.compVal.length > 0) {
      const tmpFiles = []
      for (let i = 0; i < this.compVal.length; i++) {
        await this.getFileFromUrl(this.compVal[i]).then((fileObj) => {
          tmpFiles.push({
            file: fileObj,
            url: this.compVal[i],
            src: this.getFileSrc(fileObj)
          })
        })
      }
      this.files = tmpFiles
    }
  },

  methods: {
    clearAll () {
      this.files = []
    },
    clearFile (index) {
      this.files.splice(index, 1)
    },
    onUploadDropEvent (e) {
      this.uploadDragoverEvent = false
      this.droppedFiles(e.dataTransfer.files)
    },
    droppedFiles (droppedFiles) {
      if (!droppedFiles || this.disabled) return

      for (let i = 0; i < droppedFiles.length; i++) {
        this.uploadFileToServer(droppedFiles.item(i))
      }
    },
    openFileUpload () {
      if (this.disabled) return
      this.$refs['actual-input'].click()
    },
    manualFileUpload (e) {
      const files = e.target.files
      for (let i = 0; i < files.length; i++) {
        this.uploadFileToServer(files.item(i))
      }
    },
    uploadFileToServer (file) {
      if (this.disabled) return
      this.loading = true
      storeFile(file)
        .then((response) => {
          if (!this.multiple) {
            this.files = []
          }
          if (this.moveToFormAssets) {
            // Move file to permanent storage for form assets
            opnFetch('/open/forms/assets/upload', {
              method: 'POST',
              body: {
                type: 'files',
                url: file.name.split('.').slice(0, -1).join('.') + '_' + response.uuid + '.' + response.extension
              }
            }).then(moveFileResponseData => {
              this.files.push({
                file: file,
                url: moveFileResponseData.url,
                src: this.getFileSrc(file)
              })
              this.loading = false
            }).catch((error) => {
              this.loading = false
            })
          } else {
            this.files.push({
              file: file,
              url: file.name.split('.').slice(0, -1).join('.') + '_' + response.uuid + '.' + response.extension,
              src: this.getFileSrc(file)
            })
            this.loading = false
          }
        })
        .catch((error) => {
          this.clearAll()
          this.loading = false
        })
    },
    async getFileFromUrl (url, defaultType = 'image/jpeg') {
      const response = await fetch(url)
      const data = await response.blob()
      const name = url.replace(/^.*(\\|\/|\:)/, '')
      return new File([data], name, {
        type: data.type || defaultType
      })
    },
    getFileSrc (file) {
      if (file.type && file.type.split('/')[0] === 'image') {
        return URL.createObjectURL(file)
      }
      return null
    }
  }
}
</script>
