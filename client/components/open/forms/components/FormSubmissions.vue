<template>
  <div
    id="table-page"
    class="w-full flex flex-col"
  >
    <div class="w-full md:w-4/5 lg:w-3/5 md:mx-auto md:max-w-4xl px-4 pt-4">
      <h3 class="font-semibold mb-4 text-xl">
        Form Submissions
      </h3>

      <!--  Table columns modal  -->
      <modal
        :show="showColumnsModal"
        @close="showColumnsModal=false"
      >
        <template #icon>
          <svg
            class="w-8 h-8"
            viewBox="0 0 24 24"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              d="M16 5H8C4.13401 5 1 8.13401 1 12C1 15.866 4.13401 19 8 19H16C19.866 19 23 15.866 23 12C23 8.13401 19.866 5 16 5Z"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            />
            <path
              d="M8 15C9.65685 15 11 13.6569 11 12C11 10.3431 9.65685 9 8 9C6.34315 9 5 10.3431 5 12C5 13.6569 6.34315 15 8 15Z"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            />
          </svg>
        </template>
        <template #title>
          Manage Columns
        </template>

        <div class="px-4">
          <template
            v-for="(section, sectionIndex) in sections"
            :key="sectionIndex"
          >
            <template v-if="section.fields.length > 0">
              <h4
                class="font-bold mb-2"
                :class="{ 'mt-4': sectionIndex > 0 }"
              >
                {{ section.title }}
              </h4>
              <div class="border border-gray-300">
                <div class="grid grid-cols-[1fr,auto,auto] gap-4 px-4 py-2 bg-gray-50 border-b border-gray-300">
                  <div class="font-bold text-sm">
                    Field Name
                  </div>
                  <div class="font-bold text-sm text-center w-20">
                    Display
                  </div>
                  <div class="font-bold text-sm text-center w-20">
                    Wrap Text
                  </div>
                </div>
                <div
                  v-for="(field, index) in section.fields"
                  :key="field.id"
                  class="grid grid-cols-[1fr,auto,auto] gap-4 px-4 py-2 items-center"
                  :class="{ 'border-t border-gray-300': index !== 0 }"
                >
                  <p class="truncate text-sm">
                    {{ field.name }}
                  </p>
                  <div class="flex justify-center w-20">
                    <ToggleSwitchInput
                      v-model="displayColumns[field.id]"
                      wrapper-class="mb-0"
                      label=""
                      :name="`display-${field.id}`"
                      @update:model-value="onChangeDisplayColumns"
                    />
                  </div>
                  <div class="flex justify-center w-20">
                    <ToggleSwitchInput
                      v-model="wrapColumns[field.id]"
                      wrapper-class="mb-0"
                      label=""
                      :name="`wrap-${field.id}`"
                    />
                  </div>
                </div>
              </div>
            </template>
          </template>
        </div>
      </modal>

      <Loader
        v-if="!form"
        class="h-6 w-6 text-nt-blue mx-auto"
      />
      <div v-else>
        <div
          v-if="form && tableData.length > 0"
          class="flex flex-wrap items-end"
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
          <div class="font-semibold flex gap-4">
            <p class="float-right text-xs uppercase mb-2">
              <a
                href="javascript:void(0);"
                class="text-gray-500"
                @click="showColumnsModal=true"
              >Display columns</a>
            </p>
            <p
              v-if="!exportLoading"
              class="text-right cursor-pointer text-xs uppercase"
            >
              <a
                href="#"
                @click.prevent="downloadAsCsv"
              >Export as CSV</a>
            </p>
            <p v-else>
              <loader class="w-3 h-3 text-blue-500" />
            </p>
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

export default {
  name: 'FormSubmissions',
  components: {OpenTable},
  props: {},

  setup() {
    const workingFormStore = useWorkingFormStore()
    const recordStore = useRecordsStore()
    return {
      workingFormStore,
      recordStore,
      form: storeToRefs(workingFormStore).content,
      tableData: storeToRefs(recordStore).getAll,
      runtimeConfig: useRuntimeConfig(),
      slug: useRoute().params.slug
    }
  },

  data() {
    return {
      currentPage: 1,
      fullyLoaded: false,
      showColumnsModal: false,
      properties: [],
      removed_properties: [],
      displayColumns: {},
      wrapColumns: {},
      exportLoading: false,
      searchForm: useForm({
        search: ''
      }),
    }
  },
  computed: {
    parentPage() {
      if (import.meta.server) {
        return null
      }
      return window
    },
    candidatesProperties() {
      return clonedeep(this.form.properties).filter((field) => {
        return !['nf-text', 'nf-code', 'nf-page-break', 'nf-divider', 'nf-image'].includes(field.type)
      })
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

      const filteredData = clonedeep(this.tableData)

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
    sections() {
      return [
        {
          title: 'Form Fields',
          fields: this.candidatesProperties
        },
        {
          title: 'Removed Fields',
          fields: this.removed_properties
        }
      ]
    }
  },
  watch: {
    'form.id'() {
      this.onFormChange()
    },
    'searchForm.search'() {
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
      this.initFormStructure()
      this.getSubmissionsData()
    },
    initFormStructure() {
      // check if form properties already has a created_at column
      if (!this.properties.find((property) => {
        if (property.id === 'created_at') {
          return true
        }
      })) {
        // Add a "created at" column
        this.candidatesProperties.push({
          name: 'Created at',
          id: 'created_at',
          type: 'date',
          width: 140
        })
      }
      this.properties = this.candidatesProperties
      this.removed_properties = (this.form.removed_properties) ? clonedeep(this.form.removed_properties) : []
      // Get display columns from local storage
      const tmpColumns = window.localStorage.getItem('display-columns-formid-' + this.form.id)
      if (tmpColumns !== null && tmpColumns) {
        this.displayColumns = JSON.parse(tmpColumns)
        this.onChangeDisplayColumns()
      } else {
        this.properties.forEach((field) => {
          this.displayColumns[field.id] = true
        })
      }
    },
    getSubmissionsData() {
      if (this.fullyLoaded) {
        return
      }
      this.recordStore.startLoading()
      opnFetch('/open/forms/' + this.form.id + '/submissions?page=' + this.currentPage).then((resData) => {
        this.recordStore.save(resData.data.map((record) => record.data))
        this.dataChanged()
        if (this.currentPage < resData.meta.last_page) {
          this.currentPage += 1
          this.getSubmissionsData()
        } else {
          this.recordStore.stopLoading()
          this.fullyLoaded = true
        }
      }).catch(() => {
        this.recordStore.startLoading()
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
    },
    onChangeDisplayColumns() {
      if (!import.meta.client) return
      window.localStorage.setItem('display-columns-formid-' + this.form.id, JSON.stringify(this.displayColumns))
      this.properties = clonedeep(this.candidatesProperties).concat(this.removed_properties).filter((field) => {
        return this.displayColumns[field.id] === true
      })
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
