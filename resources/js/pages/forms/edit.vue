<template>
  <div class="w-full flex flex-col">
    <form-editor v-if="pageLoaded" ref="editor"
                 :isEdit="true"
                 @on-save="formInitialHash=null"
    />
    <div v-else-if="!loading && error" class="mt-4 rounded-lg max-w-xl mx-auto p-6 bg-red-100 text-red-500">
      {{ error }}
    </div>
    <div v-else class="text-center mt-4 py-6">
      <loader class="h-6 w-6 text-nt-blue mx-auto" />
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import store from '~/store'
import Breadcrumb from '../../components/common/Breadcrumb.vue'
import Form from 'vform'
import { mapState } from 'vuex'
import SeoMeta from '../../mixins/seo-meta.js'

const loadForms = function () {
  store.commit('open/forms/startLoading')
  store.dispatch('open/workspaces/loadIfEmpty').then(() => {
    store.dispatch('open/forms/load', store.state['open/workspaces'].currentId)
  })
}

export default {
  name: 'EditForm',
  components: { Breadcrumb },

  beforeRouteEnter (to, from, next) {
    if (!store.getters['open/forms/getBySlug'](to.params.slug)) {
      loadForms()
    }
    store.commit('open/working_form/set', null) // Reset old working form
    next()
  },

  beforeRouteLeave (to, from, next) {
    if (this.isDirty()) {
      return this.alertConfirm('Changes you made may not be saved. Are you sure want to leave?', () => {
        window.onbeforeunload = null
        next()
      }, () => {})
    }
    next()
  },

  middleware: 'auth',
  mixins: [SeoMeta],

  data () {
    return {
      loading: false,
      error: null,
      formInitialHash: null
    }
  },

  computed: {
    ...mapState({
      formsLoading: state => state['open/forms'].loading
    }),
    updatedForm: {
      get () {
        return this.$store.state['open/working_form'].content
      },
      /* We add a setter */
      set (value) {
        this.$store.commit('open/working_form/set', value)
      }
    },
    form () {
      return this.$store.getters['open/forms/getBySlug'](this.$route.params.slug)
    },
    pageLoaded () {
      return !this.loading && this.updatedForm !== null
    },
    metaTitle () {
      return 'Edit ' + (this.form ? this.form.title : 'Your Form')
    },
  },

  watch: {
    form () {
      this.updatedForm = new Form(this.form)
    }
  },

  created () {},
  destroyed () {},

  mounted () {
    window.onbeforeunload = () => {
      if (this.isDirty()) {
        return false
      }
    }

    this.closeAlert()
    if (!this.form) {
      loadForms()
    } else {
      this.updatedForm = new Form(this.form)
      this.formInitialHash = this.hashString(JSON.stringify(this.updatedForm.data()))
    }

    if(!this.updatedForm.notification_settings || Array.isArray(this.updatedForm.notification_settings)){
      this.updatedForm.notification_settings = {}
    }
  },
  
  methods: {
    isDirty () {
      return !this.loading && this.formInitialHash && this.formInitialHash !== this.hashString(JSON.stringify(this.updatedForm.data()))
    },
    hashString (str, seed = 0) {
      let h1 = 0xdeadbeef ^ seed
      let h2 = 0x41c6ce57 ^ seed
      for (let i = 0, ch; i < str.length; i++) {
        ch = str.charCodeAt(i)
        h1 = Math.imul(h1 ^ ch, 2654435761)
        h2 = Math.imul(h2 ^ ch, 1597334677)
      }
      h1 = Math.imul(h1 ^ (h1 >>> 16), 2246822507) ^ Math.imul(h2 ^ (h2 >>> 13), 3266489909)
      h2 = Math.imul(h2 ^ (h2 >>> 16), 2246822507) ^ Math.imul(h1 ^ (h1 >>> 13), 3266489909)
      return 4294967296 * (2097151 & h2) + (h1 >>> 0)
    }
  }
}
</script>
