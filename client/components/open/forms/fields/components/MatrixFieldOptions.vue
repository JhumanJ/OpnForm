<template>
  <div
    v-if="field.type === 'matrix'"
    class="border-b py-2 px-4"
  >
    <h3 class="font-semibold block text-lg">
      Matrix
    </h3>
    <p class="text-gray-400 mb-3 text-xs">
      Advanced options for matrix.
    </p>

    <div class="grid grid-cols-2 gap-4">
      <div class="">
        <div v-for="row, i in field.rows" class="flex items-center space-x-2" :key="i">
          <text-input
            name="rows"
            wrapper-class="mb-1"
            v-model="field.rows[i]"
          />
          <button @click="removeMatrixRow(i)">
            <Icon name="heroicons:trash" class="text-gray-300 w-4 h-4 mb-2"/>
          </button>
        </div>
        <UButton size="xs" @click="addMatrixRow" color="gray" icon="i-heroicons-plus">
          Add row
        </UButton>
      </div>
      <div class="">
        <div v-for="column, i in field.columns" class="flex items-center space-x-2" :key="i">
          <text-input
            wrapper-class="mb-1"
            v-model="field.columns[i]"
          />
          <button @click="removeMatrixColumn(i)">
            <Icon name="heroicons:trash" class="text-gray-300 w-4 h-4 mb-2"/>
          </button>
        </div>
        <UButton size="xs" @click="addMatrixColumn" color="gray" icon="i-heroicons-plus">
          Add column
        </UButton>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  field: {
    type: Object,
    required: false
  },
})
const selectionData = computed(() => {
  return props.field.rows?.reduce((obj, row) => {
    obj[row] = ''
    return obj
  }, {})
})


function addMatrixRow() {
  props.field.rows.push(generateUniqueLabel(props.field.rows, 'Row'))
  props.field.selection_data = selectionData.value
}

function removeMatrixRow(index) {
  props.field.rows.splice(index, 1)
  props.field.selection_data = selectionData.value
}

function addMatrixColumn() {
  props.field.columns.push(generateUniqueLabel(props.field.columns, null))
  props.field.selection_data = selectionData.value
}

function removeMatrixColumn(index) {
  props.field.columns.splice(index, 1)
  props.field.selection_data = selectionData.value
}

function generateUniqueLabel(array, prefix = null) {
  let uniqueNumber = 1 // Start checking from 1
  let label = prefix ? `${prefix} ${uniqueNumber}` : uniqueNumber
  while (array.includes(label)) {
    uniqueNumber++ // Increment if the number is found in the array
    label = prefix ? `${prefix} ${uniqueNumber}` : uniqueNumber
  }
  return label // Return the first unique number found
}

</script>
