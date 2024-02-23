<template>
  <IntegrationWrapper :form="form" v-model="integration">
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
  servicekey: { type: String, required: true },
  form: { type: Object, required: false }
})

const router = useRouter()
const authStore = useAuthStore()
let user = computed(() => authStore.user)
const integration = ref({
  settings: {},
  logic: null
})
const emit = defineEmits(['close'])

const save = () => {
  console.log("Save clicked!")
}
</script>
  