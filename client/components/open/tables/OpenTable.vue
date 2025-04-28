<template>
  <table
    :id="'table-' + tableHash"
    ref="table"
    class="notion-table n-table whitespace-no-wrap bg-white dark:bg-notion-dark-light relative"
  >
    <thead
      :id="'table-header-' + tableHash"
      ref="header"
      class="n-table-head top-0 z-10"
      :class="{ absolute: data.length !== 0 }"
      style="will-change: transform; transform: translate3d(0px, 0px, 0px)"
    >
      <tr class="n-table-row overflow-x-hidden">
        <resizable-th
          v-for="(col) in columns"
          :id="'table-head-cell-' + col.id"
          :key="col.id"
          scope="col"
          :allow-resize="allowResize"
          :width="col.width ? col.width + 'px' : '150px'"
          class="n-table-cell p-0 relative"
          @resize-width="resizeCol(col, $event)"
        >
          <p
            class="bg-gray-50 border-r dark:bg-notion-dark truncate sticky top-0 border-b border-gray-200 dark:border-gray-800 px-4 py-2 text-gray-500 font-semibold tracking-wider uppercase text-xs"
          >
            {{ col.name }}
          </p>
        </resizable-th>
        <th
          v-if="hasStatus"
          class="n-table-cell p-0 relative"
          :class="{ 'border-r': hasActions }"
          style="width: 100px"
        >
          <p
            class="bg-gray-50 dark:bg-notion-dark truncate sticky top-0 border-b border-gray-200 dark:border-gray-800 px-4 py-2 text-gray-500 font-semibold tracking-wider uppercase text-xs"
          >
            Status
          </p>
        </th>
        <th
          v-if="hasActions"
          class="n-table-cell p-0 relative"
          style="width: 100px"
        >
          <p
            class="bg-gray-50 dark:bg-notion-dark truncate sticky top-0 border-b border-gray-200 dark:border-gray-800 px-4 py-2 text-gray-500 font-semibold tracking-wider uppercase text-xs"
          >
            Actions
          </p>
        </th>
      </tr>
    </thead>
    <tbody
      v-if="formData.length > 0"
      class="n-table-body bg-white dark:bg-notion-dark-light"
    >
      <tr
        v-if="objectHas($slots, 'actions')"
        :id="'table-actions-' + tableHash"
        ref="actions-row"
        class="action-row absolute w-full"
        style="will-change: transform; transform: translate3d(0px, 32px, 0px)"
      >
        <td
          :colspan="columns.length"
          class="p-1"
        >
          <slot name="actions" />
        </td>
      </tr>
      <tr
        v-for="(row, index) in formData"
        :key="row.id"
        class="n-table-row"
        :class="{ first: index === 0 }"
      >
        <td
          v-for="(col, colIndex) in columns"
          :key="col.id"
          :style="{ width: col.width ? col.width + 'px' : '150px' }"
          class="n-table-cell border-gray-100 dark:border-gray-900 text-sm p-2 overflow-hidden"
          :class="[
            {
              'border-b': index !== data.length - 1,
              'border-r': colIndex !== columns.length - 1 || hasActions,
              'whitespace-normal break-words': wrapColumns[col.id] === true,
            },
            colClasses(col),
          ]"
        >
          <component
            :is="fieldComponents[col.type]"
            class="border-gray-100 dark:border-gray-900"
            :property="col"
            :value="row[col.id]"
          />
        </td>
        <td
          v-if="hasStatus"
          class="n-table-cell border-gray-100 dark:border-gray-900 text-sm p-2 border-b"
          :class="{ 'border-r': hasActions }"
          style="width: 100px"
        >
          <UBadge
            :label="row.status === 'partial' ? 'In Progress' : 'Submitted'"
            :color="row.status === 'partial' ? 'yellow' : 'green'"
            variant="soft"
            size="xs"
          />
        </td>
        <td
          v-if="hasActions"
          class="n-table-cell border-gray-100 dark:border-gray-900 text-sm p-2 border-b"
          style="width: 100px"
        >
          <div class="flex justify-center">
            <record-operations
              :form="form"
              :structure="columns"
              :submission="row"
              @deleted="(submission) => $emit('deleted', submission)"
              @updated="(submission) => $emit('updated', submission)"
            />
          </div>
        </td>
      </tr>
      <tr
        v-if="loading"
        class="n-table-row border-t bg-gray-50 dark:bg-gray-900"
      >
        <td
          :colspan="columns.length"
          class="p-8 w-full"
        >
          <Loader class="w-4 h-4 mx-auto" />
        </td>
      </tr>
    </tbody>
    <tbody
      v-else
      key="body-content"
      class="n-table-body"
    >
      <tr class="n-table-row loader w-full">
        <td
          :colspan="columns.length"
          class="n-table-cell w-full p-8"
        >
          <Loader
            v-if="loading"
            class="w-4 h-4 mx-auto"
          />
          <p
            v-else
            class="text-gray-500 text-center"
          >
            No data found.
          </p>
        </td>
      </tr>
    </tbody>
  </table>
