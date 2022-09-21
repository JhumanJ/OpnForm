<template />

<script>
import { mapGetters } from 'vuex'

export default {

  name: 'Amplitude',

  data: function () {
    return {
      loaded: false,
      amplitudeInstance: null
    }
  },

  computed: {
    ...mapGetters({
      authenticated: 'auth/check',
      user: 'auth/user'
    })
  },

  watch: {
    $route () {
      this.loadAmplitude()
    },
    authenticated () {
      this.authenticateUser()
    }
  },

  mounted () {},

  methods: {
    authenticateUser () {
      if (this.loaded && this.authenticated) {
        this.amplitudeInstance.setUserId(this.user.id)
        this.amplitudeInstance.setUserProperties({
          email: this.user.email,
          subscribed: this.user.is_subscribed,
          enterprise_subscription: this.user.has_enterprise_subscription
        })
      }
    },
    loadAmplitude () {
      if (this.loaded || !typeof window.amplitude === 'undefined' || !window.config.amplitude_code) return

      (function (e, t) {
        const n = e.amplitude || { _q: [], _iq: {} }; const r = t.createElement('script')
        r.type = 'text/javascript'
        r.integrity = 'sha384-+EO59vL/X7v6VE2s6/F4HxfHlK0nDUVWKVg8K9oUlvffAeeaShVBmbORTC2D3UF+'
        r.crossOrigin = 'anonymous'; r.async = true
        r.src = 'https://cdn.amplitude.com/libs/amplitude-8.17.0-min.gz.js'
        r.onload = function () {
          if (!e.amplitude.runQueuedFunctions) {
            console.log('[Amplitude] Error: could not load SDK')
          }
        }
        const i = t.getElementsByTagName('script')[0]; i.parentNode.insertBefore(r, i)
        function s (e, t) {
          e.prototype[t] = function () {
            this._q.push([t].concat(Array.prototype.slice.call(arguments, 0))); return this
          }
        }
        const o = function () { this._q = []; return this }
        const a = ['add', 'append', 'clearAll', 'prepend', 'set', 'setOnce', 'unset', 'preInsert', 'postInsert', 'remove']
        for (let c = 0; c < a.length; c++) { s(o, a[c]) }n.Identify = o; const u = function () {
          this._q = []
          return this
        }
        const l = ['setProductId', 'setQuantity', 'setPrice', 'setRevenueType', 'setEventProperties']
        for (let p = 0; p < l.length; p++) { s(u, l[p]) }n.Revenue = u
        const d = ['init', 'logEvent', 'logRevenue', 'setUserId', 'setUserProperties', 'setOptOut', 'setVersionName', 'setDomain', 'setDeviceId', 'enableTracking', 'setGlobalUserProperties', 'identify', 'clearUserProperties', 'setGroup', 'logRevenueV2', 'regenerateDeviceId', 'groupIdentify', 'onInit', 'logEventWithTimestamp', 'logEventWithGroups', 'setSessionId', 'resetSessionId']
        function v (e) {
          function t (t) {
            e[t] = function () {
              e._q.push([t].concat(Array.prototype.slice.call(arguments, 0)))
            }
          }
          for (let n = 0; n < d.length; n++) { t(d[n]) }
        }v(n); n.getInstance = function (e) {
          e = (!e || e.length === 0 ? '$default_instance' : e).toLowerCase()
          if (!Object.prototype.hasOwnProperty.call(n._iq, e)) {
            n._iq[e] = { _q: [] }; v(n._iq[e])
          } return n._iq[e]
        }; e.amplitude = n
      })(window, document)

      this.amplitudeInstance = window.amplitude.getInstance()
      this.amplitudeInstance.init(window.config.amplitude_code)
      this.loaded = true
      this.authenticateUser()
    }
  }
}
</script>
