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
          <loader class="h-4 w-4 inline" />
        </div>
      </template>
    </button>
  </transition>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  data: () => ({
    loading: false
  }),

  computed: {
    ...mapGetters({
      isImpersonating: 'auth/isImpersonating'
    })
  },

  mounted () {
  },

  methods: {
    reverseImpersonation () {
      this.loading = true
      this.$store.dispatch('auth/stopImpersonating')
        .then(() => {
          this.$store.commit('open/workspaces/set', [])
          this.$router.push({ name: 'settings.admin' })
          this.loading = false
        })
    }
  }
}
</script>
