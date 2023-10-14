<template>
  <div>
    <h3 class="font-semibold text-2xl text-gray-900">Workspace settings</h3>
    <small class="text-gray-600">Manage your workspaces.</small>

    <div v-if="loading" class="w-full text-blue-500 text-center">
      <loader class="h-10 w-10 p-5"/>
    </div>
    <div v-else>
      <div v-for="workspace in workspaces" :key="workspace.id"
          class="mt-4 p-4 flex group bg-white hover:bg-gray-50 dark:bg-notion-dark items-center"
      >
        <div class="flex space-x-4 flex-grow items-center cursor-pointer" role="button" @click.prevent="switchWorkspace(workspace)">
          <img v-if="isUrl(workspace.icon)" :src="workspace.icon" :alt="workspace.name + ' icon'"
              class="rounded-full h-12 w-12"
          >
          <div v-else class="rounded-2xl bg-gray-100 h-12 w-12 text-2xl pt-2 text-center overflow-hidden"
              v-text="workspace.icon"
          />
          <div class="space-y-4 py-1">
            <div class="font-bold truncate">{{workspace.name}}</div>
          </div>
        </div>
        <div v-if="workspaces.length > 1"
            class="block md:hidden group-hover:block text-red-500 p-2 rounded hover:bg-red-50" role="button"
            @click="deleteWorkspace(workspace)">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
          </svg>
        </div>
      </div>
      <v-button :loading="loading" class="mt-4" @click="workspaceModal=true">
        <svg class="inline text-white mr-1 h-4 w-4" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M6.99996 1.16699V12.8337M1.16663 7.00033H12.8333" stroke="currentColor" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Create new workspace
      </v-button>
    </div>

    <!--  Workspace modal  -->
    <modal :show="workspaceModal" @close="workspaceModal=false" max-width="lg">
      <template #icon>
        <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path
            d="M12 8V16M8 12H16M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12Z"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </template>
      <template #title>
        Create Workspace
      </template>
      <div class="px-4">
        <form @submit.prevent="createWorkspace" @keydown="form.onKeydown($event)">
          <div>
            <text-input name="name" class="mt-4" :form="form" :required="true"
                        label="Workspace Name"
            />
            <text-input name="emoji" class="mt-4" :form="form" :required="false"
                        label="Emoji"
            />
          </div>

          <div class="w-full mt-6">
            <v-button :loading="form.busy" class="w-full my-3">Save</v-button>
          </div>

        </form>
      </div>
    </modal>

  </div>
</template>

<script>
import Form from 'vform'
import {mapActions, mapState} from 'vuex'
import SeoMeta from '../../mixins/seo-meta.js'

export default {
  components: {},
  scrollToTop: false,
  mixins: [SeoMeta],

  data: () => ({
    metaTitle: 'Workspaces',
    form: new Form({
      name: '',
      emoji: ''
    }),
    workspaceModal: false
  }),

  mounted() {
    this.loadWorkspaces()
  },

  computed: {
    ...mapState({
      workspaces: state => state['open/workspaces'].content,
      loading: state => state['open/workspaces'].loading
    })
  },

  methods: {
    ...mapActions({
      loadWorkspaces: 'open/workspaces/loadIfEmpty'
    }),
    switchWorkspace(workspace) {
      this.$store.commit('open/workspaces/setCurrentId', workspace.id)
      this.$router.push({name: 'home'})
      this.$store.dispatch('open/forms/load', workspace.id)
    },
    deleteWorkspace(workspace) {
      this.alertConfirm('Do you really want to delete this workspace? All forms created in this workspace will be removed.', () => {
        this.$store.dispatch('open/workspaces/delete', workspace.id).then(() => {
          this.alertSuccess('Workspace successfully removed.')
        })
      })
    },
    isUrl(str) {
      const pattern = new RegExp('^(https?:\\/\\/)?' + // protocol
        '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|' + // domain name
        '((\\d{1,3}\\.){3}\\d{1,3}))' + // OR ip (v4) address
        '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*' + // port and path
        '(\\?[;&a-z\\d%_.~+=-]*)?' + // query string
        '(\\#[-a-z\\d_]*)?$', 'i') // fragment locator
      return !!pattern.test(str)
    },
    async createWorkspace() {
      const {data} = await this.form.post('/api/open/workspaces/create')
      this.$store.dispatch('open/workspaces/load')
      this.workspaceModal = false
    }
  }
}
</script>
