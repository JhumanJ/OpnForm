<template>
  <modal
    :show="subscriptionModalStore.show"
    compact-header
    max-width="screen-lg"
    backdrop-blur
    class="z-50"
    @close="subscriptionModalStore.closeModal()"
  >
    <div class="overflow-hidden">
      <SlidingTransition
        direction="horizontal"
        :step="currentStep"
      >
        <div
          :key="currentStep"
          class="w-full"
        >
          <div
            v-if="currentStep === 1"
            key="step1"
            class="flex flex-col items-center px-4 pb-20 rounded-2xl relative"
          >
            <main class="flex flex-col mt-4 max-w-full text-center w-[591px] max-md:mt-10">
              <img
                src="/img/subscription-modal-icon.svg"
                alt="Subscription Icon"
                class="self-center max-w-full aspect-[0.98] w-[107px]"
              >
              <section class="flex flex-col mt-2 max-md:max-w-full">
                <h1 class="text-2xl font-bold tracking-tight leading-9 text-slate-800 max-md:max-w-full">
                  {{ subscriptionModalStore.modal_title }}
                </h1>
                <p class="mt-4 text-base leading-6 text-slate-500 max-md:max-w-full">
                  {{ subscriptionModalStore.modal_description }}
                </p>
              </section>
            </main>
            <div class="mt-8 mb-4 flex items-center justify-center">
              <MonthlyYearlySelector
                v-model="isYearly"
              />
            </div>
            <section class="flex flex-col w-full max-w-[800px] max-md:max-w-full">
              <div class="bg-white max-md:max-w-full">
                <div class="flex gap-2 max-md:flex-col max-md:gap-0">
                  <article
                    v-if="!isSubscribed"
                    class="flex flex-col w-6/12 max-md:ml-0 max-md:w-full m-auto"
                  >
                    <div class="flex flex-col grow justify-between p-6 w-full bg-blue-50 rounded-2xl max-md:px-5 max-md:mt-2">
                      <div class="flex flex-col items-center">
                        <div class="flex gap-2 py-px">
                          <h2 class="my-auto text-xl font-semibold tracking-tighter leading-5 text-slate-900">
                            Pro
                          </h2>
                          <span
                            v-if="isYearly"
                            class="justify-center px-2 py-1 text-xs font-semibold tracking-wide text-center text-emerald-600 uppercase bg-emerald-50 rounded-md"
                          >
                            Save 20%
                          </span>
                        </div>
                        <div class="flex flex-col justify-end mt-4 leading-[100%]">
                          <p class="text-2xl font-semibold tracking-tight text-slate-900 text-center">
                            <template v-if="isYearly">
                              $16
                            </template>
                            <template v-else>
                              $19
                            </template>
                          </p>
                          <p class="text-xs text-slate-500">
                            per month, billed
                            <template v-if="isYearly">
                              yearly
                            </template>
                            <template v-else>
                              monthly
                            </template>
                          </p>
                        </div>
                      </div>
                      <v-button
                        v-if="!user?.is_subscribed"
                        v-track.upgrade_modal_start_trial="{plan: 'default', period: isYearly?'yearly':'monthly'}"
                        class="relative border border-white border-opacity-20 h-10 inline-flex px-4 items-center rounded-lg text-sm font-semibold w-full justify-center mt-4"
                        @click.prevent="onSelectPlan('default')"
                      >
                        Start 3-day trial
                      </v-button>
                      <v-button
                        v-else
                        :loading="billingLoading"
                        class="relative border border-white border-opacity-20 h-10 inline-flex px-4 items-center rounded-lg text-sm font-semibold w-full justify-center mt-4"
                        target="_blank"
                        @click="openBillingDashboard"
                      >
                        Manage Plan
                      </v-button>
                    </div>
                  </article>
                </div>
              </div>
            </section>
            <section class="flex flex-col self-stretch mt-12 max-md:mt-10 max-md:max-w-full">
              <div class="justify-center max-md:pr-5 max-md:max-w-full">
                <div class="flex gap-5 max-md:flex-col max-md:gap-0">
                  <article class="flex flex-col w-[33%] max-md:ml-0 max-md:w-full">
                    <div class="flex flex-col grow text-base leading-6 text-slate-500 max-md:mt-10">
                      <Icon
                        name="mdi:star-outline"
                        class="w-5 h-5 text-nt-blue"
                      />
                      <p class="mt-2">
                        <strong class="font-semibold text-slate-800">Remove OpnForm branding.</strong>
                        <span class="text-slate-500"> Remove our watermark, create forms that match your brand.</span>
                      </p>
                    </div>
                  </article>
                  <article class="flex flex-col ml-5 w-[33%] max-md:ml-0 max-md:w-full">
                    <div class="flex flex-col grow text-base leading-6 text-slate-500 max-md:mt-10">
                      <Icon
                        name="ion:brush-outline"
                        class="w-5 h-5 text-nt-blue"
                      />
                      <p class="mt-2">
                        <strong class="font-semibold text-slate-800">Full form customization.</strong>
                        <span class="text-slate-500"> Customize the colors, themes, images etc of your forms. Inject custom CSS and JS code.</span>
                      </p>
                    </div>
                  </article>
                  <article class="flex flex-col  w-[33%] max-md:ml-0 max-md:w-full">
                    <div class="flex flex-col grow text-base leading-6 text-slate-500 max-md:mt-10">
                      <Icon
                        name="icons8:upload-2"
                        class="w-5 h-5 text-nt-blue"
                      />
                      <p class="mt-2">
                        <strong class="font-semibold text-slate-800">Larger File uploads.</strong>
                        <span class="text-slate-500"> Larger files upload in your forms (up to 50 mb). This allows you to collect bigger attachments.</span>
                      </p>
                    </div>
                  </article>
                </div>
              </div>
              <div class="justify-center mt-12 max-md:pr-5 max-md:mt-10 max-md:max-w-full">
                <div class="flex gap-5 max-md:flex-col max-md:gap-0">
                  <article class="flex flex-col w-[33%] max-md:ml-0 max-md:w-full">
                    <div class="flex flex-col grow text-base leading-6 text-slate-500 max-md:mt-10">
                      <Icon
                        name="heroicons:bell"
                        class="w-5 h-5 text-nt-blue"
                      />
                      <p class="mt-2">
                        <strong class="font-semibold text-slate-800">Access to all integrations.</strong>
                        <span class="text-slate-500"> Setup email, Slack, Discord notifications or GSheet, Zapier or webhooks integrations.</span>
                      </p>
                    </div>
                  </article>
                  <article class="flex flex-col ml-5 w-[33%] max-md:ml-0 max-md:w-full">
                    <div class="flex flex-col grow text-base leading-6 text-slate-500 max-md:mt-10">
                      <Icon
                        name="heroicons:globe-alt"
                        class="w-5 h-5 text-nt-blue"
                      />
                      <p class="mt-2">
                        <strong class="font-semibold text-slate-800">1 custom domain.</strong>
                        <span class="text-slate-500"> Host your form on your own domain for a professional look and improved branding.</span>
                      </p>
                    </div>
                  </article>
                  <article class="flex flex-col ml-5 w-[33%] max-md:ml-0 max-md:w-full">
                    <div class="flex flex-col grow text-base leading-6 text-slate-500 max-md:mt-10">
                      <Icon
                        name="mdi:pencil-outline"
                        class="w-5 h-5 text-nt-blue"
                      />
                      <p class="mt-2">
                        <strong class="font-semibold text-slate-800">Editable submissions.</strong>
                        <span class="text-slate-500"> Form respondents can go back and edit their form submissions, allowing for updates and corrections.</span>
                      </p>
                    </div>
                  </article>
                </div>
              </div>
            </section>
            <footer
              class="justify-center py-1.5 mt-12 text-base font-medium leading-6 text-center text-blue-500 max-md:mt-10"
            >
              <NuxtLink
                :to="{ name: 'pricing' }"
                target="_blank"
                class="focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                And much more. See full plans comparison
                <Icon
                  class="h-6 w-5"
                  name="heroicons:arrow-small-right"
                />
              </NuxtLink>
            </footer>
          </div>
          <section
            v-else-if="currentStep === 2"
            key="step2"
            class="flex flex-col items-center px-6 pb-4 bg-white rounded-2xl w-full"
          >
            <div class="flex gap-2 max-md:flex-wrap">
              <div class="flex justify-center items-center p-2 rounded-[1000px]">
                <Icon
                  name="heroicons:chevron-left-16-solid"
                  class="h-6 w-6 cursor-pointer"
                  @click.prevent="goBackToStep1"
                />
              </div>
              <h1 class="flex-1 my-auto text-xl font-bold leading-8 text-center text-slate-800 max-md:max-w-full">
                Confirm
                <template v-if="isSubscribed">
                  Upgrade
                </template>
                <template v-else>
                  Subscription
                </template>
              </h1>
            </div>
            <div class="flex-grow w-full max-w-sm">
              <div
                v-if="!isSubscribed"
                class="bg-blue-50 rounded-md p-4 border border-blue-200 flex flex-col my-4 gap-1"
              >
                <div class="flex w-full">
                  <p class="text-blue-500 capitalize font-medium flex-grow">
                    OpnForm - {{ currentPlan == 'default' ? 'Pro' : 'Team' }} plan
                  </p>
                  <UBadge
                    :color="isYearly?'green':'amber'"
                    variant="subtle"
                  >
                    {{ !isYearly ? 'No Discount' : 'Discount Applied' }}
                  </UBadge>
                </div>

                <p class="text-sm leading-5 text-slate-500">
                  <span
                    v-if="isYearly"
                    class="font-medium line-through mr-2"
                    v-text="'$19'"
                  />
                  <span
                    class="font-medium"
                    :class="{'text-green-700':isYearly}"
                    v-text="isYearly ? '$16' : '$19'"
                  />
                  <span
                    class="text-xs"
                    :class="{'text-green-700':isYearly}"
                  >
                    per month, billed
                    <template v-if="isYearly">
                      yearly
                    </template>
                    <template v-else>
                      monthly
                    </template>
                  </span>
                </p>
                <div v-if="shouldShowUpsell">
                  <v-form size="sm">
                    <toggle-switch-input
                      v-model="isYearly"
                      label="20% off with the yearly plan"
                      size="sm"
                      wrapper-class="mb-0"
                    />
                  </v-form>
                </div>
              </div>
              <text-input
                ref="companyName"
                label="Company Name"
                name="name"
                :required="true"
                :form="form"
                help="Name that will appear on invoices"
              />
              <text-input
                label="Invoicing Email"
                name="email"
                native-type="email"
                :required="true"
                :form="form"
                help="Where invoices will be sent"
              />
              <div
                class="flex gap-2 mt-6 w-full"
              >
                <UButton
                  v-track.upgrade_modal_confirm_submit="{plan: currentPlan.value, period: isYearly?'yearly':'monthly'}"
                  block
                  size="md"
                  class="w-auto flex-grow"
                  :loading="form.busy || loading"
                  @click="saveDetails"
                >
                  <template v-if="isSubscribed">
                    Upgrade
                  </template>
                  <template v-else>
                    Subscribe
                  </template>
                </UButton>
                <UButton
                  size="md"
                  color="white"
                  @click="goBackToStep1"
                >
                  Back
                </UButton>
              </div>
            </div>
          </section>
        </div>
      </SlidingTransition>
    </div>
  </modal>
