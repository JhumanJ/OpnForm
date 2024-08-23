<template>
  <modal
    :show="subscriptionModalStore.show"
    compact-header
    max-width="screen-lg"
    backdrop-blur
    @close="subscriptionModalStore.closeModal()"
  >
    <div
      v-if="currentStep === 1"
      class="flex flex-col items-center pr-4 pb-20 pl-6 rounded-2xl max-md:pl-5 relative"
    >
      <main class="flex flex-col mt-4 max-w-full text-center w-[591px] max-md:mt-10">
        <svg
          class="self-center max-w-full aspect-[0.98] w-[107px]"
          viewBox="0 0 108 109"
          fill="none"
          xmlns="http://www.w3.org/2000/svg"
        >
          <g opacity="0.5">
            <path
              d="M94.5371 90H95.5371V89V22C95.5371 18.134 92.4031 15 88.5371 15H37.5371C33.6711 15 30.5371 18.134 30.5371 22V89V90H31.5371H94.5371Z"
              fill="url(#paint0_linear_6894_874)"
            />
            <path
              d="M94.5371 90H95.5371V89V22C95.5371 18.134 92.4031 15 88.5371 15H37.5371C33.6711 15 30.5371 18.134 30.5371 22V89V90H31.5371H94.5371Z"
              stroke="url(#paint1_linear_6894_874)"
              stroke-width="2"
            />
            <rect
              x="39.5371"
              y="24"
              width="47"
              height="4"
              rx="2"
              fill="url(#paint2_linear_6894_874)"
            />
            <rect
              x="39.5371"
              y="34"
              width="47"
              height="4"
              rx="2"
              fill="url(#paint3_linear_6894_874)"
            />
            <rect
              x="39.5371"
              y="44"
              width="24"
              height="4"
              rx="2"
              fill="url(#paint4_linear_6894_874)"
            />
          </g>
          <path
            d="M78.5371 96H79.5371V95V28C79.5371 24.134 76.4031 21 72.5371 21H21.5371C17.6711 21 14.5371 24.134 14.5371 28V95V96H15.5371H78.5371Z"
            fill="url(#paint5_linear_6894_874)"
          />
          <path
            d="M78.5371 96H79.5371V95V28C79.5371 24.134 76.4031 21 72.5371 21H21.5371C17.6711 21 14.5371 24.134 14.5371 28V95V96H15.5371H78.5371Z"
            stroke="url(#paint6_linear_6894_874)"
            stroke-width="2"
          />
          <rect
            x="23.5371"
            y="30"
            width="47"
            height="4"
            rx="2"
            fill="url(#paint7_linear_6894_874)"
          />
          <rect
            x="23.5371"
            y="40"
            width="47"
            height="4"
            rx="2"
            fill="url(#paint8_linear_6894_874)"
          />
          <rect
            x="23.5371"
            y="50"
            width="24"
            height="4"
            rx="2"
            fill="url(#paint9_linear_6894_874)"
          />
          <rect
            width="100"
            height="100"
            transform="translate(0.5 9)"
            fill="url(#paint10_linear_6894_874)"
          />
          <path
            d="M89.7665 18.8875L88.75 22.236L87.7335 18.8875C87.1996 17.1288 85.7389 15.754 83.8702 15.2515L80.3125 14.2948L83.8702 13.3381C85.7389 12.8356 87.1996 11.4608 87.7335 9.70209L88.75 6.35363L89.7665 9.70209C90.3004 11.4608 91.7611 12.8356 93.6298 13.3381L97.1875 14.2948L93.6298 15.2515C91.7611 15.754 90.3004 17.1288 89.7665 18.8875Z"
            stroke="url(#paint11_linear_6894_874)"
            stroke-width="1.25"
            stroke-linecap="round"
            stroke-linejoin="round"
          />
          <path
            d="M100.324 10.4296L100 11.6477L99.6764 10.4296C99.2985 9.00696 98.1183 7.89618 96.6068 7.54053L95.3125 7.23598L96.6068 6.93144C98.1183 6.57579 99.2985 5.46501 99.6764 4.04241L100 2.82422L100.324 4.04241C100.701 5.46501 101.882 6.57579 103.393 6.93144L104.688 7.23598L103.393 7.54053C101.882 7.89618 100.701 9.00695 100.324 10.4296Z"
            stroke="url(#paint12_linear_6894_874)"
            stroke-width="1.25"
            stroke-linecap="round"
            stroke-linejoin="round"
          />
          <path
            d="M98.6178 24.3739L98.125 25.7654L97.6322 24.3739C97.3523 23.5835 96.6932 22.9633 95.8534 22.6998L94.375 22.236L95.8534 21.7722C96.6932 21.5087 97.3523 20.8884 97.6322 20.098L98.125 18.7066L98.6178 20.098C98.8977 20.8884 99.5568 21.5087 100.397 21.7722L101.875 22.236L100.397 22.6998C99.5568 22.9633 98.8977 23.5835 98.6178 24.3739Z"
            stroke="url(#paint13_linear_6894_874)"
            stroke-width="1.25"
            stroke-linecap="round"
            stroke-linejoin="round"
          />
          <defs>
            <linearGradient
              id="paint0_linear_6894_874"
              x1="31.6431"
              y1="17.1538"
              x2="97.3504"
              y2="81.7277"
              gradientUnits="userSpaceOnUse"
            >
              <stop stop-color="#F5F8FB" />
              <stop
                offset="1"
                stop-color="white"
              />
            </linearGradient>
            <linearGradient
              id="paint1_linear_6894_874"
              x1="31.2928"
              y1="93.4417"
              x2="93.1583"
              y2="93.3534"
              gradientUnits="userSpaceOnUse"
            >
              <stop stop-color="#CBD5E1" />
              <stop
                offset="1"
                stop-color="#EAEEF2"
              />
            </linearGradient>
            <linearGradient
              id="paint2_linear_6894_874"
              x1="39.3548"
              y1="28.2434"
              x2="85.4911"
              y2="27.347"
              gradientUnits="userSpaceOnUse"
            >
              <stop stop-color="#CBD5E1" />
              <stop
                offset="1"
                stop-color="#EAEEF2"
              />
            </linearGradient>
            <linearGradient
              id="paint3_linear_6894_874"
              x1="39.3548"
              y1="38.2434"
              x2="85.4911"
              y2="37.347"
              gradientUnits="userSpaceOnUse"
            >
              <stop stop-color="#CBD5E1" />
              <stop
                offset="1"
                stop-color="#EAEEF2"
              />
            </linearGradient>
            <linearGradient
              id="paint4_linear_6894_874"
              x1="39.444"
              y1="48.2434"
              x2="63.0096"
              y2="48.0096"
              gradientUnits="userSpaceOnUse"
            >
              <stop stop-color="#CBD5E1" />
              <stop
                offset="1"
                stop-color="#EAEEF2"
              />
            </linearGradient>
            <linearGradient
              id="paint5_linear_6894_874"
              x1="15.6431"
              y1="23.1538"
              x2="81.3504"
              y2="87.7277"
              gradientUnits="userSpaceOnUse"
            >
              <stop stop-color="#F5F8FB" />
              <stop
                offset="1"
                stop-color="white"
              />
            </linearGradient>
            <linearGradient
              id="paint6_linear_6894_874"
              x1="15.2928"
              y1="99.4417"
              x2="77.1583"
              y2="99.3534"
              gradientUnits="userSpaceOnUse"
            >
              <stop stop-color="#CBD5E1" />
              <stop
                offset="1"
                stop-color="#EAEEF2"
              />
            </linearGradient>
            <linearGradient
              id="paint7_linear_6894_874"
              x1="23.3548"
              y1="34.2434"
              x2="69.4911"
              y2="33.347"
              gradientUnits="userSpaceOnUse"
            >
              <stop stop-color="#CBD5E1" />
              <stop
                offset="1"
                stop-color="#EAEEF2"
              />
            </linearGradient>
            <linearGradient
              id="paint8_linear_6894_874"
              x1="23.3548"
              y1="44.2434"
              x2="69.4911"
              y2="43.347"
              gradientUnits="userSpaceOnUse"
            >
              <stop stop-color="#CBD5E1" />
              <stop
                offset="1"
                stop-color="#EAEEF2"
              />
            </linearGradient>
            <linearGradient
              id="paint9_linear_6894_874"
              x1="23.444"
              y1="54.2434"
              x2="47.0096"
              y2="54.0096"
              gradientUnits="userSpaceOnUse"
            >
              <stop stop-color="#CBD5E1" />
              <stop
                offset="1"
                stop-color="#EAEEF2"
              />
            </linearGradient>
            <linearGradient
              id="paint10_linear_6894_874"
              x1="50"
              y1="0"
              x2="50"
              y2="100"
              gradientUnits="userSpaceOnUse"
            >
              <stop
                offset="0.53"
                stop-color="white"
                stop-opacity="0"
              />
              <stop
                offset="0.84"
                stop-color="white"
              />
            </linearGradient>
            <linearGradient
              id="paint11_linear_6894_874"
              x1="92.5"
              y1="2.82422"
              x2="92.5"
              y2="25.7654"
              gradientUnits="userSpaceOnUse"
            >
              <stop stop-color="#B4D5FF" />
              <stop
                offset="1"
                stop-color="#2B88FD"
              />
            </linearGradient>
            <linearGradient
              id="paint12_linear_6894_874"
              x1="92.5"
              y1="2.82422"
              x2="92.5"
              y2="25.7654"
              gradientUnits="userSpaceOnUse"
            >
              <stop stop-color="#B4D5FF" />
              <stop
                offset="1"
                stop-color="#2B88FD"
              />
            </linearGradient>
            <linearGradient
              id="paint13_linear_6894_874"
              x1="92.5"
              y1="2.82422"
              x2="92.5"
              y2="25.7654"
              gradientUnits="userSpaceOnUse"
            >
              <stop stop-color="#B4D5FF" />
              <stop
                offset="1"
                stop-color="#2B88FD"
              />
            </linearGradient>
          </defs>
        </svg>
        <section class="flex flex-col mt-2 max-md:max-w-full">
          <h1
            class="justify-center self-center text-2xl font-bold tracking-tight leading-9 text-slate-800 max-md:max-w-full"
          >
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
                <div class="flex flex-col">
                  <div class="flex gap-2 py-px">
                    <h2 class="my-auto text-lg font-medium tracking-tighter leading-5 text-slate-900">
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
                    <p class="text-2xl font-semibold tracking-tight text-slate-900">
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
                  <p class="mt-4 text-sm leading-5 text-slate-500">
                    For companies and more customization on their forms.
                  </p>
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
      class="flex flex-col items-center px-6 pb-4 bg-white rounded-2xl w-full"
    >
      <div class="flex gap-2 max-md:flex-wrap">
        <div class="flex justify-center items-center p-2 rounded-[1000px]">
          <Icon
            name="heroicons:chevron-left-16-solid"
            class="h-6 w-6 cursor-pointer"
            @click.prevent="currentStep=1"
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
            @click="currentStep=1"
          >
            Back
          </UButton>
        </div>
      </div>
    </section>
  </modal>
</template>

<script setup>
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
