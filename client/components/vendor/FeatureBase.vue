<script setup>
import {onMounted} from "vue";

const scriptLoaded = ref(false);
const user = computed(() => useAuthStore().user);
const featureBaseOrganization = useRuntimeConfig().public.featureBaseOrganization;

const loadScript = () => {
  if (scriptLoaded.value || !user.value || !featureBaseOrganization) return;
  const script = document.createElement("script");
  script.src = "https://do.featurebase.app/js/sdk.js";
  script.id = "featurebase-sdk";
  document.head.appendChild(script);
  scriptLoaded.value = true;
};

const setupForUser = () => {
  if (process.server || !user.value || !featureBaseOrganization) return
  window.Featurebase(
    "identify",
    {
      organization: featureBaseOrganization,
      email: user.value.email,
      name: user.value.name,
      id: user.value.id.toString(),
      profilePicture: user.value.photo_url
    }
  );

  window.Featurebase("initialize_changelog_widget", {
    organization: featureBaseOrganization,
    placement: "right",
    theme: "light",
    alwaysShow: true,
    fullscreenPopup: true,
    usersName: user.value?.name
  })

  window.Featurebase("initialize_feedback_widget", {
    organization: featureBaseOrganization,
    theme: "light",
    placement: "right",
    email: user.value?.email,
    usersName: user.value?.name
  });
}

onMounted(() => {
  if (process.server) return

  // Setup base
  if (!window.hasOwnProperty('Featurebase') || typeof window.Featurebase !== "function") {
    window.Featurebase = function () {
      (window.Featurebase.q = window.Featurebase.q || []).push(arguments);
    };
  }

  if (!user.value) return
  loadScript()
  setupForUser()
})

watch(user, (val) => {
  if (process.server || !val) return

  loadScript()
  setupForUser()
});

</script>
<template></template>
