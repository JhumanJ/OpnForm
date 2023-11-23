<template>
  <collapse class="p-4 w-full border-b" v-model="show" @input="onChangeCollapse">
    <template #title>
      <div class="flex items-center pr-8">
        <div class="text-gray-500 mr-2">
          <slot name="icon" />
        </div>
        <h3 id="v-step-2" class="font-semibold flex-grow">
          {{ name }}
        </h3>
        <pro-tag v-if="hasProTag" />
      </div>
    </template>

    <slot />
  </collapse>
</template>

<script>
import Collapse from '../../common/Collapse.vue'
import ProTag from '../../common/ProTag.vue'

export default {
  name: 'EditorOptionsPanel',
  components: { Collapse, ProTag },
  props: {
    name: {
      type: String,
      required: true
    },
    hasProTag: {
      type: Boolean,
      default: false
    },
    value: {
      type: Boolean,
      default: false
    }
  },
  data () {
    return {
      show: this.value
    }
  },
  methods: {
    onChangeCollapse () {
      this.$emit('input', this.show)
    }
  }
}
</script>
