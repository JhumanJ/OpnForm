<template>
  <UPopover arrow>
    <TrackClick
      name="form_qr_code_click"
      :properties="{form_id: form.id, form_slug: form.slug}"
    >
      <UTooltip text="QR Code">
        <UButton
          variant="outline"
          color="neutral"
          icon="i-heroicons-qr-code"
        />
      </UTooltip>
    </TrackClick>

    <template #content>
      <div class="p-4 w-80">

        <h3 class="font-semibold text-medium">
          QR Code
        </h3>
        <p class="text-sm text-gray-600">Scan the QR code to open the form.</p>
        <div class="flex justify-center">
          <img
            v-if="QrUrl"
            ref="qrImage"
            :src="QrUrl"
            class="max-w-full h-auto"
            alt="QR Code for form"
          >
        </div>
        <div class="space-y-2">
          <TrackClick
            name="qr_code_copy_image_click"
            :properties="{form_id: form.id, form_slug: form.slug}"
          >
            <UButton
              @click="copyImage"
              :color="imageCopied ? 'success' : 'neutral'"
              :icon="imageCopied ? 'i-heroicons-check' : 'i-heroicons-document-duplicate'"
              variant="outline"
              block
            >
              {{ imageCopied ? 'Image Copied!' : 'Copy Image' }}
            </UButton>
          </TrackClick>
        </div>
      </div>
    </template>
  </UPopover>
</template>

<script>
import QRCode from "qrcode"
import { useClipboard } from '@vueuse/core'
import TrackClick from '~/components/global/TrackClick.vue'

export default {
  name: "FormQrCode",
  components: { TrackClick },
  props: {
    form: { type: Object, required: true },
    extraQueryParam: { type: String, default: "" },
  },

  data() {
    return {
      QrUrl: null,
      imageCopied: false,
    }
  },

  computed: {
    shareUrl() {
      return this.extraQueryParam
        ? this.form.share_url + "?" + this.extraQueryParam
        : this.form.share_url + this.extraQueryParam
    },
  },

  setup() {
    // Use VueUse clipboard for URL copying
    const { copy: copyToClipboard, copied: urlCopied } = useClipboard({
      copiedDuring: 2000 // Reset after 2 seconds
    })

    return {
      copyToClipboard,
      urlCopied
    }
  },

  watch: {
    shareUrl() {
      this.generateQR()
    },
  },

  mounted() {
    this.generateQR()
  },

  methods: {
    generateQR() {
      QRCode.toDataURL(this.shareUrl).then((url) => {
        this.QrUrl = url
      })
    },
    
    async copyImage() {
      try {
        // Convert data URL to blob
        const response = await fetch(this.QrUrl)
        const blob = await response.blob()
        
        // Copy to clipboard
        await navigator.clipboard.write([
          new ClipboardItem({
            [blob.type]: blob
          })
        ])
        
        // Show success state
        this.imageCopied = true
        setTimeout(() => {
          this.imageCopied = false
        }, 2000)
      } catch (error) {
        console.error('Failed to copy image:', error)
      }
    },
  },
}
</script>
