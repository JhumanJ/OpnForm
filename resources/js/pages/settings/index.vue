<template>
  <div class="bg-white">
    <div class="flex bg-gray-50">
      <div class="w-full md:w-4/5 lg:w-3/5 md:mx-auto md:max-w-4xl px-4">
        <div class="pt-4 pb-0">
          <div class="flex">
            <h2 class="flex-grow text-gray-900">
              Tài khoản của tôi
            </h2>
          </div>
          <ul class="flex text-gray-500">
            <li>{{ user.email }}</li>
          </ul>

          <div class="mt-4 border-b border-gray-200 dark:border-gray-700">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
              <li class="mr-6" v-for="(tab, i) in tabsList" :key="i+1">
                <router-link :to="{ name: tab.route }"
                  class="hover:no-underline inline-block py-4 rounded-t-lg border-b-2 text-gray-500 hover:text-gray-600"
                  active-class="text-blue-600 hover:text-blue-900 dark:text-blue-500 dark:hover:text-blue-500 border-blue-600 dark:border-blue-500">{{tab.name}}</router-link>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="flex bg-white">
      <div class="w-full md:w-4/5 lg:w-3/5 md:mx-auto md:max-w-4xl px-4">
        <div class="mt-8 pb-0">
          <transition name="fade" mode="out-in">
            <router-view />
          </transition>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  middleware: 'auth',

  data () {
    return {
    }
  },

  computed: {
    ...mapGetters({
      user: 'auth/user'
    }),
    tabsList () {
      const tabs = [
        {
          name: 'Hồ sơ',
          route: 'settings.profile'
        },
        {
          name: 'Cài đặt không gian làm việc',
          route: 'settings.workspaces'
        },
        {
          name: 'Mật khẩu',
          route: 'settings.password'
        },
        {
          name: 'Xóa tài khoản',
          route: 'settings.account'
        }
      ]

      if (this.user.is_subscribed) {
        tabs.splice(1, 0, {
          name: 'Thanh toán',
          route: 'settings.billing'
        })
      }

      if(this.user.admin){
        tabs.push({
          name: 'Quản trị viên',
          route: 'settings.admin'
        })
      }

      return tabs
    }
  },

  methods: {
  }
}
</script>
