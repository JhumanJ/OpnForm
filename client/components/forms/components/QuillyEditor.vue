<template>
  <div
    ref="container"
    class="quilly-editor"
  />
</template>
  
<script setup>
import Quill from 'quill'
import 'quill/dist/quill.snow.css'
import '../../../lib/quill/quillPatches'
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

// Safely paste HTML content with handling for empty content
const pasteHTML = (instance) => {
  if (!props.modelValue) {
    instance.setContents([])
    return
  }
  
  try {
    instance.clipboard.dangerouslyPasteHTML(props.modelValue, 'silent')
  } catch (error) {
    console.error('Error pasting HTML:', error)
    // Fallback to setting empty content
    instance.setContents([])
  }
}

const initializeQuill = () => {
  if (container.value) {
    // Merge default options with user options
    const defaultOptions = {
      formats: ['bold', 'color', 'font', 'code', 'italic', 'link', 'size', 'strike', 'script', 'underline', 'header', 'list', 'mention']
    }
    
    const mergedOptions = {
      ...defaultOptions,
      ...props.options,
      modules: {
        ...defaultOptions.modules,
        ...(props.options.modules || {})
      }
    }
    
    // Initialize Quill with merged options
    quillInstance = new Quill(container.value, mergedOptions)

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