<template>
  <div class="border border-nt-blue-light bg-blue-50 dark:bg-notion-dark-light rounded-md p-4 mb-5 w-full mx-auto mt-4 select-all">
    <div class="flex items-center">
      <p class="select-all text-nt-blue flex-grow truncate">
        <a v-if="link" :href="form.share_url" target="_blank">
          {{ form.share_url }}
        </a>
        <span v-else>
          {{ form.share_url }}
        </span>
      </p>
      <div class="hover:bg-nt-blue-lighter rounded transition-colors cursor-pointer" @click="copyToClipboard(form.share_url)">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-nt-blue" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
        </svg>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ShareFormUrl',
  props: {
    form: {
      type: Object,
      required: true
    },
    link: {
      type: Boolean,
      default: false
    }
  },

  data () {
    return {
    }
  },

  computed: {
  },

  watch: {
  },

  mounted () {
  },

  methods: {
    copyToClipboard (str) {
      const el = document.createElement('textarea')
      el.value = str
      document.body.appendChild(el)
      el.select()
      document.execCommand('copy')
      document.body.removeChild(el)
    }
  }
}
</script>
