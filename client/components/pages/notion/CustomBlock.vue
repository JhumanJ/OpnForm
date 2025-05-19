<template>
  <div
    v-if="innerJson"
    id="custom-block"
  >
    <div
      v-if="innerJson.type=='faq'"
      class="rounded-lg bg-white z-10 pt-10"
    >
      <h2 class="font-medium">
        Frequently Asked Questions
      </h2>
      <dl class="pt-4 space-y-6">
        <div
          v-for="question in innerJson.content"
          :key="question.label"
          class="space-y-2"
        >
          <dt class="font-semibold text-gray-900 dark:text-gray-100">
            {{ question.label }}
          </dt>
          <dd
            class="leading-6 text-gray-600 dark:text-gray-400"
            v-html="question.content"
          />
        </div>
      </dl>
    </div>
    <div
      v-else-if="innerJson.type=='cta'"
      class="rounded-lg relative bg-gradient-to-r from-blue-400 to-blue-600 shadow-ld p-8 z-10"
    >
      <div class="absolute inset-px rounded-[calc(var(--radius)-1px)]">
        <div class="flex justify-center w-full h-full">
          <SpotlightCard
            class="w-full p-2 rounded-[--radius] [--radius:theme(borderRadius.lg)] opacity-70"
            from="#60a5fa"
            :size="200"
          />
        </div>
      </div>
      <div class="relative z-20 flex flex-col items-center gap-4 pb-1">
        <h2 class="text-xl md:text-2xl text-center font-medium text-white">
          {{ innerJson.title ? innerJson.title : 'Ready to upgrade your OpnForm forms?' }}
        </h2>
        <UButton
          to="/register"
          color="white"
          class="hover:no-underline"
          icon="i-heroicons-arrow-right"
          trailing
        >
          Try OpnForm for free
        </UButton>
      </div>
    </div>
  </div>
</template>

<script setup>
import { blockProps } from 'vue-notion'
import useNotionBlock from '~/components/pages/notion/useNotionBlock.js'

const props = defineProps(blockProps)

const block = useNotionBlock(props)
const innerJson = computed(() => block.innerJson.value)
</script>
