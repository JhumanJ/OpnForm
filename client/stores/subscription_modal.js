import { defineStore } from 'pinia'

const DEFAULT_MODAL_TITLE = 'Upgrade and take your forms to the next level'
const DEFAULT_MODAL_DESCRIPTION = 'On the Free plan, you can try out all paid features only within the form editor. Upgrade your plan to gain full access to all features.'

export const useSubscriptionModalStore = defineStore('subscription_modal', {
  state: () => ({
    show: false,
    plan: null,
    yearly: true,
    modal_title: DEFAULT_MODAL_TITLE,
    modal_description: DEFAULT_MODAL_DESCRIPTION
  }),
  actions: {
    openModal (planName = undefined, isYearly = undefined) {
      this.plan = (planName !== undefined) ? planName : null
      this.yearly = (isYearly !== undefined) ? isYearly : true
      this.show = true
    },
    setModalContent (title = null, description = null) {
      this.modal_title = title || DEFAULT_MODAL_TITLE
      this.modal_description = description || DEFAULT_MODAL_DESCRIPTION
    },
    closeModal () {
      this.show = false
      this.plan = null
      this.modal_title = DEFAULT_MODAL_TITLE
      this.modal_description = DEFAULT_MODAL_DESCRIPTION
    }
  }
})
