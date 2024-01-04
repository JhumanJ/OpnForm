<template>
  <div class="w-full flex flex-col">
    <form-editor v-if="!formsLoading || form" ref="editor"
                 :is-edit="true"
                 @on-save="formInitialHash=null"
    />
    <div v-else-if="!formsLoading && error" class="mt-4 rounded-lg max-w-xl mx-auto p-6 bg-red-100 text-red-500">
      {{ error }}
    </div>
    <div v-else class="text-center mt-4 py-6">
      <Loader class="h-6 w-6 text-nt-blue mx-auto" />
    </div>
  </div>
</template>

<script>
import { computed } from 'vue'
import Breadcrumb from '~/components/global/Breadcrumb.vue'
import FormEditor from "~/components/open/forms/components/FormEditor.vue";
import {hash} from "~/lib/utils.js";

export default {
  name: 'EditForm',
  components: { Breadcrumb, FormEditor },
  middleware: 'auth',

  beforeRouteLeave (to, from, next) {
    if (this.isDirty()) {
      return useAlert().confirm('Changes you made may not be saved. Are you sure want to leave?', () => {
        window.onbeforeunload = null
        next()
      }, () => {})
    }
    next()
  },

  setup () {
    const formsStore = useFormsStore()
    const workingFormStore = useWorkingFormStore()
    const workspacesStore = useWorkspacesStore()

    if (!formsStore.allLoaded) {
      formsStore.startLoading()
    }
    const updatedForm = storeToRefs(workingFormStore).content
    const form = computed(() => formsStore.getByKey(useRoute().params.slug))

    // Create a form.id watcher that updates working form
    watch(form, (form) => {
      if (form) {
        updatedForm.value = useForm(form)
      }
    })

    useOpnSeoMeta({
      title: 'Edit ' + ((form && form.value) ? form.value.title : 'Your Form')
    })

    return {
      formsStore,
      workingFormStore,
      workspacesStore,
      updatedForm,
      form,
      formsLoading: computed(() => formsStore.loading),
    }
  },

  data () {
    return {
      error: null,
      formInitialHash: null
    }
  },

  computed: {
  },

  async beforeMount() {
    window.onbeforeunload = () => {
      if (this.isDirty()) {
        return false
      }
    }

    if (!this.form && !this.formsStore.allLoaded) {
      await this.formsStore.loadAll(this.workspacesStore.currentId)
    }

    this.updatedForm = useForm(this.form)
    this.formInitialHash = hash(JSON.stringify(this.updatedForm.data()))

    if (this.updatedForm && (!this.updatedForm.notification_settings || Array.isArray(this.updatedForm.notification_settings))) {
      this.updatedForm.notification_settings = {}
    }
  },

  methods: {
    isDirty () {
      return this.formInitialHash && this.formInitialHash !== hash(JSON.stringify(this.updatedForm.data()))
    }
  }
}
</script>
