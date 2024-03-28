import {hash} from "~/lib/utils.js"
import {useStorage} from "@vueuse/core"

export const pendingSubmission = (form) => {

  const formPendingSubmissionKey = computed(() => {
    return (form) ? form.form_pending_submission_key + '-' + hash(window.location.href) : ''
  })

  const enabled = computed(() => {
    return form?.auto_save ?? false
  })

  const set = (value) => {
    if (import.meta.server || !enabled.value) return
    useStorage(formPendingSubmissionKey.value).value = value === null ? value : JSON.stringify(value)
  }

  const remove = () => {
    return set(null)
  }

  const get = (defaultValue = {}) => {
    if (import.meta.server || !enabled.value) return
    const pendingSubmission = useStorage(formPendingSubmissionKey.value).value
    return pendingSubmission ? JSON.parse(pendingSubmission) : defaultValue
  }

  return {
    enabled,
    set,
    get,
    remove
  }
}
