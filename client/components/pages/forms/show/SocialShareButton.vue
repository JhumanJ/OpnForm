<template>
  <UDropdownMenu :items="shareItems" :content="{ align: 'end' }" arrow>
    <UTooltip text="Share">
      <UButton
        variant="outline"
        color="neutral"
        icon="i-heroicons-share"
      />
    </UTooltip>
  </UDropdownMenu>
</template>

<script>
export default {
  name: "SocialShareButton",
  props: {
    form: {
      type: Object,
      required: true,
    },
    shareUrl: {
      type: String,
      required: true,
    },
  },

  computed: {
    shareItems() {
      return [
        [{
          label: 'Share via Email',
          icon: 'i-heroicons-envelope',
          onSelect: () => this.shareViaEmail()
        }, {
          label: 'Share on Twitter',
          icon: 'i-simple-icons-twitter',
          onSelect: () => this.shareOnTwitter()
        }, {
          label: 'Share on LinkedIn',
          icon: 'i-simple-icons-linkedin',
          onSelect: () => this.shareOnLinkedIn()
        }]
      ]
    }
  },

  methods: {
    shareViaEmail() {
      const subject = encodeURIComponent(`Check out this form: ${this.form.title}`)
      const body = encodeURIComponent(`I'd like to share this form with you: ${this.shareUrl}`)
      window.open(`mailto:?subject=${subject}&body=${body}`)
    },

    shareOnTwitter() {
      const text = encodeURIComponent(`Check out this form: ${this.form.title}`)
      const url = encodeURIComponent(this.shareUrl)
      window.open(`https://twitter.com/intent/tweet?text=${text}&url=${url}`, '_blank')
    },

    shareOnLinkedIn() {
      const url = encodeURIComponent(this.shareUrl)
      window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${url}`, '_blank')
    },
  },
}
</script> 