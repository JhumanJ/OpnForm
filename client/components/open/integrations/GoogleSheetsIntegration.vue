<template>
  <IntegrationWrapper
    v-model="props.integrationData"
    :integration="props.integration"
    :form="form"
  >
    <div class="my-5">
      <select-input
        v-if="providers.length"
        v-model="integrationData.oauth_id"
        name="provider"
        :options="providers"
        display-key="email"
        option-key="id"
        emit-key="id"
        :required="true"
        label="Select Google Account"
      >
        <template #help>
          <InputHelp>
            <span>
              Add an entry to spreadsheets on each form submission.
              <NuxtLink
                :to="{ name: 'settings-connections' }"
              >
                Click here
              </NuxtLink>
              to connect another account.
            </span>
          </InputHelp>
        </template>
      </select-input>

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
import IntegrationWrapper from './components/IntegrationWrapper.vue'

const props = defineProps({
  integration: { type: Object, required: true },
  form: { type: Object, required: true },
  integrationData: { type: Object, required: true },
  formIntegrationId: { type: Number, required: false, default: null }
})

const providersStore = useOAuthProvidersStore()
const providers = computed(() => providersStore.getAll.filter(provider => provider.provider == 'google'))

function connect () {
  providersStore.connect('google', true)
}
</script>
