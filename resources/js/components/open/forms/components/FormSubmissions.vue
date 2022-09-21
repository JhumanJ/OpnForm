<template>
  <div
    class="my-4 w-full mx-auto">
    <h3 class="font-semibold mb-4">
      Form Submissions <span v-if="form && !isLoading && tableData.length > 0"
                             class="text-right text-xs uppercase mb-2">
      - <a :href="exportUrl" target="_blank">Export as CSV</a>
    </span>
    </h3>
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

export default {
  name: 'FormSubmissions',
  components: {ScrollShadow, OpenTable},
  props: {},
  data() {
    return {
      formInitDone: false,
      isLoading: false,
      tableData: [],
      currentPage: 1,
      fullyLoaded: false,
    }
  },
  mounted() {
    this.initFormStructure()
    this.getSubmissionsData()
  },
  watch: {
    form () {
      debugger
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
    tableStructure() {
      if (!this.form) {
        return []
      }
      let tmp = this.form.properties.filter(property => !property.hasOwnProperty('hidden') || !property.hidden)
      tmp.push({
        "name": "Create Date",
        "id": "create_date",
        "type": "date"
      });
      return tmp
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
  },
}
</script>
