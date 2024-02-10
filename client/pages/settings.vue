<template>
    <div class="bg-white">
      <div class="flex bg-gray-50">
        <div class="w-full md:w-4/5 lg:w-3/5 md:mx-auto md:max-w-4xl px-4">
          <div class="pt-4 pb-0">
            <div class="flex">
              <h2 class="flex-grow text-gray-900">
                My Account
              </h2>
            </div>
            <ul class="flex text-gray-500">
              <li>{{ user.email }}</li>
            </ul>

            <div class="mt-4 border-b border-gray-200 dark:border-gray-700">
              <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
                <li v-for="(tab, i) in tabsList" :key="i+1" class="mr-6">
                  <nuxt-link :to="{ name: tab.route }"
                               class="hover:no-underline inline-block py-4 rounded-t-lg border-b-2 text-gray-500 hover:text-gray-600"
                               active-class="text-blue-600 hover:text-blue-900 dark:text-blue-500 dark:hover:text-blue-500 border-blue-600 dark:border-blue-500"
                  >
                    {{ tab.name }}
                  </nuxt-link>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <div class="flex bg-white">
        <div class="w-full md:w-4/5 lg:w-3/5 md:mx-auto md:max-w-4xl px-4">
          <div class="mt-8 pb-0">
            <NuxtPage />
          </div>
        </div>
      </div>
    </div>
  </template>

  <script setup>
  import { computed } from 'vue'
  import { useAuthStore } from '../stores/auth'

  definePageMeta({
    middleware: "auth"
  })

  useOpnSeoMeta({
    title: 'Settings'
  })

  const authStore = useAuthStore()
  const user = computed(() => authStore.user)
  const tabsList = computed(() => {
    const tabs = [
      {
        name: 'Profile',
        route: 'settings-profile'
      },
      {
        name: 'Workspace Settings',
        route: 'settings-workspace'
      },
      {
        name: 'Password',
        route: 'settings-password'
      },
      {
        name: 'Delete Account',
        route: 'settings-account'
      }
    ]

    if (user?.value?.is_subscribed) {
      tabs.splice(1, 0, {
        name: 'Billing',
        route: 'settings-billing'
      })
    }

    if (user?.value?.admin) {
      tabs.push({
        name: 'Admin',
        route: 'settings-admin'
      })
    }

    return tabs
  })
  </script>
