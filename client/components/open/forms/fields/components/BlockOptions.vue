<template>
  <div
    v-if="field"
    class="py-2"
  >
    <div class="px-4">
      <text-input
        name="name"
        :form="field"
        wrapper-class="mb-2"
        :required="true"
        label="Block Name"
      />

      <HiddenRequiredDisabled
        :form="field"
        :field="field"
        :can-be-disabled="false"
        :can-be-hidden="true"
        :can-be-required="['nf-payment'].includes(field.type)"
      />
      
      <div class="grid grid-cols-2 gap-2 mt-2">
        <select-input
          name="width"
          class="flex-grow"
          :options="[
            { name: 'Full', value: 'full' },
            { name: '1/2', value: '1/2' },
            { name: '1/3', value: '1/3' },
            { name: '2/3', value: '2/3' },
            { name: '1/4', value: '1/4' },
            { name: '3/4', value: '3/4' },
          ]"
          :form="field"
          label="Width"
        />
        <select-input
          v-if="['nf-text', 'nf-image'].includes(field.type)"
          name="align"
          class="flex-grow"
          :options="[
            { name: 'Left', value: 'left' },
            { name: 'Center', value: 'center' },
            { name: 'Right', value: 'right' },
            { name: 'Justify', value: 'justify' },
          ]"
          :form="field"
          label="Alignment"
        />
      </div>
    </div>

    <div
      v-if="field.type == 'nf-text'"
      class="border-t mt-4"
    >
      <rich-text-area-input
        class="mx-4"
        name="content"
        :form="field"
        label="Content"
        :required="false"
      />
    </div>

    <div
      v-else-if="field.type == 'nf-page-break'"
      class="border-b py-2 px-4"
    >
      <text-input
        name="next_btn_text"
        :form="field"
        label="Next button label"
        :required="true"
      />
      <text-input
        name="previous_btn_text"
        :form="field"
        label="Previous button label"
        help="Displayed on the next page"
        :required="true"
      />
    </div>

    <div
      v-else-if="field.type == 'nf-image'"
      class="border-t mt-4"
    >
      <image-input
        name="image_block"
        class="mx-4"
        :form="field"
        label="Upload Image"
        :required="false"
      />
    </div>

    <div
      v-else-if="field.type == 'nf-code'"
      class="border-t"
    >
      <code-input
        name="content"
        class="mt-4 mx-4"
        :form="field"
        label="Content"
        help="You can add any html code, including iframes"
      />
    </div>

    <div
      v-else-if="field.type == 'nf-payment'"
      class="border-t"
    >
      <select-input
        name="currency"
        class="mx-4"
        label="Currency"
        :options="currencyList"
        :form="field"
        :required="true"
        :searchable="true"
      />
      <text-input
        name="amount"
        class="mx-4"
        label="Amount"
        :form="field"
        :required="true"
      />
      <div v-if="stripeAccounts.length > 0">
        <select-input
          name="stripe_account"
          class="mx-4"
          label="Stripe Account"
          :options="stripeAccounts"
          :form="field"
          :required="true"
        />
        <p class="mt-4 mx-4 text-sm text-center text-bold">
          OR
        </p>
      </div>
      <UButton
        icon="i-heroicons-arrow-right"
        class="mt-4 mx-4"
        block
        trailing
        :loading="stripeLoading"
        @click.prevent="connectStripe"
      >
        Connect with Stripe
      </UButton>
      <a
        target="#"
        class="mx-4 text-gray-500 cursor-pointer"
        @click.prevent="crisp.openHelpdesk()"
      >
        <Icon
          name="heroicons:information-circle-16-solid"
          class="inline h-4 w-4"
        />
        Learn about collecting payments?
      </a>
    </div>
  </div>
</template>

<script setup>
import HiddenRequiredDisabled from './HiddenRequiredDisabled.vue'

const props = defineProps({
  field: {
    type: Object,
    required: false
  },
  form: {
    type: Object,
    required: false
  }
})

