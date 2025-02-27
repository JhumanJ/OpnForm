<template>
  <modal
    :show="show"
    max-width="lg"
    @close="$emit('close')"
  >
    <text-input
      ref="companyName"
      label="Company Name"
      name="name"
      :required="true"
      :form="form"
      help="Name that will appear on invoices"
    />
    <text-input
      label="Email"
      name="email"
      native-type="email"
      :required="true"
      :form="form"
      help="Where invoices will be sent"
    />
    <UButton
      :loading="form.busy || loading"
      :disabled="form.busy || loading || !form.name || !form.email"
      class="mt-6 block mx-auto"
      :to="checkoutUrl"
      target="_blank"
    >
      Go to checkout
    </UButton>
  </modal>
</template>

<script>
import { computed } from "vue"
import { useCheckoutUrl } from '@/composables/useCheckoutUrl'

export default {
  components: {},
  props: {
    show: {
      type: Boolean,
      default: false,
    },
    plan: {
      type: String,
      default: "pro",
    },
    yearly: {
      type: Boolean,
      default: true,
    },
    currency: {
      type: String,
      default: 'usd',
    },
  },
  emits: ['close'],

  setup() {
    const authStore = useAuthStore()
    return {
      user: computed(() => authStore.user),
    }
  },

  data: () => ({
    form: useForm({
      name: "",
      email: "",
    }),
    loading: false,
  }),

  computed: {
    checkoutUrl() {
      return useCheckoutUrl({
        name: this.form.name,
        email: this.form.email,
        plan: this.plan,
        yearly: this.yearly,
        currency: this.currency
      })
    }
  },

  watch: {
    user() {
      this.updateUser()
    },
    show() {
      // Wait for modal to open and focus on first field
      setTimeout(() => {
        if (this.$refs.companyName) {
          this.$refs.companyName.$el.querySelector("input").focus()
        }
      }, 300)

      this.loading = false
    },
  },

  mounted() {
    this.updateUser()
  },

  methods: {
    updateUser() {
      if (this.user) {
        this.form.name = this.user.name
        this.form.email = this.user.email
      }
    },
  },
}
</script>
