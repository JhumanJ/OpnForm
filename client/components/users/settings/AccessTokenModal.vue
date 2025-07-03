<template>
  <UModal
    v-model:open="isOpen"
    @close="closeModal"
  >
    <template #header>
      <div class="flex items-center w-full gap-4 px-2">
        <h2 class="font-semibold">
          Create an access token
        </h2>
      </div>
      <UButton
        color="neutral"
        variant="outline"
        icon="i-heroicons-question-mark-circle"
        size="sm"
        @click="crisp.openHelpdesk()"
      >
        Help
      </UButton>
    </template>
 
    <template #body>
      <form
        @submit.prevent="createToken"
        @keydown="form.onKeydown($event)"
      >
        <div v-if="!token">
          <TextInput
            :form="tokenForm"
            name="name"
            :required="true"
            label="Name"
          />

          <FlatSelectInput
            :form="tokenForm"
            name="abilities"
            label="Abilities"
            :options="abilities"
            multiple
          />
        </div>

        <UAlert
          v-if="token"
          icon="i-heroicons-key-20-solid"
          color="success"
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
          <UButton
            v-if="!token"
            type="submit"
            block
            size="lg"
            :loading="tokenForm.busy"
          >
            Create Token
          </UButton>

          <UButton
            v-else
            block
            size="lg"
            @click.prevent="closeModal"
          >
            Close
          </UButton>
        </div>
      </form>
    </template>
  </UModal>
</template>

<script setup>
import CopyContent from "~/components/open/forms/components/CopyContent.vue"

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close'])

const accessTokenStore = useAccessTokenStore()
const abilities = computed(() => accessTokenStore.abilities.map(ability => ({
  name: ability.title,
  value: ability.name
})))
const token = ref('')
const tokenForm = useForm({
  name: "",
  abilities: abilities.value.map(ability => ability.value),
})

// Modal state
const isOpen = computed({
  get: () => props.modelValue,
  set: (value) => emit('close', value)
})

// Methods
const closeModal = () => {
  tokenForm.reset()
  token.value = ''
  isOpen.value = false
}

function createToken() {
  tokenForm.post("/settings/tokens").then((data) => {
    token.value = data.token
    accessTokenStore.fetchTokens()

    useAlert().success(data.message)
  })
}
</script>
