<template>
  <div class="flex flex-wrap flex-col">
    <!-- Step 1: Select Database -->
    <div ref="progress" class="w-full px-4 " :class="{
      'md:mx-auto md:max-w-5xl':currentStep===0}"
    >
      <div class="flex items-center justify-between pb-2">
        <v-button v-if="currentStep>0" color="gray" shade="light" class="hidden md:block mx-4 flex-shrink-0"
                  @click="goBack"
        >
          Previous
        </v-button>
        <v-button v-if="currentStep>0" :loading="loading || createFormLoading" color="nt-blue"
                  class="v-last-step hidden md:block mx-4 flex-shrink-0"
                  @click="nextStep"
        >
          {{ currentStep !== 1 ? 'Continue' : 'Create Form' }}
        </v-button>
      </div>

    </div>

    <transition v-if="stateReady" name="fade" mode="out-in">
      <!-- Step1: Form Customization -->
      <div v-if="currentStep===1" key="2">
        <form-editor v-if="!workspacesLoading" ref="editor"
                     class="w-full flex border-t flex-grow"
                     :style="{
                       'max-height': editorMaxHeight + 'px'
                     }" :error="error"
                     :validation-error-response="validationErrorResponse"
                     @mounted="onResize"
        />
        <div v-else class="text-center mt-4 py-6">
          <loader class="h-6 w-6 text-nt-blue mx-auto"/>
        </div>
      </div>
    </transition>

    <div v-if="currentStep===1" class="md:hidden pt-4 mb-16 px-6 border-t flex justify-between">
      <v-button color="gray" shade="light" class="mt-2" @click="previousStep">
        Previous
      </v-button>
      <v-button v-track.create_form_click :loading="createFormLoading" color="nt-blue" class="mt-2 px-5 v-last-step"
                @click="nextStep"
      >
        Create Form
      </v-button>
    </div>
  </div>
</template>

<script>
import store from '~/store'
import Form from 'vform'
import {mapState, mapActions} from 'vuex'
import saveUpdateAlert from '../../mixins/forms/saveUpdateAlert'
import clonedeep from 'clone-deep'

const FormEditor = () => import('../../components/open/forms/components/FormEditor')

const loadTemplates = function () {
  store.commit('open/templates/startLoading')
  store.dispatch('open/templates/loadIfEmpty').then(() => {
      store.commit('open/templates/stopLoading')
  })
}

export default {
  name: 'CreateForm',
  components: {
    FormEditor,
  },

  metaInfo() {
    return {title: 'Create a new Form'}
  },

  mixins: [saveUpdateAlert],

  beforeRouteEnter (to, from, next) {
    loadTemplates()
    next()
  },

  middleware: 'auth',

  data() {
    return {
      stateReady: false,
      validationErrorResponse: null,
      loading: false,
      createFormLoading: false,
      error: '',
      createdFormId: null,
      currentStep: 1,

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
    createdForm() {
      return this.$store.getters['open/forms/getById'](this.createdFormId)
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
    },
    user() {
      this.stateReady = true
    }
  },

  mounted() {
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
    initForm() {
      this.form = new Form({
        title: 'My Form',
        description: null,
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
    nextStep() {
      this.error = ''
      if (this.currentStep === 0) {
        this.form.workspace = clonedeep(this.workspace)
        // Init editor max height
        this.currentStep++
        this.$nextTick(() => {
          this.editorMaxHeight = window.innerHeight - (this.$refs.progress.offsetTop + this.$refs.progress.offsetHeight)
        })
        return
      } else if (this.currentStep === 1) {
        return this.submit()
      }
      this.currentStep++
    },
    submit() {
      if (this.loading) return
      this.form.workspace_id = this.workspace.id
      this.validationErrorResponse = null

      this.createFormLoading = true
      this.form.post('/api/open/forms').then((response) => {
        this.$store.commit('open/forms/addOrUpdate', response.data.form)
        this.createdFormId = response.data.form.id

        this.$logEvent('form_created', {form_id:  response.data.form.id, form_slug:  response.data.form.slug})
        this.$getCrisp().push(['set', 'session:event', [[['form_created', {
          form_id:  response.data.form.id,
          form_slug:  response.data.form.slug
        }, 'blue']]]])
        this.displayFormModificationAlert(response.data)
        this.$router.push({
          name: 'forms.show',
          params: {
            slug: this.createdForm.slug,
            new_form: response.data.users_first_form
          }
        })
      }).catch((error) => {
        if (error.response && error.response.status === 422) {
          this.validationErrorResponse = error.response.data
          this.$refs.editor.showValidationErrors()
        }
      }).finally(() => {
        this.createFormLoading = false
      })
    },
    previousStep() {
      if (this.currentStep > 0) {
        this.currentStep--
      }
    },

    /**
     * Compute max height of editor
     */
    onResize() {
      if (this.$refs.editor) {
        this.editorMaxHeight = window.innerHeight - this.$refs.editor.$el.offsetTop
      }
    },
    goBack() {
      return this.$router.back();
    }
  }
}
</script>
