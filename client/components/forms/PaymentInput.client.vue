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
        'dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200'
      ]"
    >
      <div v-if="!oauthProviderId">
        <div class="space-y-4 mt-3">
          <div class="animate-pulse flex flex-col gap-3">
            <div class="h-6 bg-gray-200 dark:bg-gray-800 rounded-md" />
            <div class="h-6 bg-gray-200 dark:bg-gray-800 rounded-md" />
            <div class="h-6 bg-gray-200 dark:bg-gray-800 rounded-md" />
          </div>
          <p class="text-sm text-gray-500 dark:text-gray-400 text-center">
            Connect Stripe account to continue
          </p>
        </div>
      </div>
      <div
        v-else-if="showSuccessState"
        class="my-4 p-4 text-center text-sm text-green-700 bg-green-100 dark:bg-green-900/50 dark:text-green-300 rounded-md"
      >
        <div class="flex items-center justify-center gap-2">
          <Icon
            name="heroicons:check-circle"
            class="w-5 h-5"
          />
          <p>{{ $t('forms.payment.success') }}.</p>
        </div>
      </div>
      <template v-else>
        <div
          v-if="shouldShowPreviewMessage"
          class="my-4 p-4 text-center text-sm text-blue-700 bg-blue-100 dark:bg-blue-900/50 dark:text-blue-300 rounded-md"
        >
          <p>Please save the form to activate the payment preview.</p>
        </div>
        <div
          v-else-if="stripeState && stripeState.isLoadingAccount"
          class="my-4 flex justify-center"
        >
          <Loader class="mx-auto h-6 w-6" />
        </div>
        <div
          v-else-if="stripeState && stripeState.hasAccountLoadingError"
          class="my-4 p-4 text-center text-sm text-red-700 bg-red-100 dark:bg-red-900/50 dark:text-red-300 rounded-md"
        >
          <p>{{ stripeState.errorMessage || 'Failed to load payment configuration' }}</p>
        </div>
        <div
          v-else-if="stripeState && stripeState.stripeAccountId && isStripeJsLoaded && publishableKey"
          class="my-2"
        >
          <div
            :class="[
              theme.default.borderRadius,
              theme.default.spacing.horizontal,
              theme.default.spacing.vertical,
              theme.default.fontSize,
            ]"
            class="mb-4 flex border border-gray-300 dark:border-gray-600 items-center justify-between bg-gray-50 dark:bg-gray-800"
          >
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $t('forms.payment.amount_to_pay') }}</span>
            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ currencySymbol }}{{ amount }}</span>
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
                <div
                  :class="[
                    theme.default.input,
                    theme.default.borderRadius,
                    theme.default.spacing.horizontal,
                    theme.default.spacing.vertical,
                    theme.default.fontSize,
                    theme.PaymentInput?.cardContainer || '',
                    {
                      [theme.PaymentInput?.focusRing || 'ring-2 ring-primary-500 border-transparent']: isCardFocused && !hasError,
                      '!ring-red-500 !ring-2 !border-transparent': hasError
                    },
                    'dark:bg-gray-800 dark:border-gray-700'
                  ]"
                >
                  <StripeElement
                    v-if="elements"
                    ref="card"
                    type="card"
                    :elements="elements"
                    :options="cardOptions"
                    @ready="onCardReady"
                    @focus="onCardFocus" 
                    @blur="onCardBlur"
                  />
                </div>
                <TextInput
                  v-model="cardHolderName"
                  name="cardholder_name"
                  :placeholder="$t('forms.payment.name_on_card')"
                  class="w-full"
                  :theme="theme"
                  :disabled="disabled"
                />
                <TextInput
                  v-model="cardHolderEmail"
                  name="billing_email"
                  :placeholder="$t('forms.payment.billing_email')"
                  class="w-full"
                  :theme="theme"
                  :disabled="disabled"
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
import { ref, computed, watch, onMounted, nextTick } from 'vue'
import { inputProps, useFormInput } from './useFormInput.js'
import InputWrapper from './components/InputWrapper.vue'
import { loadStripe } from '@stripe/stripe-js'
import { StripeElements, StripeElement } from 'vue-stripe-js'
import stripeCurrencies from "~/data/stripe_currencies.json"
import { useStripeElements } from '~/composables/useStripeElements'
import { useAlert } from '~/composables/useAlert'
import { useFeatureFlag } from '~/composables/useFeatureFlag'

