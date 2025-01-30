<template>
  <InputWrapper v-bind="props">
    <template #label>
      <slot name="label" />
    </template>

    <div
      :class="[
        theme.default.input,
        theme.default.borderRadius,
        theme.default.spacing.horizontal,
        theme.default.spacing.vertical,
        theme.default.fontSize,
        {
          '!ring-red-500 !ring-2 !border-transparent': hasError,
          '!cursor-not-allowed !bg-gray-200 dark:!bg-gray-800': disabled,
        },
      ]"
    >
      <div v-if="!oauthProviderId">
        <p>Connect Stripe account to continue</p>
      </div>
      <div v-else-if="stripeState?.intentId">
        <p>{{ $t('forms.payment.success') }}</p>
      </div>
      <template v-else>
        <div
          v-if="stripeState.isLoaded"
          class="my-4"
        >
          <div class="mb-4 flex items-center justify-between bg-gray-50 px-4 py-3 rounded-lg">
            <span class="text-sm font-medium text-gray-700">{{ $t('forms.payment.amount_to_pay') }}</span>
            <span class="text-sm font-medium text-gray-900">{{ currencySymbol }}{{ amount }}</span>
          </div>
          <StripeElements
            ref="stripeElements"
            v-slot="{ elements }"
            :stripe-key="publishableKey"
            :instance-options="stripeOptions"
          >
            <div class="space-y-4">
              <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                <StripeElement
                  ref="card"
                  :dir="props.direction"
                  :elements="elements"
                  :options="cardOptions"
                  @ready="onCardReady"
                  @change="onCardChanged"
                />
              </div>
              <TextInput
                v-model="cardHolderName"
                :placeholder="$t('forms.payment.name_on_card')"
                class="w-full"
                :theme="theme"
              />
              <TextInput
                v-model="cardHolderEmail"
                :placeholder="$t('forms.payment.billing_email')"
                class="w-full"
                :theme="theme"
              />
            </div>
          </StripeElements>         
        </div>
        <div v-else>
          <Loader class="mx-auto h-6 w-6" />
        </div>
      </template>
    </div>

    <template #help>
      <slot name="help" />
    </template>
    <template #error>
      <slot name="error" />
    </template>
  </InputWrapper>
</template>

<script setup>
import { inputProps, useFormInput } from './useFormInput.js'
import InputWrapper from './components/InputWrapper.vue'
import { loadStripe } from '@stripe/stripe-js'
import { StripeElements, StripeElement } from 'vue-stripe-js'
import stripeCurrencies from "~/data/stripe_currencies.json"

const props = defineProps({
  ...inputProps,
  direction: { type: String, default: 'ltr' },
  currency: { type: String, default: 'USD' },
  amount: { type: Number, default: 0 },
  oauthProviderId: { type: String, default: null }
})

const emit = defineEmits([])
const { ...formInput } = useFormInput(props, { emit })
const { state: stripeState } = useStripeElements()
const route = useRoute()

const stripeAccountId = ref(null)
const publishableKey = useRuntimeConfig().public.STRIPE_PUBLISHABLE_KEY
const card = ref(null)
const stripeElements = ref(null)
const cardHolderName = ref('')
const cardHolderEmail = ref('')

const onCardChanged = (event) => {
  console.log('card changed', event)
}
  
const onCardReady = (element) => {
  stripeState.value.card = card.value?.stripeElement
}

watch(cardHolderName, (newValue) => {
  stripeState.value.cardHolderName = newValue
})

watch(cardHolderEmail, (newValue) => {
  stripeState.value.cardHolderEmail = newValue
})

watch(card, (newValue) => {
  stripeState.value.card = newValue
})

watch(() => stripeState.value.intentId, (newValue) => {
  formInput.compVal.value = newValue
})
watch(() => formInput.compVal.value, (newValue) => {
  if (newValue && newValue !== stripeState.value.intentId) {
    stripeState.value.intentId = newValue
  }
}, { immediate: true })

watch(() => props.direction, async (newValue) => {
  await resetCard()
})
watch(() => props.locale, async (newValue) => {
  await resetCard()
})

const currencySymbol = computed(() => {
  return stripeCurrencies.find(item => item.code === props.currency)?.symbol
})

const stripeOptions = computed(() => ({
  locale: props.locale
}))

const cardOptions = computed(() => ({
  type: 'card',
  hidePostalCode: true,
  disableLink: true,
}))

const formSlug = computed(() => {
  if (route.name && route.name.startsWith("forms-slug")) {
    return route.params.slug
  }
  return null
})

onMounted(async () => {
  initStripe()
})

const initStripe = async () => {
  if (!formSlug.value) return
  try {
    const response = await opnFetch('/forms/' + formSlug.value + '/stripe-connect/get-account')
    if (response?.type === 'success') {
      stripeAccountId.value = response?.stripeAccount
      const stripeInstance = await loadStripe(publishableKey, { stripeAccount: stripeAccountId.value })
      if (!stripeInstance) {
        useAlert().error('Stripe initialization failed')
        return
      }
      
      stripeState.value.isLoaded = true
      stripeState.value.stripe = stripeInstance
      stripeState.value.elements = stripeElements
    } else { 
      useAlert().error(response.message)
    }
  } catch (error) {
    console.error('Stripe initialization error:', error)
    stripeState.value.isLoaded = false
  }
}

const resetCard = async () => {
  if (card.value?.stripeElement) {
    card.value.stripeElement.unmount()
    await nextTick()
    card.value.stripeElement.mount(card.value.$el)
  }
}
</script>