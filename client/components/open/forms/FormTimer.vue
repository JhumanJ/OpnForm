<script setup>
import { pendingSubmission as pendingSubmissionFun } from "~/composables/forms/pendingSubmission.js"

const props = defineProps({
  form: { type: Object, required: true }
})

const pendingSubmission = pendingSubmissionFun(props.form)
const startTime = ref(null)
const completionTime = ref(parseInt(pendingSubmission.getTimer() ?? null))
const timer = ref(null)

watch(() => completionTime.value, () => {
  if (completionTime.value) {
    pendingSubmission.setTimer(completionTime.value)
  }
}, { immediate: true })

const startTimer = () => {
  if (!startTime.value) {
    startTime.value = parseInt(pendingSubmission.getTimer() ?? 1)
    completionTime.value = startTime.value
    timer.value = setInterval(() => {
      completionTime.value += 1
    }, 1000)
  }
}

const stopTimer = () => {
  if (timer.value) {
    clearInterval(timer.value)
    timer.value = null
    startTime.value = null
  }
}

const resetTimer = () => {
  stopTimer()
  completionTime.value = null
}

defineExpose({
  completionTime,
  startTimer,
  stopTimer,
  resetTimer
})
</script>