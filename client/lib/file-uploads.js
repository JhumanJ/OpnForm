import opnformConfig from "~/opnform.config.js";

export const storeFile = async (file, options = {}) => {
  if(!opnformConfig.s3_enabled) { // If not s3 then upload to local temp
    let formData = new FormData()
    formData.append('file', file)
    const response = await useOpnApi('/upload-file', {
      method: 'POST',
      body: formData
    })
    response.data.extension = file.name.split('.').pop()
    return response.data
  }

  const response = await useOpnApi(options.signedStorageUrl ? options.signedStorageUrl : '/vapor/signed-storage-url', {
    method: 'POST',
    body: options.data,
    bucket: options.bucket || '',
    content_type: options.contentType || file.type,
    expires: options.expires || '',
    visibility: options.visibility || '',
    baseURL: options.baseURL || null,
    headers: options.headers || {},
    ...options.options
  })

  console.log(response)

  const headers = response.data.headers

  if ('Host' in headers) {
    delete headers.Host
  }

  if (typeof options.progress === 'undefined') {
    options.progress = () => {}
  }

  // Upload to S3
  await useFetch(response.data.url,{
    method: 'PUT',
    body: file,
    headers: headers,
  })

  response.data.extension = file.name.split('.').pop()

  return response.data
}
