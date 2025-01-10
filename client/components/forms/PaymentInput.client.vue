<template>
  <InputWrapper v-bind="inputWrapperProps">
    <template #label>
      <slot name="label" />
    </template>

    <div
      v-if="stripeLoaded"
      class="my-4"
    >
      <p class="mb-2 text-sm text-gray-500">
        Amount: {{ currency }} {{ amount }}
      </p>
      <StripeElements
        v-slot="{ elements, instance }"
        :stripe-key="stripeKey"
      >
        <StripeElement
          ref="card"
          :elements="elements"
        />
      </StripeElements>
    </div>
    <div v-else>
      <p>Loading...</p>
    </div>

    <template #help>
      <slot name="help" />
    </template>
    <template #error>
      <slot name="error" />
    </template>
  </InputWrapper>
</template>

<script>
import { inputProps, useFormInput } from './useFormInput.js'
import InputWrapper from './components/InputWrapper.vue'
import { loadStripe } from '@stripe/stripe-js'
import { StripeElements, StripeElement } from 'vue-stripe-js'

export default {
  name: 'PaymentInput',
  components: { InputWrapper, StripeElements, StripeElement },

  props: {
    ...inputProps,
    currency: { type: String, default: 'USD' },
    amount: { type: Number, default: 0 },
  },

  setup(props, context) {
    const stripeKey = useRuntimeConfig().public.STRIPE_PUBLISHABLE_KEY
    const stripeLoaded = ref(false)

    onMounted(async () => {
      await loadStripe(stripeKey)
      stripeLoaded.value = true
    })
    return {
      ...useFormInput(props, context),
      stripeKey,
      stripeLoaded
    }
  }
}
</script>