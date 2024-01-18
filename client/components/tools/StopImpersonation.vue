<template>
  <transition name="fade">
    <button v-if="isImpersonating"
            class="cursor-pointer group hover:bg-blue-50 text-gray-600 py-2 px-5 fixed bottom-0 left-0 rounded-tr-md bg-white border-t border-r"
            @click="reverseImpersonation"
    >
      <template v-if="!loading">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 -mt-1 group-hover:text-blue-500 inline text-gray-400"
             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
        >
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
          />
        </svg>
        Stop Impersonation
      </template>
      <template v-else>
        <div class="px-6">
          <Loader class="h-4 w-4 inline"/>
        </div>
      </template>
    </button>
  </transition>
</template>

<script>
import {computed} from 'vue'
import {useAuthStore} from '../../stores/auth.js';
import {useWorkspacesStore} from '../../stores/workspaces.js';

export default {
  setup() {
    const authStore = useAuthStore()
    const workspacesStore = useWorkspacesStore()
    return {
      authStore,
      workspacesStore,
      isImpersonating: computed(() => authStore.isImpersonating),
    }
  },

  data: () => ({
    loading: false
  }),

  computed: {},

  mounted() {
  },

  methods: {
    async reverseImpersonation() {
      this.loading = true
      this.authStore.stopImpersonating()

      // Fetch the user.
      const userData = await opnFetch('user')
      this.authStore.setUser(userData)
      const workspaces = await fetchAllWorkspaces()
      this.workspacesStore.set(workspaces.data.value)
      this.$router.push({name: 'settings-admin'})
      this.loading = false
    }
  }
}
</script>
