<template>
  <div class="w-full flex flex-col">
    <form-editor v-if="pageLoaded" ref="editor"
                 :style="{
                   'max-height': editorMaxHeight + 'px'
                 }"
                 :isEdit="true"
                 @mounted="onResize"
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
import Breadcrumb from '../../components/common/Breadcrumb'

import Form from 'vform'

import { mapState } from 'vuex'
const FormEditor = () => import('../../components/open/forms/components/FormEditor')

const loadForms = function () {
  store.commit('open/forms/startLoading')
  store.dispatch('open/workspaces/loadIfEmpty').then(() => {
    store.dispatch('open/forms/load', store.state['open/workspaces'].currentId)
  })
}

export default {
  name: 'EditForm',
  components: { Breadcrumb, FormEditor },

  beforeRouteEnter (to, from, next) {
    if (!store.getters['open/forms/getBySlug'](to.params.slug)) {
      loadForms()
    }
    next()
  },
  middleware: 'auth',

  data () {
    return {
      loading: false,
      error: null,
      editorMaxHeight: 500
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
    }
  },

  watch: {
    form () {
      this.updatedForm = new Form(this.form)
    }
  },

  created () {
    window.addEventListener('resize', this.onResize)
  },
  destroyed () {
    window.removeEventListener('resize', this.onResize)
  },

  mounted () {
    this.closeAlert()
    if (!this.form) {
      loadForms()
    } else {
      this.updatedForm = new Form(this.form)
    }
  },

  metaInfo () {
    return { title: 'Edit ' + (this.form ? this.form.title : 'Your Form') }
  },

  methods: {
    /**
     * Compute max height of editor
     */
    onResize () {
      if (this.$refs.editor) {
        this.editorMaxHeight = Math.max(500, window.innerHeight - this.$refs.editor.$el.offsetTop)
      }
    }
  }
}
</script>
