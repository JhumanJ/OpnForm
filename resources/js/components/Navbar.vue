<template>
  <nav v-if="hasNavbar" class="bg-white dark:bg-notion-dark">
    <div class="max-w-7xl mx-auto px-8">
      <div class="flex items-center justify-between h-16">
        <div class="flex items-center">
          <router-link :to="{ name: user ? 'home' : 'welcome' }" class="flex-shrink-0 font-bold flex items-center">
            <img :src="asset('img/logo.svg')" alt="notion tools logo" class="w-8 h-8">
            <span
              class="ml-3 text-xl hidden sm:inline text-black dark:text-white"
            >
              {{ appName }}</span><span
              class="ml-3 text-sm uppercase hidden sm:inline text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-blue-600"
            >Beta</span>
          </router-link>
          <workspace-dropdown class="ml-6" />
        </div>
        <div v-if="user" class="hidden md:block ml-auto relative">
          <a href="#" class="text-sm text-gray-400 hover:text-gray-800 cursor-pointer mt-1"
             @click.prevent="$getCrisp().push(['do', 'helpdesk:search'])"
          >
            Help
          </a>
        </div>
        <div v-if="showAuth" class="block">
          <div class="ml-4 flex items-center md:ml-6">
            <div class="ml-3 mr-4 relative">
              <div class="relative inline-block text-left">
                <dropdown v-if="user" dusk="nav-dropdown">
                  <template #trigger="{toggle}">
                    <button id="dropdown-menu-button" type="button"
                            class="flex items-center justify-center w-full rounded-md  px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-50 hover:bg-gray-50 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-gray-500"
                            dusk="nav-dropdown-button" @click.prevent="toggle()"
                    >
                      <img :src="user.photo_url" class="rounded-full w-6 h-6">
                      <p class="ml-2 hidden sm:inline">
                        {{ user.name }}
                      </p>
                    </button>
                  </template>

                  <router-link v-if="userOnboarded" :to="{ name: 'home' }"
                               class="block block px-4 py-2 text-md text-gray-700 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-100 dark:hover:text-white dark:hover:bg-gray-600 flex items-center"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor"
                    >
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                      />
                    </svg>
                    My Forms
                  </router-link>

                  <router-link :to="{ name: 'settings.profile' }"
                               class="block block px-4 py-2 text-md text-gray-700 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-100 dark:hover:text-white dark:hover:bg-gray-600 flex items-center"
                  >
                    <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor"
                    >
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
                      />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                      />
                    </svg>
                    {{ $t('settings') }}
                  </router-link>

                  <a href="#"
                     class="block block px-4 py-2 text-md text-gray-700 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-100 dark:hover:text-white dark:hover:bg-gray-600 flex items-center"
                     @click.prevent="logout"
                  >
                    <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor"
                    >
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                      />
                    </svg>
                    {{ $t('logout') }}
                  </a>
                </dropdown>
                <div v-else>
                  <router-link v-if="$route.name !== 'login'" :to="{ name: 'login' }"
                               class="text-gray-400 hover:text-gray-800 dark:hover:text-white px-0 sm:px-3 py-2 rounded-md text-sm font-medium"
                               active-class="text-gray-800 dark:text-white"
                  >
                    {{ $t('login') }}
                  </router-link>
                  <router-link :to="{ name: 'register' }"
                               class="text-gray-300 hover:text-gray-800 dark:hover:text-white pl-3 py-2 rounded-md text-sm font-medium"
                               active-class="text-gray-800 dark:text-white"
                  >
                    <v-button v-track.nav_create_form_click>
                      Create Form
                    </v-button>
                  </router-link>
                </div>
              </div>
            </div>
            <div>
              <transition name="fade" mode="out-in">
                <button v-if="darkModeEnabled" key="sun"
                        class="p-1 text-sm text-gray-400 hover:text-gray-800 dark:hover:text-white cursor-pointer mt-1 rounded-md hover:bg-gray-50 dark:hover:bg-gray-800"
                        @click="toggleDarkMode"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                       stroke="currentColor"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"
                    />
                  </svg>
                </button>
                <button v-else key="moon"
                        class="p-1 text-sm text-gray-400 hover:text-gray-800 dark:hover:text-white cursor-pointer mt-1 rounded-md hover:bg-gray-50 dark:hover:bg-gray-800"
                        @click="toggleDarkMode"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                       stroke="currentColor"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"
                    />
                  </svg>
                </button>
              </transition>
            </div>
          </div>
        </div>
      </div>
    </div>
  </nav>
</template>

<script>
import { mapGetters } from 'vuex'
import Dropdown from './common/Dropdown'
import axios from 'axios'
import WorkspaceDropdown from './WorkspaceDropdown'

export default {
  components: {
    WorkspaceDropdown,
    Dropdown
  },

  data: () => ({
    appName: window.config.appName,
    darkModeEnabled: false
  }),

  computed: {
    form () {
      if (this.$route.name && this.$route.name.startsWith('forms.show_public')) {
        return this.$store.getters['open/forms/getBySlug'](this.$route.params.slug)
      }
      return null
    },
    showAuth () {
      return this.$route.name && !this.$route.name.startsWith('forms.show_public')
    },
    hasNavbar () {
      if (this.isIframe) return false

      if (this.$route.name && this.$route.name.startsWith('forms.show_public')) {
        if (this.form) {
          // If there is a cover, or if branding is hidden remove nav
          if (this.form.cover_picture || this.form.no_branding) {
            return false
          }
        } else {
          return false
        }
      }
      return true
    },
    isIframe () {
      return window.location !== window.parent.location || window.frameElement
    },
    ...mapGetters({
      user: 'auth/user'
    }),
    userOnboarded () {
      return this.user && this.user.workspaces_count > 0
    }
  },

  watch: {
    darkModeEnabled: {
      handler (val) {
        window.localStorage.setItem('opnform-dark-mode-enabled', val ? 1 : 0)
      },
      deep: true
    }
  },

  mounted () {
    this.darkModeEnabled = document.body.classList.contains('dark')
  },

  methods: {
    async logout () {
      // Log out the user.
      await this.$store.dispatch('auth/logout')

      // Reset store
      this.$store.dispatch('open/workspaces/resetState')
      this.$store.dispatch('open/forms/resetState')

      // Redirect to login.
      this.$router.push({ name: 'login' })
    },
    toggleDarkMode () {
      document.body.classList.toggle('dark')
      this.darkModeEnabled = document.body.classList.contains('dark')
    }
  }
}
</script>