</template>

<script setup>
import SlidingTransition from '~/components/global/transitions/SlidingTransition.vue'
import { fetchAllWorkspaces } from '~/stores/workspaces.js'

const router = useRouter()
const subscriptionModalStore = useSubscriptionModalStore()

const currentPlan = ref(subscriptionModalStore.plan || 'default')
const currentStep = ref(1)
const isYearly = ref(subscriptionModalStore.yearly)
const loading = ref(false)
const billingLoading = ref(false)
const shouldShowUpsell = ref(false)
const form = useForm({
  name: '',
  email: ''
})

const subscribeBroadcast = useBroadcastChannel('subscribe')
const broadcastData = subscribeBroadcast.data
const confetti = useConfetti()
const authStore = useAuthStore()
const workspacesStore = useWorkspacesStore()
const authenticated = computed(() => authStore.check)
const user = computed(() => authStore.user)
const isSubscribed = computed(() => workspacesStore.isSubscribed)
const currency = 'usd'

// When opening modal with a plan already (and user not subscribed yet) - skip first step
watch(() => subscriptionModalStore.show, () => {
  currentStep.value = 1
  if (subscriptionModalStore.show && subscriptionModalStore.plan) {
    if (user.value.is_subscribed) {
      return
    }
    isYearly.value = subscriptionModalStore.yearly
    shouldShowUpsell.value = !isYearly.value
    currentStep.value = 2
    currentPlan.value = subscriptionModalStore.plan
  }
})

