<template>
  <collapse class="p-4 w-full border-b" v-model="show">
    <template #title>
      <div class="flex items-center pr-8">
        <div class="mr-3" :class="{'text-blue-600':show, 'text-gray-500':!show}">
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
import Collapse from '~/components/global/Collapse.vue'
import ProTag from '~/components/global/ProTag.vue'

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
    alreadyOpened: {
      type: Boolean,
      default: false
    }
  },
  data () {
    return {
      show: this.alreadyOpened
    }
  }
}
</script>
