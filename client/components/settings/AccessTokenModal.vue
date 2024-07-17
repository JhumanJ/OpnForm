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
        <div>
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
              />
            </div>
          </div>
        </div>

        <div
          v-if="token"
          class="mt-2 bg-green-200 text-green-600 text-sm px-4 py-2"
        >
          <div>Copy your access token. This message will only be shown once.</div>
        </div>

        <div class="flex">
          <copy-content
            v-if="token"
            :content="token"
          >
            <template #icon>
              <svg
                class="h-4 w-4 -mt-1 text-blue-600 inline mr-1"
                viewBox="0 0 20 10"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M7.49984 9.16634H5.83317C3.53198 9.16634 1.6665 7.30086 1.6665 4.99967C1.6665 2.69849 3.53198 0.833008 5.83317 0.833008H7.49984M12.4998 9.16634H14.1665C16.4677 9.16634 18.3332 7.30086 18.3332 4.99967C18.3332 2.69849 16.4677 0.833008 14.1665 0.833008H12.4998M5.83317 4.99967L14.1665 4.99968"
                  stroke="currentColor"
                  stroke-width="1.66667"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
              </svg>
            </template>
            Copy Token
          </copy-content>
        </div>

        <div class="w-full mt-6">
          <v-button
            :loading="form.busy"
            class="w-full my-3"
          >
            Save
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

const form = useForm({
  name: "",
  abilities: [],
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

const accessTokenStore = useAccessTokenStore()
const abilities = computed(() => accessTokenStore.abilities)

watch(() => props.show, () => {
  form.name = ''
  form.abilities = []
  token.value = ''
})
</script>
