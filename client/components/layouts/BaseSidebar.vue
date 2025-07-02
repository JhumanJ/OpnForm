<template>
  <aside
    :class="[
      'bg-neutral-100 border-neutral-200 flex flex-col transition-all duration-300 ease-in-out z-[60]',
      isMobileMenuOpen
        ? 'fixed inset-0'
        : 'sticky top-0 w-full h-[49px] overflow-hidden sm:flex sm:fixed sm:h-full sm:w-58 sm:border-r',
    ]"
  >
    <!-- Top Section - Only show if there's header content or on mobile -->
    <div 
      v-if="hasHeaderContent || isMobileMenuOpen"
      class="p-1 border-b border-neutral-200 h-[49px]"
    >
      <div class="flex items-center justify-between gap-1">
        <!-- Header Content Slot -->
        <slot name="header" :isMobileMenuOpen="isMobileMenuOpen" />
        
        <div class="grow">
          <slot name="mobile-header" :isMobileMenuOpen="isMobileMenuOpen" />
        </div>

        <!-- Mobile Menu Toggle -->
        <div :class="{ 'sm:hidden': !isMobileMenuOpen }">
          <UButton
            square
            size="xl"
            class="hover:bg-neutral-200/80"
            :icon="isMobileMenuOpen ? 'i-heroicons-x-mark' : 'i-heroicons-bars-3'"
            variant="ghost"
            color="neutral"
            @click="isMobileMenuOpen = !isMobileMenuOpen"
          />
        </div>
      </div>
    </div>
    
    <!-- Mobile Menu Toggle (when header is hidden on desktop) -->
    <div 
      v-else
      class="sm:hidden p-1 border-b border-neutral-200 h-[49px] flex items-center justify-start gap-2"
    >
      <div class="grow">
        <slot name="mobile-header" :isMobileMenuOpen="isMobileMenuOpen" />
      </div>
      <UButton
        square
        size="xl"
        class="hover:bg-neutral-200/80"
        icon="i-heroicons-bars-3"
        variant="ghost"
        color="neutral"
        @click="isMobileMenuOpen = !isMobileMenuOpen"
      />
    </div>

    <!-- Navigation Content -->
    <nav 
      class="flex-1 p-2 overflow-y-auto flex flex-col"
      :class="{ 'hidden': !isMobileMenuOpen, 'sm:flex': true }"
    >
      <slot name="navigation" :isMobileMenuOpen="isMobileMenuOpen" />
    </nav>

    <!-- Footer -->
    <div 
      class="p-2 border-t border-neutral-200"
      :class="{ 'hidden': !isMobileMenuOpen, 'sm:block': true }"
    >
      <slot name="footer" :isMobileMenuOpen="isMobileMenuOpen">
        <p class="text-xs text-neutral-400 text-center">
          <span class="font-bold"><NuxtLink class="text-neutral-400" :to="{ name: 'home' }">OpnForm</NuxtLink></span>
          <span class="text-neutral-500" v-if="version"> v{{ version }}</span>
        </p>
      </slot>
    </div>
  </aside>
</template>

<script setup>
const slots = useSlots()

const isMobileMenuOpen = ref(false)
const version = computed(() => useFeatureFlag('version'))

// Check if header slot has content
const hasHeaderContent = computed(() => {
  return !!(slots.header && slots.header().length > 0)
})

// Handle body overflow when mobile menu is open
watchEffect(() => {
  if (import.meta.client) {
    document.body.classList.toggle('overflow-hidden', isMobileMenuOpen.value)
  }
})

onUnmounted(() => {
  if (import.meta.client) {
    document.body.classList.remove('overflow-hidden')
  }
})

// Expose the mobile menu state to parent components
defineExpose({
  isMobileMenuOpen
})
</script> 