const props = defineProps({
  ...inputProps,
  direction: { type: String, default: 'ltr' },
  currency: { type: String, default: 'USD' },
  amount: { type: Number, default: 0 },
  oauthProviderId: { type: [String, Number], default: null },
  isAdminPreview: { type: Boolean, default: false },
  color: { type: String, default: '#000000' },
  isDark: { type: Boolean, default: false }
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
  setBillingDetails
} = stripeElements || {}

const route = useRoute()
const alert = useAlert()

const publishableKey = computed(() => {
  return useFeatureFlag('billing.stripe_publishable_key', '')
})
const card = ref(null)
const stripeElementsRef = ref(null)
const cardHolderName = ref('')
const cardHolderEmail = ref('')
const isCardFocused = ref(false)

// Keep the flag for Stripe.js loading but remove manual instance creation
const isStripeJsLoaded = ref(false)

// Computed to determine if we should show success state
const showSuccessState = computed(() => {
  return stripeState?.intentId || (compVal.value && isPaymentIntentId(compVal.value))
})

// Computed to determine if we should always show preview message in editor
const shouldShowPreviewMessage = computed(() => {
  return props.isAdminPreview && (!formSlug.value || !stripeState || !stripeElements)
})

// Helper function to check if a string looks like a Stripe payment intent ID
const isPaymentIntentId = (value) => {
  return typeof value === 'string' && value.startsWith('pi_')
}

// Initialize Stripe.js if needed
onMounted(async () => {
  try {
    // Validate publishable key
    if (!publishableKey.value || typeof publishableKey.value !== 'string' || publishableKey.value.trim() === '') {
      if (stripeState) {
        stripeState.isLoadingAccount = false
        stripeState.hasAccountLoadingError = true
        stripeState.errorMessage = 'Missing Stripe configuration. Please check your settings.'
      }
      return
    }

    // We'll check if Stripe is already available globally
    if (typeof window !== 'undefined' && !window.Stripe) {
      await loadStripe(publishableKey.value)
      isStripeJsLoaded.value = true
    } else {
      isStripeJsLoaded.value = true
    }

    // If stripeElements or stripeState is not available, we need to handle that
    if (!stripeElements || !stripeState) {
      console.warn('Stripe elements provider not found or not properly initialized.')
      return
    }

    // If compVal already contains a payment intent ID, sync it to stripeState
    if (compVal.value && isPaymentIntentId(compVal.value) && stripeState) {
      stripeState.intentId = compVal.value
    }

    // For unsaved forms in admin preview, show the preview message
    if (props.isAdminPreview && !formSlug.value && stripeState) {
      stripeState.isLoadingAccount = false
      stripeState.showPreviewMessage = true
      return
    }

    // Fetch account but don't manually create Stripe instance
    const slug = formSlug.value
    if (slug && props.oauthProviderId && prepareStripeState) {
      const result = await prepareStripeState(slug, props.oauthProviderId, props.isAdminPreview)
      
      if (!result.success && result.message && !result.requiresSave) {
        alert.error(result.message)
      }
    } else if (props.isAdminPreview && stripeState) {
      // If we're in admin preview and any required parameter is missing, show preview message
      stripeState.isLoadingAccount = false
      stripeState.showPreviewMessage = true
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
    return
  }
  
  if (setStripeInstance) {
    setStripeInstance(stripe)
  }
  
  if (elements && setElementsInstance) {
    setElementsInstance(elements)
  }
}

const onStripeError = (_error) => {
  alert.error('Failed to load payment component. Please check configuration or refresh.')
}

// Card focus/blur event handlers
const onCardFocus = () => {
  isCardFocused.value = true
}

const onCardBlur = () => {
  isCardFocused.value = false
}

const onCardReady = (_element) => {
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

const cardOptions = computed(() => {
  // Extract placeholder color from theme
  const darkPlaceholderColor = props.theme.default?.input?.includes('dark:placeholder-gray-500') ? '#6B7280' : '#9CA3AF'
  const lightPlaceholderColor = props.theme.default?.input?.includes('placeholder-gray-400') ? '#9CA3AF' : '#A0AEC0'
  
  return {
    hidePostalCode: true,
    disableLink: true,
    disabled: props.disabled || false,
    style: {
      base: {
        iconColor: props.color,
        color: props.isDark ? '#D1D5DB' : '#374151',
        fontSmoothing: 'antialiased',
        fontSize: '16px',
        '::placeholder': {
          color: props.isDark ? darkPlaceholderColor : lightPlaceholderColor
        }
      },
      invalid: {
        iconColor: '#df1b41',
        color: '#df1b41'
      }
    }
  }
})

const formSlug = computed(() => {
  // Return the slug from route params regardless of route name
  if (route.params && route.params.slug) {
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
      setStripeInstance(newRef.instance)
    }
    
    if (newRef.elements && setElementsInstance) {
      setElementsInstance(newRef.elements)
    }
  }
}, { immediate: true })
</script>