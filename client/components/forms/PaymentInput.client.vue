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
      <div v-if="!oauthProviderId">
        <div class="space-y-4 mt-3">
            <div class="animate-pulse flex flex-col gap-3">
              <div class="h-6 bg-gray-200 dark:bg-gray-800 rounded-md"></div>
              <div class="h-6 bg-gray-200 dark:bg-gray-800 rounded-md"></div>
              <div class="h-6 bg-gray-200 dark:bg-gray-800 rounded-md"></div>
          </div>
          <p class="text-sm text-gray-500 text-center">Connect Stripe account to continue</p>
        </div>
      </div>
      <div class="my-4 p-4 text-center text-sm text-green-700 bg-green-100 rounded-md" v-else-if="stripeState.intentId">
        <div class="flex items-center justify-center gap-2">
          <Icon name="heroicons:check-circle" class="w-5 h-5" />
          <p>{{ $t('forms.payment.success') }}.</p>
        </div>
      </div>
      <template v-else>
        <div v-if="stripeState.isLoadingAccount" class="my-4 flex justify-center">
          <Loader class="mx-auto h-6 w-6" />
        </div>
        <div v-else-if="stripeState.showPreviewMessage" class="my-4 p-4 text-center text-sm text-blue-700 bg-blue-100 rounded-md">
          <p>Please save the form to activate the payment preview.</p>
        </div>
        <div v-else-if="stripeState.hasAccountLoadingError" class="my-4 p-4 text-center text-sm text-red-700 bg-red-100 rounded-md">
          <p>{{ stripeState.errorMessage || 'Failed to load payment configuration' }}</p>
        </div>
        <div v-else-if="stripeState.stripeAccountId && isStripeJsLoaded" class="my-4">
           <div class="mb-4 flex items-center justify-between bg-gray-50 px-4 py-3 rounded-lg">
             <span class="text-sm font-medium text-gray-700">{{ $t('forms.payment.amount_to_pay') }}</span>
             <span class="text-sm font-medium text-gray-900">{{ currencySymbol }}{{ amount }}</span>
           </div>
           <StripeElements
             ref="stripeElementsRef"
             :stripe-key="publishableKey"
             :stripe-account="stripeState.stripeAccountId"
             :instance-options="{ stripeAccount: stripeState.stripeAccountId }"
             :elements-options="{ locale: props.locale }"
             @ready="onStripeReady"
             @error="onStripeError"
           >
             <template #default="{ elements }">
               <div class="space-y-4">
                 <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                   <StripeElement
                     v-if="elements"
                     ref="card"
                     type="card"
                     :elements="elements"
                     :options="cardOptions"
                     @ready="onCardReady" 
                   />
                 </div>
                 <TextInput
                   v-model="cardHolderName"
                   name="cardholder_name"
                   :placeholder="$t('forms.payment.name_on_card')"
                   class="w-full"
                   :theme="theme"
                 />
                 <TextInput
                   v-model="cardHolderEmail"
                   name="billing_email"
                   :placeholder="$t('forms.payment.billing_email')"
                   class="w-full"
                   :theme="theme"
                 />
               </div>
             </template>
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
import { ref, computed, watch, onMounted } from 'vue'
import { inputProps, useFormInput } from './useFormInput.js'
import InputWrapper from './components/InputWrapper.vue'
import { loadStripe } from '@stripe/stripe-js'
import { StripeElements, StripeElement } from 'vue-stripe-js'
import stripeCurrencies from "~/data/stripe_currencies.json"
import { useStripeElements } from '~/composables/useStripeElements'

const props = defineProps({
  ...inputProps,
  direction: { type: String, default: 'ltr' },
  currency: { type: String, default: 'USD' },
  amount: { type: Number, default: 0 },
  oauthProviderId: { type: [String, Number], default: null },
  isAdminPreview: { type: Boolean, default: false }
})