</template>

<script>
import OpenText from "./components/OpenText.vue"
import OpenUrl from "./components/OpenUrl.vue"
import OpenSelect from "./components/OpenSelect.vue"
import OpenMatrix from "./components/OpenMatrix.vue"
import OpenDate from "./components/OpenDate.vue"
import OpenFile from "./components/OpenFile.vue"
import OpenCheckbox from "./components/OpenCheckbox.vue"
import OpenPayment from "./components/OpenPayment.vue"
import ResizableTh from "./components/ResizableTh.vue"
import RecordOperations from "../components/RecordOperations.vue"
import clonedeep from "clone-deep"
import { hash } from "~/lib/utils.js"
import { default as _has } from "lodash/has"

export default {
  components: { ResizableTh, RecordOperations },
  props: {
    columns: {
      type: Array,
      default: () => [],
    },
    wrapColumns: {
      type: Object,
      default: () => {},
    },
    data: {
      type: Array,
      default: () => [],
    },
    loading: {
      type: Boolean,
      default: false,
    },
    allowResize: {
      required: false,
      default: true,
      type: Boolean,
    },
    scrollParent: {
      type: [Boolean, Object],
      default: null
    },
  },
  emits: ["updated", "deleted", "resize", "update-columns"],

  setup() {
    const workingFormStore = useWorkingFormStore()
    return {
      workingFormStore,
      form: storeToRefs(workingFormStore).content,
      user: useAuthStore().user,
      workspace: useWorkspacesStore().getCurrent,
    }
  },

  data() {
    return {
      tableHash: null,
      skip: false,
      internalColumns: [],
      rafId: null,
      fieldComponents: {
        text: shallowRef(OpenText),
        rich_text: shallowRef(OpenText),
        number: shallowRef(OpenText),
        rating: shallowRef(OpenText),
        scale: shallowRef(OpenText),
        slider: shallowRef(OpenText),
        select: shallowRef(OpenSelect),
        matrix: shallowRef(OpenMatrix),
        multi_select: shallowRef(OpenSelect),
        date: shallowRef(OpenDate),
        files: shallowRef(OpenFile),
        checkbox: shallowRef(OpenCheckbox),
        url: shallowRef(OpenUrl),
        email: shallowRef(OpenText),
        phone_number: shallowRef(OpenText),
        signature: shallowRef(OpenFile),
        payment: shallowRef(OpenPayment),
        barcode: shallowRef(OpenText),
      },
    }
  },

  computed: {
    hasActions() {
      return !this.workspace.is_readonly
    },
    hasStatus() {
      return this.form.is_pro && (this.form.enable_partial_submissions ?? false)
    },
    formData() {
      return [...this.data].sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
    }
  },

  watch: {
    columns: {
      handler() {
        this.internalColumns = clonedeep(this.columns)
        this.onStructureChange()
      },
      deep: true
    },
    data() {
      this.$nextTick(() => {
        this.handleScroll()
      })
    }
  },

  mounted() {
    this.internalColumns = clonedeep(this.columns)
    const parent = this.scrollParent ?? document.getElementById("table-page")
    this.tableHash = hash(JSON.stringify(this.form.properties))
    if (parent) {
      parent.addEventListener("scroll", this.handleScroll, { passive: false })
    }
    window.addEventListener("resize", this.handleScroll)
    this.onStructureChange()
    this.handleScroll()
  },
  beforeUnmount() {
    const parent = this.scrollParent ?? document.getElementById("table-page")
    if (parent) {
      parent.removeEventListener("scroll", this.handleScroll)
    }
    window.removeEventListener("resize", this.handleScroll)
  },

  methods: {
    colClasses(col) {
      let  colColor, colFontWeight, colWrap

      // Column align
      const colAlign = `text-${col.alignment ? col.alignment : "left"}`

      // Column color
      colColor = null
      if (!_has(col, "color") || col.color === "default") {
        colColor = "text-gray-700 dark:text-gray-300"
      }
      colColor = `text-${col.color}`

      // Column font weight
      if (_has(col, "bold") && col.bold) {
        colFontWeight = "font-semibold"
      }

      // Column wrapping
      if (!_has(col, "wrap_text") || !col.wrap_text) {
        colWrap = "truncate"
      }

      return [colAlign, colColor, colWrap, colFontWeight]
    },
    onStructureChange() {
      if (this.internalColumns) {
        this.$nextTick(() => {
          this.internalColumns.forEach((col) => {
            if (!_has(col, "width")) {
              if (
                this.allowResize &&
                this.internalColumns.length &&
                document.getElementById("table-head-cell-" + col.id)
              ) {
                // Within editor
                this.resizeCol(
                  col,
                  document.getElementById("table-head-cell-" + col.id)
                    .offsetWidth,
                )
              }
            }
          })
        })
      }
    },
    resizeCol(col, width) {
      if (!this.form) return
      const index = this.internalColumns.findIndex((c) => c.id === col.id)
      this.internalColumns[index].width = width
      this.setColumns(this.internalColumns)
      this.$nextTick(() => {
        this.$emit("resize")
      })
    },
    handleScroll() {
      if (this.rafId) {
        cancelAnimationFrame(this.rafId)
      }

      this.rafId = requestAnimationFrame(() => {
        const table = this.$refs.table
        const tableHeader = document.getElementById(
          "table-header-" + this.tableHash,
        )
        const tableActionsRow = document.getElementById(
          "table-actions-" + this.tableHash,
        )

        if (!table || !tableHeader) return

        const scrollTop =
          window.pageYOffset || document.documentElement.scrollTop
        const tableRect = table.getBoundingClientRect()

        // The starting point of the table relative to the viewport
        const tableStart = tableRect.top + scrollTop
        // The end point of the table relative to the viewport
        const tableEnd = tableStart + tableRect.height

        let headerY = scrollTop - tableStart
        let actionsY = scrollTop + window.innerHeight - tableEnd

        if (headerY < 0) headerY = 0
        if (scrollTop + window.innerHeight > tableEnd) {
          actionsY =
            tableRect.height - (scrollTop + window.innerHeight - tableEnd)
        } else {
          actionsY = tableRect.height
        }

        if (tableHeader) {
          tableHeader.style.transform = `translate3d(0px, ${headerY}px, 0px)`
        }

        if (tableActionsRow) {
          tableActionsRow.style.transform = `translate3d(0px, ${actionsY}px, 0px)`
        }
      })
    },
    setColumns(val) {
      this.$emit("update-columns", val)
    },
    objectHas(object, key) {
      return _has(object, key)
    },
  },
}
</script>

<style lang="scss">
.n-table {
  .n-table-head {
    height: 33px;

    .resize-handler {
      height: 33px;
      width: 5px;
      margin-left: -3px;
    }
  }

  .n-table-row {
    display: flex;

    &.first,
    &.loader {
      margin-top: 33px;
    }
  }

  .n-table-cell {
    min-width: 80px;
  }
}
</style>
