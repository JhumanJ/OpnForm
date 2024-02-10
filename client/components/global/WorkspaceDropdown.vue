<template>
  <dropdown v-if="user && workspaces && workspaces.length > 1" ref="dropdown"
            dropdown-class="origin-top-left absolute left-0 mt-2 w-56 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 z-50"
            dusk="workspace-dropdown"
  >
    <template v-if="workspace" #trigger="{toggle}">
      <div class="flex items-center cursor group" role="button" @click.stop="toggle()">
        <div class="rounded-full h-8 8">
          <img v-if="isUrl(workspace.icon)"
               :src="workspace.icon"
               :alt="workspace.name + ' icon'" class="flex-shrink-0 h-8 w-8 rounded-full shadow"
          />
          <div v-else class="rounded-full pt-2 text-xs truncate bg-nt-blue-lighter h-8 w-8 text-center shadow"
               v-text="workspace.icon"
          />
        </div>
        <p class="hidden group-hover:underline lg:block max-w-10 truncate ml-2 text-gray-800 dark:text-gray-200">
          {{ workspace.name }}
        </p>
      </div>
    </template>

    <template v-for="worksp in workspaces" :key="worksp.id">
      <a href="#"
         class="px-4 py-2 text-md text-gray-700 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-100 dark:hover:text-white dark:hover:bg-gray-600 flex items-center"
         :class="{'bg-blue-100 dark:bg-blue-900':workspace?.id === worksp?.id}" @click.prevent="switchWorkspace(worksp)"
      >
        <div class="rounded-full h-8 w-8 flex-shrink-0" role="button">
          <img v-if="isUrl(worksp.icon)"
               :src="worksp.icon"
               :alt="worksp.name + ' icon'" class="flex-shrink-0 h-8 w-8 rounded-full shadow"
          />
          <div v-else class="rounded-full flex-shrink-0 pt-1 text-xs truncate bg-nt-blue-lighter h-8 w-8 text-center shadow"
               v-text="worksp.icon"
          />
        </div>
        <p class="ml-4 truncate">{{ worksp.name }}</p>
      </a>
    </template>
  </dropdown>
</template>

<script>
import { computed } from 'vue'
import Dropdown from '~/components/global/Dropdown.vue'

export default {

  name: 'WorkspaceDropdown',
  components: {
    Dropdown
  },

  setup () {
    const authStore = useAuthStore()
    const formsStore = useFormsStore()
    const workspacesStore = useWorkspacesStore()
    return {
      formsStore,
      workspacesStore,
      user: computed(() => authStore.user),
      workspaces: computed(() => workspacesStore.getAll),
      loading: computed(() => workspacesStore.loading)
    }
  },

  computed: {
    workspace () {
      return this.workspacesStore.getCurrent
    }
  },

  watch: {
  },

  mounted () {
  },

  methods: {
    switchWorkspace (workspace) {
      this.workspacesStore.setCurrentId(workspace.id)
      this.formsStore.resetState()
      this.formsStore.loadAll(workspace.id)
      const router = useRouter()
      const route = useRoute()
      if (route.name !== 'home') {
        router.push({ name: 'home' })
      }
      this.formsStore.loadAll(workspace.id)
    },
    isUrl (str) {
      try {
        new URL(str)
      } catch (_) {
        return false
      }
      return true
    }
  }
}
</script>

<style>
</style>
