import { toValue } from 'vue';
// Assume Stripe is loaded globally or via another mechanism if needed client-side
// For server-side/Nuxt API routes, import Stripe library properly.

/**
 * @fileoverview Composable for handling payment processing, currently focused on Stripe.
 */
export function useFormPayment(formConfig, form) {

  /**
   * Gets the Stripe client secret from the backend.
   * Assumes a specific API endpoint.
   * @param {Object} paymentBlock - The payment field configuration.
   * @returns {Promise<String>} The client secret.
   */
  const _getStripeClientSecret = async (paymentBlock) => {
    const config = toValue(formConfig);
    const formData = toValue(form); // Access the vForm instance
    const url = `/forms/${config.slug}/payment/stripe/intent`;

    console.log('Fetching Stripe client secret...');
    try {
      // Use the vForm instance's post method to make the request
      const response = await formData.post(url, {
        amount: paymentBlock.amount, // Pass amount from payment block config
        currency: paymentBlock.currency || 'usd', // Default or from config
        metadata: { // Add any relevant metadata
          form_slug: config.slug,
          // potentially other form data ids if needed
        }
      });

      if (!response || !response.data || !response.data.client_secret) {
          throw new Error('Invalid response structure from payment intent endpoint.');
      }

      console.log('Stripe client secret received.');
      return response.data.client_secret;
    } catch (error) {
      console.error('Failed to get Stripe client secret:', error?.response?.data || error);
      // Propagate error details if possible
      const errorMessage = error?.response?.data?.message || 'Could not initiate payment. Please check your details and try again.';
      // Set error on the form instance if possible, e.g., using a general payment error key
      formData.errors.set('payment', errorMessage);
      throw new Error(errorMessage); // Re-throw to stop processing
    }
  };

  /**
   * Confirms the Stripe payment on the client-side using Stripe.js.
   * Requires Stripe.js to be loaded.
   * @param {String} clientSecret - The Stripe client secret.
   * @param {Object} paymentElement - The Stripe Payment Element instance.
   * @param {Object} stripe - The Stripe.js instance.
   * @returns {Promise<Object>} The result of the payment confirmation.
   */
  const _confirmStripePayment = async (clientSecret, paymentElement, stripe) => {
    if (!stripe || !paymentElement) {
      throw new Error('Stripe.js or Payment Element not available for confirmation.');
    }
    const config = toValue(formConfig);
    // TODO: Construct the return URL properly based on form config/mode
    const returnUrl = `${window.location.origin}/forms/${config.slug}/payment/complete`; // Example

    console.log('Confirming Stripe payment...');
    const { error, paymentIntent } = await stripe.confirmPayment({
      elements: paymentElement, // Use the mounted PaymentElement
      clientSecret,
      confirmParams: {
        return_url: returnUrl, // URL for redirection after success/failure
      },
      redirect: 'if_required', // Handle redirection manually if needed
    });

    if (error) {
      console.error('Stripe payment confirmation failed:', error);
      // Set error on the form instance
      toValue(form).errors.set('payment', error.message || 'Payment failed. Please try again.');
      throw error; // Rethrow to signal failure
    }

    console.log('Stripe payment confirmed. Status:', paymentIntent?.status);
    // Handle different paymentIntent statuses (succeeded, processing, requires_action)
    if (paymentIntent?.status !== 'succeeded' && paymentIntent?.status !== 'processing') {
        const failMessage = `Payment status: ${paymentIntent?.status}. Please contact support.`;
        toValue(form).errors.set('payment', failMessage);
        throw new Error(failMessage);
    }

    // Optionally store payment intent ID or status in form data
    toValue(form).set('stripe_payment_intent_id', paymentIntent.id);
    toValue(form).set('payment_status', paymentIntent.status);

    return { success: true, paymentIntent };
  };

  /**
   * Processes payment for a given payment block.
   * Currently supports Stripe.
   * @param {Object} paymentBlock - The payment field configuration.
   * @param {Object} stripe - The Stripe.js instance (passed from component).
   * @param {Object} paymentElement - The Stripe Payment Element instance (passed from component).
   * @returns {Promise<Object>} Result object { success: boolean, error?: any, paymentIntent?: object }
   */
  const processPayment = async (paymentBlock, stripe, paymentElement) => {
    if (!paymentBlock || paymentBlock.type !== 'payment') {
      return { success: true }; // No payment block or not a payment type
    }

    const provider = paymentBlock.provider || 'stripe'; // Default to stripe
    console.log(`Processing payment for provider: ${provider}`);
    toValue(form).errors.clear('payment'); // Clear previous payment errors

    if (provider === 'stripe') {
      try {
        const clientSecret = await _getStripeClientSecret(paymentBlock);
        // Confirmation now likely happens on submit, triggered by the component
        // This function mainly ensures the intent is created.
        // We might store the clientSecret in the form data temporarily if needed before submission
        toValue(form).set('_stripe_client_secret', clientSecret); // Temporary store
        console.log('Stripe payment intent created successfully.');
        return { success: true }; // Indicate intent creation was successful

        // --- Client-side confirmation logic (moved to component/submit) ---
        // const confirmationResult = await _confirmStripePayment(clientSecret, paymentElement, stripe);
        // return confirmationResult;
      } catch (error) {
        console.error('Stripe payment processing failed:', error);
        // Error should already be set on the form by internal methods
        return { success: false, error: error };
      }
    } else {
      console.warn(`Unsupported payment provider: ${provider}`);
      return { success: false, error: new Error(`Unsupported payment provider: ${provider}`) };
    }
  };

  // Expose the main payment processing function
  return {
    processPayment,
    // Expose internal methods only if needed externally (unlikely)
    // _getStripeClientSecret,
    // _confirmStripePayment
  };
} 