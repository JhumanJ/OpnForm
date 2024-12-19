<template>
  <modal
    :show="show && form.format === 'focused' && removeBlocks.length > 0"
    :compact-header="true"
    :closeable="false"
    @close="onCancel"
  >
    <template #icon>
      <Icon
        name="heroicons:document-text"
        class="w-10 h-10 text-blue"
      />
    </template>
    <template #title>
      Format Change
    </template>
    
    <div class="p-4">
      If you change the format, below block(s) will be removed from the form:
      <ul
        v-if="removeBlocks.length > 0"
        class="list-disc ml-4 mt-2"
      >
        <li
          v-for="block in removeBlocks"
          :key="block.id"
        >
          {{ block.name }}
        </li>
      </ul>

      <div class="flex justify-end mt-4 px-6">
        <v-button
          class="mr-2"
          @click.prevent="changeFormat"
        >
          It's ok
        </v-button>
        <v-button
          color="white"
          @click.prevent="onCancel"
        >
          Cancel
        </v-button>
      </div>
    </div>    
  </modal>
</template>

<script setup>
const props = defineProps({
  show: {
    type: Boolean,
    default: false,
  }
})

const emit = defineEmits(['close'])
const removeTypes = ['nf-page-break','nf-divider','nf-image','nf-code']
const formStore = useWorkingFormStore()
const form = computed(() => formStore.content)

const removeBlocks = computed(() => form.value.properties.filter(field => removeTypes.includes(field.type)))

const changeFormat = () => {
  form.value.properties = form.value.properties.filter(property => !removeTypes.includes(property.type))
  form.value.format = 'focused'
  emit('close')
}

const onCancel = () => {
  form.value.format = 'regular'
  emit('close')
}
</script>
