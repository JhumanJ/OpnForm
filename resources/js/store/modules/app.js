import { nextTick } from 'vue'

export const state = {
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
}

export const mutations = {
  setLayout (state, layout) {
    state.layout = layout ?? 'default'
  },
  loaderStart (state) {
    state.loader.show = true
    state.loader.canSuccess = true
    if (state.loader._timer) {
      clearInterval(state.loader._timer)
      state.loader.percent = 0
    }
    state.loader._cut = 10000 / Math.floor(state.loader.duration)
  },
  loaderIncrease (state, num) {
    state.loader.percent = state.loader.percent + Math.floor(num)
  },
  loaderDecrease (state, num) {
    state.loader.percent = state.loader.percent - Math.floor(num)
  },
  loaderFinish (state) {
    state.loader.percent = 100
    mutations.loaderHide(state)
  },
  loaderSetTimer (state, timerVal) {
    state._timer = timerVal
  },
  loaderPause (state) {
    clearInterval(state.loader._timer)
  },
  loaderHide (state) {
    clearInterval(state.loader._timer)
    state.loader._timer = null
    setTimeout(() => {
      state.loader.show = false
      nextTick(() => {
        setTimeout(() => {
          state.loader.percent = 0
        }, 200)
      })
    }, 500)
  },
  loaderFail () {
    state.loader.canSuccess = false
  }
}

export const actions = {
  loaderStart ({ commit, dispatch }) {
    mutations.loaderStart()
    mutations.loaderSetTimer(setInterval(() => {
      mutations.loaderIncrease(state.loader._cut * Math.random())
      if (state.loader.percent > 95) {
        mutations.loaderFinish()
      }
    }, 100))
  }
}
