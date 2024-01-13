
async function storeLocalFile(file, options={}) {
  let formData = new FormData()
  formData.append('file', file)
  const response = await opnFetch('/upload-file', {
    method: 'POST',
    body: formData
  })
  response.extension = file.name.split('.').pop()
  return response
}

export const storeFile = async (file, options = {}) => {
  if(!useRuntimeConfig().public.s3Enabled) return storeLocalFile(file, options)

  const response = await opnFetch(options.signedStorageUrl || 'vapor/signed-storage-url', {
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

  // Upload to S3
  await useFetch(response.url,{
    method: 'PUT',
    body: file,
  })

  response.extension = file.name.split('.').pop()

  return response
}
