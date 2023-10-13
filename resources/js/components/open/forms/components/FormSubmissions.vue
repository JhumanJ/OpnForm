<template>
  <div
    class="my-4 w-full mx-auto">
    <h3 class="font-semibold mb-4 text-xl">
      Form Submissions
    </h3>

    <!--  Table columns modal  -->
    <modal :show="showColumnsModal" @close="showColumnsModal=false">
      <template #icon>
        <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M16 5H8C4.13401 5 1 8.13401 1 12C1 15.866 4.13401 19 8 19H16C19.866 19 23 15.866 23 12C23 8.13401 19.866 5 16 5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M8 15C9.65685 15 11 13.6569 11 12C11 10.3431 9.65685 9 8 9C6.34315 9 5 10.3431 5 12C5 13.6569 6.34315 15 8 15Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </template>
      <template #title>
        Display columns
      </template>

      <div class="px-4">
        <template v-if="properties.length > 0">
          <h4 class="font-bold mb-2">Form Fields</h4>
          <div v-for="field in properties" :key="field.id" class="p-2 border">
            {{ field.name }}
            <v-switch v-model="displayColumns[field.id]" @input="onChangeDisplayColumns" class="float-right"/>
          </div>
        </template>
        <template v-if="removed_properties.length > 0">
          <h4 class="font-bold mb-2 mt-4">Removed Fields</h4>
          <div v-for="field in removed_properties" :key="field.id" class="p-2 border">
            {{ field.name }}
            <v-switch v-model="displayColumns[field.id]" @input="onChangeDisplayColumns" class="float-right"/>
          </div>
        </template>
      </div>
    </modal>

    <loader v-if="!form || isLoading" class="h-6 w-6 text-nt-blue mx-auto"/>
    <div v-else>
      <div class="flex flex-wrap items-end">
        <div class="flex-grow">
          <text-input class="w-64" :form="searchForm" name="search" placeholder="Search..." />
        </div>
        <div class="font-semibold flex gap-4">
          <p v-if="form && !isLoading && formInitDone" class="float-right text-xs uppercase mb-2"> <a
            href="javascript:void(0);" class="text-gray-500" @click="showColumnsModal=true">Display columns</a></p>
          <p v-if="form && !isLoading && tableData.length > 0" class="text-right text-xs uppercase"><a
            :href="exportUrl" target="_blank">Export as CSV</a></p>
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
        >
        </open-table>
      </scroll-shadow>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import Fuse from 'fuse.js'
import Form from 'vform'
import ScrollShadow from '../../../common/ScrollShadow.vue'
import OpenTable from '../../tables/OpenTable.vue'
import clonedeep from "clone-deep";
import VSwitch from '../../../forms/components/VSwitch.vue'

export default {
  name: 'FormSubmissions',
  components: {ScrollShadow, OpenTable, VSwitch},
  props: {},
  data() {
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
  mounted() {
    this.initFormStructure()
    this.getSubmissionsData()
  },
  watch: {
    form() {
      if (this.form === null) {
        return
      }
      this.initFormStructure()
      this.getSubmissionsData()
    }
  },
  computed: {
    form: {
      get() {
        return this.$store.state['open/working_form'].content
      },
      set(value) {
        this.$store.commit('open/working_form/set', value)
      }
    },
    exportUrl() {
      if (!this.form) {
        return ''
      }
      return '/api/open/forms/' + this.form.id + '/submissions/export'
    },
    filteredData () {
      if(!this.tableData) return []

      let filteredData = clonedeep(this.tableData)

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
  methods: {
    initFormStructure() {
      if (!this.form || this.formInitDone) {
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
          "name": "Created at",
          "id": "created_at",
          "type": "date",
          "width": 140,
        })
        this.$set(this.form, 'properties', columns)
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
    getSubmissionsData() {
      if (!this.form || this.fullyLoaded) {
        return
      }
      this.isLoading = true
      axios.get('/api/open/forms/' + this.form.id + '/submissions?page=' + this.currentPage).then((response) => {
        const resData = response.data

        this.tableData = this.tableData.concat(resData.data.map((record) => record.data))

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
    dataChanged() {
      if (this.$refs.shadows) {
        this.$refs.shadows.toggleShadow()
        this.$refs.shadows.calcDimensions()
      }
    },
    onChangeDisplayColumns() {
      window.localStorage.setItem('display-columns-formid-' + this.form.id, JSON.stringify(this.displayColumns))
      const final_properties = this.properties.concat(this.removed_properties).filter((field) => {
        return this.displayColumns[field.id] === true
      })
      this.$set(this.form, 'properties', final_properties)
    },
    onDeleteRecord() {
      this.fullyLoaded = false
      this.tableData = []
      this.getSubmissionsData()
    }
  },
}
</script>
