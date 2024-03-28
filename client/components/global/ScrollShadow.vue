<template>
  <div class="scroll-shadow max-w-full" :class="[$style.wrap,{'w-max':!shadow.left && !shadow.right}]">
    <div
      ref="scrollContainer"
      :class="[$style['scroll-container'],{'no-scrollbar':hideScrollbar}]"
      :style="{ width: width?width:'auto', height }"
      @scroll.passive="throttled.toggleShadow"
    >
      <slot />
      <span :class="[$style['shadow-top'], shadow.top && $style['is-active']]" :style="{
        top: shadowTopOffset+'px',
      }"
      />
      <span :class="[$style['shadow-right'], shadow.right && $style['is-active']]" />
      <span :class="[$style['shadow-bottom'], shadow.bottom && $style['is-active']]" />
      <span :class="[$style['shadow-left'], shadow.left && $style['is-active']]" />
    </div>
  </div>
</template>

<script>
import throttle from 'lodash/throttle'
function newResizeObserver (callback) {
  // Skip this feature for browsers which
  // do not support ResizeObserver.
  // https://caniuse.com/#search=resizeobserver
  if (typeof ResizeObserver === 'undefined') return

  return new ResizeObserver(e => e.map(callback))
}

export default {
  name: 'ScrollShadow',
  props: {
    hideScrollbar: {
      type: Boolean,
      default: false
    },
    shadowTopOffset: {
      type: Number,
      default: 0
    }
  },
  data () {
    return {
      width: undefined,
      height: undefined,
      shadow: {
        top: false,
        right: false,
        bottom: false,
        left: false
      },
      scrollContainerObserver: null,
      wrapObserver: null,
      throttled: {}
    }
  },
  mounted () {
    this.throttled.toggleShadow = throttle(this.toggleShadow, 100);
    this.throttled.calcDimensions = throttle(this.calcDimensions, 100);

    window.addEventListener('resize', this.throttled.calcDimensions)

    // Check if shadows are necessary after the element is resized.
    const scrollContainerObserver = newResizeObserver(this.throttled.toggleShadow)
    if (scrollContainerObserver) {
      scrollContainerObserver.observe(this.$refs.scrollContainer)
    }

    // Recalculate the container dimensions when the wrapper is resized.
    this.wrapObserver = newResizeObserver(this.throttled.calcDimensions)
    if (this.wrapObserver) {
      this.wrapObserver.observe(this.$el)
    }
  },
  unmounted () {
    window.removeEventListener('resize', this.throttled.calcDimensions)
    // Cleanup when the component is unmounted.
    this.wrapObserver.disconnect()
    if (this.scrollContainerObserver) {
      this.scrollContainerObserver.disconnect()
    }
  },
  methods: {
    async calcDimensions () {
      // Reset dimensions for correctly recalculating parent dimensions.
      this.width = undefined
      this.height = undefined
      await this.$nextTick()

      this.width = `${this.$el.clientWidth}px`
      this.height = `${this.$el.clientHeight}px`
    },
    // Check if shadows are needed.
    toggleShadow () {
      if (!this.$refs.scrollContainer) return
      const hasHorizontalScrollbar =
        this.$refs.scrollContainer.clientWidth <
        this.$refs.scrollContainer.scrollWidth
      const hasVerticalScrollbar =
        this.$refs.scrollContainer.clientHeight <
        this.$refs.scrollContainer.scrollHeight

      const scrolledFromLeft =
        this.$refs.scrollContainer.offsetWidth +
        this.$refs.scrollContainer.scrollLeft
      const scrolledFromTop =
        this.$refs.scrollContainer.offsetHeight +
        this.$refs.scrollContainer.scrollTop

      const scrolledToTop = this.$refs.scrollContainer.scrollTop === 0
      const scrolledToRight =
        scrolledFromLeft >= this.$refs.scrollContainer.scrollWidth
      const scrolledToBottom =
        scrolledFromTop >= this.$refs.scrollContainer.scrollHeight
      const scrolledToLeft = this.$refs.scrollContainer.scrollLeft === 0

      this.$nextTick(() => {
        this.shadow.top = hasVerticalScrollbar && !scrolledToTop
        this.shadow.right = hasHorizontalScrollbar && !scrolledToRight
        this.shadow.bottom = hasVerticalScrollbar && !scrolledToBottom
        this.shadow.left = hasHorizontalScrollbar && !scrolledToLeft
      })
    }
  }
}
</script>

<style lang="scss" module>
.wrap {
  overflow: hidden;
  position: relative;
}

.scroll-container {
  overflow: auto;
}

.shadow-top,
.shadow-right,
.shadow-bottom,
.shadow-left {
  position: absolute;
  border-radius: 6em;
  opacity: 0;
  transition: opacity 0.2s;
  pointer-events: none;
}

.shadow-top,
.shadow-bottom {
  right: 0;
  left: 0;
  height: 1em;
  border-top-right-radius: 0;
  border-top-left-radius: 0;
  background-image: linear-gradient(rgba(#555, 0.1) 0%, rgba(#FFF, 0) 100%);
}

.shadow-top {
  top: 0;
}

.shadow-bottom {
  bottom: 0;
  transform: rotate(180deg);
}

.shadow-right,
.shadow-left {
  top: 0;
  bottom: 0;
  width: 1em;
  border-top-left-radius: 0;
  border-bottom-left-radius: 0;
  background-image: linear-gradient(90deg, rgba(#555, 0.1) 0%, rgba(#FFF, 0) 100%);
}

.shadow-right {
  right: 0;
  transform: rotate(180deg);
}

.shadow-left {
  left: 0;
}

.is-active {
  opacity: 1;
}
</style>
