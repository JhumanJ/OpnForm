<template>
  <modal :show="show" max-width="lg" @close="close">
    <text-input ref="companyName" label="Company Name" name="name" :required="true" :form="form" help="Name that will appear on invoices" />
    <text-input label="Email" name="email" native-type="email" :required="true" :form="form" help="Where invoices will be sent" />
    <v-button :loading="form.busy || loading" :disabled="form.busy || loading" class="mt-6 block mx-auto"
              @click="saveDetails" :arrow="true"
    >
      Go to checkout
    </v-button>
  </modal>
</template>

<script>
import { mapGetters } from 'vuex'
import TextInput from '../../forms/TextInput.vue'
import Form from 'vform'
import VButton from '../../common/Button.vue'
import axios from 'axios'

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
  data: () => ({
    form: new Form({
      name: '',
      email: ''
    }),
    loading: false
  }),

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
    updateUser() {
      if (this.user) {
        this.form.name = this.user.name
        this.form.email = this.user.email
      }
    },
    saveDetails () {
      if (this.form.busy) return
      this.form.put('api/subscription/update-customer-details').then(() => {
        this.loading = true
        axios.get('/api/subscription/new/' + this.plan + '/' + (!this.yearly ? 'monthly' : 'yearly') + '/checkout/with-trial').then((response) => {
          window.location = response.data.checkout_url
        }).catch((error) => {
          this.alertError(error.response.data.message)
        }).finally(() => {
          this.loading = false
          this.close()
        })
      })
    },
    close () {
      this.$emit('close')
    }
  },

  computed: {
    ...mapGetters({
      user: 'auth/user'
    })
  }
}
</script>
