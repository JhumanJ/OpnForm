<template>
  <div class="flex flex-col">
    <div v-if="form && !isIframe && (form.logo_picture || form.cover_picture)">
      <div v-if="form.cover_picture">
        <div id="cover-picture" class="max-h-56 w-full overflow-hidden flex items-center justify-center">
          <img alt="Form Cover Picture" :src="form.cover_picture" class="w-full">
        </div>
      </div>
      <div v-if="form.logo_picture" class="w-full p-5 relative mx-auto"
           :class="{'pt-20':!form.cover_picture, 'md:w-3/5 lg:w-1/2 md:max-w-2xl': form.width === 'centered', 'max-w-7xl': (form.width === 'full' && !isIframe) }"
      >
        <img alt="Logo Picture" :src="form.logo_picture"
             :class="{'top-5':!form.cover_picture, '-top-10':form.cover_picture}"
             class="w-20 h-20 object-contain absolute left-5 transition-all"
        >
      </div>
    </div>
    <div class="w-full mx-auto px-4"
         :class="{'mt-6':!isIframe, 'md:w-3/5 lg:w-1/2 md:max-w-2xl': form && (form.width === 'centered'), 'max-w-7xl': (form && form.width === 'full' && !isIframe)}"
    >
      <div v-if="!formLoading && !form">
        <h1 class="mt-6" v-text="'Whoops'" />
        <p class="mt-6">
          Unfortunately we could not find this form. It may have been deleted by it's author.
        </p>
        <p class="mb-10 mt-4">
          <router-link :to="{name:'welcome'}">
            Create your form for free with OpnForm
          </router-link>
        </p>
      </div>
      <div v-else-if="formLoading">
        <p class="text-center mt-6 p-4">
          <loader class="h-6 w-6 text-nt-blue mx-auto" />
        </p>
      </div>
      <open-complete-form v-else ref="open-complete-form" :form="form" class="mb-10" @password-entered="passwordEntered" />
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import store from '~/store'
import { mapState } from 'vuex'
import OpenCompleteForm from '../../components/open/forms/OpenCompleteForm'
import Cookies from 'js-cookie'
import sha256 from 'js-sha256'
import SeoMeta from '../../mixins/seo-meta'

const isFrame = window.location !== window.parent.location || window.frameElement

function handleDarkMode (form) {
  // Dark mode
  const body = document.body
  if (form.dark_mode === 'dark') {
    body.classList.add('dark')
  } else if (form.dark_mode === 'light') {
    body.classList.remove('dark')
  } else if (form.dark_mode === 'auto' && isFrame) {
    // Remove dark mode if embed in a notion basic site
    let parentUrl
    try {
      parentUrl = window.location.ancestorOrigins[0]
    } catch (e) {
      parentUrl = (window.location !== window.parent.location)
        ? document.referrer
        : document.location.href
    }
    if (parentUrl.includes('.notion.site')) {
      body.classList.remove('dark')
    }
  }
}

function handleTransparentMode (form) {
  const isFrame = window.location !== window.parent.location || window.frameElement
  if (!isFrame || !form.transparent_background) return

  const app = document.getElementById('app')
  app.classList.remove('bg-white')
  app.classList.remove('dark:bg-notion-dark')
  app.classList.add('bg-transparent')
}

function loadForm (slug) {
  if (store.state['open/forms'].loading) return
  store.commit('open/forms/startLoading')
  return axios.get('/api/forms/' + slug).then((response) => {
    const form = response.data
    store.commit('open/forms/set', [response.data])

    // Custom code injection
    if (form.custom_code) {
      const scriptEl = document.createRange().createContextualFragment(form.custom_code)
      document.head.append(scriptEl)
    }

    handleDarkMode(form)
    handleTransparentMode(form)

    store.commit('open/forms/stopLoading')
  }).catch(() => {
    store.commit('open/forms/stopLoading')
  })
}

export default {
  components: { OpenCompleteForm },
  mixins: [SeoMeta],

  beforeRouteEnter (to, from, next) {
    if (window.$crisp) {
      window.$crisp.push(['do', 'chat:hide'])
    }
    next()
  },

  beforeRouteLeave (to, from, next) {
    if (window.$crisp) {
      window.$crisp.push(['do', 'chat:show'])
    }
    next()
  },

  data () {
    return {
      loading: false,
      submitted: false
    }
  },

  mounted () {
    loadForm(this.formSlug).then(() => {
      if (this.isIframe) return
      // Auto focus on first input
      const visibleElements = []
      document.querySelectorAll('input,button,textarea,[role="button"]').forEach(ele => {
        if (ele.offsetWidth !== 0 || ele.offsetHeight !== 0) {
          visibleElements.push(ele)
        }
      })
      if (visibleElements.length > 0) {
        visibleElements[0].focus()
      }
    })
  },

  methods: {
    passwordEntered (password) {
      Cookies.set('password-' + this.form.slug, sha256(password), { expires: 7 })
      loadForm(this.formSlug).then(() => {
        if (this.form.is_password_protected) {
          this.$refs['open-complete-form'].addPasswordError('Invalid password.')
        }
      })
    }
  },

  computed: {
    ...mapState({
      forms: state => state['open/forms'].content,
      formLoading: state => state['open/forms'].loading
    }),
    formSlug () {
      return this.$route.params.slug
    },
    form () {
      return this.$store.getters['open/forms/getBySlug'](this.formSlug)
    },
    isIframe () {
      return window.location !== window.parent.location || window.frameElement
    },
    metaTitle () {
      return this.form ? this.form.title : 'Create beautiful forms'
    },
    metaDescription () {
      return (this.form && this.form.description) ? this.form.description.substring(0,160) : null
    },
    metaImage () {
      return (this.form && this.form.cover_picture) ? this.form.cover_picture : null
    },
    metaTags () {
      return (this.form && this.form.can_be_indexed) ? [] : [{ name: 'robots', content: 'noindex' }]
    }
  }
}
</script>
