<template>
  <div
    v-if="field.type === 'payment'"
    class="px-4"
  >
    <EditorSectionHeader
      icon="i-heroicons-credit-card-20-solid"
      title="Payment"
    />

    <select-input
      name="currency"
      label="Currency"
      :options="currencyList"
      :form="field"
      :required="true"
      :searchable="true"
      :disabled="stripeAccounts.length === 0"
    />
    <text-input
      name="amount"
      label="Amount"
      native-type="number"
      :form="field"
      :required="true"
      :disabled="stripeAccounts.length === 0"
    />
    <div v-if="stripeAccounts.length > 0">
      <select-input
        name="stripe_account_id"
        label="Stripe Account"
        :options="stripeAccounts"
        :form="field"
        :required="true"
      />
      <p class="mt-4 text-sm text-center text-bold">
        OR
      </p>
    </div>
    <UButton
      class="mt-4"
      icon="i-heroicons-arrow-right"
      block
      trailing
      :loading="stripeLoading"
      @click.prevent="connectStripe"
    >
      Connect with Stripe
    </UButton>
    <a
      target="#"
      class="text-gray-500 cursor-pointer"
      @click.prevent="crisp.openHelpdeskArticle('how-to-collect-payment-svig30')"
    >
      <Icon
        name="heroicons:information-circle-16-solid"
        class="h-4 w-4 mt-1"
      />
      Learn about collecting payments?
    </a>
  </div>
</template>

<script setup>
import EditorSectionHeader from '~/components/open/forms/components/form-components/EditorSectionHeader.vue'

const props = defineProps({
  field: {
    type: Object,
    required: true
  }
})

const crisp = useCrisp()
const providersStore = useOAuthProvidersStore()
const stripeLoading = ref(false)

onMounted(() => {
  providersStore.fetchOAuthProviders()

  if(props.field?.currency === undefined || props.field?.currency === null) {
    props.field.currency = 'USD'
  }
  if(props.field?.amount === undefined || props.field?.amount === null) {
    props.field.amount = 10
  }
})

const stripeAccounts = computed(() => providersStore.getAll.filter((item) => item.provider === 'stripe').map((item) => ({
  name: item.name + (item.email ? ' (' + item.email + ')' : ''),
  value: item.id
})))

const currencyList = computed(() => {
  const currencies = useFeatureFlag('services.stripe.currencies') || {}
  return Object.keys(currencies).map((item) => ({
    name: currencies[item],
    value: item
  }))
})


const connectStripe = () => {
  stripeLoading.value = true
  providersStore.connect('stripe', true, true)
}
</script>