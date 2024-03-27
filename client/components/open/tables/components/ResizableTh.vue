<template>
  <th ref="th" :style="{width: width}">
    <slot/>
    <div v-if="allowResize" class="absolute right-0 top-0 w-0 z-10">
      <div class="resize-handler bg-transparent cursor-move	 hover:bg-blue-500 opacity-80 transition-colors"
           @mousedown="mouseDownHandler"
      />
    </div>
  </th>
</template>

<script>
export default {
  components: {},
  props: {
    allowResize: {
      required: true
    },
    width: {
      required: true
    }
  },

  data() {
    return {
      x: 0,
      w: 0,
      lastEmit: Date.now(),
      throttlePeriod: 50 // milliseconds
    }
  },

  methods: {
    mouseDownHandler(e) {
      if (import.meta.server) return
      // Get the current mouse position
      this.x = e.clientX

      // Calculate the dimension of element
      const styles = window.getComputedStyle(this.$refs.th)
      this.w = parseInt(styles.width, 10)

      // Attach the listeners to `document`
      document.addEventListener('mousemove', this.mouseMoveHandler)
      document.addEventListener('mouseup', this.mouseUpHandler)
    },
    mouseMoveHandler(e) {
      const now = Date.now()
      if (now - this.lastEmit > this.throttlePeriod) {
        const dx = e.clientX - this.x
        this.$emit('resize-width', this.w + dx)
        this.lastEmit = now
      }
    },
    mouseUpHandler() {
      // Remove the handlers of `mousemove` and `mouseup`
      document.removeEventListener('mousemove', this.mouseMoveHandler)
      document.removeEventListener('mouseup', this.mouseUpHandler)
    }
  }
}
</script>
