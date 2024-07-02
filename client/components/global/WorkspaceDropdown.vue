<template>
  <Dropdown
    v-if="user && workspaces && workspaces.length > 1"
    ref="dropdown"
    dropdown-class="origin-top-left absolute left-0 mt-2 w-56 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 z-50"
    dusk="workspace-dropdown"
  >
    <template
      v-if="workspace"
      #trigger="{ toggle }"
    >
      <div
        class="flex items-center cursor border border-transparent hover:border-gray-200 py-2 px-3 hover:bg-gray-50 rounded-md transition-colors"
        role="button"
        @click.stop="toggle()"
      >
        <WorkspaceIcon :workspace="workspace" />
        <p
          class="hidden md:block max-w-10 truncate text-sm ml-2 text-gray-800 dark:text-gray-200"
        >
          {{ workspace.name }}
        </p>
      </div>
    </template>

    <div class="px-1">
      <a
        v-for="worksp in workspaces"
        :key="worksp.id"
        href="#"
        class="px-4 py-2 text-md rounded text-gray-700 hover:no-underline hover:bg-neutral-50 hover:text-gray-900 dark:text-gray-100 dark:hover:text-white dark:hover:bg-gray-600 flex items-center"
        :class="{
          'bg-blue-100 dark:bg-blue-900 hover:bg-blue-200':
            workspace?.id === worksp?.id,
        }"
        @click.prevent="switchWorkspace(worksp)"
      >
        <WorkspaceIcon :workspace="worksp" />
        <p class="ml-4 truncate text-sm">{{ worksp.name }}</p>
      </a>
    </div>
  </Dropdown>
</template>

<script>
import { computed } from "vue"
import Dropdown from "~/components/global/Dropdown.vue"
import WorkspaceIcon from "~/components/workspaces/WorkspaceIcon.vue"

export default {
  name: "WorkspaceDropdown",
  components: {
    WorkspaceIcon,
    Dropdown,
  },

  setup() {
    const authStore = useAuthStore()
    const formsStore = useFormsStore()
    const workspacesStore = useWorkspacesStore()
    return {
      formsStore,
      workspacesStore,
      user: computed(() => authStore.user),
      workspaces: computed(() => workspacesStore.getAll),
      loading: computed(() => workspacesStore.loading),
    }
  },

  computed: {
    workspace() {
      return this.workspacesStore.getCurrent
    },
  },

  watch: {},

  mounted() {},

  methods: {
    switchWorkspace(workspace) {
      this.workspacesStore.setCurrentId(workspace.id)
      this.formsStore.resetState()
      this.formsStore.loadAll(workspace.id)
      const router = useRouter()
      const route = useRoute()
      if (route.name !== "home") {
        router.push({ name: "home" })
      }
      this.formsStore.loadAll(workspace.id)
    },
  },
}
</script>

<style></style>