const emit = defineEmits([])
const { compVal, hasError, inputWrapperProps } = useFormInput(props, { emit })
const stripeElements = useStripeElements()
const { 
  state: stripeState, 
  prepareStripeState, 
  setStripeInstance, 
  setElementsInstance,
  setCardElement,
  setBillingDetails,
  isReadyForPayment
} = stripeElements || {}

const route = useRoute()
const alert = useAlert()

const publishableKey = useRuntimeConfig().public.STRIPE_PUBLISHABLE_KEY
const card = ref(null)
const stripeElementsRef = ref(null)
const cardHolderName = ref('')
const cardHolderEmail = ref('')

// Keep the flag for Stripe.js loading but remove manual instance creation
const isStripeJsLoaded = ref(false)
// Don't create our own stripeInstance
// let stripeInstance = null

// Initialize Stripe.js if needed
onMounted(async () => {
  try {
    // We'll check if Stripe is already available globally
    if (typeof window !== 'undefined' && !window.Stripe) {
      await loadStripe(publishableKey)
      isStripeJsLoaded.value = true
    } else {
      isStripeJsLoaded.value = true
    }

    // Fetch account but don't manually create Stripe instance
    const slug = formSlug.value
    if (slug && props.oauthProviderId && prepareStripeState) {
      const result = await prepareStripeState(slug, props.oauthProviderId, props.isAdminPreview)
      
      if (!result.success && result.message && !result.requiresSave) {
        alert.error(result.message)
      }
    }
  } catch (error) {
    alert.error('Failed to initialize Stripe. Please refresh and try again.')
  }
})

// Watch for provider ID changes
watch(() => props.oauthProviderId, async (newVal, oldVal) => {
  if (newVal && newVal !== oldVal && prepareStripeState) {
    const slug = formSlug.value
    if (slug) {
      await prepareStripeState(slug, newVal, props.isAdminPreview)
    }
  }
})

// Update onStripeReady to always use the stripe instance from the component
const onStripeReady = ({ stripe, elements }) => {
  if (!stripe) {
    return;
  }
  
  if (setStripeInstance) {
    setStripeInstance(stripe);
  }
  
  if (elements && setElementsInstance) {
    setElementsInstance(elements);
  }
}

const onStripeError = (error) => {
  alert.error('Failed to load payment component. Please check configuration or refresh.')
}

const onCardReady = (element) => {
  if (card.value?.stripeElement) {
    if (setCardElement) {
      setCardElement(card.value.stripeElement)
    }
  }
}

// Billing details
watch(cardHolderName, (newValue) => {
  setBillingDetails({ name: newValue })
})

watch(cardHolderEmail, (newValue) => {
  setBillingDetails({ email: newValue })
})

// Payment intent sync
watch(() => stripeState?.intentId, (newValue) => {
  if (newValue) compVal.value = newValue
})

watch(compVal, (newValue) => {
  if (newValue && stripeState && newValue !== stripeState.intentId) {
    stripeState.intentId = newValue
  }
}, { immediate: true })

// Direction and locale handling
watch(() => props.direction, async () => {
  await resetCard()
})

watch(() => props.locale, async () => {
  await resetCard()
})

// Computed properties
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

// Reset card element
const resetCard = async () => {
  if (card.value?.stripeElement) {
    card.value.stripeElement.unmount()
    await nextTick()
    
    if (stripeElementsRef.value?.elements) {
      card.value.stripeElement.mount(stripeElementsRef.value.elements)
      setCardElement(card.value.stripeElement)
    } else {
      console.error('Cannot remount card, Stripe Elements instance not found.')
    }
  }
}

// Add watcher to check when stripeElementsRef becomes available for fallback access
watch(() => stripeElementsRef.value, async (newRef) => {
  if (newRef) {
    // If @ready event hasn't fired, try accessing the instance directly
    if (newRef.instance && setStripeInstance && !stripeState.isStripeInstanceReady) {
      setStripeInstance(newRef.instance);
    }
    
    if (newRef.elements && setElementsInstance) {
      setElementsInstance(newRef.elements);
    }
  }
}, { immediate: true });
</script>