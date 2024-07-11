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
          <div v-for="row, i in field.rows" class="flex items-center space-x-2" :key="row+i">
            <text-input
                name="rows"
              class="mb-0"
              v-model="field.rows[i]"
            />
            <button @click="removeMatrixRow(i)">
              <Icon name="heroicons:trash" class="text-gray-300 w-4 h-4"/>
            </button>
          </div>
          <button @click="addMatrixRow" class="space-x-1 flex items-center bg-gray-200 rounded text-xs p-1 px-2">
            <Icon name="heroicons:plus" class="w-4 h-4"/>
            <span>Add rows</span>
          </button>
        </div>
        <div class="">
          <div v-for="column, i in field.columns" class="flex items-center space-x-2" :key="i">
            <text-input
              class="mb-0"
              v-model="field.columns[i]"
            />
            <button @click="removeMatrixColumn(i)">
              <Icon name="heroicons:trash" class="text-gray-300 w-4 h-4"/>
            </button>
          </div>
          <button @click="addMatrixColumn" class="space-x-1 flex items-center bg-gray-200 rounded text-xs p-1 px-2">
            <Icon name="heroicons:plus" class="w-4 h-4"/>
            <span>Add column</span>
          </button>
        </div>
      </div>
    </div>
</template>

<script>
export default {
    name: 'MatrixFieldOptions',
    props: {
        field: {
            type: Object,
            required: false
        },
    },
    computed: {
        selectionData() {
            return this.field.rows?.reduce((obj, row) => {
                obj[row] = '';
                return obj;
            }, {});
        }
    },

    methods: {
        addMatrixRow() {
            this.field.rows.push(this.generateUniqueLabel(this.field.rows, 'Row'))
            this.field.selection_data = this.selectionData
        },
        removeMatrixRow(index) {
            this.field.rows.splice(index, 1)
            this.field.selection_data = this.selectionData
        },

        addMatrixColumn() {
            this.field.columns.push(this.generateUniqueLabel(this.field.columns, null))
            this.field.selection_data = this.selectionData
        },
        removeMatrixColumn(index) {
            this.field.columns.splice(index, 1)
            this.field.selection_data = this.selectionData
        },

        generateUniqueLabel(array, prefix = null) {
            let uniqueNumber = 1; // Start checking from 1
            let label = prefix ? `${prefix} ${uniqueNumber}` : uniqueNumber
            while (array.includes(label)) {
                uniqueNumber++; // Increment if the number is found in the array
                label = prefix ? `${prefix} ${uniqueNumber}` : uniqueNumber
            }
            return label; // Return the first unique number found
        }
    }
}
</script>