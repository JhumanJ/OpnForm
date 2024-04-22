<template>
  <div  v-if="form">
    <div
      v-if="loadingDuplicate || loadingDelete"
      class="pr-4 pt-2"
    >
      <Loader class="h-6 w-6 mx-auto" />
    </div>
    <UDropdown v-else :items="items">
      <v-button
        color="white"
      >
        <Icon
          name="heroicons:ellipsis-horizontal"
        />
      </v-button>
    </UDropdown>

    <!-- Delete Form Modal -->
    <modal
      :show="showDeleteFormModal"
      icon-color="red"
      max-width="sm"
      @close="showDeleteFormModal = false"
    >
      <template #icon>
        <svg
          class="w-10 h-10"
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
          />
        </svg>
      </template>
      <template #title>
        Delete form
      </template>
      <div class="p-3">
        <p>
          If you want to permanently delete this form and all of its data, you
          can do so below.
        </p>
        <div class="flex mt-4">
          <v-button
            class="sm:w-1/2 mr-4"
            color="white"
            @click.prevent="showDeleteFormModal = false"
          >
            Cancel
          </v-button>
          <v-button
            class="sm:w-1/2"
            color="red"
            :loading="loadingDelete"
            @click.prevent="deleteForm"
          >
            Yes, delete it
          </v-button>
        </div>
      </div>
    </modal>
    <form-template-modal
      v-if="!isMainPage && user"
      :form="form"
      :show="showFormTemplateModal"
      @close="showFormTemplateModal = false"
    />
    <form-workspace-modal
      v-if="user"
      :form="form"
      :show="showFormWorkspaceModal"
      @close="showFormWorkspaceModal = false"
    />
  </div>
</template>

<script setup>
import { ref, defineProps, computed } from "vue"
import Dropdown from "~/components/global/Dropdown.vue"
import FormTemplateModal from "../../../open/forms/components/templates/FormTemplateModal.vue"
import FormWorkspaceModal from "../../../open/forms/components/FormWorkspaceModal.vue"

const { copy } = useClipboard()
const router = useRouter()

const props = defineProps({
  form: { type: Object, required: true },
  isMainPage: { type: Boolean, required: false, default: false },
})

const authStore = useAuthStore()
const formsStore = useFormsStore()
const formEndpoint = "/open/forms/{id}"
const user = computed(() => authStore.user)

const loadingDuplicate = ref(false)
const loadingDelete = ref(false)
const showDeleteFormModal = ref(false)
const showFormTemplateModal = ref(false)
const showFormWorkspaceModal = ref(false)

const items = computed(() => {
  return [
    [
      ...props.isMainPage ? [{
        label: 'View form',
        icon: 'i-heroicons-eye-16-solid',
        click: () => {
          if (props.isMainPage && props.form.visibility === 'draft') {
            showDraftFormWarningNotification()
          } else {
            window.open(props.form.share_url, '_blank')
          }
        }
      }] : [],
      ...props.isMainPage ? [{
        label: 'Copy link to share',
        icon: 'i-heroicons-clipboard-document-check-20-solid',
        click: () => {
          copyLink()
        }
      }] : []
    ],
    [
      ...props.isMainPage ? [{
        label: 'Edit',
        icon: 'i-heroicons-pencil-square-20-solid',
        to: { name: 'forms-slug-edit', params: { slug: props.form.slug } }
      }] : [],
      {
        label: 'Duplicate form',
        icon: 'i-heroicons-document-duplicate-20-solid',
        click: () => {
          duplicateForm()
        }
      }], 
    [
      ...props.isMainPage ? [] : [{
        label: 'Create Template',
        icon: 'i-heroicons-document-plus-20-solid',
        click: () => {
          showFormTemplateModal.value = true
        }
      }],
      {
        label: 'Change workspace',
        icon: 'i-heroicons-building-office-2-20-solid',
        click: () => {
          showFormWorkspaceModal.value = true
        }
      },
    ],[
      {
        label: 'Delete form',
        icon: 'i-heroicons-trash-20-solid',
        click: () => {
          showDeleteFormModal.value = true
        },
        class: 'text-red-800 hover:bg-red-50 hover:text-red-600 group',
        iconClass: 'text-red-900 group-hover:text-red-800'
      }
    ]
  ].filter((group) => group.length > 0)
})

const copyLink = () => {
  copy(props.form.share_url)
  useAlert().success("Copied!")
}
const duplicateForm = () => {
  if (loadingDuplicate.value) return
  loadingDuplicate.value = true
  opnFetch(formEndpoint.replace("{id}", props.form.id) + "/duplicate", {
    method: "POST",
  })
    .then((data) => {
      formsStore.save(data.new_form)
      router.push({
        name: "forms-slug-show",
        params: { slug: data.new_form.slug },
      })
      useAlert().success(data.message)
      loadingDuplicate.value = false
    })
    .catch((error) => {
      useAlert().error(error.data.message)
      loadingDuplicate.value = false
    })
}
const deleteForm = () => {
  if (loadingDelete.value) return
  loadingDelete.value = true
  opnFetch(formEndpoint.replace("{id}", props.form.id), { method: "DELETE" })
    .then((data) => {
      formsStore.remove(props.form)
      router.push({ name: "home" })
      useAlert().success(data.message)
      loadingDelete.value = false
    })
    .catch((error) => {
      useAlert().error(error.data.message)
      loadingDelete.value = false
    })
}

const showDraftFormWarningNotification = () => {
  useAlert().warning(
    "This form is currently in Draft mode and is not publicly accessible, You can change the form status on the edit form page.",
  )
}
</script>
