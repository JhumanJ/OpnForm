<template>
  <div class="w-full">
    <section class="py-4 bg-white dark:bg-notion-dark">
      <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="flex items-center justify-center">
          <monthly-yearly-selector v-model="isYearly" class="mt-5" />
        </div><!-- lg+ -->
        <div class="hidden mt-10 lg:block">
          <table class="w-full">
            <thead>
              <tr class="sticky top-0 bg-white dark:bg-notion-dark">
                <th class="py-8 pr-4" />

                <th class="px-4 py-8 text-center">
                  <span class="text-base font-medium text-blue-600"> Free </span>
                  <p class="mt-6 text-4xl font-bold">
                    $0
                  </p>
                  <p class="mt-2 text-base font-normal text-gray-500">
                    Per month
                  </p>
                </th>

                <th class="px-4 py-8 text-center rounded-t-xl from-blue-400 to-blue-600 bg-gradient-to-r">
                  <span class="px-4 py-2 text-base font-medium text-white bg-blue-600 rounded-full"> Pro </span>
                  <template v-if="!isYearly">
                    <p class="mt-6 text-4xl font-bold text-white">
                      $24
                    </p>
                    <p class="mt-2 text-base font-normal text-gray-200">
                      Per month
                    </p>
                  </template>
                  <template v-else>
                    <p class="mt-6 text-4xl font-bold text-white">
                      $20
                    </p>
                    <p class="mt-2 text-base font-normal text-gray-200">
                      Per month
                    </p>
                  </template>
                </th>

                <th class="px-4 py-8 text-center">
                  <span class="text-base font-medium text-blue-600"> Enterprise </span>
                  <template v-if="!isYearly">
                    <p class="mt-6 text-4xl font-bold">
                      $59
                    </p>
                    <p class="mt-2 text-base font-normal text-gray-500">
                      Per month
                    </p>
                  </template>
                  <template v-else>
                    <p class="mt-6 text-4xl font-bold">
                      $50
                    </p>
                    <p class="mt-2 text-base font-normal text-gray-500">
                      Per month
                    </p>
                  </template>
                </th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="(row, i) in pricingInfo" :key="i">
                <td class="py-4 pr-4 font-medium border-b border-gray-200">
                  <a href="javascript:void(0);" class="flex items-center" @click.prevent="currentFeature=i">
                    <div v-if="row.icon" class="flex items-center justify-center w-8 h-8 rounded-full bg-nt-blue-lighter">
                      <svg xmlns="http://www.w3.org/2000/svg" stroke-width="2" class="w-4 h-4 text-nt-blue" fill="none"
                           viewBox="0 0 24 24" stroke="currentColor" v-html="row.icon"
                      />
                    </div>
                    <span class="mx-2 text-gray-900 dark:text-gray-200">{{ row.title }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-300" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2"
                    >
                      <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                      />
                    </svg>
                  </a>
                </td>

                <template v-for="(planRow, j) in row.plans">
                  <td v-if="planRow === true" :key="j"
                      :class="{'px-4 py-4 text-center border-b border-gray-200':(j!==1), 'px-4 py-4 text-center text-white border-b border-white/20 from-blue-400 to-blue-600 bg-gradient-to-r':(j===1)}"
                  >
                    <svg class="w-5 h-5 mx-auto" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                         fill="currentColor"
                    >
                      <path fill-rule="evenodd"
                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                            clip-rule="evenodd"
                      />
                    </svg>
                  </td>
                  <td v-else-if="planRow === false" :key="j"
                      :class="{'px-4 py-4 text-center border-b border-gray-200':(j!==1), 'px-4 py-4 text-center text-white border-b border-white/20 from-blue-400 to-blue-600 bg-gradient-to-r':(j===1)}"
                  >
                    -
                  </td>
                  <td v-else :key="j"
                      :class="{'px-4 py-4 text-center border-b border-gray-200':(j!==1), 'px-4 py-4 text-center text-white border-b border-white/20 from-blue-400 to-blue-600 bg-gradient-to-r':(j===1)}"
                  >
                    {{ planRow }}
                  </td>
                </template>
              </tr>
              <tr>
                <td class="py-6 pr-4" />
                <template v-if="!authenticated">
                  <td class="px-4 py-6 text-center">
                    <router-link :to="{name:'register'}" title=""
                                 class="inline-flex items-center font-semibold text-blue-600 hover:text-blue-700"
                    >
                      Get Started
                      <svg class="w-4 h-4 ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                           fill="currentColor"
                      >
                        <path fill-rule="evenodd"
                              d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                              clip-rule="evenodd"
                        />
                      </svg>
                    </router-link>
                  </td>
                  <td class="px-4 py-6 text-center text-white bg-nt-blue-default rounded-b-xl">
                    <router-link :to="{name:'register'}" title=""
                                 class="inline-flex items-center font-semibold text-white"
                    >
                      Start Trial
                      <svg class="w-4 h-4 ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                           fill="currentColor"
                      >
                        <path fill-rule="evenodd"
                              d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                              clip-rule="evenodd"
                        />
                      </svg>
                    </router-link>
                  </td>
                  <td class="px-4 py-6 text-center">
                    <router-link :to="{name:'register'}" title=""
                                 class="inline-flex items-center font-semibold text-blue-600 hover:text-blue-700"
                    >
                      Start Trial
                      <svg class="w-4 h-4 ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                           fill="currentColor"
                      >
                        <path fill-rule="evenodd"
                              d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                              clip-rule="evenodd"
                        />
                      </svg>
                    </router-link>
                  </td>
                </template>
                <template v-else>
                  <td class="px-4 py-6 text-center">
                    <svg v-if="!user.is_subscribed" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke-width="1.5" stroke="currentColor" class="w-6 h-6 inline text-gray-500"
                    >
                      <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                      />
                    </svg>
                    <span v-else>-</span>
                  </td>
                  <td class="px-4 py-6 text-center text-white bg-nt-blue-default rounded-b-xl">
                    <a v-if="!user.is_subscribed"
                       v-track.pricing_table_subscribe_click="{plan:'pro',period:isYearly?'yearly':'monthly'}" href="#"
                       title=""
                       class="inline-flex group items-center font-semibold text-white"
                       @click.prevent="openCustomerCheckout('default')"
                    >
                      Start Trial
                      <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform"
                           xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                           fill="currentColor"
                      >
                        <path fill-rule="evenodd"
                              d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                              clip-rule="evenodd"
                        />
                      </svg>
                    </a>
                    <svg v-else-if="!user.has_enterprise_subscription" xmlns="http://www.w3.org/2000/svg" fill="none"
                         viewBox="0 0 24 24"
                         stroke-width="1.5" stroke="currentColor" class="w-6 h-6 inline text-blue-500"
                    >
                      <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                      />
                    </svg>
                    <span v-else>-</span>
                  </td>
                  <td class="px-4 py-6 text-center">
                    <a v-if="!user.is_subscribed"
                       v-track.pricing_table_subscribe_click="{plan:'pro',period:isYearly?'yearly':'monthly'}" href="#"
                       title=""
                       class="inline-flex group items-center font-semibold text-blue-500"
                       @click.prevent="openCustomerCheckout('enterprise')"
                    >
                      Start Trial
                      <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform"
                           xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                           fill="currentColor"
                      >
                        <path fill-rule="evenodd"
                              d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                              clip-rule="evenodd"
                        />
                      </svg>
                    </a>
                    <a v-else-if="user.is_subscribed && !user.has_enterprise_subscription"
                       v-track.pricing_table_subscribe_click="{plan:'enterprise',period:isYearly?'yearly':'monthly'}"
                       href="#"
                       title=""
                       class="inline-flex group items-center font-semibold text-blue-500"
                       @click.prevent="openBilling"
                    >
                      Upgrade Now
                      <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform"
                           xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                           fill="currentColor"
                      >
                        <path fill-rule="evenodd"
                              d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                              clip-rule="evenodd"
                        />
                      </svg>
                    </a>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke-width="1.5" stroke="currentColor" class="w-6 h-6 inline text-blue-500"
                    >
                      <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                      />
                    </svg>
                  </td>
                </template>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- xs to lg -->
      <div class="block mt-10 border-t border-b border-gray-200 divide-y divide-gray-200 lg:hidden w-full">
        <div class="grid grid-cols-4 text-center divide-x divide-gray-200 sticky top-0 bg-white dark:bg-notion-dark">
          <div class="px-2 py-2">
            <span class="text-sm font-medium text-blue-600"> Free </span>
            <p class="mt-2 text-xl font-bold">
              $0
            </p>
            <span class="mt-1 text-sm font-normal text-gray-500"> Per month </span>
          </div>

          <div class="px-2 py-2">
            <span class="text-sm font-medium text-blue-600"> Pro </span>
            <template v-if="!isYearly">
              <p class="mt-2 text-xl font-bold">
                $24
              </p>
              <span class="mt-1 text-sm font-normal text-gray-500"> Per month </span>
            </template>
            <template v-else>
              <p class="mt-2 text-xl font-bold">
                $20
              </p>
              <span class="mt-1 text-sm font-normal text-gray-500"> Per month </span>
            </template>
          </div>

          <div class="px-2 py-2">
            <span class="text-sm font-medium text-blue-600"> Enterprise </span>
            <template v-if="!isYearly">
              <p class="mt-2 text-xl font-bold">
                $59
              </p>
              <span class="mt-1 text-sm font-normal text-gray-500"> Per month </span>
            </template>
            <template v-else>
              <p class="mt-2 text-xl font-bold">
                $50
              </p>
              <span class="mt-1 text-sm font-normal text-gray-500"> Per month </span>
            </template>
          </div>
        </div>

        <template v-for="(row, i) in pricingInfo">
          <div :key="'s-'+i" class="px-2 py-4 sm:px-4 flex">
            <a href="javascript:void(0);" @click.prevent="currentFeature=i">
              <p class="font-semibold mr-2">
                {{ row.title }}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-300 inline -mt-1" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                >
                  <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                  />
                </svg>
              </p>
            </a>
          </div>
          <div :key="i" class="grid grid-cols-4 text-center divide-x divide-gray-200">
            <template v-for="(planRow, j) in row.plans">
              <td v-if="planRow === true" :key="'s'+j" class="px-2 py-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mx-auto text-nt-blue" viewBox="0 0 20 20"
                     fill="currentColor"
                >
                  <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd"
                  />
                </svg>
              </td>
              <td v-else-if="planRow === false" :key="'s'+j" class="px-2 py-2">
                -
              </td>
              <td v-else :key="'s'+j" class="px-2 py-2">
                {{ planRow }}
              </td>
            </template>
          </div>
        </template>

        <div v-if="!authenticated" class="grid grid-cols-4 text-center divide-x divide-gray-200">
          <div class="px-1 py-2 sm:px-4">
            <span class="text-sm font-medium text-blue-600"> Free </span>
            <p class="mt-2 text-xl font-bold">
              $0
            </p>
            <span class="mt-1 text-sm font-normal text-gray-500"> Per month </span>
            <router-link :to="{name:'register'}" title=""
                         class="flex items-center justify-center w-full px-1 py-2 mt-5 text-xs text-white transition-all duration-200 bg-blue-600 border border-transparent rounded-md hover:bg-blue-700"
                         role="button"
            >
              Get Started
            </router-link>
          </div>

          <div class="px-1 py-2 sm:px-4">
            <span class="text-sm font-medium text-blue-600"> Pro </span>
            <template v-if="!isYearly">
              <p class="mt-2 text-xl font-bold">
                $24
              </p>
              <span class="mt-1 text-sm font-normal text-gray-500"> Per month </span>
            </template>
            <template v-else>
              <p class="mt-2 text-xl font-bold">
                $240
              </p>
              <span class="mt-1 text-sm font-normal text-gray-500"> Per year </span>
            </template>
            <router-link :to="{name:'register'}" title=""
                         class="flex items-center justify-center w-full px-1 py-2 mt-5 text-xs text-white transition-all duration-200 bg-blue-600 border border-transparent rounded-md hover:bg-blue-700"
                         role="button"
            >
              Start Trial
            </router-link>
          </div>

          <div class="px-1 pt-2 pb-4 sm:px-4">
            <span class="text-sm font-medium text-blue-600"> Enterprise </span>
            <template v-if="!isYearly">
              <p class="mt-2 text-xl font-bold">
                $59
              </p>
              <span class="mt-1 text-sm font-normal text-gray-500"> Per month </span>
            </template>
            <template v-else>
              <p class="mt-2 text-xl font-bold">
                $599
              </p>
              <span class="mt-1 text-sm font-normal text-gray-500"> Per year </span>
            </template>
            <router-link :to="{name:'register'}" title=""
                         class="flex items-center justify-center w-full px-1 py-2 mt-5 text-xs text-white transition-all duration-200 bg-blue-600 border border-transparent rounded-md hover:bg-blue-700"
                         role="button"
            >
              Start Trial
            </router-link>
          </div>
        </div>
        <div v-else class="grid grid-cols-4 text-center divide-x divide-gray-200">
          <div class="px-1 py-2 sm:px-4">
            <span class="text-sm font-medium text-blue-600"> Free </span>
            <p class="mt-2 text-xl font-bold">
              $0
            </p>
            <span class="mt-1 text-sm font-normal text-gray-500"> Per month </span>
            <div class="mt-5 text-gray-500">
              <svg v-if="!user.is_subscribed" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                   stroke-width="1.5" stroke="currentColor" class="w-6 h-6 inline text-blue-500"
              >
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
              <span v-else>
                -
              </span>
            </div>
          </div>

          <div class="px-1 py-2 sm:px-4">
            <span class="text-sm font-medium text-blue-600"> Pro </span>
            <template v-if="!isYearly">
              <p class="mt-2 text-xl font-bold">
                $24
              </p>
              <span class="mt-1 text-sm font-normal text-gray-500"> Per month </span>
            </template>
            <template v-else>
              <p class="mt-2 text-xl font-bold">
                $240
              </p>
              <span class="mt-1 text-sm font-normal text-gray-500"> Per year </span>
            </template>
            <a v-if="!user.is_subscribed" href="#" :to="{name:'register'}" title=""
               class="flex items-center justify-center w-full px-1 py-2 mt-5 text-xs text-white transition-all duration-200 bg-blue-600 border border-transparent rounded-md hover:bg-blue-700"
               role="button"
               @click.prevent="openCustomerCheckout('default')"
            >
              Start Trial
            </a>
            <div v-else class="mt-5 text-gray-500">
              <svg v-if="!user.has_enterprise_subscription" xmlns="http://www.w3.org/2000/svg" fill="none"
                   viewBox="0 0 24 24"
                   stroke-width="1.5" stroke="currentColor" class="w-6 h-6 inline text-blue-500"
              >
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
              <span v-else>
                -
              </span>
            </div>
          </div>

          <div class="px-1 pt-2 pb-4 sm:px-4">
            <span class="text-sm font-medium text-blue-600"> Enterprise </span>
            <template v-if="!isYearly">
              <p class="mt-2 text-xl font-bold">
                $59
              </p>
              <span class="mt-1 text-sm font-normal text-gray-500"> Per month </span>
            </template>
            <template v-else>
              <p class="mt-2 text-xl font-bold">
                $599
              </p>
              <span class="mt-1 text-sm font-normal text-gray-500"> Per year </span>
            </template>
            <a v-if="!user.is_subscribed" href="#" :to="{name:'register'}" title=""
               class="flex items-center justify-center w-full px-1 py-2 mt-5 text-xs text-white transition-all duration-200 bg-blue-600 border border-transparent rounded-md hover:bg-blue-700"
               role="button"
               @click.prevent="openCustomerCheckout('default')"
            >
              Start Trial
            </a>
            <a v-else-if="!user.has_enterprise_subscription" href="#" :to="{name:'register'}" title=""
               class="flex items-center justify-center w-full px-1 py-2 mt-5 text-xs text-white transition-all duration-200 bg-blue-600 border border-transparent rounded-md hover:bg-blue-700"
               role="button"
               @click.prevent="openBilling"
            >
              Upgrade
            </a>
            <div v-else class="mt-5 text-gray-500">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                   viewBox="0 0 24 24"
                   stroke-width="1.5" stroke="currentColor" class="w-6 h-6 inline text-blue-500"
              >
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!--  Modal for view more -->
    <modal :show="currentFeature!==false" @close="currentFeature=false">
      <div v-if="getCurrentFeatureInfo" class="px-4">
        <h2 class="flex items-center text-nt-blue text-3xl font-bold mb-4">
          <div v-if="getCurrentFeatureInfo.icon"
               class="flex items-center justify-center w-12 h-12 mr-2 rounded-full bg-nt-blue-lighter"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-nt-blue" stroke-width="2" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor" v-html="getCurrentFeatureInfo.icon"
            />
          </div>
          {{ getCurrentFeatureInfo.title }}
        </h2>

        <p>
          {{ getCurrentFeatureInfo.description }}
        </p>

        <div class="flex justify-center my-4">
          <v-button color="gray" shade="light" @click="currentFeature=false">
            Close
          </v-button>
        </div>
      </div>
    </modal>
    <checkout-details-modal :show="showDetailsModal" :yearly="isYearly" :plan="selectedPlan"
                            @close="showDetailsModal=false"
    />
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import VSwitch from '../../forms/components/VSwitch.vue'
import CheckoutDetailsModal from './CheckoutDetailsModal.vue'
import axios from 'axios'
import VButton from '../../common/Button.vue'
import MonthlyYearlySelector from './MonthlyYearlySelector.vue'

export default {

  components: {
    MonthlyYearlySelector,
    VButton,
    CheckoutDetailsModal,
    VSwitch
  },
  props: {},
  data: () => ({
    isYearly: false,
    currentFeature: false,
    selectedPlan: 'pro',
    showDetailsModal: false,

    pricingInfo: [
      {
        title: 'Infinite Number of Fields',
        description: 'There are no limits on the number of input fields in your forms. Create as many as your Open database columns as you need to. Organize fields and decide which are required.',
        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14v6m-3-3h6M6 10h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2zm10 0h2a2 2 0 002-2V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2zM6 20h2a2 2 0 002-2v-2a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2z" />',
        plans: [true, true, true]
      },
      {
        title: 'Infinite Number of Forms',
        description: 'You can create as many forms as you need. You can even create multiple forms for the same Open database, with more or less fields.',
        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />',
        plans: [true, true, true]
      },
      {
        title: 'Infinite Responses',
        description: 'All of you forms can have unlimited responses, no need to worry about quotas and other stressful metrics.',
        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />',
        plans: [true, true, true]
      },
      {
        title: 'Notifications',
        description: 'Receive notifications directly in your mailbox whenever your from has a new submission.',
        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />',
        plans: [true, true, true]
      },
      {
        title: 'Integrate Anywhere',
        description: 'You can integrate your form anywhere: on your website or even directly within a Open Page.',
        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />',
        plans: [true, true, true]
      },
      {
        title: 'Dark Mode',
        description: 'A beautiful dark mode. Integrate your form to your Open page, and it will look beautiful. Even if you\'re using the dark mode.',
        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />',
        plans: [true, true, true]
      },

      // Pro
      {
        title: 'Customize Everything',
        description: 'Change form themes, change texts, colors, add images, add custom thank you pages and much more to come.',
        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />',
        plans: [false, true, true]
      },
      {
        title: 'Your own branding',
        description: 'Remove OpnForms branding, use your brand colors and add your logo.',
        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />',
        plans: [false, true, true]
      },
      {
        title: 'Slack Notifications',
        description: 'Receive notifications directly in Slack whenever your from has a new submission.',
        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />',
        plans: [true, true, true]
      },
      {
        title: 'Discord Notifications',
        description: 'Receive notifications directly in Slack whenever your from has a new submission.',
        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />',
        plans: [true, true, true]
      },
      {
        title: 'Multi-pages Forms',
        description: 'You have many questions? Split your form into many steps/pages, just like with Typeform.',
        icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />',
        plans: [false, true, true]
      },
      {
        title: 'Custom scripts & CSS',
        description: 'There\'s no limit on what you can do with your own CSS and your own scripts. Add an analytics script or a live chat on your form page.',
        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />',
        plans: [false, true, true]
      },
      {
        title: 'Built-in Analytics',
        description: 'Need to know how many people saw your form? How many submitted an answer? No problem.',
        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />',
        plans: [false, true, true]
      },
      {
        title: 'File Uploads',
        description: 'Need your respondants to upload files? Easy.',
        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />',
        plans: [false, true, true]
      },
      {
        title: 'Zapier Integration',
        description: 'In just a few minutes, you can set up automated workflows (called Zaps) that connect your OpnForms with the other apps you use most.',
        icon: '<svg class="h-6 w-6 text-nt-blue" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 512 512"><path d="M318 256c0 19-4 36-10 52-16 7-34 10-52 10-19 0-36-3-52-9-7-17-10-34-10-53 0-18 3-36 10-52 16-6 33-10 52-10 18 0 36 4 52 10 6 16 10 34 10 52zm182-41H355l102-102c-8-11-17-22-26-32-10-9-21-18-32-26L297 157V12c-13-2-27-3-41-3s-28 1-41 3v145L113 55c-12 8-22 17-32 26-10 10-19 21-27 32l102 102H12s-3 27-3 41 1 28 3 41h144L54 399c16 23 36 43 59 59l102-102v144c13 2 27 3 41 3s28-1 41-3V356l102 102c11-8 22-17 32-27 9-10 18-20 26-32L355 297h145c2-13 3-27 3-41s-1-28-3-41z" /></svg>',
        plans: [false, true, true]
      },
      {
        title: 'Webhooks',
        description: 'Do you need to do more with your form? Receive form submissions wherever you want with webhooks.',
        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />',
        plans: [false, true, true]
      },
      {
        title: 'Relation Support',
        description: 'Use relation type columns and select in your forms from your related Opn tables.',
        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />',
        plans: [true, true, true]
      },
      {
        title: 'Password Protection',
        description: 'Protect your form with a password to make sure only the right people can submit their answers.',
        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />',
        plans: [false, true, true]
      },
      {
        title: 'URL Parameters Prefill',
        description: 'Prefill your forms with custom URLs. For instance you can send out forms via email and pre-fill the name and email of the person you send it to.',
        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />',
        plans: [false, true, true]
      },
      {
        title: 'Redirect on Submission',
        description: 'Redirect respondants to your website on form submission. Don\'t loose any traffic, and have a 100% customized submission success page.',
        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />',
        plans: [false, true, true]
      },
      {
        title: 'Update Records on Submission',
        description: 'Update an existing database record instead creating a new one on form submission.',
        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />',
        plans: [false, true, true]
      },
      {
        title: 'Write in Opn Page Body',
        description: 'Receive text from your form submission content directly into the body of the created Opn Page.',
        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />',
        plans: [false, true, true]
      },
      {
        title: 'Unique Submission IDs',
        description: 'Generate unique a unique submission ID for each of your form submissions.',
        icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />',
        plans: [false, true, true]
      },
      {
        title: 'Close form at date',
        description: 'Expire form at a specific date to prevent respondents to submit passed a certain point.',
        icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />',
        plans: [false, true, true]
      },
      {
        title: 'Captcha Protection',
        description: 'Protect your form from bots with a captcha ensure that respondants are humans.',
        icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />',
        plans: [false, true, true]
      },
      {
        title: 'Submission Confirmation Emails',
        description: 'Send a custom confirmation email to your form respondents. Customize the email content and subject.',
        icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />',
        plans: [false, true, true]
      },
      {
        title: 'Custom Blocks',
        description: 'Add our custom blocks to your forms: text, images, dividers and more.',
        icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M17 14v6m-3-3h6M6 10h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2zm10 0h2a2 2 0 002-2V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2zM6 20h2a2 2 0 002-2v-2a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2z" />',
        plans: [false, true, true]
      },
      {
        title: 'Form Logic',
        description: 'Add logic to your form! Conditionnaly show fields, or make them required!',
        icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />',
        plans: [false, true, true]
      },
      {
        title: 'Premium Support',
        description: 'All the help you need within 24 hours. The truth is, most of times we\'ll be able to help within the hour.',
        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />',
        plans: [false, false, true]
      },
      {
        title: 'Opn Workspaces',
        description: 'Enterprise subscribers can connect as many Opn workspaces as needed, while free and Pro users connect 1 Opn workspace.',
        icon: '<path stroke-linecap="round" stroke-linejoin="round"\n' +
          'd="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>',
        plans: ['1', '1', '∞']
      },
      {
        title: 'Users (collaboration)',
        description: 'Enterprise subscribers can collaborate with as many collaborators as needed. Free and Pro users can only have 1 user per account.',
        icon: '<path stroke-linecap="round" stroke-linejoin="round"\n' +
          '              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>',
        plans: ['1', '1', '∞']
      },
      {
        title: 'File Upload Limit',
        description: 'Pro subscribers have a limit of 5mb per uploaded file while Enterprise subscribers have a limit of 20mb per uploaded file.',
        icon: '<path stroke-linecap="round" stroke-linejoin="round"\n' +
          '              d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>',
        plans: ['-', '5 mb', '20 mb']
      },
      {
        title: 'Custom Domains',
        description: 'Host forms on your own (sub)domain. There can be 1 domain per Opn workspace.',
        icon: '<path stroke-linecap = "round" stroke-linejoin = "round" d = "M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" />',
        plans: [false, false, true]
      },
      {
        title: 'Editable Submissions',
        description: 'With OpnForms, form respondents can edit their form submissions if you allow them to. When a respondents submits a form that\'s editing a previous submission, OpnForms directly update the associated database record in Opn. This feature is really convenient, for various cases such as event attendance, or CMS external updates. Note that this is a Pro feature.',
        icon: ' <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>\n',
        plans: [false, false, true]
      },
      {
        title: 'Lookup pages',
        description: 'This feature allows form owners to add a lookup page before respondents access the main form. On this page, respondents can select an existing record to update, or create a new one if necessary.',
        icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>\n',
        plans: [false, false, true]
      }
    ]
  }),

  methods: {
    openCustomerCheckout (plan) {
      this.selectedPlan = plan
      this.showDetailsModal = true
    },
    openBilling () {
      this.billingLoading = true
      axios.get('/api/subscription/billing-portal').then((response) => {
        this.billingLoading = false
        const url = response.data.portal_url
        window.location = url
      })
    }
  },

  computed: {
    ...mapGetters({
      authenticated: 'auth/check',
      user: 'auth/user'
    }),
    getCurrentFeatureInfo () {
      if (this.currentFeature !== false) {
        return (this.pricingInfo[this.currentFeature] !== undefined) ? this.pricingInfo[this.currentFeature] : false
      }
      return false
    }
  }
}
</script>
