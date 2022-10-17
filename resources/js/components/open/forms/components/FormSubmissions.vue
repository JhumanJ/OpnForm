<template>
  <div
    class="my-4 w-full mx-auto">
    <h3 class="font-semibold mb-4">
      Form Submissions 
      <span v-if="form && !isLoading && tableData.length > 0" class="text-right text-xs uppercase mb-2"> - <a :href="exportUrl" target="_blank">Export as CSV</a></span>
      <span v-if="form && !isLoading && formInitDone" class="float-right text-xs uppercase mb-2"> <a href="javascript:void(0);" @click="showColumnsModal=true">Display columns</a></span>
    </h3>

    <!--  Table columns modal  -->
    <modal :show="showColumnsModal" @close="showColumnsModal=false">
      <div class="-m-6">
        <div class="px-6 py-3">
          <h2 class="text-nt-blue text-3xl font-bold">
            Display columns
          </h2>
        </div>
        <div class="border-t py-4 px-6">
          <template v-if="properties.length > 0">
            <h4 class="font-bold mb-2">Form Fields</h4>
            <div v-for="field in properties" :key="field.id" class="p-2 border">
              {{ field.name }}
              <v-switch v-model="displayColumns[field.id]" @input="onChangeDisplayColumns" class="float-right" />
            </div>
          </template>
          <template v-if="removed_properties.length > 0">
            <h4 class="font-bold mb-2 mt-4">Removed Fields</h4>
            <div v-for="field in removed_properties" :key="field.id" class="p-2 border">
              {{ field.name }}
              <v-switch v-model="displayColumns[field.id]" @input="onChangeDisplayColumns" class="float-right" />
            </div>
          </template>
        </div>
        <div class="flex justify-end mt-4 pb-5 px-6">
          <v-button color="gray" shade="light" @click="showColumnsModal=false">Close</v-button>
        </div>
      </div>
    </modal>

    <loader v-if="!form || isLoading" class="h-6 w-6 text-nt-blue mx-auto"/>
    <div v-else>
      <scroll-shadow
        ref="shadows"
        class="border max-h-full h-full notion-database-renderer"
        :shadow-top-offset="0"
        :hide-scrollbar="true"
      >
        <open-table
          ref="table"
          class="max-h-full"
          :data="tableData"
          :loading="isLoading"
          @resize="dataChanged()"
        >
        </open-table>
      </scroll-shadow>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import ScrollShadow from '../../../common/ScrollShadow'
import OpenTable from '../../tables/OpenTable'
import clonedeep from "clone-deep";
import VSwitch from '../../../forms/components/VSwitch'

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
    }
  },
  mounted() {
    this.initFormStructure()
    this.getSubmissionsData()
  },
  watch: {
    form () {
      if(!this.form){
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
    }
  },
  methods: {
    initFormStructure() {
      if (!this.form || this.formInitDone) {
        return
      }

      // Add a "created at" column
      const columns = clonedeep(this.form.properties)
      columns.push({
        "name": "Created at",
        "id": "created_at",
        "type": "date",
        "width": 140,
      })
      this.$set(this.form, 'properties', columns)
      this.formInitDone = true

      this.properties = clonedeep(this.form.properties)
      this.removed_properties = clonedeep(this.form.removed_properties)

      // Get display columns from local storage
      const tmpColumns = window.localStorage.getItem('display-columns-formid-'+this.form.id)
      if(tmpColumns !== null && tmpColumns){
        this.displayColumns = JSON.parse(tmpColumns)
        this.onChangeDisplayColumns()
      }else{
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
        const resData = response.data;

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
      this.$refs.shadows.toggleShadow()
      this.$refs.shadows.calcDimensions()
    },
    onChangeDisplayColumns(){
      window.localStorage.setItem('display-columns-formid-'+this.form.id, JSON.stringify(this.displayColumns))
      const final_properties = this.properties.concat(this.removed_properties).filter((field) => {
        return this.displayColumns[field.id] === true
      })
      this.$set(this.form, 'properties', final_properties)
    }
  },
}
</script>
