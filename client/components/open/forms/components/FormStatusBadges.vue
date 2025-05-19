<template>
  <div 
    v-if="shouldDisplayBadges" 
    class="flex items-center flex-wrap gap-1"
  >
    <!-- Draft Badge -->
    <UTooltip v-if="form.visibility === 'draft'" text="Not publicly accessible">
      <UBadge
        color="amber"
        variant="subtle"
        icon="i-heroicons-exclamation-triangle"
        :size="size"
      >
        Draft
      </UBadge>
    </UTooltip>
    
    <!-- Closed Badge -->
    <UTooltip v-else-if="form.visibility === 'closed'" text="Won't accept new submissions">
      <UBadge
        color="gray"
        variant="subtle"
        icon="i-heroicons-lock-closed"
        :size="size"
      >
        Closed
      </UBadge>
    </UTooltip>
    
    <!-- Time Limited Badge -->
    <UTooltip v-if="form.closes_at && !form.is_closed" :text="`Will close on ${closesDate}`">
      <UBadge
        color="amber"
        variant="subtle"
        icon="i-heroicons-clock"
        :size="size"
      >
        Time limited
      </UBadge>
    </UTooltip>
    
    <!-- Submission Limited Badge -->
    <UTooltip 
      v-if="form.max_submissions_count > 0 && !form.max_number_of_submissions_reached" 
      :text="`Limited to ${form.max_submissions_count} submissions`"
    >
      <UBadge
        color="amber"
        variant="subtle"
        icon="i-heroicons-chart-bar"
        :size="size"
      >
        Submission limited
      </UBadge>
    </UTooltip>
    
    <!-- Tags Badges -->
    <UBadge
      v-for="tag in form.tags"
      :key="tag"
      color="white"
      variant="solid"
      class="capitalize"
      :size="size"
    >
      {{ tag }}
    </UBadge>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  form: {
    type: Object,
    required: true
  },
  size: {
    type: String,
    default: 'sm',
    validator: (value) => ['xs', 'sm', 'md', 'lg'].includes(value)
  }
})

const closesDate = computed(() => {
  if (props.form && props.form.closes_at) {
    try {
      const dateObj = new Date(props.form.closes_at)
      return dateObj.getFullYear() + '-' +
        String(dateObj.getMonth() + 1).padStart(2, '0') + '-' +
        String(dateObj.getDate()).padStart(2, '0') + ' ' +
        String(dateObj.getHours()).padStart(2, '0') + ':' +
        String(dateObj.getMinutes()).padStart(2, '0')
    } catch (e) {
      console.error(e)
      return null
    }
  }
  return null
})

// Conditional to determine if badges should be displayed
const shouldDisplayBadges = computed(() => {
  return ['draft', 'closed'].includes(props.form.visibility) || 
         (props.form.tags && props.form.tags.length > 0) || 
         props.form.closes_at || 
         (props.form.max_submissions_count > 0)
})
</script> 