watch(broadcastData, () => {
  if (import.meta.server || !subscriptionModalStore.show || !broadcastData.value || !broadcastData.value.type) {
    return
  }

  if (broadcastData.value.type === 'success') {
    // Now we need to reload workspace and user
    opnFetch('user').then((userData) => {
      authStore.setUser(userData)

      try {
        const eventData = {
          plan: user.value.has_enterprise_subscription ? 'enterprise' : 'pro'
        }
        useAmplitude().logEvent('subscribed', eventData)
        useCrisp().pushEvent('subscribed', eventData)
        useGtm().trackEvent({ event: 'subscribed', ...eventData })
        if (import.meta.client && window.rewardful) {
          window.rewardful('convert', { email: user.value.email })
        }
        console.log('Subscription registered 🎊')
      } catch (error) {
        console.error('Failed to register subscription event 😔',error)
      }
    })
    fetchAllWorkspaces().then((workspaces) => {
      workspacesStore.set(workspaces.data.value)
    })

    if (user.value.has_enterprise_subscription) {
      useAlert().success(
        'Awesome! Your subscription to OpnForm is now confirmed! You now have access to all Team '
        + 'features. No need to invite your teammates, just ask them to create a OpnForm account and to connect the same Notion workspace. Feel free to contact us if you have any question 🙌'
      )
    } else {
      useAlert().success(
        'Awesome! Your subscription to OpnForm is now confirmed! You now have access to all Pro '
        + 'features. Feel free to contact us if you have any question 🙌'
      )
    }
    confetti.play()
    subscriptionModalStore.closeModal()
  } else {
    useAlert().error(
      'Unfortunately we could not confirm your subscription. Please try again and contact us if the issue persists.'
    )
    currentStep.value = 1
    shouldShowUpsell.value = true
  }
  subscribeBroadcast.close()
})

