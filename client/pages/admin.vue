<template>
  <div class="mb-8">
    <div
      v-if="userInfo"
      class="flex gap-2 items-center flex-wrap"
    >
      <h1 class="text-xl">
        {{ userInfo.name }}
      </h1>
      <div class="text-xs select-all bg-gray-50 rounded-md px-2 py-1 border">
        {{ userInfo.id }}
      </div>
      <div class="text-xs select-all bg-gray-50 rounded-md px-2 py-1 border">
        {{ userInfo.email }}
      </div>
      <a
        v-if="userInfo.stripe_id"
        :href="'https://dashboard.stripe.com/customers/'+userInfo.stripe_id"
        target="_blank"
        class="text-xs select-all bg-purple-50 border-purple-200 text-purple-500 rounded-md px-2 py-1 border"
      >
        <Icon
          name="bx:bxl-stripe"
          class="h-4 w-4 inline-block"
        />
        {{ userInfo.stripe_id }}
      </a>
      <div
        v-if="userPlan"
        :class="userPlanStyles"
      >
        {{ userPlan }}
      </div>
    </div>
    <h3
      v-else
      class="font-semibold text-2xl text-gray-900 mb-4"
    >
      Admin settings
    </h3>


    <template v-if="!userInfo">
      <form
        class="pb-8 max-w-lg"
        @submit.prevent="fetchUser"
        @keydown="fetchUserForm.onKeydown($event)"
      >
        <text-input
          name="identifier"
          :form="fetchUserForm"
          label="Identifier"
          :required="true"
          help="User Id, User Email, Form Slug or View Slug"
        />
        <v-button
          :loading="loading"
          type="success"
          color="blue"
          class="mt-4 w-full"
        >
          Fetch User
        </v-button>
      </form>

      <form
        class="pb-8 max-w-lg"
        @submit.prevent="createTemplate"
        @keydown="createTemplateForm.onKeydown($event)"
      >
        <text-area-input
          name="template_prompt"
          :form="createTemplateForm"
          label="Template Description"
          :required="true"
          help="Describe the template you want to create"
        />
        <v-button
          :loading="templateLoading"
          type="success"
          color="blue"
          class="mt-4 w-full"
        >
          Create Template
        </v-button>
      </form>
    </template>

    <div
      v-else
      class="flex flex-col"
    >
      <div
        id="admin-buttons"
        class="flex gap-1 my-4"
      >
        <impersonate-user :user="userInfo" />
        <send-password-reset-email :user="userInfo" />
      </div>
      <div
        class="w-full grid gap-2 grid-cols-1 lg:grid-cols-2"
      >
        <discount-on-subscription
          :user="userInfo"
        />
        <extend-trial
          :user="userInfo"
        />
        <cancel-subscription
          :user="userInfo"
        />
        <billing-email
          :user="userInfo"
        />
        <user-workspaces
          :user="userInfo"
        />
        <user-subscriptions
          :user="userInfo"
          class="lg:col-span-2"
        />
        <user-payments
          :user="userInfo"
          class="lg:col-span-2"
        />
        <deleted-forms
          :user="userInfo"
          class="lg:col-span-2"
        />
      </div>
    </div>
  </div>
</template>

<script>
import { computed } from 'vue'

export default {
  setup() {
    useOpnSeoMeta({
      title: 'Admin'
    })
    definePageMeta({
      middleware: 'moderator'
    })

    const authStore = useAuthStore()
    return {
      authStore,
      user: computed(() => authStore.user),
      useAlert: useAlert()
    }
  },

  data: () => ({
    userInfo: null,
    userPlan: 'free',
    fetchUserForm: useForm({
      identifier: ''
    }),
    createTemplateForm: useForm({
      template_prompt: ''
    }),
    loading: false,
    templateLoading: false
  }),

  computed: {
    userPlanStyles() {
      switch (this.userPlan) {
        case 'pro':
          return 'capitalize text-xs select-all bg-green-50 rounded-md px-2 py-1 border border-green-200 text-green-500'
        case 'enterprise':
          return 'capitalize text-xs select-all bg-blue-50 rounded-md px-2 py-1 border border-blue-200  text-blue-500'
        default:
          return 'capitalize text-xs select-all bg-gray-50 rounded-md px-2 py-1 border'
      }
    }
  },

  mounted() {
    // Shortcut link to impersonate users
    const urlSearchParams = new URLSearchParams(window.location.search)
    const params = Object.fromEntries(urlSearchParams.entries())
    if (params.impersonate) {
      this.fetchUserForm.identifier = params.impersonate
    }
    if (params.user_id) {
      this.fetchUserForm.identifier = params.user_id
    }
    if (this.fetchUserForm.identifier) {
      this.fetchUser()
    }
  },

  methods: {
    async fetchUser() {
      if (!this.fetchUserForm.identifier) {
        this.useAlert.error('Identifier is required.')
        return
      }

      this.loading = true
      opnFetch(`/moderator/fetch-user/${encodeURI(this.fetchUserForm.identifier)}`).then(async (data) => {
        this.loading = false
        this.userInfo = { ...data.user, workspaces: data.workspaces }
        this.getUserPlan(data.workspaces)
        this.useAlert.success(`User Fetched: ${this.userInfo.name}`)
      })
        .catch((error) => {
          this.useAlert.error(error.data.message)
          this.loading = false
        })
    },

    getUserPlan(workspaces) {
      if (workspaces.some(w => w.plan === 'enterprise')) {
        this.userPlan = 'enterprise'
      } else if (workspaces.some(w => w.plan === 'pro')) {
        this.userPlan = 'pro'
      }
    },
    async createTemplate() {
      if (!this.createTemplateForm.template_prompt) {
        this.useAlert.error('Template prompt is required.')
        return  
      }

      this.templateLoading = true
      opnFetch(`/moderator/create-template`, {
        method: 'POST',
        body: {
          template_prompt: this.createTemplateForm.template_prompt
        }
      }).then((data) => {
        this.templateLoading = false
        this.createTemplateForm.reset()
        this.useAlert.success('Template created.')
        useRouter().push({ name: 'templates-slug', params: { slug: data.template_slug } })
      })
      .catch((error) => {
        this.templateLoading = false
        this.useAlert.error(error.data.message)
      })
    }
  }
}
</script>
