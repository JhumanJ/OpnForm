<template>
  <UPopover
    v-if="changeTypeOptions.length > 0"
    v-model:open="open"
    class="-mb-1"
  >
    <div class="flex items-center gap-1.5 group">
      <Icon
        name="heroicons:arrows-right-left-20-solid"
        class="flex-shrink-0 w-5 h-5 text-gray-400 group-hover:text-gray-500"
      />
      <span class="truncate">Change Type</span>
    </div>

    <template #panel>
      <a
        v-for="(op, index) in changeTypeOptions"
        :key="index"
        href="#"
        class="block px-4 py-2 text-md text-gray-700 dark:text-white hover:bg-gray-100 hover:text-gray-900 dark:text-gray-100 dark:hover:text-white dark:hover:bg-gray-600 flex items-center"
        @click.prevent="changeType(op.value)"
      >
        {{ op.name }}
      </a>
    </template>
  </UPopover>
</template>

<script>
export default {
  name: "ChangeFieldType",
  components: {},
  props: {
    field: {
      type: Object,
      required: true,
    },
  },
  emits:  ['changeType'],
  data() {
    return {
      open: false,
    }
  },

  computed: {
    changeTypeOptions() {
      let newTypes = []
      if (
        [
          "text",
          "email",
          "phone_number",
          "number",
          "slider",
          "rating",
          "scale",
        ].includes(this.field.type)
      ) {
        newTypes = [
          { name: "Text Input", value: "text" },
          { name: "Email Input", value: "email" },
          { name: "Phone Input", value: "phone_number" },
          { name: "Number Input", value: "number" },
          { name: "Slider Input", value: "slider" },
          { name: "Rating Input", value: "rating" },
          { name: "Scale Input", value: "scale" },
        ]
      }
      if (["select", "multi_select"].includes(this.field.type)) {
        newTypes = [
          { name: "Select Input", value: "select" },
          { name: "Multi-Select Input", value: "multi_select" },
        ]
      }
      return newTypes
        .filter((item) => {
          return item.value !== this.field.type
        })
        .map((item) => {
          return {
            name: item.name,
            value: item.value,
          }
        })
    },
  },

  watch: {},

  mounted() {},

  methods: {
    changeType(newType) {
      if (newType) {
        this.$emit("changeType", newType)
        this.open = false
      }
    },
  },
}
</script>
