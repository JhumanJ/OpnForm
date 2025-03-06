<template>
  <collapse
    class="py-5 w-full"
    :model-value="false"
  >
    <template #title>
      <div class="flex">
        <h3 class="font-semibold block text-lg">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 inline text-gray-500 mr-2 -mt-1"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M4 6h16M4 12h16M4 18h16"
            />
          </svg>
          Show advanced sharing options
        </h3>
      </div>
    </template>
    <toggle-switch-input
      :model-value="modelValue.auto_submit"
      name="auto_submit"
      class="mt-4"
      label="Auto Submit Form"
      help="Form will auto submit immediate after open URL"
      @update:model-value="onChangeAutoSubmit"
    />
  </collapse>
</template>

<script>
import Collapse from "~/components/global/Collapse.vue"
export default {
  name: "AdvancedFormUrlSettings",
  components: { Collapse },
  props: {
    form: {
      type: Object,
      required: true,
    },
    modelValue: {
      type: Object,
      required: true,
    },
  },

  data() {
    return {}
  },

  methods: {
    onChangeAutoSubmit(val) {
      this.modelValue.auto_submit = val
    },
  },
}
</script>
