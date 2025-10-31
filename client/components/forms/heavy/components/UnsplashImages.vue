<template>
  <div class="unsplash-images-container">
    <div class="py-2">
      <UInput
        v-model="searchTerm"
        variant="outline"
        class="w-full"
        placeholder="Search image..."
        icon="i-heroicons-magnifying-glass-solid"
        :ui="{ trailing: 'pe-1' }"
      >
        <template v-if="searchTerm?.length" #trailing>
          <UButton
            color="neutral"
            variant="link"
            size="sm"
            icon="i-heroicons-x-mark-20-solid"
            aria-label="Clear"
            title="Clear"
            @click="searchTerm = ''"
          />
        </template>
      </UInput>
    </div>

    <div v-if="loading">
      <Loader class="h-6 w-6 text-blue-500 mx-auto" />
    </div>
    <div v-else class="grid grid-cols-3 gap-2 p-5 mb-5 overflow-y-scroll max-h-[24rem] border rounded-md bg-neutral-50">
      <div v-for="image in images" :key="image.id" class=" cursor-pointer" @click="selectImage(image)">
        <img :src="image.url" :alt="image.alt_text" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { contentApi } from "~/api"
import { refDebounced } from '@vueuse/core'

const loading = ref(true)
const images = ref([])
const searchTerm = ref("")
const debouncedTerm = refDebounced(searchTerm, 300)

const emit = defineEmits(['selectImage'])

const buildOptions = (term = "") => term ? { query: { term } } : {}

const fetchUnsplashImages = async (term = "") => {
  loading.value = true
  const options = buildOptions(term)
  await contentApi.unsplash.list(options).then((response) => {
    images.value = response
    loading.value = false
  })
}

const selectImage = (image) => {
  emit('selectImage', image.url)
}

onMounted(() => fetchUnsplashImages())

watch(debouncedTerm, (term) => {
  fetchUnsplashImages(term?.trim() || "")
})

</script>