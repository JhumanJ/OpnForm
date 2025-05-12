<template>
  <div
    ref="container"
    class="quilly-editor"
  />
</template>
  
<script setup>
import Quill from 'quill'
import 'quill/dist/quill.snow.css'
import { onMounted, onBeforeUnmount, ref, watch } from 'vue'

const props = defineProps({
  modelValue: {
    type: String,
    default: null
  },
  options: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits([
  'update:modelValue',
  'text-change',
  'selection-change',
  'editor-change',
  'blur',
  'focus',
  'ready'
])

let quillInstance = null
const container = ref(null)
const model = ref(props.modelValue)

const pasteHTML = (instance) => {
  instance.clipboard.dangerouslyPasteHTML(props.modelValue || '', 'silent')
}

const initializeQuill = () => {
  if (container.value) {
    quillInstance = new Quill(container.value, props.options)

    quillInstance.on('selection-change', (range, oldRange, source) => {
      if (!range) {
        emit('blur', quillInstance)
      } else {
        emit('focus', quillInstance)
      }
      emit('selection-change', { range, oldRange, source })
    })

    quillInstance.on('text-change', (delta, oldContents, source) => {
      // Update local model only on user input
      model.value = quillInstance.getSemanticHTML()
      emit('text-change', { delta, oldContents, source })
    })

    quillInstance.on('editor-change', (eventName, ...args) => {
      emit('editor-change', eventName, ...args)
    })

    if (props.modelValue) {
      pasteHTML(quillInstance)
      model.value = quillInstance.getSemanticHTML()
    }

    emit('ready', quillInstance)
  }
}

onMounted(() => {
  initializeQuill()
})

// Watch modelValue and paste HTML if has changes
watch(
  () => props.modelValue,
  (newValue) => {
    if (!quillInstance) return
    if (newValue && newValue !== model.value) {
      pasteHTML(quillInstance)
      model.value = quillInstance.getSemanticHTML()
    } else if (!newValue) {
      quillInstance.setContents([])
      model.value = ''
    }
  }
)

// Watch model and update modelValue if has changes
watch(model, (newValue, oldValue) => {
  if (!quillInstance) return
  if (newValue && newValue !== oldValue) {
    emit('update:modelValue', newValue)
  } else if (!newValue) {
    quillInstance.setContents([])
  }
})

onBeforeUnmount(() => {
  if (quillInstance) {
    quillInstance.off('selection-change')
    quillInstance.off('text-change')
    quillInstance.off('editor-change')
  }
  quillInstance = null
})
</script>