
import { defineStore } from 'pinia'
import { nextTick } from 'vue'

export const useAppStore = defineStore('app', {
  state: () => ({
    layout: 'default',

    // App Loader
    loader: {
      percent: 0,
      show: false,
      canSuccess: true,
      duration: 3000,
      _timer: null,
      _cut: null
    }
  }),
  actions: {
    setLayout (layout) {
      this.layout = layout ?? 'default'
    },
    loaderIncrease (num) {
      this.loader.percent = this.loader.percent + Math.floor(num)
    },
    loaderDecrease (num) {
      this.loader.percent = this.loader.percent - Math.floor(num)
    },
    loaderFinish () {
      this.loader.percent = 100
      this.loaderHide()
    },
    loaderSetTimer (timerVal) {
      this._timer = timerVal
    },
    loaderPause () {
      clearInterval(this.loader._timer)
    },
    loaderHide () {
      clearInterval(this.loader._timer)
      this.loader._timer = null
      setTimeout(() => {
        this.loader.show = false
        nextTick(() => {
          setTimeout(() => {
            this.loader.percent = 0
          }, 200)
        })
      }, 500)
    },
    loaderFail () {
      this.loader.canSuccess = false
    },
    loaderStart () {
      this.loader.show = true
      this.loader.canSuccess = true
      if (this.loader._timer) {
        clearInterval(this.loader._timer)
        this.loader.percent = 0
      }
      this.loader._cut = 10000 / Math.floor(this.loader.duration)

      this.loaderSetTimer(setInterval(() => {
        this.loaderIncrease(this.loader._cut * Math.random())
        if (this.loader.percent > 95) {
          this.loaderFinish()
        }
      }, 100))
    }
  }
})