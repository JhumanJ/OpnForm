<template>
  <div
    id="table-page"
    class="w-full flex flex-col"
  >
    <div class="w-full md:w-4/5 lg:w-3/5 md:mx-auto md:max-w-4xl px-4 pt-4">
      <h3 class="font-semibold mb-4 text-xl">
        Form Submissions
      </h3>

      <!-- Settings Modal -->
      <form-columns-settings-modal
        v-if="form"
        :show="showColumnsModal"
        :form="form"
        :columns="properties"
        :display-columns="displayColumns"
        :wrap-columns="wrapColumns"
        @update:display-columns="displayColumns = $event"
        @update:wrap-columns="wrapColumns = $event"
        @close="showColumnsModal = false"
        @update:columns="onColumnUpdated"
      />

      <Loader
        v-if="!form"
        class="h-6 w-6 text-nt-blue mx-auto"
      />
      <div v-else>
        <div
          v-if="form && tableData.length > 0"
          class="flex flex-wrap items-center mb-4"
        >
          <div class="flex-grow">
            <VForm size="sm">
              <text-input
                class="w-64"
                :form="searchForm"
                name="search"
                placeholder="Search..."
              />
            </VForm>
          </div>
          <div class="font-semibold flex gap-2">
            <USelectMenu
              class="w-32"
              v-if="form.enable_partial_submissions"
              v-model="selectedStatus"
              :options="statusList"
              value-attribute="value"
              option-attribute="label"
            />
            <UButton
              size="sm"
              color="white"
              icon="heroicons:adjustments-horizontal"
              label="Edit columns"
              @click="showColumnsModal=true"
            />
            <UButton
              size="sm"
              color="white"
              icon="heroicons:arrow-down-tray"
              label="Export"
              :loading="exportLoading"
              @click="downloadAsCsv"
            />
          </div>
        </div>
      </div>
    </div>
    <div class="px-4 pb-4 flex justify-center">
      <scroll-shadow
        ref="shadows"
        class="border h-full notion-database-renderer"
        :shadow-top-offset="0"
        :hide-scrollbar="true"
      >
        <open-table
          v-if="form"
          ref="table"
          class="max-h-full"
          :columns="properties"
          :wrap-columns="wrapColumns"
          :data="filteredData"
          :loading="isLoading"
          :scroll-parent="parentPage"
          @resize="dataChanged()"
          @deleted="onDeleteRecord"
          @updated="(submission)=>onUpdateRecord(submission)"
          @update-columns="onColumnUpdated"
        />
      </scroll-shadow>
    </div>
  </div>
</template>

<script>
import Fuse from 'fuse.js'
import clonedeep from 'clone-deep'
import OpenTable from '../../tables/OpenTable.vue'
import FormColumnsSettingsModal from './FormColumnsSettingsModal.vue'

export default {
  name: 'FormSubmissions',
  components: { OpenTable, FormColumnsSettingsModal },
  props: {},

  setup() {
    const workingFormStore = useWorkingFormStore()
    const recordStore = useRecordsStore()
    const form = storeToRefs(workingFormStore).content
    const tableData = storeToRefs(recordStore).getAll

    return {
      workingFormStore,
      recordStore,
      form,
      tableData,
      runtimeConfig: useRuntimeConfig(),
      slug: useRoute().params.slug
    }
  },

  data() {
    return {
      fullyLoaded: false,
      showColumnsModal: false,
      properties: [],
      exportLoading: false,
      searchForm: useForm({
        search: ''
      }),
      displayColumns: {},
      wrapColumns: {},
      statusList: [
        { label: 'All', value: 'all' },
        { label: 'Submitted', value: 'completed' },
        { label: 'In Progress', value: 'partial' }
      ],
      selectedStatus: 'all',
    }
  },

  computed: {
    parentPage() {
      if (import.meta.server) {
        return null
      }
      return window
    },
    exportUrl() {
      if (!this.form) {
        return ''
      }
      return this.runtimeConfig.public.apiBase + '/open/forms/' + this.form.id + '/submissions/export'
    },
    isLoading() {
      return this.recordStore.loading
    },
    filteredData() {
      if (!this.tableData) return []

      let filteredData = clonedeep(this.tableData)

      if (this.selectedStatus !== 'all') {
        filteredData = filteredData.filter((submission) => submission.status === this.selectedStatus)
      }

      if (this.searchForm.search === '' || this.searchForm.search === null) {
        return filteredData
      }

      // Fuze search
      const fuzeOptions = {
        keys: this.properties.map((field) => field.id)
      }
      const fuse = new Fuse(filteredData, fuzeOptions)
      return fuse.search(this.searchForm.search).map((res) => {
        return res.item
      })
    },
  },

  watch: {
    'form.id'() {
      this.onFormChange()
    },
    'searchForm.search'() {
      this.dataChanged()
    },
    'selectedStatus'() {
      this.dataChanged()
    }
  },

  mounted() {
    this.onFormChange()
  },

  methods: {
    onFormChange() {
      if (this.form === null || this.form.slug !== this.slug) {
        return
      }
      this.fullyLoaded = false
      this.getSubmissionsData()
    },
    getSubmissionsData() {
      if (this.fullyLoaded) {
        return
      }
      this.recordStore.startLoading()
      
      opnFetch('/open/forms/' + this.form.id + '/submissions?page=1').then((firstResponse) => {
        this.recordStore.save(firstResponse.data.map((record) => record.data))
        
        const lastPage = firstResponse.meta.last_page
        
        if (lastPage > 1) {
          // Create an array of promises for remaining pages
          const remainingPages = Array.from({ length: lastPage - 1 }, (_, i) => {
            const page = i + 2 // Start from page 2
            return opnFetch('/open/forms/' + this.form.id + '/submissions?page=' + page)
          })
          
          // Fetch all remaining pages in parallel
          return Promise.all(remainingPages)
        }
        return []
      }).then(responses => {
        // Save all responses data
        responses.forEach(response => {
          this.recordStore.save(response.data.map((record) => record.data))
        })
        
        this.fullyLoaded = true
        this.recordStore.stopLoading()
        this.dataChanged()
      }).catch(() => {
        this.recordStore.stopLoading()
      })
    },
    dataChanged() {
      if (this.$refs.shadows) {
        this.$refs.shadows.toggleShadow()
        this.$refs.shadows.calcDimensions()
      }
    },
    onColumnUpdated(columns) {
      this.properties = columns
      this.dataChanged()
    },
    onUpdateRecord(submission) {
      this.recordStore.save(submission)
      this.dataChanged()
    },
    onDeleteRecord(submission) {
      this.recordStore.remove(submission)
      this.dataChanged()
    },
    downloadAsCsv() {
      if (this.exportLoading) {
        return
      }
      this.exportLoading = true
      opnFetch(this.exportUrl, {
        responseType: "blob",
        method: "POST",
        body: {
          columns: this.displayColumns
        }
      }).then(blob => {
        const filename = `${this.form.slug}-${Date.now()}-submissions.csv`
        const a = document.createElement("a")
        document.body.appendChild(a)
        a.style = "display: none"
        const url = window.URL.createObjectURL(blob)
        a.href = url
        a.download = filename
        a.click()
        window.URL.revokeObjectURL(url)
      }).catch((error) => {
        console.error(error)
      }).finally(() => {
        this.exportLoading = false
      })
    }
  }
}
</script>
