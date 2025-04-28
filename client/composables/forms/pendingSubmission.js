import { hash } from "~/lib/utils.js"
import { useStorage } from "@vueuse/core"

export const pendingSubmission = (form) => {
  const formPendingSubmissionKey = computed(() => {
    return form
      ? form.form_pending_submission_key + "-" + hash(window.location.href)
      : ""
  })
  const formPendingSubmissionTimerKey = computed(() => {
    return formPendingSubmissionKey.value + "-timer"
  })

  const enabled = computed(() => {
    return form?.auto_save ?? false
  })

  const set = (value) => {
    if (import.meta.server || !enabled.value) return
    useStorage(formPendingSubmissionKey.value).value =
      value === null ? value : JSON.stringify(value)
  }

  const remove = () => {
    return set(null)
  }

  const get = (defaultValue = {}) => {
    if (import.meta.server || !enabled.value) return
    const pendingSubmission = useStorage(formPendingSubmissionKey.value).value
    return pendingSubmission ? JSON.parse(pendingSubmission) : defaultValue
  }

  const setSubmissionHash = (hash) => {
    set({
      ...get(),
      submission_hash: hash
    })
  }

  const getSubmissionHash = () => {
    return get()?.submission_hash ?? null
  }

  const setTimer = (value) => {
    if (import.meta.server) return
    useStorage(formPendingSubmissionTimerKey.value).value = value
  }

  const removeTimer = () => {
    return setTimer(null)
  }

  const getTimer = (defaultValue = null) => {
    if (import.meta.server) return
    return useStorage(formPendingSubmissionTimerKey.value).value ?? defaultValue
  }

  return {
    formPendingSubmissionKey,
    enabled,
    set,
    get,
    remove,
    setSubmissionHash,
    getSubmissionHash,
    setTimer,
    removeTimer,
    getTimer,
  }
}
