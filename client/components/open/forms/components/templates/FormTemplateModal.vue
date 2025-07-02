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
      <div class="flex justify-end gap-x-2">
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
        <UButton
          :loading="templateForm?.busy"
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

const props = defineProps({
  show: { type: Boolean, required: true },
  form: { type: Object, required: true },
  template: { type: Object, required: false, default: () => {} },
})

const authStore = useAuthStore()
const templatesStore = useTemplatesStore()
const router = useRouter()
const user = computed(() => authStore.user)
const templates = computed(() => [...templatesStore.content.values()])
const industries = computed(() => [...templatesStore.industries.values()])
const types = computed(() => [...templatesStore.types.values()])

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
      types: null,
      industries: null,
      related_templates: null,
      questions: [],
    },
  )
})

watch(
  () => props.show,
  () => {
    if (props.show) {
      loadAllTemplates(templatesStore)
    }
  },
)

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

const onSubmit = () => {
  if (props.template) {
    updateFormTemplate()
  } else {
    createFormTemplate()
  }
}
const createFormTemplate = async () => {
  templateForm.value.form = props.form
  await templateForm.value.post("/templates").then((data) => {
    if (data.message) {
      useAlert().success(data.message)
    }
    templatesStore.save(data.data)
    emit("close")
  })
}
const updateFormTemplate = async () => {
  templateForm.value.form = props.form
  await templateForm.value
    .put("/templates/" + props.template.id)
    .then((data) => {
      if (data.message) {
        useAlert().success(data.message)
      }
      templatesStore.save(data.data)
      emit("close")
    })
}
const deleteFormTemplate = async () => {
  if (!props.template) return
  opnFetch("/templates/" + props.template.id, { method: "DELETE" }).then(
    (data) => {
      if (data.message) {
        useAlert().success(data.message)
      }
      router.push({ name: "templates" })
      templatesStore.remove(props.template)
      emit("close")
    },
  )
}
</script>
