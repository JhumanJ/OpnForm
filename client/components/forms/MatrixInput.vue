<template>
 <input-wrapper v-bind="inputWrapperProps">
    <template #label>
      <slot name="label" />
    </template> 
    <!-- <div class="flex mb-2">
        <div class="w-1/4"></div>
        <div class="">
            <div class="flex space-x-6 py-2" :class="columnGrids">
                <div v-for="column in columns" :key="column" class="w-4 flex justify-center">
                    {{ column }}
                </div>
            </div>
        </div>
    </div>
    <div v-for="row, index in matrixData" class="w-full flex items-center" :key="row">
        <div class="w-1/4">
            {{row.label}}
        </div>
        <div class="flex space-x-6 py-2" :class="columnGrids">
            <div v-for="option in row.options" :key="row.label+option" class="w-4 flex justify-center">
                <input
                    type="radio"
                    :value="option"
                    :aria-checked="true"
                    :name="row.label"
                    v-model="selection[row.label]"
                    @update:model-value="onSelection(row, $event)"
                    class="styled-radio"
                    id="radio-{{ row.label }}-{{ option }}"
                >
            </div>
        </div>
    </div>        -->
        <table class="w-full table-fixed overflow-hidden" :class="[theme.default.borderRadius]">
            <thead class="">
                <tr class="bg-gray-200/40">
                    <th @click="test" class="border-t border-l">
                        
                    </th>
                    <td v-for="column in columns" :key="column" class="border">
                        <div class="p-2 w-full flex items-center justify-center capitalize">
                            <span>
                                {{ column }}
                            </span>
                        </div>
                    </td>
                </tr>
            </thead>

            <tbody>
                <tr v-for="row, rowIndex in rows" :key="rowIndex" class="border">
                    <td class="border">
                        <div class="p-2">
                            {{ row }}
                        </div>
                    </td>
                    <td v-for="column, columnIndex in columns" :key="row + column" class="border">
                        <div class="w-full flex items-center justify-center">
                            <div
                                role="radio"
                                :aria-checked="selection[row] === column"
                                :class="[
                                theme.FlatSelectInput.spacing.vertical,
                                theme.FlatSelectInput.fontSize,
                                theme.FlatSelectInput.option,
                                ]"
                                @click="onSelect(row, column)"
                            >
                                <Icon
                                    v-if="selection[row] === column"
                                    :key="row+column"
                                    name="material-symbols:radio-button-checked-outline"
                                    class="text-inherit"
                                    :color="color"
                                    :class="[theme.FlatSelectInput.icon]"
                                />
                                <Icon
                                    v-else
                                    :key="row+column"
                                    name="material-symbols:radio-button-unchecked"
                                    :class="[theme.FlatSelectInput.icon,theme.FlatSelectInput.unselectedIcon]"
                                />
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    <template #help>
      <slot name="help" />
    </template>
    <template #error>
      <slot name="error" />
    </template>
  </input-wrapper>
</template>
<script>
import { inputProps, useFormInput } from "./useFormInput.js"
import InputWrapper from "./components/InputWrapper.vue"

export default {
  name: "MatrixInput",
    components: { InputWrapper },

    props: {
        ...inputProps,
        rows: { type: Array, required: true },
        columns: { type: Array, required: true },
        selectionData: { type: Object, required: true },
    },
    data() {
        return {
            selection: {},
            selected: false
        }
    },
    setup(props, context) {
        return {
            ...useFormInput(props, context),
        }
    },
    computed: {
        matrixData() {
            const options = this.columns
            return this.rows?.map(row => {
                return {
                    label: row,
                    options
                }
            })
        },
        columnGrids() {
            return 'grid-cols-' + this.columns?.length
        }

    },
    methods: {
        test() {
            console.log(this.selection)
        },
        handleCompValChanged() {

            this.selection = this.selectionData
        },
        onSelect(row, column) {
            if (this.selection[row] == column) {
                this.selection[row] = null
            } else {
                this.selection[row] = column
            }
            this.compVal = this.selection
        },
        onSelection() {
            console.log(this.selection)
            this.compval = this.selection
        },
    },
    mounted() {
        this.handleCompValChanged()
    },

    watch: {
        compVal: {
            handler(newVal, oldVal) {
                if (!oldVal) {
                    this.handleCompValChanged()
                }
            },
            immediate: false
        }
    },
}
</script>
