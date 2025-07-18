<template>
  <div class="flex gap-1">
    <TrackClick
      name="edit_record_click"
      :properties="{}"
    >
      <UButton
        size="xs"
        color="neutral"
        variant="outline"
        icon="heroicons:pencil-square"
        @click="showEditSubmissionModal = true"
      />
    </TrackClick>
    <TrackClick
      name="delete_record_click"
      :properties="{}"
    >
      <UButton
        size="xs"
        color="error"
        variant="outline"
        icon="heroicons:trash"
        @click="onDeleteClick"
      />
    </TrackClick>
  </div>
  
  <EditSubmissionModal
    :show="showEditSubmissionModal"
    :form="form"
    :submission="submission"
    @close="showEditSubmissionModal = false"
    @updated="(submission) => $emit('updated', submission)"
  />
</template>

<script setup>
import EditSubmissionModal from "./EditSubmissionModal.vue"
import TrackClick from "~/components/global/TrackClick.vue"
import { formsApi } from "~/api/forms"

const props = defineProps({
  form: {
    type: Object,
    required: true,
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

const deleteRecord = () => {
  formsApi.submissions.delete(props.form.id, props.submission.id)
    .then((data) => {
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
