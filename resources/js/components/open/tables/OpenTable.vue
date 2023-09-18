<template>
  <table :id="'table-'+tableHash" ref="table"
         class="notion-table n-table whitespace-no-wrap bg-white dark:bg-notion-dark-light relative"
  >
    <thead :id="'table-header-'+tableHash" ref="header"
           class="n-table-head top-0"
           :class="{'absolute': data.length !== 0}"
           style="will-change: transform; transform: translate3d(0px, 0px, 0px)"
    >
    <tr class="n-table-row overflow-x-hidden">
      <resizable-th v-for="col, index in form.properties" :id="'table-head-cell-' + col.id" :key="col.id"
                    scope="col" :allow-resize="allowResize" :width="(col.cell_width ? col.cell_width + 'px':'auto')"
                    class="n-table-cell p-0 relative"
                    @resize-width="resizeCol(col, $event)"
      >
        <p
          :class="{'border-r': index < form.properties.length - 1 || hasActions}"
          class="bg-gray-50 dark:bg-notion-dark truncate sticky top-0 border-b border-gray-200 dark:border-gray-800 px-4 py-2 text-gray-500 font-semibold tracking-wider uppercase text-xs"
        >
          {{ col.name }}
        </p>
      </resizable-th>
      <th v-if="hasActions" class="n-table-cell p-0 relative" style="width: 100px">
        <p
          class="bg-gray-50 dark:bg-notion-dark truncate sticky top-0 border-b border-gray-200 dark:border-gray-800 px-4 py-2 text-gray-500 font-semibold tracking-wider uppercase text-xs">
          Actions
        </p>
      </th>
    </tr>
    </thead>
    <tbody v-if="data.length > 0" class="n-table-body bg-white dark:bg-notion-dark-light">
    <tr v-if="$slots.hasOwnProperty('actions')"
        :id="'table-actions-'+tableHash"
        ref="actions-row"
        class="action-row absolute w-full"
        style="will-change: transform; transform: translate3d(0px, 32px, 0px)"
    >
      <td :colspan="form.properties.length" class="p-1">
        <slot name="actions"/>
      </td>
    </tr>
    <tr v-for="row, index in data" :key="index" class="n-table-row" :class="{'first':index===0}">
      <td v-for="col, colIndex in form.properties"
          :key="col.id"
          :style="{width: col.cell_width + 'px'}"
          class="n-table-cell border-gray-100 dark:border-gray-900 text-sm p-2 overflow-hidden"
          :class="[{'border-b': index !== data.length - 1, 'border-r': colIndex !== form.properties.length - 1 || hasActions},
                     colClasses(col)]"
      >
        <component :is="fieldComponents[col.type]" class="border-gray-100 dark:border-gray-900"
                   :property="col" :value="row[col.id]"
        />
      </td>
      <td v-if="hasActions" class="n-table-cell border-gray-100 dark:border-gray-900 text-sm p-2 border-b"
          style="width: 100px"
      >
        <record-operations :form="form" :structure="form.properties" :rowid="row.id" @deleted="$emit('deleted')" />
      </td>
    </tr>
    <tr v-if="loading" class="n-table-row border-t bg-gray-50 dark:bg-gray-900">
      <td :colspan="form.properties.length" class="p-8 w-full">
        <loader class="w-4 h-4 mx-auto"/>
      </td>
    </tr>
    </tbody>
    <tbody v-else key="body-content" class="n-table-body">
    <tr class="n-table-row loader w-full">
      <td :colspan="form.properties.length" class="n-table-cell w-full p-8">
        <loader v-if="loading" class="w-4 h-4 mx-auto"/>
        <p v-else class="text-gray-500 text-center">
          No data found.
        </p>
      </td>
    </tr>
    </tbody>
  </table>
</template>

<script>
import OpenText from './components/OpenText.vue'
import OpenUrl from './components/OpenUrl.vue'
import OpenSelect from './components/OpenSelect.vue'
import OpenDate from './components/OpenDate.vue'
import OpenFile from './components/OpenFile.vue'
import OpenCheckbox from './components/OpenCheckbox.vue'
import ResizableTh from './components/ResizableTh.vue'
import RecordOperations from '../components/RecordOperations.vue'
import clonedeep from 'clone-deep'

