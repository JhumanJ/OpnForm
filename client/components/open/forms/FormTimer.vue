<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { pendingSubmission as pendingSubmissionFun } from "~/composables/forms/pendingSubmission.js"

const props = defineProps({
  form: { type: Object, required: true }
})

const pendingSubmission = pendingSubmissionFun(props.form)
const startTime = ref(null)
const completionTime = ref(parseInt(pendingSubmission.getTimer() ?? null))
let timer = null

watch(() => completionTime.value, () => {
  if (completionTime.value) {
    pendingSubmission.setTimer(completionTime.value)
  }
}, { immediate: true })

const startTimer = () => {
  if (!startTime.value) {
    startTime.value = parseInt(pendingSubmission.getTimer() ?? 1)
    completionTime.value = startTime.value
    timer = setInterval(() => {
      completionTime.value += 1
    }, 1000)
  }
}

const stopTimer = () => {
  if (timer) {
    clearInterval(timer)
    timer = null
  }
}

const resetTimer = () => {
  stopTimer()
  startTime.value = null
  completionTime.value = null
}

onMounted(() => {
  document.addEventListener('input', startTimer)
})

onUnmounted(() => {
  document.removeEventListener('input', startTimer)
  stopTimer()
})

defineExpose({
  completionTime,
  stopTimer,
  resetTimer
})
</script>