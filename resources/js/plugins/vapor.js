import Vue from 'vue'
import axios from "axios"

const assetUrl = import.meta.env.VITE_VAPOR_ASSET_URL
  ? import.meta.env.VITE_VAPOR_ASSET_URL
  : ''

Vue.mixin({
  methods: {
    asset (path) {
      return assetUrl + '/' + path
    },

    /**
     * Store a file in S3 and return its UUID, key, and other information.
     */
    async storeFile (file, options = {}) {
      if(!window.config.s3_enabled) { // If not s3 then upload to local temp
        if (typeof options.progress === 'undefined') {
          options.progress = () => {}
        }
        const cleanAxios = axios.create()
        let formData = new FormData();
        formData.append('file', file);
        const response = await cleanAxios.post('/upload-file', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          },
          onUploadProgress: (progressEvent) => {
            options.progress(progressEvent.loaded / progressEvent.total)
          }
        })
  
        response.data.extension = file.name.split('.').pop()
        return response.data
      }

      const response = await axios.post(options.signedStorageUrl ? options.signedStorageUrl : '/vapor/signed-storage-url', {
        bucket: options.bucket || '',
        content_type: options.contentType || file.type,
        expires: options.expires || '',
        visibility: options.visibility || '',
        ...options.data
      }, {
        baseURL: options.baseURL || null,
        headers: options.headers || {},
        ...options.options
      })

      const headers = response.data.headers

      if ('Host' in headers) {
        delete headers.Host
      }

      if (typeof options.progress === 'undefined') {
        options.progress = () => {}
      }

      const cancelToken = options.cancelToken || ''

      // Remove authorization headers
      const cleanAxios = axios.create()
      cleanAxios.defaults.headers.common = {}
      await cleanAxios.put(response.data.url, file, {
        cancelToken: cancelToken,
        headers: headers,
        onUploadProgress: (progressEvent) => {
          options.progress(progressEvent.loaded / progressEvent.total)
        }
      })

      response.data.extension = file.name.split('.').pop()

      return response.data
    }
  }
})
