<template>
  <UButtonGroup
    size="xs"
    orientation="horizontal"
  >
    <UButton
      v-track.edit_record_click
      size="sm"
      color="neutral"
      variant="outline"
      icon="heroicons:pencil-square"
      @click="showEditSubmissionModal = true"
    />
    <UButton
      v-track.delete_record_click
      size="sm"
      color="error"
      variant="outline"
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

<script setup>
import EditSubmissionModal from './EditSubmissionModal.vue'

const props = defineProps({
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
})

const emit = defineEmits(["updated", "deleted"])

const alert = useAlert()
const showEditSubmissionModal = ref(false)

const onDeleteClick = () => {
  alert.confirm("Do you really want to delete this record?", deleteRecord)
}

const deleteRecord = async () => {
  opnFetch("/open/forms/" + props.form.id + "/submissions/" + props.submission.id, { method: "DELETE" })
    .then(async (data) => {
      if (data.type === "success") {
        emit("deleted", props.submission)
        alert.success(data.message)
      } else {
        alert.error("Something went wrong!")
      }
    })
    .catch((error) => {
      alert.error(error.data.message)
    })
}
</script>
