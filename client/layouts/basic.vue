<template>
  <div class="basic-layout flex items-center justify-center m-0"
  :class="{'bg-transparent':transparentBackground,'bg-white dark:bg-notion-dark':!transparentBackground}">
    <slot />
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

<style lang="scss">
.basic-layout {
  color: #636b6f;
  height: 100vh;
  font-weight: 100;
  position: relative;

  .links > a {
    color: #636b6f;
    padding: 0 25px;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.1rem;
    text-decoration: none;
    text-transform: uppercase;
  }
}
</style>
