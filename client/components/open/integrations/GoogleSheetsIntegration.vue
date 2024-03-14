<template>
  <IntegrationWrapper :service="service" :form="form" v-model="integration">
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
import IntegrationWrapper from './IntegrationWrapper.vue'

const props = defineProps({
  service: { type: Object, required: true },
  form: { type: Object, required: true },
  integrationData: { type: Object, required: true },
});

const alert = useAlert()
const router = useRouter()
const formIntegrationsStore = useFormIntegrationsStore()
const formIntegrationId = computed(() => parseInt(useRoute().params.id) ?? null)
const integration = ref(props.integrationData)

const save = () => {
  console.log("Save clicked!")
  alert.warning("Coming Soon...")
}
</script>