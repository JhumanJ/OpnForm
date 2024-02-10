<template>
  <div>
    <div class="flex flex-wrap items-center gap-y-4 flex-wrap-reverse">
      <div class="flex-grow">
        <h3 class="font-semibold text-2xl text-gray-900">
          Workspace settings
        </h3>
        <small class="text-gray-600">Manage your workspaces.</small>
      </div>
      <v-button color="outline-blue" :loading="loading" @click="workspaceModal=true">
        <svg class="inline -mt-1 mr-1 h-4 w-4" viewBox="0 0 14 14" fill="none"
             xmlns="http://www.w3.org/2000/svg"
        >
          <path d="M6.99996 1.16699V12.8337M1.16663 7.00033H12.8333" stroke="currentColor" stroke-width="1.67"
                stroke-linecap="round" stroke-linejoin="round"
          />
        </svg>
        Create new workspace
      </v-button>
    </div>

    <div v-if="loading" class="w-full text-blue-500 text-center">
      <Loader class="h-10 w-10 p-5" />
    </div>
    <div v-else-if="workspace">
      <div class="mt-4 flex group bg-white items-center">
        <div class="flex space-x-4 flex-grow items-center">
          <img v-if="isUrl(workspace.icon)" :src="workspace.icon" :alt="workspace.name + ' icon'"
               class="rounded-full h-12 w-12"
          />
          <div v-else class="rounded-2xl bg-gray-100 h-12 w-12 text-2xl pt-2 text-center overflow-hidden"
               v-text="workspace.icon"
          />
          <div class="space-y-4 py-1">
            <div class="font-bold truncate">
              {{ workspace.name }}
            </div>
          </div>
        </div>
      </div>

      <template v-if="customDomainsEnabled">
        <text-area-input :form="customDomainsForm" name="custom_domains" class="mt-4" :required="false"
                         :disabled="!workspace.is_pro"
                         label="Workspace Custom Domains" wrapper-class="" placeholder="yourdomain.com - 1 per line"
        />
        <p class="text-gray-500 text-sm">
          Read our <a href="#"
                      @click.prevent="crisp.openHelpdeskArticle('how-to-use-my-own-domain-9m77g7')"
        >custom domain instructions</a> to learn how to use your own domain.
        </p>
      </template>

      <div class="flex flex-wrap justify-between gap-2 mt-4">
        <v-button v-if="customDomainsEnabled" class="w-full sm:w-auto" :loading="customDomainsLoading"
                  @click="saveChanges">
          <svg class="w-4 h-4 text-white inline mr-1 -mt-1" viewBox="0 0 24 24" fill="none"
               xmlns="http://www.w3.org/2000/svg"
          >
            <path
              d="M17 21V13H7V21M7 3V8H15M19 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H16L21 8V19C21 19.5304 20.7893 20.0391 20.4142 20.4142C20.0391 20.7893 19.5304 21 19 21Z"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            />
          </svg>
          Save Domains
        </v-button>
        <v-button v-if="workspaces.length > 1" color="white" class="group w-full sm:w-auto" :loading="loading"
                  @click="deleteWorkspace(workspace.id)"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 -mt-1 inline group-hover:text-red-700" fill="none"
               viewBox="0 0 24 24" stroke="currentColor"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
            />
          </svg>
          Remove workspace
        </v-button>
      </div>
    </div>

    <!--  Workspace modal  -->
    <modal :show="workspaceModal" max-width="lg" @close="workspaceModal=false">
      <template #icon>
        <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path
            d="M12 8V16M8 12H16M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12Z"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
          />
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
            <v-button :loading="form.busy" class="w-full my-3">
              Save
            </v-button>
          </div>
        </form>
      </div>
    </modal>
  </div>
</template>

<script setup>
import {watch} from "vue";
import {fetchAllWorkspaces} from "~/stores/workspaces.js";

const crisp = useCrisp()
const workspacesStore = useWorkspacesStore()
const workspaces = computed(() => workspacesStore.getAll)
let loading = computed(() => workspacesStore.loading)

useOpnSeoMeta({
  title: 'Workspaces'
})
definePageMeta({
  middleware: "auth"
})

let form = useForm({
  name: '',
  emoji: ''
})
let workspaceModal = ref(false)
let customDomainsForm = useForm({
  custom_domain: ''
})
let customDomainsLoading = ref(false)

let workspace = computed(() => workspacesStore.getCurrent)
let customDomainsEnabled = computed(() => useRuntimeConfig().public.customDomainsEnabled)

watch(() => workspace, () => {
  initCustomDomains()
})

onMounted(() => {
  fetchAllWorkspaces()
  initCustomDomains()
})

const saveChanges = () => {
  if (customDomainsLoading.value) return
  customDomainsLoading.value = true

  // Update the workspace custom domain
  customDomainsForm.put('/open/workspaces/' + workspace.value.id + '/custom-domains', {
    data: {
      custom_domains: customDomainsForm?.custom_domains?.split('\n')
        .map(domain => domain ? domain.trim() : null)
        .filter(domain => domain && domain.length > 0)
    }
  }).then((data) => {
    workspacesStore.save(data)
    useAlert().success('Custom domains saved.')
  }).catch((error) => {
    useAlert().error('Failed to update custom domains: ' + error.response.data.message)
  }).finally(() => {
    customDomainsLoading.value = false
  })
}

const initCustomDomains = () => {
  if (!workspace || !workspace.value.custom_domains) return
  customDomainsForm.custom_domains = workspace.value?.custom_domains.join('\n')
}

const deleteWorkspace = (workspaceId) => {
  if (workspaces.length <= 1) {
    useAlert().error('You cannot delete your only workspace.')
    return
  }
  useAlert().confirm('Do you really want to delete this workspace? All forms created in this workspace will be removed.', () => {
    opnFetch('/open/workspaces/' + workspaceId, {method: 'DELETE'}).then((data) => {
      useAlert().success('Workspace successfully removed.')
      workspacesStore.remove(workspaceId)
    })
  })
}

const isUrl = (str) => {
  const pattern = new RegExp('^(https?:\\/\\/)?' + // protocol
    '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|' + // domain name
    '((\\d{1,3}\\.){3}\\d{1,3}))' + // OR ip (v4) address
    '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*' + // port and path
    '(\\?[;&a-z\\d%_.~+=-]*)?' + // query string
    '(\\#[-a-z\\d_]*)?$', 'i') // fragment locator
  return !!pattern.test(str)
}
const createWorkspace = () => {
  form.post('/open/workspaces/create').then((data) => {
    workspacesStore.save(data.workspace)
    workspacesStore.currentId = data.workspace.id
    workspaceModal.value = false
    useAlert().success('Workspace successfully created! You are now editing settings for your new workspace.')
  })
}

</script>
