<template>
  <div
    class="border border-nt-blue-light bg-blue-50 dark:bg-notion-dark-light rounded-md p-4 mb-5 w-full mx-auto mt-4 select-all"
  >
    <div class="flex items-center">
      <p class="select-all text-nt-blue flex-grow">
        {{ embedCode }}
      </p>
      <div class="hover:bg-nt-blue-lighter rounded transition-colors cursor-pointer" @click="copyToClipboard">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-nt-blue" fill="none" viewBox="0 0 24 24"
             stroke="currentColor"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"
          />
        </svg>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'EmbedFormCode',
  props: {
    form: {
      type: Object,
      required: true
    }
  },

  data () {
    return {}
  },

  computed: {
    embedCode () {
      return '<iframe style="border:none;width:100%;" height="' + this.formHeight + 'px" src="' + this.form.share_url + '"></iframe>'
    },
    formHeight () {
      let height = 200
      if (!this.form.hide_title) {
        height += 60
      }
      height += this.form.properties.filter((property) => {
        return !property.hidden
      }).length * 70

      return height
    }
  },

  watch: {},

  mounted () {
  },

  methods: {
    copyToClipboard () {
      const str = this.embedCode
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
