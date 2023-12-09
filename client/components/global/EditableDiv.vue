<template>
  <div ref="parentRef"
       tabindex="0"
       :class="{
         'hover:bg-gray-100 dark:hover:bg-gray-800 rounded px-2 cursor-pointer': !editing
       }"
       class="relative"
       :style="{ height: editing ? divHeight + 'px' : 'auto' }"
       @focus="startEditing"
  >
    <slot v-if="!editing" :content="content">
      <label class="cursor-pointer truncate w-full">
        {{ content }}
      </label>
    </slot>
    <div v-if="editing" class="absolute inset-0 border-2 transition-colors"
         :class="{ 'border-transparent': !editing, 'border-blue-500': editing }"
    >
      <input ref="editInputRef" v-model="content"
             class="absolute inset-0 focus:outline-none bg-white transition-colors"
             :class="[{'bg-blue-50': editing}, contentClass]" @blur="editing = false" @keyup.enter="editing = false"
             @input="handleInput"
      >
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, nextTick, defineProps, defineEmits } from 'vue'

const props = defineProps({
  modelValue: { type: String, required: true },
  textAlign: { type: String, default: 'left' },
  contentClass: { type: String, default: '' }
})

const emit = defineEmits()
const content = ref(props.modelValue)
const editing = ref(false)
const divHeight = ref(0)

const parentRef = ref(null) // Ref for parent element
const editInputRef = ref(null) // Ref for edit input element

const startEditing = () => {
  if (parentRef.value) {
    divHeight.value = parentRef.value.offsetHeight
    editing.value = true
    nextTick(() => {
      if (editInputRef.value) {
        editInputRef.value.focus()
      }
    })
  }
}

const handleInput = () => {
  emit('update:modelValue', content.value)
}

// Watch for changes in props.modelValue and update the local content
watch(() => props.modelValue, (newValue) => {
  content.value = newValue
})

// Wait until the component is mounted to set the initial divHeight
onMounted(() => {
  if (parentRef.value) {
    divHeight.value = parentRef.value.offsetHeight
  }
})
</script>
