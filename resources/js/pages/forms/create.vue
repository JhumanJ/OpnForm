<template>
  <div class="flex flex-wrap flex-col">
    <transition v-if="stateReady" name="fade" mode="out-in">
      <div key="2">
        <form-editor v-if="!workspacesLoading" ref="editor"
                     class="w-full flex flex-grow"
                     :style="{
                       'max-height': editorMaxHeight + 'px'
                     }" :error="error"
                     @mounted="onResize"
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
import initForm from "../../mixins/form_editor/initForm";
import SeoMeta from '../../mixins/seo-meta'

const loadTemplates = function () {
  store.commit('open/templates/startLoading')
  store.dispatch('open/templates/loadIfEmpty').then(() => {
    store.commit('open/templates/stopLoading')
  })
}

export default {
  name: 'CreateForm',

  mixins: [initForm, SeoMeta],
  components: {},

  beforeRouteEnter(to, from, next) {
    loadTemplates()
    next()
  },

  middleware: 'auth',

  data() {
    return {
      metaTitle: 'Create a new Form',
      stateReady: false,
      loading: false,
      error: '',
      editorMaxHeight: 500
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
    this.initForm()
    if (this.$route.query.template !== undefined && this.$route.query.template) {
      const template = this.$store.getters['open/templates/getBySlug'](this.$route.query.template)
      if (template && template.structure) {
        this.form = new Form({...this.form.data(), ...template.structure})
      }
    }
    this.closeAlert()
    this.loadWorkspaces()

    this.stateReady = this.user !== null
  },

  created() {
    window.addEventListener('resize', this.onResize)
  },
  destroyed() {
    window.removeEventListener('resize', this.onResize)
  },

  methods: {
    ...mapActions({
      loadWorkspaces: 'open/workspaces/loadIfEmpty'
    }),
    /**
     * Compute max height of editor
     */
    onResize() {
      if (this.$refs.editor) {
        this.editorMaxHeight = window.innerHeight - this.$refs.editor.$el.offsetTop
      }
    }
  }
}
</script>
