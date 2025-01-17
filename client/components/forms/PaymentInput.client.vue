<template>
  <InputWrapper v-bind="inputWrapperProps">
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
      <div
        v-if="stripeState.isLoaded"
        class="my-4"
      >
        <div class="mb-4 flex items-center justify-between bg-gray-50 px-4 py-3 rounded-lg">
          <span class="text-sm font-medium text-gray-700">Amount to pay</span>
          <span class="text-sm font-medium text-gray-900">{{ currency }} {{ amount }}</span>
        </div>
        <StripeElements
          ref="stripeElements"
          v-slot="{ elements }"
          :stripe-key="stripeKey"
          :instance-options="stripeOptions"
          :elements-options="elementsOptions"
        >
          <div class="space-y-4">
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
              <StripeElement
                ref="card"
                :elements="elements"
                type="card"
              />
            </div>
            <TextInput
              v-model="cardHolderName"
              placeholder="Name on card"
              class="w-full"
              :theme="theme"
            />
            <TextInput
              v-model="cardHolderEmail"
              placeholder="Email address"
              class="w-full"
              :theme="theme"
            />
          </div>
        </StripeElements>         
      </div>
      <div v-else>
        <Loader class="mx-auto h-6 w-6" />
      </div>
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
import { inputProps } from './useFormInput.js'
import InputWrapper from './components/InputWrapper.vue'
import { loadStripe } from '@stripe/stripe-js'
import { StripeElements, StripeElement } from 'vue-stripe-js'

const props = defineProps({
  ...inputProps,
  currency: { type: String, default: 'USD' },
  amount: { type: Number, default: 0 }
})

const stripeKey = useRuntimeConfig().public.STRIPE_PUBLISHABLE_KEY
const { state: stripeState } = useStripeElements()

const card = ref(null)
const cardHolderName = ref('')
const cardHolderEmail = ref('')

const onStripeReady = ({ stripe: stripeInstance, elements: elementsInstance }) => {
  if (!stripeInstance || !elementsInstance) {
    console.error('Stripe initialization failed')
    return
  }
  
  stripeState.value.isLoaded = true
  stripeState.value.stripe = stripeInstance
  stripeState.value.elements = elementsInstance
  stripeState.value.card = card.value
  stripeState.value.cardHolderName = cardHolderName.value
  stripeState.value.cardHolderEmail = cardHolderEmail.value
}

const stripeOptions = computed(() => ({
  locale: props.locale
}))

const elementsOptions = computed(() => ({
  mode: "payment",
  amount: props.amount,
  currency: props.currency.toLowerCase(),
  payment_method_types: ['card'],
  appearance: {
    theme: 'stripe',
    labels: 'above',
  },
  fields: {
    billingDetails: {
      name: 'always', // 'always', 'never', or 'auto'
      email: 'always',
    }
  }
}))

onMounted(async () => {
  try {
    const stripeInstance = await loadStripe(stripeKey)
    if (!stripeInstance) {
      useAlert().error('Failed to load Stripe')
    }
    onStripeReady({ stripe: stripeInstance, elements: stripeInstance.elements })
  } catch (error) {
    console.error('Stripe initialization error:', error)
    stripeState.value.isLoaded = false
  }
})
</script>
