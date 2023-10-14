<template>
  <div class="flex flex-wrap flex-col">
    <transition v-if="stateReady" name="fade" mode="out-in">
      <div key="2">
        <create-form-base-modal @form-generated="formGenerated" :show="showInitialFormModal"
                                @close="showInitialFormModal=false"/>
        <form-editor v-if="!workspacesLoading" ref="editor"
                     class="w-full flex flex-grow"
                     :error="error"
                     @on-save="formInitialHash=null"
        />
        <div v-else class="text-center mt-4 py-6">
          <loader class="h-6 w-6 text-nt-blue mx-auto"/>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
import store from '~/store'
import Form from 'vform'
import {mapState, mapActions} from 'vuex'
import initForm from "../../mixins/form_editor/initForm.js";
import SeoMeta from '../../mixins/seo-meta.js'
import CreateFormBaseModal from "../../components/pages/forms/create/CreateFormBaseModal.vue"

const loadTemplates = function () {
  store.commit('open/templates/startLoading')
  store.dispatch('open/templates/loadIfEmpty').then(() => {
    store.commit('open/templates/stopLoading')
  })
}

export default {
  name: 'CreateForm',

  mixins: [initForm, SeoMeta],
  components: {CreateFormBaseModal},

  beforeRouteEnter(to, from, next) {
    loadTemplates()
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

  data() {
    return {
      metaTitle: 'Create a new Form',
      stateReady: false,
      loading: false,
      error: '',
      showInitialFormModal: false,
      formInitialHash: null
    }
  },

  computed: {
    ...mapState({
      workspaces: state => state['open/workspaces'].content,
      workspacesLoading: state => state['open/workspaces'].loading,
      user: state => state.auth.user
    }),
    form: {
      get() {
        return this.$store.state['open/working_form'].content
      },
      /* We add a setter */
      set(value) {
        this.$store.commit('open/working_form/set', value)
      }
    },
    workspace() {
      return this.$store.getters['open/workspaces/getCurrent']()
    },
  },

  watch: {
    workspace() {
      if (this.workspace) {
        this.form.workspace_id = this.workspace.id
      }
    },
    user() {
      this.stateReady = true
    }
  },

  mounted() {
    window.onbeforeunload = () => {
      if (this.isDirty()) {
        return false
      }
    }

    this.initForm()
    this.formInitialHash = this.hashString(JSON.stringify(this.form.data()))
    if (this.$route.query.template !== undefined && this.$route.query.template) {
      const template = this.$store.getters['open/templates/getBySlug'](this.$route.query.template)
      if (template && template.structure) {
        this.form = new Form({...this.form.data(), ...template.structure})
      }
    } else {
      // No template loaded, ask how to start
      this.showInitialFormModal = true
    }
    this.closeAlert()
    this.loadWorkspaces()

    this.stateReady = this.user !== null
  },

  created() {},
  destroyed() {},

  methods: {
    ...mapActions({
      loadWorkspaces: 'open/workspaces/loadIfEmpty'
    }),
    formGenerated(form) {
      this.form = new Form({...this.form.data(), ...form})
    },
    isDirty () {
      return !this.loading && this.formInitialHash && this.formInitialHash !== this.hashString(JSON.stringify(this.form.data()))
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
