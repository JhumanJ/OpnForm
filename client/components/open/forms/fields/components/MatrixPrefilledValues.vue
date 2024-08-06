<template>
    <p class="font-semibold">Prefilled values</p>
    <select-input
        v-for="row in matrixData"
        :key="row.label"
        name="prefill"
        class="mt-3"
        :options="row.options"
        :label="row.label"
        v-model="selection[row.label]"
        @update:model-value="onSelection"
      />
</template>
<script>
export default {
    name: 'MatrixPrefilledValues',
    props: {
        field: {
            type: Object,
            required: false
        },
        form: {
            type: Object,
            required: false
        }
    },
    data() {
        return {
            selection: {}
        }
    },
    computed: {
        matrixData() {
            const options = this.field.columns || []
            return (this.field.row || []).map(row => {
                return {
                    label: row,
                    options: options?.map(option => ({ name: option, value: option }))
                }
            })
        },
    },
    mounted() {
        this.selection = this.field.prefill ?? this.field.selection_data ?? {}
    },
    methods: {
        onSelection() {
            this.field.prefill = this.selection
        }
    }
}
</script>