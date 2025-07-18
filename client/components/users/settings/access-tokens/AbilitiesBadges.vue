<template>
  <div class="flex items-center gap-1 flex-wrap">
    <!-- First 2 abilities as badges -->
    <UBadge
      v-for="(ability, index) in abilities.slice(0, limit)"
      :key="index"
      variant="soft"
      color="neutral"
      size="sm"
    >
      {{ getAbility(ability).title }}
    </UBadge>
    
    <!-- "+x more" badge with tooltip if there are more than 2 abilities -->
    <UTooltip 
      v-if="abilities.length > limit"
      :text="abilities.slice(limit).map(ability => getAbility(ability).title).join(', ')"
      class="max-w-xs"
    >
      <UBadge
        variant="soft"
        color="primary"
        size="sm"
      >
        +{{ abilities.length - limit }}
      </UBadge>
    </UTooltip>
  </div>
</template>

<script setup>
defineProps({
  abilities: {
    type: Array,
    required: true
  },
  limit: {
    type: Number,
    default: 4
  }
})

const { getAbility } = useTokens()
</script> 