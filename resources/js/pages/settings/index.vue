<template>
  <div class="flex flex-wrap mt-6 md:max-w-3xl w-full md:mx-auto">
    <div class="w-full md:w-1/3 md:pr-4">
      <card :padding="false" class="bg-gray-50 dark:bg-notion-dark-light">
        <ul>
          <li v-for="tab in tabs" :key="tab.route">
            <router-link :to="{ name: tab.route }"
                         class="px-6 py-4 flex items-center text-gray-600 dark:text-gray-400 dark:hover:text-gray-300 hover:text-gray-900 hover:bg-gray-50 dark:hover:bg-gray-900 rounded"
                         active-class="text-nt-blue bg-indigo-50 dark:bg-gray-800 hover:bg-blue-50"
            >
              <template v-if="tab.route == 'settings.profile'">
                <svg class="w-6 h-6 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                  />
                </svg>
              </template>
              <template v-else-if="tab.route == 'settings.account'">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6" />
                </svg>
              </template>
              <template v-else-if="tab.route == 'settings.workspaces'">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
              </template>
              <template v-else-if="tab.route == 'settings.billing'">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"
                  />
                </svg>
              </template>
              <template v-else-if="tab.route == 'settings.password'">
                <svg class="w-6 h-6 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                  />
                </svg>
              </template>
              <span class="ml-2">
                {{ tab.name }}
              </span>
            </router-link>
          </li>
          <li v-if="user.admin">
            <router-link :to="{ name: 'settings.admin' }"
                         class="px-6 py-4 flex items-center text-gray-600 dark:text-gray-400 dark:hover:text-gray-300 hover:text-gray-900 hover:bg-gray-50 dark:hover:bg-gray-900 rounded"
                         active-class="text-nt-blue bg-indigo-50 dark:bg-gray-800 hover:bg-blue-50"
            >
              <svg class="w-6 h-6 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                   stroke="currentColor"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                />
              </svg>
              <span class="ml-2">
                Admin
              </span>
            </router-link>
          </li>
        </ul>
      </card>
    </div>

    <div class="w-full md:w-2/3">
      <transition name="fade" mode="out-in">
        <router-view />
      </transition>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  middleware: 'auth',

  computed: {
    tabs () {
      const tabs = [
        {
          name: 'Workspaces',
          route: 'settings.workspaces'
        },
        {
          name: this.$t('profile'),
          route: 'settings.profile'
        },
        {
          name: this.$t('password'),
          route: 'settings.password'
        },
        {
          name: 'Delete Account',
          route: 'settings.account'
        }
      ]

      if (this.user.is_subscribed) {
        tabs.splice(1, 0, {
          name: 'Billing',
          route: 'settings.billing'
        })
      }

      return tabs
    },
    ...mapGetters({
      user: 'auth/user'
    })
  }
}
</script>
