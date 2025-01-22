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
        >
          <div class="space-y-4">
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
              <StripeElement
                ref="card"
                :elements="elements"
                :options="cardOptions"
                @ready="onCardReady"
                @change="onCardChanged"
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
import { inputProps, useFormInput } from './useFormInput.js'
import InputWrapper from './components/InputWrapper.vue'
import { loadStripe } from '@stripe/stripe-js'
import { StripeElements, StripeElement } from 'vue-stripe-js'

const props = defineProps({
  ...inputProps,
  currency: { type: String, default: 'USD' },
  amount: { type: Number, default: 0 }
})

const emit = defineEmits([])
const { ...formInput } = useFormInput(props, { emit })

const stripeKey = useRuntimeConfig().public.STRIPE_PUBLISHABLE_KEY
const { state: stripeState } = useStripeElements()

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

const onStripeReady = (stripeInstance) => {
  if (!stripeInstance) {
    console.error('Stripe initialization failed')
    return
  }
  
  stripeState.value.isLoaded = true
  stripeState.value.stripe = stripeInstance
  stripeState.value.elements = stripeElements
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

const stripeOptions = computed(() => ({
  locale: props.locale || 'en'
}))

const cardOptions = computed(() => ({
  type: 'card',
  hidePostalCode: true,
  disableLink: true,
}))

onMounted(async () => {
  try {
    const stripeInstance = await loadStripe(stripeKey)
    if (!stripeInstance) {
      useAlert().error('Failed to load Stripe')
    }
    onStripeReady(stripeInstance)
  } catch (error) {
    console.error('Stripe initialization error:', error)
    stripeState.value.isLoaded = false
  }
})
</script>