const providersStore = useOAuthProvidersStore()
const crisp = useCrisp()
const stripeLoading = ref(false)

const currencyList = ref([
  { name: 'AED - UAE Dirham', value: 'AED' },
  { name: 'AUD - Australian Dollar', value: 'AUD' },
  { name: 'BGN - Bulgarian lev', value: 'BGN' },
  { name: 'BRL - Brazilian real', value: 'BRL' },
  { name: 'CAD - Canadian dollar', value: 'CAD' },
  { name: 'CHF - Swiss franc', value: 'CHF' },
  { name: 'CNY - Yuan Renminbi', value: 'CNY' },
  { name: 'CZK - Czech Koruna', value: 'CZK' },
  { name: 'DKK - Danish Krone', value: 'DKK' },
  { name: 'EUR - Euro', value: 'EUR' },
  { name: 'GBP - Pound sterling', value: 'GBP' },
  { name: 'HKD - Hong Kong dollar', value: 'HKD' },
  { name: 'HRK - Croatian kuna', value: 'HRK' },
  { name: 'HUF - Hungarian forint', value: 'HUF' },
  { name: 'IDR - Indonesian Rupiah', value: 'IDR' },
  { name: 'ILS - Israeli Shekel', value: 'ILS' },
  { name: 'INR - Indian Rupee', value: 'INR' },
  { name: 'ISK - Icelandic króna', value: 'ISK' },
  { name: 'JPY - Japanese yen', value: 'JPY' },
  { name: 'KRW - South Korean won', value: 'KRW' },
  { name: 'MAD - Moroccan Dirham', value: 'MAD' },
  { name: 'MXN - Mexican peso', value: 'MXN' },
  { name: 'MYR - Malaysian ringgit', value: 'MYR' },
  { name: 'NOK - Norwegian krone', value: 'NOK' },
  { name: 'NZD - New Zealand dollar', value: 'NZD' },
  { name: 'PHP - Philippine peso', value: 'PHP' },
  { name: 'PLN - Polish złoty', value: 'PLN' },
  { name: 'RON - Romanian leu', value: 'RON' },
  { name: 'RSD - Serbian dinar', value: 'RSD' },
  { name: 'RUB - Russian Rouble', value: 'RUB' },
  { name: 'SAR - Saudi riyal', value: 'SAR' },
  { name: 'SEK - Swedish krona', value: 'SEK' },
  { name: 'SGD - Singapore dollar', value: 'SGD' },
  { name: 'THB - Thai baht', value: 'THB' },
  { name: 'TWD - New Taiwan dollar', value: 'TWD' },
  { name: 'UAH - Ukrainian hryvnia', value: 'UAH' },
  { name: 'USD - United States Dollar', value: 'USD' },
  { name: 'VND - Vietnamese dong', value: 'VND' },
  { name: 'ZAR - South African rand', value: 'ZAR' }
])

const stripeAccounts = computed(() => providersStore.getAll.filter((item) => item.provider === 'stripe').map((item) => ({
  name: item.name + ' (' + item.email + ')',
  value: item.id
})))

watch(() => props.field?.width, (val) => {
  if (val === undefined || val === null) {
    props.field.width = 'full'
  }
}, { immediate: true })

watch(() => props.field?.align, (val) => {
  if (val === undefined || val === null) {
    props.field.align = 'left'
  }
}, { immediate: true })

watch(() => props.field?.currency, (val) => {
  if (val === undefined || val === null) {
    props.field.currency = 'USD'
  }
}, { immediate: true })

watch(() => props.field?.amount, (val) => {
  if (val === undefined || val === null) {
    props.field.amount = 10
  }
}, { immediate: true })

onMounted(() => {
  providersStore.fetchOAuthProviders()

  if (props.field?.width === undefined || props.field?.width === null) {
    props.field.width = 'full'
  }
})

const connectStripe = () => {
  stripeLoading.value = true
  providersStore.connect('stripe', true, true)
}
</script>
