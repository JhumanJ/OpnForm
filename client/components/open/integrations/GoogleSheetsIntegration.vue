<template>
  <IntegrationWrapper
    v-model="props.integrationData"
    :integration="props.integration"
    :form="form"
  >
    <div class="mb-4">
      <p class="text-gray-500 mb-4">
        Adds new entry to spreadsheets on each form submission.
      </p>
      <FlatSelectInput
        v-if="providers.length"
        v-model="integrationData.oauth_id"
        name="provider"
        :options="providers"
        :disable-options="disableProviders"
        disable-options-tooltip="Re-connect account to fix permissions"
        display-key="email"
        option-key="id"
        emit-key="id"
        :required="true"
        label="Select Google Account"
      >
        <template #help>
          <InputHelp>
            <span>
              <NuxtLink
                class="text-blue-500"
                :to="{ name: 'settings-connections' }"
              >
                Click here
              </NuxtLink>
              to connect another account.
            </span>
          </InputHelp>
        </template>
      </FlatSelectInput>

      <v-button
        v-else
        color="white"
        :loading="providersStore.loading"
        @click.prevent="connect"
      >
        Connect Google account
      </v-button>
    </div>
  </IntegrationWrapper>
</template>

<script setup>
import FlatSelectInput from '~/components/forms/FlatSelectInput.vue'
import IntegrationWrapper from './components/IntegrationWrapper.vue'

const props = defineProps({
  integration: { type: Object, required: true },
  form: { type: Object, required: true },
  integrationData: { type: Object, required: true },
  formIntegrationId: { type: Number, required: false, default: null }
})

const providersStore = useOAuthProvidersStore()
const providers = computed(() => providersStore.getAll.filter(provider => provider.provider == 'google'))
const disableProviders = computed(() => providersStore.getAll.filter(provider => !provider.scopes.includes(providersStore.googleDrivePermission)).map((provider) => provider.id))

function connect () {
  providersStore.connect('google', true)
}
</script>
