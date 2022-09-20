<template>
  <iframe v-if="!isDarkMode" id="testimonialto-carousel-all-notionforms"
          loading="lazy"
          src="https://embed.testimonial.to/carousel/all/notionforms?theme=light&autoplay=on&showmore=on&one-row=on&same-height=off"
          frameBorder="0" scrolling="no" width="100%"
  />
  <iframe v-else id="testimonialto-carousel-all-notionforms" src="https://embed.testimonial.to/carousel/all/notionforms?theme=dark&autoplay=on&showmore=on&one-row=on&same-height=off" frameborder="0" scrolling="no" width="100%" />
</template>

<script>
export default {

  props: {
    featuresOnly: {
      type: Boolean,
      default: false
    }
  },
  data: () => ({}),

  computed: {
    isDarkMode () {
      return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches
    }
  },

  mounted () {
    this.loadScript()
  },

  methods: {
    loadScript () {
      const script = document.createElement('script')
      script.setAttribute('src', 'https://testimonial.to/js/iframeResizer.min.js')
      document.head.appendChild(script)
      script.addEventListener('load', function () {
        window.iFrameResize({
          log: false,
          checkOrigin: false
        }, '#testimonialto-carousel-all-notionforms')
      })
    }
  }
}
</script>
