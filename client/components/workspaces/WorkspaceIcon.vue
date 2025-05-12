<template>
  <img
    v-if="isUrl(workspace.icon)"
    :src="workspace.icon"
    :alt="`${workspace.name} icon`"
    class="flex-shrink-0 rounded"
    :class="size"
  >
  <div
    v-else
    :class="size"
    class="rounded text-xs truncate bg-neutral-100 text-center flex items-center justify-center"
  >
    <p
      class="font-semibold text-neutral-500"
      v-text="workspace.icon"
    />
  </div>
</template>

<script>
export default {
  name: 'WorkspaceIcon',
  components: {},
  props: {
    workspace: {
      type: Object,
      required: true,
    },
    size: {
      type: String,
      default: 'h-6 w-6',
    },
  },
  methods: {
    isUrl(str) {
      try {
        new URL(str)
      }
      catch {
        return false
      }
      return true
    },
  },
}
</script>
