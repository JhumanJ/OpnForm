<template>
  <div>
    <div class="flex flex-wrap items-end mt-5">
      <h3 class="flex-grow font-medium text-lg mb-3">
        Views & Submission History
      </h3>
      <DateInput
        :form="filterForm"
        name="filter_date"
        class="flex-1 !mb-0"
        :date-range="true"
        :disable-future-dates="true"
        :disabled="!form.is_pro"
      />
    </div>
    <div
      class="border border-gray-300 rounded-lg shadow-sm p-4 mb-5 w-full mx-auto mt-4 select-all"
    >
      <div
        v-if="!form.is_pro"
        class="relative"
      >
        <div class="absolute inset-0 z-10">
          <div class="p-5 max-w-md mx-auto mt-5">
            <p class="text-center">
              You need a <pro-tag
                upgrade-modal-title="Upgrade today to access form analytics"
                class="mx-1"
              /> subscription to access your form
              analytics.
            </p>
            <p class="mt-5 text-center">
              <v-button
                class="w-full"
                @click.prevent="subscriptionModalStore.openModal()"
              >
                Subscribe
              </v-button>
            </p>
          </div>
        </div>
        <img
          src="/img/pages/forms/blurred_graph.png"
          alt="Sample Graph"
          class="mx-auto filter blur-md z-0"
        >
      </div>
      <Loader
        v-else-if="isLoading"
        class="h-6 w-6 text-nt-blue mx-auto"
      />
      <LineChart
        v-else
        :options="chartOptions"
        :data="chartData"
      />
    </div>
  </div>
</template>

<script>
import { Line as LineChart } from "vue-chartjs"
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  LinearScale,
  CategoryScale,
  PointElement,
} from "chart.js"

ChartJS.register(
  Title,
  Tooltip,
  Legend,
  LineElement,
  LinearScale,
  CategoryScale,
  PointElement,
)

export default {
  name: "FormStats",
  components: { LineChart },
  props: {
    form: {
      type: Object,
      required: true,
    },
  },
  setup() {
    const subscriptionModalStore = useSubscriptionModalStore()
    const filterForm = useForm({
      filter_date: null,
    })
    return {
      subscriptionModalStore,
      filterForm
    }
  },
  data() {
    return {
      isLoading: true,
      chartData: {
        labels: [],
        datasets: [
          {
            label: "Form Views",
            backgroundColor: "rgba(59, 130, 246, 1)",
            borderColor: "rgba(59, 130, 246, 1)",
            data: [],
          },
          {
            label: "Form Submissions",
            backgroundColor: "rgba(16, 185, 129, 1)",
            borderColor: "rgba(16, 185, 129, 1)",
            data: [],
          },
        ].concat(this.form.enable_partial_submissions ? [{
            label: "Partial Submissions",
            backgroundColor: "rgba(255, 193, 7, 1)",
            borderColor: "rgba(255, 193, 7, 1)",
            data: [],
          }] : []),
      },
      chartOptions: {
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              precision: 0,
            },
          },
        },
        responsive: true,
        maintainAspectRatio: true,
      },
    }
  },
  watch: {
    filterForm: {
      deep: true,
      handler(newVal) {
        if(newVal.filter_date && Array.isArray(newVal.filter_date) && newVal.filter_date[0] && newVal.filter_date[1]) {
          this.getChartData()
        }
      }
    }
  },
  mounted() {
    if (this.form.is_pro) {
      const toDate = new Date()
      const fromDate = new Date(toDate)
      fromDate.setDate(toDate.getDate() - 29)
      this.filterForm.filter_date = [fromDate.toISOString().split('T')[0], toDate.toISOString().split('T')[0]]
    }
  },
  methods: {
    getChartData() {
      if (!this.form || !this.form.is_pro) { return null }
      this.isLoading = true
      opnFetch(
        "/open/workspaces/" +
          this.form.workspace_id +
          "/form-stats/" +
          this.form.id,
        {
          params: {
            date_from: this.filterForm.filter_date[0] ? this.filterForm.filter_date[0].split('T')[0] : null,
            date_to: this.filterForm.filter_date[1] ? this.filterForm.filter_date[1].split('T')[0] : null,
          }
        }
      ).then((statsData) => {
        if (statsData && statsData.views !== undefined) {
          this.chartData.labels = Object.keys(statsData.views)
          this.chartData.datasets[0].data = statsData.views
          this.chartData.datasets[1].data = statsData.submissions
          if (this.form.enable_partial_submissions) {
            this.chartData.datasets[2].data = statsData.partial_submissions
          }
          this.isLoading = false
        }
      }).catch((error) => {
        this.isLoading = false
        useAlert().error(error.data.message)
      })
    },
  },
}
</script>
