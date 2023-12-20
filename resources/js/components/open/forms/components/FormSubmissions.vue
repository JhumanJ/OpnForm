<template>
  <div
    class="my-4 w-full mx-auto"
  >
    <h3 class="font-semibold mb-4 text-xl">
      Form Submissions
    </h3>

    <!--  Table columns modal  -->
    <modal :show="showColumnsModal" @close="showColumnsModal=false">
      <template #icon>
        <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M16 5H8C4.13401 5 1 8.13401 1 12C1 15.866 4.13401 19 8 19H16C19.866 19 23 15.866 23 12C23 8.13401 19.866 5 16 5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
          <path d="M8 15C9.65685 15 11 13.6569 11 12C11 10.3431 9.65685 9 8 9C6.34315 9 5 10.3431 5 12C5 13.6569 6.34315 15 8 15Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
      </template>
      <template #title>
        Display columns
      </template>

      <div class="px-4">
        <template v-if="properties.length > 0">
          <h4 class="font-bold mb-2">
            Form Fields
          </h4>
          <div v-for="field in properties" :key="field.id" class="p-2 border">
            {{ field.name }}
            <v-switch v-model="displayColumns[field.id]" class="float-right" @update:model-value="onChangeDisplayColumns" />
          </div>
        </template>
        <template v-if="removed_properties.length > 0">
          <h4 class="font-bold mb-2 mt-4">
            Removed Fields
          </h4>
          <div v-for="field in removed_properties" :key="field.id" class="p-2 border">
            {{ field.name }}
            <v-switch v-model="displayColumns[field.id]" class="float-right" @update:model-value="onChangeDisplayColumns" />
          </div>
        </template>
      </div>
    </modal>

    <loader v-if="!form || !formInitDone" class="h-6 w-6 text-nt-blue mx-auto" />
    <div v-else>
      <div v-if="form && tableData.length > 0" class="flex flex-wrap items-end">
        <div class="flex-grow">
          <text-input class="w-64" :form="searchForm" name="search" placeholder="Search..." />
        </div>
        <div class="font-semibold flex gap-4">
          <p class="float-right text-xs uppercase mb-2">
            <a
              href="javascript:void(0);" class="text-gray-500" @click="showColumnsModal=true"
            >Display columns</a>
          </p>
          <p class="text-right text-xs uppercase">
            <a
              :href="exportUrl" target="_blank"
            >Export as CSV</a>
          </p>
        </div>
      </div>

      <scroll-shadow
        ref="shadows"
        class="border max-h-full h-full notion-database-renderer"
        :shadow-top-offset="0"
        :hide-scrollbar="true"
      >
        <open-table
          ref="table"
          class="max-h-full"
          :data="filteredData"
          :loading="isLoading"
          @resize="dataChanged()"
          @deleted="onDeleteRecord()"
        />
      </scroll-shadow>
    </div>
  </div>
</template>

<script>
import Fuse from 'fuse.js'
import Form from 'vform'
import { useWorkingFormStore } from '../../../../stores/working_form'
import ScrollShadow from '../../../common/ScrollShadow.vue'
import OpenTable from '../../tables/OpenTable.vue'
import clonedeep from 'clone-deep'
import VSwitch from '../../../forms/components/VSwitch.vue'
import { opnFetch } from '~/composables/useOpnApi.js'

export default {
  name: 'FormSubmissions',
  components: { ScrollShadow, OpenTable, VSwitch },
  props: {},

  setup () {
    const workingFormStore = useWorkingFormStore()
    return {
      workingFormStore
    }
  },

  data () {
    return {
      formInitDone: false,
      isLoading: false,
      tableData: [],
      currentPage: 1,
      fullyLoaded: false,
      showColumnsModal: false,
      properties: [],
      removed_properties: [],
      displayColumns: {},
      searchForm: new Form({
        search: ''
      })
    }
  },
  computed: {
    form: {
      get () {
        return this.workingFormStore.content
      },
      set (value) {
        this.workingFormStore.set(value)
      }
    },
    exportUrl () {
      if (!this.form) {
        return ''
      }
      return '/api/open/forms/' + this.form.id + '/submissions/export'
    },
    filteredData () {
      if (!this.tableData) return []

      const filteredData = clonedeep(this.tableData)

      if (this.searchForm.search === '' || this.searchForm.search === null) {
        return filteredData
      }

      // Fuze search
      const fuzeOptions = {
        keys: this.form.properties.map((field) => field.id)
      }
      const fuse = new Fuse(filteredData, fuzeOptions)
      return fuse.search(this.searchForm.search).map((res) => {
        return res.item
      })
    }
  },
  watch: {
    'form.id' () {
      if (this.form === null) {
        return
      }
      this.initFormStructure()
      this.getSubmissionsData()
    }
  },
  mounted () {
    this.initFormStructure()
    this.getSubmissionsData()
  },
  methods: {
    initFormStructure () {
      if (!this.form || !this.form.properties || this.formInitDone) {
        return
      }

      // check if form properties already has a created_at column
      let hasCreatedAt = false
      this.form.properties.forEach((property) => {
        if (property.id === 'created_at') {
          hasCreatedAt = true
        }
      })

      if (!hasCreatedAt) {
        // Add a "created at" column
        const columns = clonedeep(this.form.properties)
        columns.push({
          name: 'Created at',
          id: 'created_at',
          type: 'date',
          width: 140
        })
        this.form.properties = columns
      }
      this.formInitDone = true

      this.properties = clonedeep(this.form.properties)
      this.removed_properties = (this.form.removed_properties) ? clonedeep(this.form.removed_properties) : []

      // Get display columns from local storage
      const tmpColumns = window.localStorage.getItem('display-columns-formid-' + this.form.id)
      if (tmpColumns !== null && tmpColumns) {
        this.displayColumns = JSON.parse(tmpColumns)
        this.onChangeDisplayColumns()
      } else {
        this.form.properties.forEach((field) => {
          this.displayColumns[field.id] = true
        })
      }
    },
    getSubmissionsData () {
      if (!this.form || this.fullyLoaded) {
        return
      }
      this.isLoading = true
      opnFetch('/open/forms/' + this.form.id + '/submissions?page=' + this.currentPage).then((resData) => {
        this.tableData = this.tableData.concat(resData.data.map((record) => record.data))
        this.dataChanged()

        if (this.currentPage < resData.meta.last_page) {
          this.currentPage += 1
          this.getSubmissionsData()
        } else {
          this.isLoading = false
          this.fullyLoaded = true
        }
      }).catch((error) => {
        console.error(error)
        this.isLoading = false
      })
    },
    dataChanged () {
      if (this.$refs.shadows) {
        this.$refs.shadows.toggleShadow()
        this.$refs.shadows.calcDimensions()
      }
    },
    onChangeDisplayColumns () {
      window.localStorage.setItem('display-columns-formid-' + this.form.id, JSON.stringify(this.displayColumns))
      this.form.properties = this.properties.concat(this.removed_properties).filter((field) => {
        return this.displayColumns[field.id] === true
      })
    },
    onDeleteRecord () {
      this.fullyLoaded = false
      this.tableData = []
      this.getSubmissionsData()
    }
  }
}
</script>
