<template>
  <UTooltip
    v-if="shouldDisplayProTag"
    :text="upgradeModalTitle??'You need a Pro plan to use this feature'"
    class="inline normal-case"
  >
    <div
      v-track.pro_tag_click="{title:upgradeModalTitle}"
      class="bg-blue-500 text-white px-2 text-xs uppercase inline rounded-full font-semibold cursor-pointer"
      @click.stop="onClick"
    >
      PRO
    </div>
  </UTooltip>
</template>

<script setup>
import { computed } from "vue"

const props = defineProps({
  upgradeModalTitle: {
    type: String
  },
  upgradeModalDescription: {
    type: String
  }
})

const { openSubscriptionModal } = useAppModals()
const { data: user } = useAuth().user()
const { current: workspace } = useCurrentWorkspace()

const shouldDisplayProTag = computed(() => {
  if (!useFeatureFlag('billing.enabled')) return false
  if (!user.value || !workspace.value) return true
  return !workspace.value.is_pro
})

function onClick () {
  openSubscriptionModal({
    modal_title: props.upgradeModalTitle,
    modal_description: props.upgradeModalDescription
  })
}
</script>
