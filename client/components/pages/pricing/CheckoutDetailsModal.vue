<template>
  <modal :show="show" max-width="lg" @close="close">
    <text-input ref="companyName" label="Company Name" name="name" :required="true" :form="form" help="Name that will appear on invoices" />
    <text-input label="Email" name="email" native-type="email" :required="true" :form="form" help="Where invoices will be sent" />
    <v-button :loading="form.busy || loading" :disabled="(form.busy || loading)?true:null" class="mt-6 block mx-auto"
              :arrow="true" @click="saveDetails"
    >
      Go to checkout
    </v-button>
  </modal>
</template>

<script>
import { computed } from 'vue'
import TextInput from '../../forms/TextInput.vue'
import VButton from '~/components/global/VButton.vue'

export default {
  components: { VButton, TextInput },
  props: {
    show: {
      type: Boolean,
      default: false
    },
    plan: {
      type: String,
      default: 'pro'
    },
    yearly: {
      type: Boolean,
      default: true
    }
  },

  setup () {
    const authStore = useAuthStore()
    return {
      user: computed(() => authStore.user)
    }
  },

  data: () => ({
    form: useForm({
      name: '',
      email: ''
    }),
    loading: false
  }),

  computed: {},

  watch: {
    user () {
      this.updateUser()
    },
    show () {
      // Wait for modal to open and focus on first field
      setTimeout(() => {
        if (this.$refs.companyName) {
          this.$refs.companyName.$el.querySelector('input').focus()
        }
      }, 300)

      this.loading = false
    }
  },

  mounted () {
    this.updateUser()
  },

  methods: {
    updateUser () {
      if (this.user) {
        this.form.name = this.user.name
        this.form.email = this.user.email
      }
    },
    saveDetails () {
      if (this.form.busy) return
      this.form.put('subscription/update-customer-details').then(() => {
        this.loading = true
        opnFetch('/subscription/new/' + this.plan + '/' + (!this.yearly ? 'monthly' : 'yearly') + '/checkout/with-trial').then((data) => {
          window.location = data.checkout_url
        }).catch((error) => {
          useAlert().error(error.data.message)
        }).finally(() => {
          this.loading = false
          this.close()
        })
      })
    },
    close () {
      this.$emit('close')
    }
  }
}
</script>
