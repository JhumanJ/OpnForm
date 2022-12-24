<template>
  <nav v-if="hasNavbar" class="bg-white dark:bg-notion-dark border-b">
    <div class="max-w-7xl mx-auto px-8">
      <div class="flex items-center justify-between h-16">
        <div class="flex items-center">
          <router-link :to="{ name: user ? 'home' : 'welcome' }" class="flex-shrink-0 font-semibold hover:no-underline flex items-center">
            <img :src="asset('img/logo.svg')" alt="notion tools logo" class="w-8 h-8">
            <span
              class="ml-2 text-md hidden sm:inline text-black dark:text-white"
            >
              {{ appName }}</span><span
            class="bg-gray-100 text-gray-600 font-semibold inline-block ml-1 px-2 rounded-full text-black text-xs tracking-wider"
          >BETA</span>
          </router-link>
          <workspace-dropdown class="ml-6"/>
        </div>
        <div class="hidden md:block ml-auto relative">
          <router-link :to="{name:'templates'}"
                       class="text-sm text-gray-600 dark:text-white hover:text-gray-800 cursor-pointer mt-1 mr-8">
            Templates
          </router-link>
          <a href="#" class="text-sm text-gray-600 dark:text-white hover:text-gray-800 cursor-pointer mt-1"
             @click.prevent="$crisp.push(['do', 'helpdesk:search'])" v-if="hasCrisp"
          >
            Help
          </a>
          <a href="https://help.opnform.com/en/" class="text-sm text-gray-600 dark:text-white hover:text-gray-800 cursor-pointer mt-1"
             target="_blank" v-else
          >
            Help
          </a>
        </div>
        <div v-if="showAuth" class="hidden md:block pl-5 border-gray-300 border-r h-5"></div>
        <div v-if="showAuth" class="block">
          <div class="flex items-center">
            <div class="ml-3 mr-4 relative">
              <div class="relative inline-block text-left">
                <dropdown v-if="user" dusk="nav-dropdown">
                  <template #trigger="{toggle}">
                    <button id="dropdown-menu-button" type="button"
                            class="flex items-center justify-center w-full rounded-md  px-4 py-2 text-sm text-gray-700 dark:text-gray-50 hover:bg-gray-50 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-gray-500"
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
                     class="block block px-4 py-2 text-md text-gray-700 dark:text-white hover:bg-gray-100 hover:text-gray-900 dark:text-gray-100 dark:hover:text-white dark:hover:bg-gray-600 flex items-center"
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
                <div class="flex gap-2" v-else>
                  <router-link v-if="$route.name !== 'login'" :to="{ name: 'login' }"
                               class="text-gray-600 dark:text-white hover:text-gray-800 dark:hover:text-white px-0 sm:px-3 py-2 rounded-md text-sm"
                               active-class="text-gray-800 dark:text-white"
                  >
                    {{ $t('login') }}
                  </router-link>

                  <v-button size="small" :to="{ name: 'forms.create.guest' }" color="outline-blue" v-track.nav_create_form_click :arrow="true">
                    Create a form
                  </v-button>

                </div>
              </div>
            </div>
            <div>
              <a class="m-1.5" :href="githubUrl" target="_blank">
                <button
                  class="duration-200 text-gray-600 hover:text-blue-600 ease-in focus:outline-none focus:ring-2 focus:ring-gray-100 focus:ring-offset-2 focus:ring-offset-gray-50 font-semibold dark:bg-white p-2 rounded-full rounded-lg text-base text-center transition dark:text-white"
                  v-track.github_link_click
                >
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="w-6 h-6"
                       viewBox="0 0 256 250" version="1.1" preserveAspectRatio="xMidYMid" fill="currentColor">
                    <g>
                      <path fill="currentColor"
                        d="M128.00106,0 C57.3172926,0 0,57.3066942 0,128.00106 C0,184.555281 36.6761997,232.535542 87.534937,249.460899 C93.9320223,250.645779 96.280588,246.684165 96.280588,243.303333 C96.280588,240.251045 96.1618878,230.167899 96.106777,219.472176 C60.4967585,227.215235 52.9826207,204.369712 52.9826207,204.369712 C47.1599584,189.574598 38.770408,185.640538 38.770408,185.640538 C27.1568785,177.696113 39.6458206,177.859325 39.6458206,177.859325 C52.4993419,178.762293 59.267365,191.04987 59.267365,191.04987 C70.6837675,210.618423 89.2115753,204.961093 96.5158685,201.690482 C97.6647155,193.417512 100.981959,187.77078 104.642583,184.574357 C76.211799,181.33766 46.324819,170.362144 46.324819,121.315702 C46.324819,107.340889 51.3250588,95.9223682 59.5132437,86.9583937 C58.1842268,83.7344152 53.8029229,70.715562 60.7532354,53.0843636 C60.7532354,53.0843636 71.5019501,49.6441813 95.9626412,66.2049595 C106.172967,63.368876 117.123047,61.9465949 128.00106,61.8978432 C138.879073,61.9465949 149.837632,63.368876 160.067033,66.2049595 C184.49805,49.6441813 195.231926,53.0843636 195.231926,53.0843636 C202.199197,70.715562 197.815773,83.7344152 196.486756,86.9583937 C204.694018,95.9223682 209.660343,107.340889 209.660343,121.315702 C209.660343,170.478725 179.716133,181.303747 151.213281,184.472614 C155.80443,188.444828 159.895342,196.234518 159.895342,208.176593 C159.895342,225.303317 159.746968,239.087361 159.746968,243.303333 C159.746968,246.709601 162.05102,250.70089 168.53925,249.443941 C219.370432,232.499507 256,184.536204 256,128.00106 C256,57.3066942 198.691187,0 128.00106,0 Z M47.9405593,182.340212 C47.6586465,182.976105 46.6581745,183.166873 45.7467277,182.730227 C44.8183235,182.312656 44.2968914,181.445722 44.5978808,180.80771 C44.8734344,180.152739 45.876026,179.97045 46.8023103,180.409216 C47.7328342,180.826786 48.2627451,181.702199 47.9405593,182.340212 Z M54.2367892,187.958254 C53.6263318,188.524199 52.4329723,188.261363 51.6232682,187.366874 C50.7860088,186.474504 50.6291553,185.281144 51.2480912,184.70672 C51.8776254,184.140775 53.0349512,184.405731 53.8743302,185.298101 C54.7115892,186.201069 54.8748019,187.38595 54.2367892,187.958254 Z M58.5562413,195.146347 C57.7719732,195.691096 56.4895886,195.180261 55.6968417,194.042013 C54.9125733,192.903764 54.9125733,191.538713 55.713799,190.991845 C56.5086651,190.444977 57.7719732,190.936735 58.5753181,192.066505 C59.3574669,193.22383 59.3574669,194.58888 58.5562413,195.146347 Z M65.8613592,203.471174 C65.1597571,204.244846 63.6654083,204.03712 62.5716717,202.981538 C61.4524999,201.94927 61.1409122,200.484596 61.8446341,199.710926 C62.5547146,198.935137 64.0575422,199.15346 65.1597571,200.200564 C66.2704506,201.230712 66.6095936,202.705984 65.8613592,203.471174 Z M75.3025151,206.281542 C74.9930474,207.284134 73.553809,207.739857 72.1039724,207.313809 C70.6562556,206.875043 69.7087748,205.700761 70.0012857,204.687571 C70.302275,203.678621 71.7478721,203.20382 73.2083069,203.659543 C74.6539041,204.09619 75.6035048,205.261994 75.3025151,206.281542 Z M86.046947,207.473627 C86.0829806,208.529209 84.8535871,209.404622 83.3316829,209.4237 C81.8013,209.457614 80.563428,208.603398 80.5464708,207.564772 C80.5464708,206.498591 81.7483088,205.631657 83.2786917,205.606221 C84.8005962,205.576546 86.046947,206.424403 86.046947,207.473627 Z M96.6021471,207.069023 C96.7844366,208.099171 95.7267341,209.156872 94.215428,209.438785 C92.7295577,209.710099 91.3539086,209.074206 91.1652603,208.052538 C90.9808515,206.996955 92.0576306,205.939253 93.5413813,205.66582 C95.054807,205.402984 96.4092596,206.021919 96.6021471,207.069023 Z"
                        />
                    </g>
                  </svg>
                </button>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </nav>
</template>

<script>
import {mapGetters} from 'vuex'
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
  }),

  computed: {
    githubUrl: () => window.config.links.github_url,
    form() {
      if (this.$route.name && this.$route.name.startsWith('forms.show_public')) {
        return this.$store.getters['open/forms/getBySlug'](this.$route.params.slug)
      }
      return null
    },
    showAuth() {
      return this.$route.name && !this.$route.name.startsWith('forms.show_public')
    },
    hasNavbar() {
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
    isIframe() {
      return window.location !== window.parent.location || window.frameElement
    },
    ...mapGetters({
      user: 'auth/user'
    }),
    userOnboarded() {
      return this.user && this.user.workspaces_count > 0
    },
    hasCrisp() {
      return window.config.crisp_website_id
    }
  },

  methods: {
    async logout() {
      // Log out the user.
      await this.$store.dispatch('auth/logout')

      // Reset store
      this.$store.dispatch('open/workspaces/resetState')
      this.$store.dispatch('open/forms/resetState')

      // Redirect to login.
      this.$router.push({name: 'login'})
    },
  }
}
</script>
