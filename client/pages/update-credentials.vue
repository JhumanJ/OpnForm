<template>
  <modal :show="showModal" @close="logout" max-width="lg">
    <div class="">
      <h2 class="font-medium text-3xl mb-3">Welcome to OpnForm!</h2>
      <p class="text-sm text-gray-600">
        You're using the self-hosted version of OpnForm and need to set up your account.
        Please enter your email and create a password to continue.
      </p>
    </div>

    <form
      class="mt-4"
      @submit.prevent="updateCredentials"
      @keydown="form.onKeydown($event)"
    >
      <!-- Email -->
      <text-input
        name="email"
        :form="form"
        label="Email"
        :required="true"
        placeholder="Your email address"
      />

      <!-- Password -->
      <text-input
        native-type="password"
        placeholder="Your password"
        name="password"
        :form="form"
        label="Password"
        :required="true"
      />

      <!-- Password Confirmation-->
      <text-input
        native-type="password"
        :form="form"
        :required="true"
        placeholder="Enter confirm password"
        name="password_confirmation"
        label="Confirm Password"
      />

      <!-- Submit Button -->
      <v-button class="mx-auto" :loading="form.busy || loading">
        Update Credentials
      </v-button>
    </form>
  </modal>
</template>

<script setup>
import { onMounted } from "vue";

const authStore = useAuthStore();
const workspacesStore = useWorkspacesStore();
const formsStore = useFormsStore();
const user = computed(() => authStore.user);
const router = useRouter();
const showModal = ref(true);
const form = useForm({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
  agree_terms: false,
  appsumo_license: null,
});

onMounted(() => {
  form.email = user?.value?.email;
});

const updateCredentials = () => {
  form
    .post("update-credentials")
    .then(async (data) => {
      authStore.setUser(data.user);
      const workspaces = await fetchAllWorkspaces();
      workspacesStore.set(workspaces.data.value);
      formsStore.loadAll(workspacesStore.currentId);
      router.push({ name: "home" });
    })
    .catch((error) => {
      console.error(error);
      useAlert().error(error.response._data.message);
    });
};

const logout = () => {
  authStore.logout();
  showModal.value = false;
  router.push({ name: "login" });
};
</script>