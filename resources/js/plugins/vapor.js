import Vue from 'vue'
const axios = require('axios')

const assetUrl = process.env.MIX_VAPOR_ASSET_URL
  ? process.env.MIX_VAPOR_ASSET_URL
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
