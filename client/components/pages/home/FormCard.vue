<template>
  <div class="p-4 flex gap-2 group items-center relative border rounded-lg shadow-xs hover:bg-neutral-100 transition-all cursor-pointer">
    <!-- Title -->
    <div class="flex-grow items-center truncate relative">
      <span class="font-semibold text-neutral-900 dark:text-white">{{ form.title }}</span>
    </div>
    
    <!-- Stats and Menu -->
    <div class="flex items-center gap-4 relative text-sm text-neutral-500">
      <!-- Status Badges -->
      <FormStatusBadges class="hidden md:block" :form="form" :with-tags="false" size="sm" />
      
      <!-- Last Updated -->
      <span class="hidden lg:inline text-xs whitespace-nowrap" title="Last updated">Updated {{ form.last_edited_human }}</span>

      <!-- Views -->
      <UTooltip :text="`${formatNumberWithCommas(form.views_count)} views`">
        <div class="flex items-center gap-1" title="Form views">
          <UIcon name="i-heroicons-eye" />
          <span>{{ formatNumber(form.views_count) }}</span>
        </div>
      </UTooltip>
      
      <!-- Submissions -->
      <UTooltip :text="`${formatNumberWithCommas(form.submissions_count)} submissions`">
        <div class="flex items-center gap-1" title="Form submissions">
          <UIcon name="i-heroicons-document-text" />
          <span>{{ formatNumber(form.submissions_count) }}</span>
        </div>
      </UTooltip>
      
      <!-- Extra Menu -->
      <div class="relative z-20">
        <ExtraMenu :form="form" :is-main-page="true" portal="#home-portals">
          <UButton
            class="hover:bg-neutral-200"
            color="neutral"
            variant="ghost"
            icon="i-heroicons-ellipsis-horizontal"
            size="md"
          />
        </ExtraMenu>
      </div>
    </div>

    <!-- Link overlay covering entire card -->
    <NuxtLink
      :to="{name:'forms-slug-show-submissions', params: {slug:form.slug}}"
      class="absolute inset-0 z-10"
    />
  </div>
</template>

<script setup>
import ExtraMenu from "~/components/pages/forms/show/ExtraMenu.vue"
import FormStatusBadges from "~/components/open/forms/components/FormStatusBadges.vue"
import { formatNumber, formatNumberWithCommas } from "~/lib/utils.js"

defineProps({
  form: {
    type: Object,
    required: true,
  },
})
</script> 