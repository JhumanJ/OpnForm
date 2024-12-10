<template>
  <iframe
    :id="iframeId"
    title="OpnForm testimonial"
    loading="lazy"
    height="500px"
    :src="'https://embed-v2.testimonial.to/w/notionforms?theme=light&card=base&loadMore=on&initialCount=8&tag=all'"
    frameBorder="0"
    scrolling="no"
    width="100%"
  />
</template>

<script>
export default {
  props: {
    featuresOnly: {
      type: Boolean,
      default: false
    }
  },

  data: () => ({
    iframeId: 'testimonialto-carousel-all-notionforms'
  }),

  computed: {},

  mounted () {
    window.addEventListener('load', () => {
      this.loadScript()
    })
  },

  methods: {
    loadScript () {
      if (import.meta.server)
        return
      const script = document.createElement('script')
      script.setAttribute(
        'src',
        'https://testimonial.to/js/iframeResizer.min.js'
      )
      script.setAttribute('defer', 'defer')
      document.head.appendChild(script)
      script.addEventListener('load', () => {
        window.iFrameResize(
          {
            log: false,
            checkOrigin: false
          },
          '#' + this.iframeId
        )
      })
    }
  }
}
</script>


