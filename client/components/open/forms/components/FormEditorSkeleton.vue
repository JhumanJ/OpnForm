<template>
  <div class="relative flex w-full flex-col grow max-h-screen">
    <!-- Mobile warning (same as FormEditor) -->
    <div
      class="border-b bg-white md:hidden fixed inset-0 w-full z-50 flex flex-col items-center justify-center"
    >
      <Icon
        name="heroicons:exclamation-circle"
        class="w-10 h-10 text-blue-800"
      />
      <div class="p-5 text-blue-800 text-center">
        OpnForm is not optimized for mobile devices. Please open this page on a device with a larger screen.
      </div>
      <div>
        <UButton
          color="neutral"
          variant="outline"
          size="lg"
          class="w-full"
          :to="{ name: 'home' }"
        >
          Back to dashboard
        </UButton>
      </div>
    </div>

    <!-- Navbar Skeleton -->
    <div class="border-b bg-white px-4 py-3 flex items-center justify-between">
      <!-- Back button and title -->
      <div class="flex items-center gap-3">
        <a
          v-if="backButton"
          href="#"
          class="ml-2 flex text-blue font-semibold text-sm -m-1 hover:bg-blue-500/10 rounded-md p-1 group"
          @click.prevent="$emit('go-back')"
        >
          <Icon
            name="heroicons:arrow-left-20-solid"
            class="text-blue mr-1 w-6 h-6 group-hover:-translate-x-0.5 transition-all"
          />
        </a>
        <USkeleton v-else class="h-9 w-9" />
        <USkeleton class="h-6 w-32" />
      </div>
      
      <!-- Save button -->
      <USkeleton class="h-9 w-20" />
    </div>

    <!-- Main content area -->
    <div class="w-full flex grow overflow-y-scroll relative bg-white">
      <!-- Left sidebar skeleton -->
      <div class="relative w-full shrink-0 overflow-y-scroll border-r md:w-1/2 md:max-w-xs lg:w-2/5">
        <div class="p-4 space-y-4">
          <!-- Tab buttons -->
          <div class="flex gap-2 mb-6">
            <USkeleton class="h-8 w-16" />
            <USkeleton class="h-8 w-16" />
          </div>
          
          <!-- Form fields section -->
          <div class="space-y-4">
            <USkeleton class="h-5 w-24" />
            <USkeleton class="h-10 w-full" />
            <USkeleton class="h-5 w-32" />
            <USkeleton class="h-8 w-full" />
            <USkeleton class="h-5 w-28" />
            <USkeleton class="h-12 w-full" />
            <USkeleton class="h-5 w-20" />
            <USkeleton class="h-6 w-full" />
          </div>
          
          <!-- Additional form options -->
          <div class="space-y-3 mt-6">
            <USkeleton class="h-4 w-36" />
            <USkeleton class="h-9 w-full" />
            <USkeleton class="h-4 w-28" />
            <USkeleton class="h-9 w-full" />
            <USkeleton class="h-4 w-32" />
            <USkeleton class="h-16 w-full" />
          </div>
        </div>
      </div>

      <!-- Center preview area skeleton -->
      <div class="flex-1 flex flex-col bg-neutral-50">
        <div class="p-4 bg-white border-b">
          <div class="flex items-center justify-between">
            <USkeleton class="h-6 w-32" />
            <div class="flex gap-2">
              <USkeleton class="h-8 w-8" />
              <USkeleton class="h-8 w-8" />
            </div>
          </div>
        </div>
        
        <div class="flex-1 p-6 overflow-y-auto">
          <div class="max-w-lg mx-auto bg-white rounded-lg shadow-sm border p-6">
            <!-- Form preview skeleton -->
            <div class="space-y-6">
              <div class="space-y-2">
                <USkeleton class="h-8 w-3/4" />
                <USkeleton class="h-4 w-full" />
                <USkeleton class="h-4 w-2/3" />
              </div>
              
              <div class="space-y-4">
                <div class="space-y-2">
                  <USkeleton class="h-4 w-24" />
                  <USkeleton class="h-10 w-full" />
                </div>
                
                <div class="space-y-2">
                  <USkeleton class="h-4 w-32" />
                  <USkeleton class="h-10 w-full" />
                </div>
                
                <div class="space-y-2">
                  <USkeleton class="h-4 w-28" />
                  <USkeleton class="h-24 w-full" />
                </div>
                
                <div class="space-y-2">
                  <USkeleton class="h-4 w-20" />
                  <USkeleton class="h-10 w-full" />
                </div>
              </div>
              
              <USkeleton class="h-10 w-32" />
            </div>
          </div>
        </div>
      </div>

      <!-- Right sidebar skeleton -->
      <div class="w-80 border-l bg-white p-4 space-y-4">
        <div class="flex items-center justify-between">
          <USkeleton class="h-6 w-24" />
          <USkeleton class="h-6 w-6" />
        </div>
        
        <div class="space-y-4">
          <div class="space-y-2">
            <USkeleton class="h-4 w-20" />
            <USkeleton class="h-9 w-full" />
          </div>
          
          <div class="space-y-2">
            <USkeleton class="h-4 w-24" />
            <USkeleton class="h-9 w-full" />
          </div>
          
          <div class="space-y-2">
            <USkeleton class="h-4 w-32" />
            <USkeleton class="h-16 w-full" />
          </div>
          
          <div class="space-y-2">
            <USkeleton class="h-4 w-28" />
            <USkeleton class="h-9 w-full" />
          </div>
          
          <!-- Field options -->
          <div class="border-t pt-4 space-y-3">
            <USkeleton class="h-4 w-36" />
            <USkeleton class="h-8 w-full" />
            <USkeleton class="h-4 w-28" />
            <USkeleton class="h-8 w-full" />
            <USkeleton class="h-4 w-32" />
            <USkeleton class="h-12 w-full" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
// Define props
defineProps({
  backButton: {
    type: Boolean,
    default: true
  }
})

// Define emits
defineEmits(['go-back'])
</script> 