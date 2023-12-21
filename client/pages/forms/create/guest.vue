<template>
  <div class="flex flex-wrap flex-col">
    <transition v-if="stateReady" name="fade" mode="out-in">
      <div key="2">
        <create-form-base-modal :show="showInitialFormModal" @form-generated="formGenerated"
                                @close="showInitialFormModal=false"
        />
        <form-editor v-if="!workspacesLoading" ref="editor"
                     class="w-full flex flex-grow"
                     :error="error"
                     :is-guest="isGuest"
                     @openRegister="openRegister"
        />
        <div v-else class="text-center mt-4 py-6">
          <Loader class="h-6 w-6 text-nt-blue mx-auto" />
        </div>
      </div>
    </transition>

    <quick-register :show-register-modal="registerModal" @close="registerModal=false" @reopen="registerModal=true"
                    @afterLogin="afterLogin"
    />
  </div>
</template>

<script>
import { computed } from 'vue'
import Form from 'vform'
import { useTemplatesStore } from '../../../stores/templates.js'
import { useWorkingFormStore } from '../../../stores/working_form.js'
import { useWorkspacesStore } from '../../../stores/workspaces.js'
import QuickRegister from '~/components/pages/auth/components/QuickRegister.vue'
import initForm from '../../../mixins/form_editor/initForm.js'
import SeoMeta from '../../../mixins/seo-meta.js'
import CreateFormBaseModal from '../../../components/pages/forms/create/CreateFormBaseModal.vue'

const loadTemplates = function () {
  const templatesStore = useTemplatesStore()
  templatesStore.startLoading()
  templatesStore.loadIfEmpty().then(() => {
    templatesStore.stopLoading()
  })
}

export default {
  name: 'CreateFormGuest',
  components: {
    QuickRegister, CreateFormBaseModal
  },
  mixins: [initForm, SeoMeta],
  middleware: 'guest',

  beforeRouteEnter (to, from, next) {
    loadTemplates()
    next()
  },

  setup () {
    const templatesStore = useTemplatesStore()
    const workingFormStore = useWorkingFormStore()
    const workspacesStore = useWorkspacesStore()
    return {
      templatesStore,
      workingFormStore,
      workspacesStore,
      workspaces : computed(() => workspacesStore.content),
      workspacesLoading : computed(() => workspacesStore.loading)
    }
  },

  data () {
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
    form: {
      get () {
        return this.workingFormStore.content
      },
      /* We add a setter */
      set (value) {
        this.workingFormStore.set(value)
      }
    },
    workspace () {
      return this.workspacesStore.getCurrent()
    }
  },

  watch: {
    workspace () {
      if (this.workspace) {
        this.form.workspace_id = this.workspace.id
      }
    }
  },

  mounted () {
    // Set as guest user
    const guestWorkspace = {
      id: null,
      name: 'Guest Workspace',
      is_enterprise: false,
      is_pro: false
    }
    this.workspacesStore.set([guestWorkspace])
    this.workspacesStore.setCurrentId(guestWorkspace.id)

    this.initForm()
    if (this.$route.query.template !== undefined && this.$route.query.template) {
      const template = this.templatesStore.getByKey(this.$route.query.template)
      if (template && template.structure) {
        this.form = new Form({ ...this.form.data(), ...template.structure })
      }
    } else {
      // No template loaded, ask how to start
      this.showInitialFormModal = true
    }
    this.closeAlert()
    this.stateReady = true
  },

  created () {},
  unmounted () {},

  methods: {
    openRegister () {
      this.registerModal = true
    },
    afterLogin () {
      this.registerModal = false
      this.isGuest = false
      this.workspacesStore.load()
      setTimeout(() => {
        if (this.$refs.editor) {
          this.$refs.editor.saveFormCreate()
        }
      }, 500)
    },
    formGenerated (form) {
      this.form = new Form({ ...this.form.data(), ...form })
    }
  }
}
</script>