const cyrb53 = function (str, seed = 0) {
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

export default {
  components: {ResizableTh, RecordOperations},
  props: {
    data: {
      type: Array,
      default: () => []
    },
    loading: {
      type: Boolean,
      default: false
    },
    allowResize: {
      required: false,
      default: true,
      type: Boolean
    },
  },

  data() {
    return {
      tableHash: null,
      skip: false
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
    hasActions() {
      // In future if want to hide based on condition
      return true
    },
    fieldComponents() {
      return {
        text: OpenText,
        number: OpenText,
        select: OpenSelect,
        multi_select: OpenSelect,
        date: OpenDate,
        files: OpenFile,
        checkbox: OpenCheckbox,
        url: OpenUrl,
        email: OpenText,
        phone_number: OpenText,
        signature: OpenFile,
      }
    },
  },

  watch: {
    'form.properties': {
      handler() {
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
    const parent = document.getElementById('table-page')
    this.tableHash = cyrb53(JSON.stringify(this.form.properties))
    parent.addEventListener('scroll', this.handleScroll, {passive: true})
    window.addEventListener('resize', this.handleScroll)
    this.onStructureChange()
    this.handleScroll()
  },

  beforeDestroy() {
    const parent = document.getElementById('table-page')
    if (parent) {
      parent.removeEventListener('scroll', this.handleScroll)
    }
    window.removeEventListener('resize', this.handleScroll)
  },

  methods: {
    colClasses(col) {
      let colAlign, colColor, colFontWeight, colWrap

      // Column align
      colAlign = `text-${col.alignment ? col.alignment : 'left'}`

      // Column color
      colColor = null
      if (!col.hasOwnProperty('color') || col.color === 'default') {
        colColor = 'text-gray-700 dark:text-gray-300'
      }
      colColor = `text-${col.color}`

      // Column font weight
      if (col.hasOwnProperty('bold') && col.bold) {
        colFontWeight = 'font-semibold'
      }

      // Column wrapping
      if (!col.hasOwnProperty('wrap_text') || !col.wrap_text) {
        colWrap = 'truncate'
      }

      return [colAlign, colColor, colWrap, colFontWeight]
    },
    onStructureChange() {
      if (this.form && this.form.properties) {
        this.$nextTick(() => {
          this.form.properties.forEach(col => {
            if (!col.hasOwnProperty('cell_width')) {
              if (this.allowResize && this.form !== null && document.getElementById('table-head-cell-' + col.id)) {
                // Within editor
                this.resizeCol(col, document.getElementById('table-head-cell-' + col.id).offsetWidth)
              }
            }
          })
        })
      }
    },
    resizeCol(col, width) {
      if (!this.form) return
      const columns = clonedeep(this.form.properties)
      const index = this.form.properties.findIndex(c => c.id === col.id)
      columns[index].cell_width = width
      this.$set(this.form, 'properties', columns)
      this.$nextTick(() => {
        this.$emit('resize')
      })
    },
    handleScroll() {
      const parent = document.getElementById('table-page')
      const posTop = parent.getBoundingClientRect().top
      const tablePosition = Math.max(0, posTop - this.$refs.table.getBoundingClientRect().top)
      const tableHeader = document.getElementById('table-header-' + this.tableHash)

      // Set position of table header
      if (tableHeader) {
        tableHeader.style.transform = `translate3d(0px, ${tablePosition}px, 0px)`
        if (tablePosition > 0) {
          tableHeader.classList.add('border-t')
        } else {
          tableHeader.classList.remove('border-t')
        }
      }

      // Set position of actions row
      if (this.$slots.hasOwnProperty('actions')) {
        const tableActionsRow = document.getElementById('table-actions-' + this.tableHash)
        if (tableActionsRow) {
          if (tablePosition > 100) {
            tableActionsRow.style.transform = `translate3d(0px, ${tablePosition + 33}px, 0px)`
          } else {
            const parentContainer = document.getElementById('table-page')
            tableActionsRow.style.transform = `translate3d(0px, ${parentContainer.offsetHeight + (posTop - this.$refs.table.getBoundingClientRect().top) - 35}px, 0px)`
          }
        }
      }
    },
  }
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

    &.first, &.loader {
      margin-top: 33px;
    }
  }

  .n-table-cell {
    min-width: 80px;
  }
}

.notion-table {

  td {
    &.text-gray {
      color: #787774;
    }

    &.text-brown {
      color: #9f6b53;
    }

    &.text-orange {
      color: #d9730d;
    }

    &.text-yellow {
      color: #cb912f;
    }

    &.text-green {
      color: #448361;
    }

    &.text-blue {
      color: #337ea9;
    }

    &.text-purple {
      color: #9065b0;
    }

    &.text-pink {
      color: #c14c8a;
    }

    &.text-red {
      color: #d44c47;
    }
  }
}

.dark {
  .notion-table {
    td {
      &.text-gray {
        color: #9b9b9b;
      }

      &.text-brown {
        color: #ba856f;
      }

      &.text-orange {
        color: #c77d48;
      }

      &.text-yellow {
        color: #ca9849;
      }

      &.text-green {
        color: #529e72;
      }

      &.text-blue {
        color: #5e87c9;
      }

      &.text-purple {
        color: #9d68d3;
      }

      &.text-pink {
        color: #d15796;
      }

      &.text-red {
        color: #df5452;
      }
    }
  }
}
</style>
