<template>
  <div class="flex flex-wrap flex-col">
    <transition v-if="stateReady" name="fade" mode="out-in">
      <div key="2">
        <create-form-base-modal @form-generated="formGenerated" :show="showInitialFormModal"
                                @close="showInitialFormModal=false"/>
        <form-editor v-if="!workspacesLoading" ref="editor"
                     class="w-full flex flex-grow"
                     :error="error"
                     :isGuest="isGuest"
                     @openRegister="openRegister"
        />
        <div v-else class="text-center mt-4 py-6">
          <loader class="h-6 w-6 text-nt-blue mx-auto"/>
        </div>
      </div>
    </transition>

    <quick-register :showRegisterModal="registerModal" @close="registerModal=false" @reopen="registerModal=true"
                    @afterLogin="afterLogin"/>

  </div>
</template>

<script>
import store from '~/store'
import Form from 'vform'
import {mapState, mapActions} from 'vuex'
import QuickRegister from '../auth/components/QuickRegister.vue'
import initForm from "../../mixins/form_editor/initForm.js"
import SeoMeta from '../../mixins/seo-meta.js'
import CreateFormBaseModal from "../../components/pages/forms/create/CreateFormBaseModal.vue"

const loadTemplates = function () {
  store.commit('open/templates/startLoading')
  store.dispatch('open/templates/loadIfEmpty').then(() => {
    store.commit('open/templates/stopLoading')
  })
}

export default {
  name: 'CreateFormGuest',
  mixins: [initForm, SeoMeta],
  components: {
    QuickRegister, CreateFormBaseModal
  },

  middleware: 'guest',

  beforeRouteEnter(to, from, next) {
    loadTemplates()
    next()
  },

  data() {
    return {
      metaTitle: 'Create a new Form as Guest',
      stateReady: false,
      loading: false,
      error: '',
      registerModal: false,
      isGuest: true,
      showInitialFormModal: false
    }
  },

  computed: {
    ...mapState({
      workspaces: state => state['open/workspaces'].content,
      workspacesLoading: state => state['open/workspaces'].loading,
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
    }
  },

  mounted() {
    // Set as guest user
    const guestWorkspace = {
      id: null,
      name: "Guest Workspace",
      is_enterprise: false,
      is_pro: false
    }
    this.$store.commit('open/workspaces/set', [guestWorkspace])
    this.$store.commit('open/workspaces/setCurrentId', guestWorkspace.id)

    this.initForm()
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
    this.stateReady = true
  },

  created() {},
  destroyed() {},

  methods: {
    ...mapActions({
      loadWorkspaces: 'open/workspaces/load'
    }),
    openRegister() {
      this.registerModal = true
    },
    afterLogin() {
      this.registerModal = false
      this.isGuest = false
      this.loadWorkspaces()
      setTimeout(() => {
        if (this.$refs.editor) {
          this.$refs.editor.saveFormCreate()
        }
      }, 500)
    },
    formGenerated(form) {
      this.form = new Form({...this.form.data(), ...form})
    }
  }
}
</script>
