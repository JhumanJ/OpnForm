<template>
  <Modal
    :show="show"
    max-width="lg"
    @close="$emit('close')"
  >
    <FlatSelectInput
      :form="form"
      name="format"
      :options="[
        { name: 'Regular format', value: 'regular' },
        { name: 'Focused format (1 question per page)', value: 'focused' },
      ]"
      label="Select form format"
    />
    <div class="flex">
      <UButton
        size="md"
        block
        @click="$emit('close')"
      >
        Ok
      </UButton>
    </div>
  </Modal>
</template>
<script setup>
import { defineProps } from "vue"
 
const props = defineProps({
  show: { type: Boolean, required: true },
  form: { type: Object, required: true },
})
</script>