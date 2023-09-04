<template>
  <div>
      <h3 class="font-semibold text-xl">Share Link</h3>
      <p>Your form is now published and ready to be shared with the world! Copy this link to share your form
          on social media, messaging apps or via email.</p>
      <copy-content :content="share_url" :is-draft="form.visibility=='draft'">
        <template #icon>
          <svg class="h-4 w-4 -mt-1 text-blue-600 inline mr-1" viewBox="0 0 20 10" fill="none"
                  xmlns="http://www.w3.org/2000/svg">
              <path
              d="M7.49984 9.16634H5.83317C3.53198 9.16634 1.6665 7.30086 1.6665 4.99967C1.6665 2.69849 3.53198 0.833008 5.83317 0.833008H7.49984M12.4998 9.16634H14.1665C16.4677 9.16634 18.3332 7.30086 18.3332 4.99967C18.3332 2.69849 16.4677 0.833008 14.1665 0.833008H12.4998M5.83317 4.99967L14.1665 4.99968"
              stroke="currentColor" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </template>
        Copy Link
      </copy-content>
  </div>
</template>
    
<script>
import CopyContent from '../../../open/forms/components/CopyContent.vue'

export default {
    name: 'ShareLink',
    components: { CopyContent },
    props: {
      form: { type: Object, required: true },
      extraQueryParam: { type: String, default: '' }
    },

    data: () => ({
      
    }),

    computed: {
      share_url () {
        return (this.extraQueryParam) ? this.form.share_url + '?' + this.extraQueryParam : this.form.share_url + this.extraQueryParam
      }
    },

    methods: {}
}
</script>
    