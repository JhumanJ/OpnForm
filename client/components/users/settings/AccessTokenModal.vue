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
      <template v-if="token">
        <UAlert
          icon="i-heroicons-key-20-solid"
          color="success"
          variant="subtle"
          title="Copy your access token"
          description="Your token will only be shown once. Make sure to save it safely."
        />
        <CopyContent
          class="mt-4"
          :content="token"
          label="Copy Token"
        />
      </template>

      <VForm v-else size="sm">
        <form
          @submit.prevent="createToken"
          @keydown="tokenForm.onKeydown($event)"
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
              :options="abilitiesOptions"
              multiple
            />
          </div>
        </form>
      </VForm>
    </template>

    <template #footer>
      <UButton
        color="neutral"
        variant="outline"
        @click="closeModal"
      >
        Close
      </UButton>
      <UButton
        v-if="!token"
        type="submit"
        block
        size="lg"
        :loading="createTokenMutation.isPending.value"
        @click="createToken"
      >
        Create Token
      </UButton>
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

const { abilities, create } = useTokens()
const alert = useAlert()

const abilitiesOptions = computed(() => abilities.map(ability => ({
  name: ability.title,
  value: ability.name
})))

const token = ref('')
const tokenForm = useForm({
  name: "",
  abilities: abilitiesOptions.value.map(ability => ability.value),
})

// Create token mutation
const createTokenMutation = create()

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
  createTokenMutation.mutateAsync(tokenForm.data()).then((response) => {
    // Assuming the response contains the token
    token.value = response.token || response.data?.token || response
  }).catch(() => {
    alert.error("An error occurred while creating the token")
  })
}
</script>
