<template>
  <div>
    <v-button
      class="w-full"
      color="light-gray"
      v-track.share_embed_form_popup_click="{form_id:form.id, form_slug:form.slug}"
      @click="showEmbedFormAsPopupModal=true"
    >
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-4 text-blue-600 inline" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.068.157 2.148.279 3.238.364.466.037.893.281 1.153.671L12 21l2.652-3.978c.26-.39.687-.634 1.153-.67 1.09-.086 2.17-.208 3.238-.365 1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"
        />
      </svg>
      Embed form as popup
    </v-button>

    <modal :show="showEmbedFormAsPopupModal" @close="onClose">
      <template #icon>
        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-blue" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round"
              d="M2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.068.157 2.148.279 3.238.364.466.037.893.281 1.153.671L12 21l2.652-3.978c.26-.39.687-.634 1.153-.67 1.09-.086 2.17-.208 3.238-.365 1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"
          />
        </svg>
      </template>
      <template #title>
        <span>Add the popup to your website</span>
      </template>

      <div class="p-4">
        <h3 class="border-t text-xl font-semibold mb-2 pt-6">
          Demo
        </h3>
        <p class="pb-6">
          A live preview of your form popup was just added to this page. <span class="font-semibold text-blue-800">Click on the button on the bottom
            {{ advancedOptions.position }} corner to try it</span>.
        </p>  

        <h3 class="border-t text-xl font-semibold mb-2 pt-6">
          How does it work?
        </h3>
        <p>Paste the following code snippet in the <b>&lt;head&gt;</b> section of your website.</p>

        <div
          class="border border-nt-blue-light bg-blue-50 dark:bg-notion-dark-light rounded-md p-4 mb-5 w-full mx-auto mt-4 select-all"
        >
          <div class="flex items-center">
            <p class="select-all text-nt-blue flex-grow break-all">
              {{ embedPopupCode }}
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

        <collapse class="py-5 w-full border rounded-md px-4" :default-value="false">
          <template #title>
            <div class="flex">
              <h3 class="font-semibold block text-lg">
                Advanced options
              </h3>
            </div>
          </template>
          <div class="border-t mt-4 -mx-4" />
          <toggle-switch-input v-model="advancedOptions.hide_title" name="hide_title" class="mt-4"
                              label="Hide Form Title"
                              :disabled="form.hide_title===true"
                              :help="hideTitleHelp"
          />
          <color-input v-model="advancedOptions.bgcolor" name="bgcolor" class="mt-4"
                      label="Circle Background Color"
          />
          <text-input v-model="advancedOptions.emoji" name="emoji" class="mt-4"
                      label="Emoji" :max-char-limit="2"
          />
          <flat-select-input v-model="advancedOptions.position" name="position" class="mt-4"
                            label="Position"
                            :options="[
                              {name:'Bottom Right',value:'right'},
                              {name:'Bottom Left',value:'left'},
                            ]"
          />
          <text-input v-model="advancedOptions.width" name="width" class="mt-4"
                      label="Form pop max width (px)" native-type="number"
          />
        </collapse>

        <div class="flex justify-end mt-4">
          <v-button color="gray" shade="light" @click="onClose">
            Close
          </v-button>
        </div>
      </div>
    </modal>
  </div>
</template>

<script>
import Collapse from '../../../common/Collapse.vue'

export default {
  name: 'EmbedFormAsPopupModal',
  components: { Collapse },
  props: {
    form: { type: Object, required: true }
  },

  data: () => ({
    showEmbedFormAsPopupModal: false,
    embedScriptUrl: 'widgets/embed-min.js',
    advancedOptions: {
      hide_title: false,
      emoji: '💬',
      position: 'right',
      bgcolor: '#3B82F6',
      width: '500'
    }
  }),

  computed: {
    hideTitleHelp () {
      return this.form.hide_title ? 'This option is disabled because the form title is already hidden' : null
    },
    shareUrl () {
      return (this.advancedOptions.hide_title) ? this.form.share_url + '?hide_title=true' : this.form.share_url
    },
    embedPopupCode () {
      const nfData = {
        formurl: this.shareUrl,
        emoji: this.advancedOptions.emoji,
        position: this.advancedOptions.position,
        bgcolor: this.advancedOptions.bgcolor,
        width: this.advancedOptions.width
      }
      this.previewPopup(nfData)
      return '<script async data-nf=\'' + JSON.stringify(nfData) + '\' src=\'' + this.asset(this.embedScriptUrl) + '\'></scrip' + 't>'
    }
  },

  mounted () {
    this.advancedOptions.bgcolor = this.form.color
  },

  methods: {
    onClose () {
      this.removePreview()
      this.$crisp.push(['do', 'chat:show'])
      this.showEmbedFormAsPopupModal = false
    },
    copyToClipboard () {
      const str = this.embedPopupCode
      const el = document.createElement('textarea')
      el.value = str
      document.body.appendChild(el)
      el.select()
      document.execCommand('copy')
      document.body.removeChild(el)
    },
    removePreview () {
      const oldP = document.head.querySelector('#nf-popup-preview')
      if (oldP) {
        oldP.remove()
      }
      const oldM = document.body.querySelector('.nf-main')
      if (oldM) {
        oldM.remove()
      }
    },
    previewPopup (nfData) {
      if (!this.showEmbedFormAsPopupModal) {
        return
      }

      // Remove old preview, if there
      this.removePreview()

      // Hide crisp
      this.$crisp.push(['do', 'chat:hide'])

      // Add new preview
      const el = document.createElement('script')
      el.id = 'nf-popup-preview'
      el.async = true
      el.src = this.asset(this.embedScriptUrl)
      el.setAttribute('data-nf', JSON.stringify(nfData))
      document.head.appendChild(el)
    }
  }
}
</script>
