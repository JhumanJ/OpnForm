<template>
  <div class="inline" v-if="shouldDisplayProTag">
    <UTooltip text="Upgrade to use this feature">
      <div role="button" class="bg-nt-blue text-white px-2 text-xs uppercase inline rounded-full font-semibold cursor-pointer"
           @click="showPremiumModal=true">
        PRO
      </div>
      <modal :show="showPremiumModal" @close="showPremiumModal=false">
        <h2 class="text-nt-blue">
          OpnForm PRO
        </h2>
        <h4 v-if="user && user.is_subscribed" class="text-center mt-5">
          We're happy to have you as a Pro customer. If you're having any issue with OpnForm, or if you have a
          feature request, please <a href="mailto:contact@opnform.com">contact us</a>.
        </h4>
        <div v-if="!user || !user.is_subscribed" class="mt-4">
          <p>
            All the features with a<span
            class="bg-nt-blue text-white px-2 text-xs uppercase inline rounded-full font-semibold mx-1"
          >
          PRO
        </span> tag are available in the Pro plan of OpnForm. <b>You can play around and try all Pro features
            within
            the form editor, but you can't use them in your real forms</b>. You can subscribe now to gain unlimited
            access
            to
            all our pro features!
          </p>
        </div>

        <div class="my-4 text-center">
          <v-button color="white" @click="showPremiumModal=false">
            Close
          </v-button>
        </div>
      </modal>
    </UTooltip>
  </div>
</template>

<script setup>
import {computed} from 'vue'

const authStore = useAuthStore()
const workspacesStore = useWorkspacesStore()
const user = computed(() => authStore.user)
const workspace = computed(() => workspacesStore.getCurrent)
const showPremiumModal = ref(false)

const shouldDisplayProTag = computed(() => {
  if (!useRuntimeConfig().public.paidPlansEnabled) return false
  if (!user.value || !workspace.value) return true
  return !(workspace.value.is_pro)
})
</script>
