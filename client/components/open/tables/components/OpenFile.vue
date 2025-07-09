<template>
  <p class="text-xs">
    <span
      v-for="file in parsedFiles"
      :key="file.file_url"
      class="whitespace-nowrap rounded-md transition-colors hover:decoration-none"
      :class="{
        'open-file text-gray-700 dark:text-gray-300 truncate': !file.is_image,
        'open-file-img': file.is_image,
      }"
    >
      <a
        class="text-gray-700 dark:text-gray-300"
        :href="file.file_url"
        target="_blank"
        rel="nofollow"
      >
        <div
          v-if="file.is_image"
          class="w-8 h-8 rounded border border-neutral-200"
        >
          <img
            class="object-cover h-full w-full"
            :src="file.file_url"
            @error="failedImages.push(file.file_url)"
          >
        </div>
        <UBadge
          v-else
          size="sm"
          color="neutral"
          variant="ghost"
        >
          <a
            :href="file.file_url"
            target="_blank"
            download
          >{{
            file.displayed_file_name
          }}</a>
        </UBadge>
      </a>
    </span>
  </p>
</template>

<script setup>
const props = defineProps({
  value: {
    type: Array,
    required: false,
  },
})

const failedImages = ref([])

const parsedFiles = computed(() => {
  return props.value && Array.isArray(props.value)
    ? props.value.map((file) => {
        return {
          file_name: file.file_name,
          file_url: file.file_url,
          displayed_file_name: displayedFileName(file.file_name),
          is_image:
            !failedImages.value.includes(file.file_url) &&
            isImage(file.file_name),
        }
      })
    : []
})

const isImage = (fileName) => {
  return ["png", "gif", "jpg", "jpeg", "tif"].some((suffix) => {
    return fileName && fileName.endsWith(suffix)
  })
}

const displayedFileName = (fileName) => {
  const extension = fileName.substr(fileName.lastIndexOf(".") + 1)
  const filename = fileName.substr(0, fileName.lastIndexOf("."))

  if (filename.length > 10) {
    return filename.substr(0, 10) + "[...]." + extension
  }
  return filename + "." + extension
}
</script>

<style lang="scss">
.open-file {
  max-width: 120px;
  background-color: #e3e2e0;
}

.dark {
  .open-file {
    background-color: #5a5a5a;
  }
}
</style>
