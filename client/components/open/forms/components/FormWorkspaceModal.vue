<template>
    <modal :show="show" @close="emit('close')">
        <template #icon>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z" />
            </svg>
        </template>
        <template #title>        
            Change form workspace
        </template>
        <div class="p-4">
            <div class="flex space-x-4 items-center">
                <p>Current workspace:</p>
                <div class="flex items-center cursor group p-2 rounded border">
                    <div class="rounded-full h-8 8">
                        <img v-if="isUrl(workspace.icon)"
                            :src="workspace.icon"
                            :alt="workspace.name + ' icon'" class="flex-shrink-0 h-8 w-8 rounded-full shadow"
                        />
                        <div v-else class="rounded-full pt-2 text-xs truncate bg-nt-blue-lighter h-8 w-8 text-center shadow"
                            v-text="workspace.icon"
                        />
                    </div>
                    <p class="lg:block max-w-10 truncate ml-2 text-gray-800 dark:text-gray-200">
                        {{ workspace.name }}
                    </p>
                </div>
            </div>
            <form @submit.prevent="onSubmit">
                <div class=" my-4">
                    <select-input name="workspace" class=""
                            :options="workspacesSelectOptions"
                            v-model="selectedWorkspace"
                            :required="true"
                            label="Select workspace"
                    />
                </div>
                <div class="flex justify-end mt-4 pb-5">
                    <v-button class="mr-2" :loading="loading">
                        Change workspace
                    </v-button>
                    <v-button color="white" @click.prevent="emit('close')">
                        Close
                    </v-button>
                </div>
            </form>
        </div>
    </modal>
</template>

<script setup>
import { ref, defineProps, defineEmits, computed } from 'vue'
const emit = defineEmits(['close'])
const workspacesStore = useWorkspacesStore()
const formsStore = useFormsStore()

const  selectedWorkspace = ref(null);
const props = defineProps({
    show: { type: Boolean, required: true },
    form: { type: Object, required: true },
})
const workspaces = computed(() => workspacesStore.getAll)
const workspace = computed(() => workspacesStore.getByKey(props.form?.workspace_id))
const loading = ref(false)
const workspacesSelectOptions = computed(()=> workspaces.value.filter((w)=>{
    return w.id !== workspace.value.id
}).map(workspace => ({ name: workspace.name, value: workspace.id })))


const onSubmit = () => {
    const endpoint = '/open/forms/' + props.form.id + '/workspace/' + selectedWorkspace.value
    if(! selectedWorkspace.value) {
        useAlert().error('Please select a workspace!') 
        return;
    }
    opnFetch(endpoint, { method: 'POST' }).then(data => {
        loading.value = false;
        emit('close')
        useAlert().success('Form workspace updated successfully.')
        workspacesStore.setCurrentId(selectedWorkspace.value)
        formsStore.resetState()
        formsStore.loadAll(selectedWorkspace.value)
        const router = useRouter()
        const route = useRoute()
        if (route.name !== 'home') {
            router.push({ name: 'home' })
        }
        formsStore.loadAll(selectedWorkspace.value)
    }).catch((error) => {
        useAlert().error(error?.data?.message ??   'Something went wrong, please try again!') 
        loading.value = false;
    })
}

const isUrl = (str) => {
    try {
        new URL(str)
    } catch (_) {
        return false
    }
    return true
}
</script>