<template>
  <modal
    :show="show"
    :compact-header="true"
    @close="$emit('close')"
  >
    <template #icon>
      <Icon
        name="ci:font"
        class="w-10 h-10 text-blue"
      />
    </template>
    <template #title>
      Google fonts
    </template>
    
    <div v-if="loading">
      <Loader class="h-6 w-6 text-nt-blue mx-auto" />
    </div>
    <div v-else>
      <text-input
        v-model="search"
        class="px-4"
        name="search"
        placeholder="Search fonts"
      />

      <div
        ref="scrollContainer"
        class="grid grid-cols-3 gap-2 p-5 mb-5 overflow-y-scroll max-h-[24rem]"
      >
        <div
          v-for="(fontName, index) in enrichedFonts"
          :key="fontName"
          :ref="el => setFontRef(el, index)"
          class="flex flex-col p-3 rounded-md shadow border-gray-200 border-[0.5px] justify-between w-full cursor-pointer hover:ring ring-blue-300"
          :class="{'ring': selectedFont === fontName }"
          @click="selectedFont=fontName"
        >
          <link
            v-if="visible[index]"
            :href="getFontUrl(fontName)"
            rel="stylesheet"
          >
          <div
            v-if="visible[index]"
            class="text-lg mb-3 font-normal"
            :style="{ 'font-family': `${fontName} !important` }"
          >
            The quick brown fox jumped over the lazy dog
          </div>
          <div class="text-gray-400 flex justify-between">
            <div>{{ fontName }}</div>
            <Icon
              v-if="selectedFont === fontName"
              name="heroicons:check-circle-16-solid"
              class="w-5 h-5 text-nt-blue"
            />
          </div>
        </div>
      </div>

      <div class="flex">
        <UButton
          size="md"
          color="white"
          class="mr-2"
          @click="$emit('apply', null)"
        >
          Reset
        </UButton>
        <UButton
          size="md"
          :disabled="!selectedFont"
          class="flex-grow"
          @click="$emit('apply', selectedFont)"
        >
          Apply
        </UButton>
      </div>
    </div>
  </modal>
</template>

<script setup>
import { defineEmits } from "vue"
import Fuse from "fuse.js"
import {refDebounced, useElementVisibility} from "@vueuse/core"

const props = defineProps({
  show: {
    type: Boolean,
    default: false,
  },
  font: {
    type: String,
    default: null,
  },
})

const emit = defineEmits(['close','apply'])
const loading = ref(false)
const fonts = ref([])
const selectedFont = ref(props.font || null)
const search = ref("")
const debouncedSearch = refDebounced(search, 500)
const scrollContainer = ref(null)
const fontRefs = new Map()
const visible = ref([])

const setFontRef = (el, index) => {
  if (el) fontRefs.set(index, el)
}

const initializeVisibilityTracking = async () => {
  await nextTick() // Ensure DOM has been fully updated
  fontRefs.forEach((el, index) => {
    const visibility = useElementVisibility(el, {
      root: scrollContainer.value,
      threshold: 0.1
    })
    watch(
      () => visibility.value,
      (isVisible) => {
        if (isVisible) {
          visible.value[index] = true
        }
      },
      { immediate: true }
    )
  })
}

const fetchFonts = async () => {
  if (props.show) {
    selectedFont.value = props.font || null
    loading.value = true
    opnFetch('/fonts/').then((data) => {
      fonts.value = data || []
      loading.value = false
      initializeVisibilityTracking()
    })
  }
}
watch(() => props.show, fetchFonts)


const enrichedFonts = computed(() => {
  const enrichedFonts = fonts.value

  if (search.value === "" || search.value === null) {
    return enrichedFonts
  }

  // Fuze search
  const fuse = new Fuse(enrichedFonts)
  return fuse.search(debouncedSearch.value).map((res) => {
    return res.item
  })
})

const getFontUrl = (fontName) =>  {
  const family = fontName.replace(/ /g, '+')
  return `https://fonts.googleapis.com/css?family=${family}:wght@400&display=swap`
}
</script>
