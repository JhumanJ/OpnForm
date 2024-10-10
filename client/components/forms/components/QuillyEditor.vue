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
  let isInternalChange = false
  
  const setContents = (content) => {
    if (!quillInstance) return
  
    isInternalChange = true
    quillInstance.root.innerHTML = content
    quillInstance.update()
    isInternalChange = false
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
        if (!isInternalChange) {
          const html = quillInstance.root.innerHTML
          emit('text-change', { delta, oldContents, source })
          emit('update:modelValue', html)
        }
      })
  
      quillInstance.on('editor-change', (eventName, ...args) => {
        emit('editor-change', eventName, ...args)
      })
  
      if (props.modelValue) {
        setContents(props.modelValue)
      }
  
      emit('ready', quillInstance)
    }
  }
  
  onMounted(() => {
    initializeQuill()
  })
  
  watch(() => props.modelValue, (newValue) => {
    if (quillInstance && newValue !== quillInstance.root.innerHTML) {
      setContents(newValue || '')
    }
  }, { immediate: true })
  
  onBeforeUnmount(() => {
    if (quillInstance) {
      quillInstance.off('selection-change')
      quillInstance.off('text-change')
      quillInstance.off('editor-change')
    }
    quillInstance = null
  })
  </script>