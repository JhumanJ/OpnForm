<template>
  <div>
    <h3 class="font-semibold text-xl">QR Code</h3>
    <p>Scan the QR code to open the form (Right click to copy the image)</p>
    <div class="flex items-center">
      <img v-if="QrUrl" :src="QrUrl" class="m-auto" />
    </div>
  </div>
</template>

<script>
import QRCode from 'qrcode'
export default {
  name: 'FormQrCode',
  props: {
    form: { type: Object, required: true },
    extraQueryParam: { type: String, default: '' }
  },

  data () {
    return {
      QrUrl: null
    }
  },

  computed: {
    shareUrl () {
      return (this.extraQueryParam) ? this.form.share_url + "?" + this.extraQueryParam : this.form.share_url + this.extraQueryParam
    }
  },

  watch: {
    shareUrl () {
      this.generateQR()
    }
  },

  mounted () {
    this.generateQR()
  },

  methods: {
    generateQR () {
      QRCode.toDataURL(this.shareUrl).then(url => {
        this.QrUrl = url
      })
    }
  }
}
</script>
