<template>
  <UModal
    v-model:open="isOpen"
    :ui="{ content: 'sm:max-w-4xl' }"
    :title="template ? 'Edit Template' : 'Create Template'"
  >
    <template #body>
      <p v-if="!template" class="mb-4">
        New template will be create from your form:
        <span class="font-semibold">{{ form.title }}</span>.
      </p>

      <form
        v-if="templateForm"
        @submit.prevent="onSubmit"
        @keydown="templateForm.onKeydown($event)"
      >
        <div class="space-y-4">
          <toggle-switch-input
            v-if="user && (user.admin || user.template_editor)"
            name="publicly_listed"
            :form="templateForm"
            label="Publicly Listed?"
          />
          <text-input
            name="name"
            :form="templateForm"
            label="Title"
            :required="true"
          />
          <text-input
            name="slug"
            :form="templateForm"
            label="Slug"
            :required="true"
          />
          <text-area-input
            name="short_description"
            :form="templateForm"
            label="Short Description"
            :required="true"
          />
          <rich-text-area-input
            name="description"
            :form="templateForm"
            label="Description"
            :required="true"
          />
          <text-input
            name="image_url"
            :form="templateForm"
            label="Image"
            :required="true"
          />
          <select-input
            name="types"
            :form="templateForm"
            label="Types"
            :options="typesOptions"
            :multiple="true"
            :searchable="true"
          />
          <select-input
            name="industries"
            :form="templateForm"
            label="Industries"
            :options="industriesOptions"
            :multiple="true"
            :searchable="true"
          />
          <select-input
            name="related_templates"
            :form="templateForm"
            label="Related Templates"
            :options="templatesOptions"
            :multiple="true"
            :searchable="true"
          />
          <questions-editor
            name="questions"
            :questions="templateForm.questions"
            label="Frequently asked questions"
          />
        </div>
      </form>
    </template>

    <template #footer>
      <div class="flex justify-end gap-x-2 w-full">
        <UButton
          color="neutral"
          variant="outline"
          @click="close"
          label="Close"
        />
        <UButton
          v-if="template"
          color="error"
          variant="outline"
          @click="
            useAlert().confirm(
              'Do you really want to delete this template?',
              deleteFormTemplate,
            )
          "
          label="Delete template"
        />
        <div class="grow"/>
        <UButton
          class="px-8"
          :loading="createMutation.isPending.value || updateMutation.isPending.value"
          @click="onSubmit"
          :label="template ? 'Update' : 'Create'"
        />
      </div>
    </template>
  </UModal>
</template>

<script setup>
import { ref, defineProps, defineEmits, computed } from "vue"
import QuestionsEditor from "./QuestionsEditor.vue"
import { useTemplates } from "~/composables/query/useTemplates"
import { useTemplateMeta } from "~/composables/useTemplateMeta"

const props = defineProps({
  show: { type: Boolean, required: true },
  form: { type: Object, required: true },
  template: { type: Object, required: false, default: () => {} },
})

const router = useRouter()
const { data: user } = useAuth().user()

const { list, create, update, remove } = useTemplates()
const { data: templates } = list()

const { industries: industriesMap, types: typesMap } = useTemplateMeta()

const industries = computed(() => [...(industriesMap.value?.values() ?? [])])
const types = computed(() => [...(typesMap.value?.values() ?? [])])

const templateForm = ref(null)
const emit = defineEmits(["close"])

// Modal state
const isOpen = computed({
  get() {
    return props.show
  },
  set(value) {
    if (!value) {
      close()
    }
  }
})

onMounted(() => {
  templateForm.value = useForm(
    props.template ?? {
      publicly_listed: false,
      name: "",
      slug: "",
      short_description: "",
      description: "",
      image_url: "",
      types: [],
      industries: [],
      related_templates: [],
      questions: [],
    },
  )
})

const typesOptions = computed(() => {
  return Object.values(types.value).map((type) => {
    return {
      name: type.name,
      value: type.slug,
    }
  })
})
const industriesOptions = computed(() => {
  return Object.values(industries.value).map((industry) => {
    return {
      name: industry.name,
      value: industry.slug,
    }
  })
})
const templatesOptions = computed(() => {
  if (!templates.value) return []
  return Object.values(templates.value).map((template) => {
    return {
      name: template.name,
      value: template.slug,
    }
  })
})

const close = () => {
  emit("close")
}

const createMutation = create({
  onSuccess: () => {
    useAlert().success("Template created successfully")
    emit("close")
  },
  onError: (error) => useAlert().error(error.message),
})

const updateMutation = update({
  onSuccess: () => {
    useAlert().success("Template updated successfully")
    emit("close")
  },
  onError: (error) => useAlert().error(error.message),
})

const deleteMutation = remove({
  onSuccess: () => {
    useAlert().success("Template deleted successfully")
    router.push({ name: "templates" })
    emit("close")
  },
  onError: (error) => useAlert().error(error.message),
})

const onSubmit = () => {
  if (props.template) {
    updateFormTemplate()
  } else {
    createFormTemplate()
  }
}
const createFormTemplate = () => {
  templateForm.value.form = props.form
  createMutation.mutate(templateForm.value)
}
const updateFormTemplate = () => {
  templateForm.value.form = props.form
  updateMutation.mutate({ id: props.template.id, data: templateForm.value })
}
const deleteFormTemplate = () => {
  if (!props.template) return
  deleteMutation.mutate(props.template.id)
}
</script>
