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
                     @on-save="formInitialHash=null"
        />
        <div v-else class="text-center mt-4 py-6">
          <Loader class="h-6 w-6 text-nt-blue mx-auto" />
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
import Form from 'vform'
import { computed } from 'vue'
import { useAuthStore } from '../../stores/auth'
import { useTemplatesStore } from '../../stores/templates'
import { useWorkingFormStore } from '../../stores/working_form'
import { useWorkspacesStore } from '../../stores/workspaces'
import initForm from '../../mixins/form_editor/initForm.js'
import SeoMeta from '../../mixins/seo-meta.js'
import CreateFormBaseModal from '../../components/pages/forms/create/CreateFormBaseModal.vue'

const loadTemplates = function () {
  const templatesStore = useTemplatesStore()
  templatesStore.startLoading()
  templatesStore.loadIfEmpty().then(() => {
    templatesStore.stopLoading()
  })
}

export default {
  name: 'CreateForm',
  components: { CreateFormBaseModal },

  mixins: [initForm, SeoMeta],
  middleware: 'auth',

  beforeRouteEnter (to, from, next) {
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

  setup () {
    const authStore = useAuthStore()
    const templatesStore = useTemplatesStore()
    const workingFormStore = useWorkingFormStore()
    const workspacesStore = useWorkspacesStore()
    return {
      templatesStore,
      workingFormStore,
      workspacesStore,
      user: computed(() => authStore.user),
      workspaces : computed(() => workspacesStore.content),
      workspacesLoading : computed(() => workspacesStore.loading)
    }
  },

  data () {
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
    },
    user () {
      this.stateReady = true
    }
  },

  mounted () {
    window.onbeforeunload = () => {
      if (this.isDirty()) {
        return false
      }
    }

    this.initForm()
    this.formInitialHash = this.hashString(JSON.stringify(this.form.data()))
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
    this.workspacesStore.loadIfEmpty()

    this.stateReady = this.user !== null
  },

  created () {},
  unmounted () {},

  methods: {
    formGenerated (form) {
      this.form = new Form({ ...this.form.data(), ...form })
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
