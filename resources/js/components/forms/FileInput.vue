<template>
  <div :class="wrapperClass">
    <label v-if="label"
           :class="[theme.default.label,{'uppercase text-xs':uppercaseLabels, 'text-sm':!uppercaseLabels}]"
    >
      {{ label }}
      <span v-if="required" class="text-red-500 required-dot">*</span>
    </label>
    <small v-if="help && helpPosition=='above_input'" :class="theme.default.help" class="flex mb-1">
      <slot name="help"><span class="field-help" v-html="help" /></slot>
    </small>
    <span class="inline-block w-full rounded-md shadow-sm">
      <button type="button" aria-haspopup="listbox" aria-expanded="true" aria-labelledby="listbox-label" role="button"
              class="cursor-pointer relative flex"
              :class="[theme.default.input,{'!ring-red-500 !ring-2': hasValidation && form.errors.has(name), '!cursor-not-allowed !bg-gray-200':disabled}]"
              :style="inputStyle" @click.self="showUploadModal=true"
      >
        <div v-if="currentUrl==null" class="h-6 text-gray-600 dark:text-gray-400 flex-grow truncate"
             @click.prevent="showUploadModal=true"
        >
          Upload {{ multiple ? 'file(s)' : 'a file' }} <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 inline"
                                                            fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
            />
          </svg>
        </div>
        <template v-else>
          <div class="flex-grow h-6 text-gray-600 dark:text-gray-400 truncate" @click.prevent="showUploadModal=true">
            <div>
              <p v-if="files.length==1"><svg xmlns="http://www.w3.org/2000/svg"
                                             class="h-6 w-6 inline mr-2 -mt-1" fill="none"
                                             viewBox="0 0 24 24"
                                             stroke="currentColor"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                />
              </svg>{{ files[0].file.name }}</p>
              <p v-else><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 inline mr-2 -mt-1" fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                />
              </svg>{{ files.length }} file(s)</p>
            </div>
          </div>
          <div v-if="files.length>0">
            <a href="#" class="hover:text-nt-blue" role="button" @click.prevent="clearAll">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                   stroke="currentColor"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                />
              </svg>
            </a>
          </div>
        </template>
      </button>
    </span>
    <small v-if="help && helpPosition=='below_input'" :class="theme.default.help">
      <slot name="help"><span class="field-help" v-html="help" /></slot>
    </small>
    <has-error v-if="hasValidation" :form="form" :field="name" />

    <!--  Modal  -->
    <modal :portal-order="2" :show="showUploadModal" @close="showUploadModal=false">
      <h2 class="text-lg font-semibold">
        Upload {{ multiple ? 'file(s)' : 'a file' }}
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
                <loader class="h-6 w-6 mx-auto m-10" />
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
                  <input ref="actual-input" class="hidden" :multiple="multiple" type="file" :name="name"
                         :accept="acceptExtensions"
                         @change="manualFileUpload"
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
                      Upload {{ multiple ? 'file(s)' : 'a file' }},
                    </button>
                    use drag and drop or paste it
                  </p>
                  <p class="mt-1 text-xs text-gray-500">
                    Up to {{ mbLimit }}mb
                  </p>
                </div>
              </template>
            </div>
            <div v-if="files.length" class="mt-4">
              <div class="border rounded-md">
                <div v-for="file,index in files" class="flex p-2" :class="{'border-t':index!==0}">
                  <p class="flex-grow truncate text-gray-500">
                    {{ file.file.name }}
                  </p>
                  <div>
                    <a href="#" class="text-gray-400 dark:text-gray-600 hover:text-nt-blue flex" role="button"
                       @click.prevent="clearFile(index)"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                           stroke="currentColor"
                      >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                        />
                      </svg>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </modal>
  </div>
</template>

<script>
import Modal from '../Modal.vue'
import axios from 'axios'
import inputMixin from '~/mixins/forms/input.js'

export default {
  name: 'FileInput',

  components: { Modal },
  mixins: [inputMixin],
  props: {
    multiple: { type: Boolean, default: true },
    mbLimit: { type: Number, default: 5 },
    accept: { type: String, default: '' },
    moveToFormAssets: { type: Boolean, default: false }
  },

  data: () => ({
    showUploadModal: false,

    files: [],
    uploadDragoverTracking: false,
    uploadDragoverEvent: false,
    loading: false
  }),

  computed: {
    currentUrl () {
      return this.form[this.name]
    },
    acceptExtensions () {
      if (this.accept) {
        return this.accept.split(',').map((i) => {
          return '.' + i.trim()
        }).join(',')
      }
      return ''
    }
  },

  watch: {
    showUploadModal: {
      handler (val) {
        if(this.disabled){
          this.showUploadModal = false
        }
        document.removeEventListener('paste', this.onUploadPasteEvent)
        if(this.showUploadModal){
          document.addEventListener("paste", this.onUploadPasteEvent)
        }
      }
    },
    files: {
      deep: true,
      handler (files) {
        this.compVal = files.map(file => file.url)
      }
    }
  },

  async created () {
    if(this.compVal && this.compVal.length > 0) {
      let tmpFiles = []
      for (let i = 0; i < this.compVal.length; i++) {
        await this.getFileFromUrl(this.compVal[i]).then((fileObj) => {
          tmpFiles.push({
            file: fileObj,
            url: this.compVal[i]
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
      if(!this.showUploadModal) return
      this.uploadDragoverEvent = false
      this.uploadDragoverTracking = false
      this.droppedFiles(e.clipboardData.files)
    },
    droppedFiles (droppedFiles) {
      if (!droppedFiles) return

      for (let i = 0; i < droppedFiles.length; i++) {
        this.uploadFileToServer(droppedFiles.item(i))
      }
    },
    openFileUpload () {
      this.$refs['actual-input'].click()
    },
    manualFileUpload (e) {
      const files = e.target.files
      for (let i = 0; i < files.length; i++) {
        this.uploadFileToServer(files.item(i))
      }
    },
    uploadFileToServer (file) {
      this.loading = true
      this.storeFile(file).then(response => {
        if (!this.multiple) {
          this.files = []
        }
        if (this.moveToFormAssets) {
          // Move file to permanent storage for form assets
          axios.post('/api/open/forms/assets/upload', {
            type: 'files',
            url: file.name.split('.').slice(0, -1).join('.') + '_' + response.uuid + '.' + response.extension
          }).then(moveFileResponse => {
            this.files.push({
              file: file,
              url: moveFileResponse.data.url
            })
            this.showUploadModal = false
            this.loading = false
          }).catch((error) => {
            this.showUploadModal = false
            this.loading = false
          })
        } else {
          this.files.push({
            file: file,
            url: file.name.split('.').slice(0, -1).join('.') + '_' + response.uuid + '.' + response.extension
          })
          this.showUploadModal = false
          this.loading = false
        }
      }).catch((error) => {
        this.clearAll()
        this.showUploadModal = false
        this.loading = false
      })
    },
    async getFileFromUrl(url, defaultType='image/jpeg'){
      const response = await fetch(url)
      const data = await response.blob()
      const name = url.replace(/^.*(\\|\/|\:)/, '')
      return new File([data], name, {
        type: data.type || defaultType,
      })
    }
  }
}
</script>
