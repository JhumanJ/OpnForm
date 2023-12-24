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

export default {
  name: 'EditForm',
  components: { Breadcrumb, FormEditor },
  middleware: 'auth',

  beforeRouteLeave (to, from, next) {
    if (this.isDirty()) {
      return this.alertConfirm('Changes you made may not be saved. Are you sure want to leave?', () => {
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
    metaTitle () {
      return 'Edit ' + (this.form ? this.form.title : 'Your Form')
    }
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
    this.formInitialHash = this.hashString(JSON.stringify(this.updatedForm.data()))

    if (this.updatedForm && (!this.updatedForm.notification_settings || Array.isArray(this.updatedForm.notification_settings))) {
      this.updatedForm.notification_settings = {}
    }
  },

  methods: {
    isDirty () {
      return this.formInitialHash && this.formInitialHash !== this.hashString(JSON.stringify(this.updatedForm.data()))
    },
    hashString (str, seed = 0) {
      let h1 = 0xdeadbeef ^ seed
      let h2 = 0x41c6ce57 ^ seed
      for (let i = 0, ch; i < str.length; i++) {
        ch = str.charCodeAt(i)
        h1 = Math.imul(h1 ^ ch, 2654435761)
        h2 = Math.imul(h2 ^ ch, 1597334677)
      }
      h1 = Math.imul(h1 ^ (h1 >>> 16), 2246822507) ^ Math.imul(h2 ^ (h2 >>> 13), 3266489909)
      h2 = Math.imul(h2 ^ (h2 >>> 16), 2246822507) ^ Math.imul(h1 ^ (h1 >>> 13), 3266489909)
      return 4294967296 * (2097151 & h2) + (h1 >>> 0)
    }
  }
}
</script>
