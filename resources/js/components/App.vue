<template>
  <div id="app" class="bg-white dark:bg-notion-dark">
    <loading v-show="!isIframe" ref="loading" />

<!--    <hotjar />-->
    <amplitude />
    <crisp />
<!--    <llamafi />-->

    <transition enter-active-class="linear duration-200 overflow-hidden"
                enter-class="max-h-0"
                enter-to-class="max-h-screen"
                leave-active-class="linear duration-200 overflow-hidden"
                leave-class="max-h-screen"
                leave-to-class="max-h-0"
    >
      <div v-if="announcement && !isIframe" class="bg-nt-blue text-white text-center p-3 relative">
        <a class="text-white font-semibold" href="" target="_blank">🚨
          OpnForm beta is over 🚨</a>
        <div role="button" class="text-white absolute right-0 top-0 p-3 cursor-pointer" @click="announcement=false">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd"
                  d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                  clip-rule="evenodd"
            />
          </svg>
        </div>
      </div>
    </transition>

    <transition name="page" mode="out-in">
      <component :is="layout" v-if="layout" />
    </transition>
    <portal-target name="modals" multiple />
    <stop-impersonation />
    <notifications />
  </div>
</template>

<script>
import Loading from './Loading.vue'
import Hotjar from './service/Hotjar.vue'
import Amplitude from './service/Amplitude.vue'
import Crisp from './service/Crisp.vue'
import StopImpersonation from './pages/StopImpersonation.vue'
import Notifications from "./common/Notifications.vue"
import SeoMeta from '../mixins/seo-meta.js'

// Load layout components dynamically.
const requireContext = import.meta.glob('../layouts/**.vue', { eager: true })

const layouts = {}
Object.keys(requireContext)
  .map(file =>
    [file.match(/[^/]*(?=\.[^.]*$)/)[0], requireContext[file]]
  )
  .forEach(([name, component]) => {
    layouts[name] = component.default || component
  })

export default {
  el: '#app',

  components: {
    Notifications,
    StopImpersonation,
    Crisp,
    Amplitude,
    Hotjar,
    Loading
  },

  mixins: [SeoMeta],

  data: () => ({
    metaTitle: 'OpnForm',
    metaDescription: 'Create beautiful forms for free. Unlimited fields, unlimited submissions. It\'s free and it takes less than 1 minute to create your first form.',
    layout: null,
    defaultLayout: 'default',
    announcement: false,
    alert: {
      type: null,
      autoClose: 0,
      message: '',
      confirmationProceed: null,
      confirmationCancel: null
    },
    navbarHidden: false
  }),

  mounted () {
    this.$loading = this.$refs.loading
  },

  methods: {
    /**
     * Set the application layout.
     *
     * @param {String} layout
     */
    setLayout (layout) {
      if (!layout || !layouts[layout]) {
        layout = this.defaultLayout
      }

      this.layout = layouts[layout]
    },
    workspaceAdded () {
      this.$router.push({ name: 'home' })
    },
    hideNavbar (hidden = true) {
      this.navbarHidden = hidden
    }
  },

  computed: {
    isIframe () {
      return window.location !== window.parent.location || window.frameElement
    },
    isOnboardingPage () {
      return this.$route.name === 'onboarding'
    }
  },

  watch: {
  }
}
</script>
