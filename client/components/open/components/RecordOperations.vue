<template>
  <UButtonGroup
    size="xs"
    orientation="horizontal"
  >
    <UButton
      v-track.edit_record_click
      size="sm"
      color="white"
      icon="heroicons:pencil-square"
      @click="showEditSubmissionModal = true"
    />
    <UButton
      v-track.delete_record_click
      size="sm"
      color="white"
      icon="heroicons:trash"
      @click="onDeleteClick"
    />
  </UButtonGroup>
  <EditSubmissionModal
    :show="showEditSubmissionModal"
    :form="form"
    :submission="submission"
    @close="showEditSubmissionModal = false"
    @updated="(submission) => $emit('updated', submission)"
  />
</template>

<script>
import EditSubmissionModal from "./EditSubmissionModal.vue"

export default {
  components: { EditSubmissionModal },
  props: {
    form: {
      type: Object,
      required: true,
    },
    structure: {
      type: Array,
      default: () => [],
    },
    submission: {
      type: Object,
      default: () => {},
    },
  },
  emits: ["updated", "deleted"],
  setup() {
    return {
      useAlert: useAlert(),
    }
  },
  data() {
    return {
      showEditSubmissionModal: false,
    }
  },
  computed: {},
  mounted() {},
  methods: {
    onDeleteClick() {
      this.useAlert.confirm(
        "Do you really want to delete this record?",
        this.deleteRecord,
      )
    },
    async deleteRecord() {
      opnFetch(
        "/open/forms/" +
          this.form.id +
          "/records/" +
          this.submission.id +
          "/delete",
        { method: "DELETE" },
      )
        .then(async (data) => {
          if (data.type === "success") {
            this.$emit("deleted", this.submission)
            this.useAlert.success(data.message)
          } else {
            this.useAlert.error("Something went wrong!")
          }
        })
        .catch((error) => {
          this.useAlert.error(error.data.message)
        })
    },
  },
}
</script>
