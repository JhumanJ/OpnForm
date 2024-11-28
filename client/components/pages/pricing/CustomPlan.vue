<template>
  <section class="relative">
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
      <div class="max-w-5xl mx-auto bg-white shadow-xl rounded-3xl ring-1 ring-gray-200 lg:flex isolate">
        <div class="p-8 sm:p-8 lg:flex-auto">
          <h3 class="text-3xl font-semibold tracking-tight text-blue-500">
            Enterprise Plan
          </h3>
          <p class="mt-2 text-base font-medium leading-7 text-gray-600">
            Unlock ultimate scalability and enterprise-grade features with the Enterprise Plan. Tailored to meet the needs of organizations requiring dedicated infrastructure, advanced security, and priority support.
          </p>
          <div class="flex items-center mt-6 gap-x-4">
            <h4 class="flex-none text-sm font-semibold leading-6 tracking-widest text-gray-400 uppercase">
              What's included
            </h4>
            <div class="flex-auto h-px bg-gray-200" />
          </div>
          <ul
            role="list"
            class="grid grid-cols-1 gap-4 mt-4 text-sm font-medium leading-6 text-gray-900 sm:grid-cols-2 sm:gap-x-6 sm:gap-y-2"
          >
            <li class="flex gap-x-3">
              <svg
                aria-hidden="true"
                class="w-5 h-5 shrink-0 stroke-blue-600"
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M20 6L9 17L4 12"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
              </svg>
              Everything in Pro Plan
            </li>
            <li class="flex gap-x-3">
              <svg
                aria-hidden="true"
                class="w-5 h-5 shrink-0 stroke-blue-600"
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M20 6L9 17L4 12"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
              </svg>
              Self-hosted/dedicated instance
            </li>
            <li class="flex gap-x-3">
              <svg
                aria-hidden="true"
                class="w-5 h-5 shrink-0 stroke-blue-600"
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M20 6L9 17L4 12"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
              </svg>
              Priority support
            </li>
            <li class="flex gap-x-3">
              <svg
                aria-hidden="true"
                class="w-5 h-5 shrink-0 stroke-blue-600"
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M20 6L9 17L4 12"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
              </svg>
              Payment via invoice/PO
            </li>
            <li class="flex gap-x-3">
              <svg
                aria-hidden="true"
                class="w-5 h-5 shrink-0 stroke-blue-600"
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M20 6L9 17L4 12"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
              </svg>
              SAML single sign-on (SSO)
            </li>
            <li class="flex gap-x-3">
              <svg
                aria-hidden="true"
                class="w-5 h-5 shrink-0 stroke-blue-600"
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M20 6L9 17L4 12"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
              </svg>
              Security audits
            </li>
            <li class="flex gap-x-3">
              <svg
                aria-hidden="true"
                class="w-5 h-5 shrink-0 stroke-blue-600"
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M20 6L9 17L4 12"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
              </svg>
              Two-factor authentication (2FA)
            </li>
            <li class="flex gap-x-3">
              <Icon
                class="w-5 h-5 shrink-0 text-blue-600"
                name="i-heroicons-users-16-solid"
              />
              Unlimited users
            </li>
          </ul>
        </div>
        <div class="p-2 -mt-2 flex-col lg:mt-0 lg:w-full lg:max-w-md lg:flex-shrink-0">
          <div class="grow h-full py-10 text-center rounded-2xl bg-gray-50 ring-1 ring-inset ring-gray-900/5 lg:flex lg:flex-col lg:justify-center lg:py-12">
            <div class="max-w-xs px-8 mx-auto space-y-6">
              <div class="flex items-center justify-center mb-10">
                <monthly-yearly-selector v-model="isYearly" />
              </div>
              <p class="flex flex-col items-center">
                <span class="text-6xl font-semibold tracking-tight text-gray-950">
                  <template v-if="isYearly">$160</template>
                  <template v-else>$200</template>
                </span>
                <span class="text-sm font-medium leading-6 text-gray-600">
                  starting from per month
                </span>
              </p>
              <div class="flex justify-center">
                <button 
                  class="v-btn py-2 px-4 bg-blue-600 hover:bg-blue-700 focus:ring-blue-500 focus:ring-offset-blue-200 text-white transition ease-in duration-200 text-center text-base font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 rounded-lg flex items-center hover:no-underline"
                  @click="customPlanClick"
                >
                  <span class="no-underline mx-auto">Contact Us</span>
                </button>
              </div>
              <p class="text-xs font-medium leading-5 text-gray-600">
                Let us design a tailored plan to meet your enterprise needs.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue'
const isYearly = ref(true)
</script>

<script>
export default {
  name: "CustomPlan",
  components: {},
  props: {},

  data: () => ({}),

  computed: {},

  methods: {
    customPlanClick() {
      useCrisp().sendTextMessage(
        "Hi, I would like to discuss about an enterprise plan",
      )
    },
  },
}
</script>
