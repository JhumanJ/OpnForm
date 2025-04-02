<template>
  <VTransition>
    <section
      v-if="hasCleanings && !hideWarning"
      class="flex gap-3 p-4 bg-blue-50 dark:bg-blue-950 rounded-lg border border-blue-300 border-solid max-md:flex-wrap mb-2"
      aria-labelledby="notification-title"
    >
      <div class="flex justify-center items-center self-start py-px">
        <Icon
          name="i-heroicons-sparkles-16-solid"
          class="w-6 h-6 text-nt-blue"
        />
      </div>
      <div class="flex flex-col flex-1 max-md:max-w-full">
        <div class="flex flex-col text-sm leading-5 text-slate-900 max-md:max-w-full">
          <h5
            id="notification-title"
            class="font-medium max-md:max-w-full text-blue-500"
          >
            Upgrade to unlock all features
          </h5>
          <p class="mt-2 max-md:max-w-full text-slate-500">
            <span v-if="specifyFormOwner">Only you are seeing this notification, as owner of the form.</span> The
            following features are disabled on the published form:
          </p>
          <div
            class="text-slate-500"
            v-html="cleaningContent"
          />
        </div>
        <div class="flex gap-0 self-start mt-2 text-xs font-medium leading-3 text-center">
          <UButton
            v-track.form_cleanings_unlock_all_features
            icon="i-heroicons-check-badge-16-solid"
            @click.prevent="onUpgradeClick"
          >
            <template v-if="form.is_pro">
              Upgrade plan - Unlock all features
            </template>
            <template v-else>
              Start free trial - Unlock all features
            </template>
          </UButton>
          <UButton
            v-if="specifyFormOwner"
            variant="link"
            @click.prevent="hideWarning=true"
          >
            Dismiss
          </UButton>
        </div>
      </div>
    </section>
  </VTransition>
</template>

<script>
import VTransition from '~/components/global/transitions/VTransition.vue'

export default {
  name: 'FormCleanings',
  components: { VTransition },
  props: {
    form: { type: Object, required: true },
    specifyFormOwner: { type: Boolean, default: false },
    hideable: { type: Boolean, default: false }
  },
  setup () {
    const subscriptionModalStore = useSubscriptionModalStore()
    return {
      subscriptionModalStore
    }
  },
  data () {
    return {
      collapseOpened: false,
      hideWarning: false
    }
  },
  computed: {
    hasCleanings () {
      return this.form.cleanings && Object.keys(this.form.cleanings).length > 0
    },
    cleanings () {
      return this.form.cleanings
    },
    cleaningContent () {
      let message = ''
      Object.keys(this.cleanings).forEach((key) => {
        let fieldName = key.charAt(0).toUpperCase() + key.slice(1)
        if (fieldName !== 'Form') {
          fieldName = '"' + fieldName + '" field'
        }
        let fieldInfo = '<br><span class="font-semibold">' + fieldName + '</span><br/><ul class=\'list-disc list-inside\'>'
        this.cleanings[key].forEach((msg) => {
          if (!msg) return
          fieldInfo = fieldInfo + '<li>' + msg + '</li>'
        })
        if (fieldInfo) {
          message = message + fieldInfo + '</ul>'
        }
      })
      return message
    }
  },
  methods: {
    onUpgradeClick () {
      this.subscriptionModalStore.setModalContent('Upgrade to unlock all features for your form', 'Some features are disabled on the published form. Upgrade your plan to unlock these features and much more. Gain full access to all advanced features.')
      this.subscriptionModalStore.openModal()
    }
  }
}
</script>