onMounted(() => {
  if (user && user.value) {
    form.name = user.value.name
    form.email = user.value.email
  }
})

const onSelectPlan = (planName) => {
  if (!authenticated.value) {
    subscriptionModalStore.closeModal()
    router.push({ name: "register" })
    return
  }

  loading.value = false
  currentPlan.value = planName
  shouldShowUpsell.value = !isYearly.value
  currentStep.value = 2
}

const goBackToStep1 = () => {
  currentStep.value = 1
}

const saveDetails = () => {
  if (form.busy)
    return
  form.put('subscription/update-customer-details').then(() => {
    loading.value = true

    // Get param trial_duration from url
    const urlParams = new URLSearchParams(window.location.search)
    const trialDuration = urlParams.get('trial_duration') || null
    if (trialDuration) {
      useAmplitude().logEvent('extended_trial_used', {
        duration: trialDuration
      })
    }

    const params = {
      trial_duration: trialDuration,
      currency: currency
    }
    opnFetch(
      `/subscription/new/${
        currentPlan.value
      }/${
        !isYearly.value ? 'monthly' : 'yearly'
      }/checkout/with-trial?${
        new URLSearchParams(params).toString()}`
    )
      .then((data) => {
        window.open(data.checkout_url, '_blank')
      })
      .catch((error) => {
        useAlert().error(error.data.message)
        loading.value = false
      })
      .finally(() => {

      })
  })
}

const openBillingDashboard = () => {
  billingLoading.value = true
  opnFetch('/subscription/billing-portal').then((data) => {
    const url = data.portal_url
    window.location = url
  }).catch((error) => {
    useAlert().error(error.data.message)
  }).finally(() => {
    billingLoading.value = false
  })
}
</script>
