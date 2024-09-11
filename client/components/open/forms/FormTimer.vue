<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const startTime = ref(null)
const completionTime = ref(null)
let timer = null

const startTimer = () => {
  if (!startTime.value) {
    startTime.value = Date.now()
    timer = setInterval(() => {
      completionTime.value = Math.floor((Date.now() - startTime.value) / 1000)
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