<template>
  <p class="text-xs">
    <span v-for="file in value" :key="file.file_url"
          class="whitespace-nowrap rounded-md transition-colors hover:decoration-none"
          :class="{'open-file text-gray-700 dark:text-gray-300 truncate':!isImage(file.file_url), 'open-file-img':isImage(file.file_url)}"
    >
      <a class="text-gray-700 dark:text-gray-300" :href="file.file_url" target="_blank"
         rel="nofollow"
      >
  <div v-if="isImage(file.file_url)" class="w-8 h-8">
    <img class="object-cover h-full w-full rounded" :src="file.file_url">
  </div>
  <span v-else
        class="py-1 px-2"
  >
          <a :href="file.file_url" target="_blank" download>{{ displayedFileName(file.file_name) }}</a>
        </span>
  </a>
  </span>
  </p>
</template>

<script>
export default {
  components: {},
  props: {
    value: {
      type: Array,
      required: false
    }
  },

  data() {
    return {}
  },

  computed: {},
  mounted() {
  },

  methods: {
    isImage(url) {
      return ['png', 'gif', 'jpg', 'jpeg', 'tif'].some((suffix) => {
        return url && url.endsWith(suffix)
      })
    },
    displayedFileName(fileName) {
      const extension = fileName.substr(fileName.lastIndexOf(".") + 1)
      const filename = fileName.substr(0, fileName.lastIndexOf("."))

      if (filename.length > 12) {
        return filename.substr(0, 12) + '(...).' + extension
      }
      return filename + '.' + extension
    }
  }
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
