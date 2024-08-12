<template>
  <modal
    :show="show"
    max-width="lg"
    @close="emit('close')"
  >
    <template #icon>
      <svg
        class="w-8 h-8"
        viewBox="0 0 24 24"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
      >
        <path
          d="M12 8V16M8 12H16M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12Z"
          stroke="currentColor"
          stroke-width="2"
          stroke-linecap="round"
          stroke-linejoin="round"
        />
      </svg>
    </template>

    <template #title>
      Create an access token
    </template>

    <div class="px-4">
      <form
        @submit.prevent="createToken"
        @keydown="form.onKeydown($event)"
      >
        <div v-if="!token">
          <text-input
            name="name"
            class="mt-4"
            :form="form"
            :required="true"
            label="Name"
          />

          <div>
            <label class="input-label">Abilities</label>

            <div class="mt-2">
              <checkbox-input
                v-for="ability in abilities"
                :key="ability.name"
                v-model="form.abilities"
                :value="ability.name"
                :label="ability.title"
                disabled
              />
            </div>
          </div>
        </div>

        <UAlert
          v-if="token"
          icon="i-heroicons-key-20-solid"
          color="green"
          variant="subtle"
          title="Copy your access token"
          description="Your token will only be shown once. Make sure to save it safely."
        />

        <div class="flex">
          <copy-content
            v-if="token"
            :content="token"
          >
            <template #icon>
              <Icon
                name="heroicons:link"
                class="w-4 h-4 -mt-1 text-blue-600 mr-3"
              />
            </template>
            Copy Token
          </copy-content>
        </div>

        <div class="w-full mt-6">
          <v-button
            v-if="!token"
            :loading="form.busy"
            class="w-full my-3"
          >
            Save
          </v-button>

          <v-button
            v-else
            class="w-full my-3"
            @click.prevent="emit('close')"
          >
            Close
          </v-button>
        </div>
      </form>
    </div>
  </modal>
</template>

<script setup>
import CopyContent from "../open/forms/components/CopyContent.vue"

const props = defineProps({
  show: Boolean
})

const emit = defineEmits(['close'])

const accessTokenStore = useAccessTokenStore()
const abilities = computed(() => accessTokenStore.abilities)

const form = useForm({
  name: "",
  abilities: abilities.value.map(ability => ability.name),
})

const token = ref('')

function createToken() {
  form.post("/settings/tokens").then((data) => {
    // workspacesStore.save(data.workspace)
    // workspacesStore.currentId = data.workspace.id
    // workspaceModal.value = false

    token.value = data.token
    accessTokenStore.fetchTokens()

    useAlert().success(
      "Access token successfully created!",
    )
  })
}

watch(() => props.show, () => {
  form.name = ''
  form.abilities = abilities.value.map(ability => ability.name),
  token.value = ''
})
</script>
