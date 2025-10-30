<template>
  <div class="unsplash-images-container">
    <div v-if="loading">
      <Loader class="h-6 w-6 text-blue-500 mx-auto" />
    </div>
    <div v-else class="grid grid-cols-3 gap-2 p-5 mb-5 overflow-y-scroll max-h-[24rem] border rounded-md bg-neutral-50">
      <div v-for="image in images" :key="image.id" class=" cursor-pointer" @click="selectImage(image)">
        <img :src="image.urls.regular" :alt="image.description" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { contentApi } from "~/api"

const loading = ref(true)
const images = ref([])

const emit = defineEmits(['selectImage'])

onMounted(() => {
  fetchUnsplashImages()
})

const fetchUnsplashImages = async () => {
  loading.value = true
  contentApi.unsplash.list().then((response) => {
    images.value = response
    loading.value = false
  })
}

const selectImage = (image) => {
  emit('selectImage', image.urls.regular)
}
</script> 