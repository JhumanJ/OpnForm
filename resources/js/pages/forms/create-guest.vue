<template>
  <div class="flex flex-wrap flex-col">
    <transition v-if="stateReady" name="fade" mode="out-in">
      <div key="2">
        <form-editor v-if="!workspacesLoading" ref="editor"
                     class="w-full flex flex-grow"
                     :style="{
                       'max-height': editorMaxHeight + 'px'
                     }" :error="error"
                     :isGuest="isGuest"
                     @mounted="onResize"
                     @openRegister="openRegister"
        />
        <div v-else class="text-center mt-4 py-6">
          <loader class="h-6 w-6 text-nt-blue mx-auto"/>
        </div>
      </div>
    </transition>

    <quick-register :showRegisterModal="registerModal" @close="registerModal=false" @reopen="registerModal=true" @afterLogin="afterLogin" />

  </div>
</template>

<script>
import store from '~/store'
import Form from 'vform'
import {mapState, mapActions} from 'vuex'
import QuickRegister from '../auth/components/QuickRegister'

const FormEditor = () => import('../../components/open/forms/components/FormEditor')

const loadTemplates = function () {
  store.commit('open/templates/startLoading')
  store.dispatch('open/templates/loadIfEmpty').then(() => {
      store.commit('open/templates/stopLoading')
  })
}

export default {
  name: 'CreateFormGuest',
  components: {
    FormEditor,
    QuickRegister
  },

  middleware: 'guest',

  metaInfo() {
    return {title: 'Create a new Form as Guest'}
  },

  beforeRouteEnter (to, from, next) {
    loadTemplates()
    next()
  },

  data() {
    return {
      stateReady: false,
      loading: false,
      error: '',
      editorMaxHeight: 500,
      registerModal: false,
      isGuest: true
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
    fromOnboarding() {
      return this.$route.params.from_onboarding
    },
    fbGroupLink() {
      return window.config.links.facebook_group
    }
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


    if(this.$route.query.template !== undefined && this.$route.query.template){
      let template = this.$store.getters['open/templates/getBySlug'](this.$route.query.template)
      if(template && template.structure){
        this.form = new Form(template.structure)
      }else{
        this.initForm()
      }
    }else{
      this.initForm()
    }

    this.closeAlert()
    this.stateReady = true
  },

  created() {
    window.addEventListener('resize', this.onResize)
  },
  destroyed() {
    window.removeEventListener('resize', this.onResize)
  },

  methods: {
    ...mapActions({
      loadWorkspaces: 'open/workspaces/load'
    }),
    initForm() {
      this.form = new Form({
        title: 'My Form',
        description: null,
        visibility: 'public',
        workspace_id: this.workspace?.id,
        properties: [],

        notifies: false,
        slack_notifies: false,
        send_submission_confirmation: false,
        webhook_url: null,

        // Customization
        theme: 'default',
        width: 'centered',
        dark_mode: 'auto',
        color: '#3B82F6',
        hide_title: false,
        no_branding: false,
        uppercase_labels: true,
        transparent_background: false,
        closes_at: null,
        closed_text: 'This form has now been closed by its owner and does not accept submissions anymore.',

        // Submission
        submit_button_text: 'Submit',
        re_fillable: false,
        re_fill_button_text: 'Fill Again',
        submitted_text: 'Amazing, we saved your answers. Thank you for your time and have a great day!',
        notification_sender: 'OpnForm',
        notification_subject: 'We saved your answers',
        notification_body: 'Hello there 👋 <br>This is a confirmation that your submission was successfully saved.',
        notifications_include_submission: true,
        use_captcha: false,
        is_rating: false,
        rating_max_value: 5,
        max_submissions_count: null,
        max_submissions_reached_text: 'This form has now reached the maximum number of allowed submissions and is now closed.',

        // Security & Privacy
        can_be_indexed: true
      })
    },
    /**
     * Compute max height of editor
     */
    onResize() {
      if (this.$refs.editor) {
        this.editorMaxHeight = window.innerHeight - this.$refs.editor.$el.offsetTop
      }
    },
    openRegister () {
      this.registerModal = true
    },
    afterLogin(){
      this.registerModal = false
      this.isGuest = false
      this.loadWorkspaces()
      setTimeout(() => {
        this.$refs.editor.saveFormCreate()
      }, 500)
    }
  }
}
</script>
