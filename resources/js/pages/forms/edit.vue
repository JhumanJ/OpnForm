<template>
  <div class="w-full flex flex-col">
    <div class="flex justify-center md:justify-between pb-2 px-2">
      <div class="hidden md:block" />
      <breadcrumb class="hidden md:flex sm:px-6 mx-auto max-w-lg" :path="breadcrumbs" />
      <v-button v-if="!loading && updatedForm"
                v-track.save_form_click
                lass="hidden md:block"
                :loading="updateFormLoading" @click="saveForm"
      >
        Save changes
      </v-button>
    </div>
    <form-editor v-if="pageLoaded" ref="editor"
                 :style="{
                   'max-height': editorMaxHeight + 'px'
                 }"
                 :validation-error-response="validationErrorResponse"
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

import saveUpdateAlert from '../../mixins/forms/saveUpdateAlert'
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
  mixins: [saveUpdateAlert],

  beforeRouteEnter (to, from, next) {
    if (!store.getters['open/forms/getBySlug'](to.params.slug)) {
      loadForms()
    }
    next()
  },
  beforeRouteLeave (to, from, next) {
    if(!this.isDirty() || confirm("Changes you made may not be saved. Are you sure want to leave?") === true){
      window.onbeforeunload = null
      next()
    }
    return false
  },

  middleware: 'auth',

  data () {
    return {
      loading: false,
      updateFormLoading: false,
      error: null,
      validationErrorResponse: null,

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
    breadcrumbs () {
      if (!this.form) {
        return [{ route: { name: 'home' }, label: 'Your Forms' }]
      }
      return [
        { route: { name: 'home' }, label: 'Your Forms' },
        { label: this.form ? this.form.title : 'Your Form', route: { name: 'forms.show', params: { slug: this.form.slug } } },
        { label: 'Edit' }
      ]
    },
    formEndpoint: () => '/api/open/forms/{id}/',
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
    window.onbeforeunload = () => {
      if(this.isDirty()){
        return false
      }
    };

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
    saveForm () {
      if (this.updateFormLoading) return

      this.updateFormLoading = true
      this.validationErrorResponse = null
      this.updatedForm.put(this.formEndpoint.replace('{id}', this.form.id)).then((response) => {
        const data = response.data
        this.$store.commit('open/forms/addOrUpdate', data.form)
        this.$router.push({ name: 'forms.show', params: { slug: this.form.slug } })
        this.$logEvent('form_saved', { form_id: this.form.id, form_slug: this.form.slug })
        this.displayFormModificationAlert(data)
      }).catch((error) => {
        if (error.response.status === 422) {
          this.validationErrorResponse = error.response.data
          this.$refs.editor.showValidationErrors()
        }
      }).finally(() => {
        this.updateFormLoading = false
      })
    },
    /**
     * Compute max height of editor
     */
    onResize () {
      if (this.$refs.editor) {
        this.editorMaxHeight = Math.max(500, window.innerHeight - this.$refs.editor.$el.offsetTop)
      }
    },
    isDirty(){
      return !this.updateFormLoading && JSON.stringify(this.form) !== JSON.stringify(this.updatedForm.data())
    }
  }
}
</script>
