<template>
  <IntegrationWrapper :integration="props.integration" :form="form" v-model="integration">
    <div class="my-5">
      Coming Soon...
    </div>
    <template #submit>
      <v-button @click.prevent="save">
        Save
      </v-button>
    </template>
  </IntegrationWrapper>
</template>

<script setup>
import IntegrationWrapper from './components/IntegrationWrapper.vue'

const props = defineProps({
  integration: { type: Object, required: true },
  form: { type: Object, required: true },
  integrationData: { type: Object, required: true },
  formIntegrationId: { type: Number, required: false, default: null }
});

const alert = useAlert()
const router = useRouter()
const formIntegrationsStore = useFormIntegrationsStore()
const integration = ref(props.integrationData)

const save = () => {
  console.log("Save clicked!")
  alert.warning("Coming Soon...")
}
</script>
