<template>
  <div :style="{
    width: `${percent}%`,
    height: height,
    opacity: show ? 1 : 0,
    'background-color': canSuccess ? color : failedColor
  }" class="progress"
  />
</template>

<script>
// https://github.com/nuxt/nuxt.js/blob/master/lib/app/components/nuxt-loading.vue
import { computed } from 'vue'
import { useAppStore } from '../stores/app';

export default {
  data: () => ({
    height: '2px',
    color: '#77b6ff',
    failedColor: 'red'
  }),

  setup () {
    const appStore = useAppStore()
    return {
      percent : computed(() => appStore.loader.percent),
      canSuccess : computed(() => appStore.loader.canSuccess),
      show : computed(() => appStore.loader.show)
    }
  }
}
</script>

<style scoped>
.progress {
  position: fixed;
  top: 0px;
  left: 0px;
  right: 0px;
  height: 2px;
  width: 0%;
  transition: width 0.2s, opacity 0.4s;
  opacity: 1;
  background-color: #efc14e;
  z-index: 999999;
}
</style>
