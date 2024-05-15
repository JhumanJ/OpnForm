<template>
  <div 
    class="main-layout min-h-screen flex flex-col"
    :class="{ 'bg-transparent': transparentBackground, 'bg-white dark:bg-notion-dark': !transparentBackground }"
  >
    <Navbar />

    <slot class="flex-grow" />
  </div>
</template>
<script>
import { computed } from "vue"
import { useAppStore } from "~/stores/app"

export default {
  name: 'default',

  props: {},

  setup() {
    const appStore = useAppStore()
    return {
      transparentBackground: computed(() => appStore.transparentBackground),
    }
  },
  mounted(){
    console.log('mounted',this.transparentBackground)
  }
}
</script>

