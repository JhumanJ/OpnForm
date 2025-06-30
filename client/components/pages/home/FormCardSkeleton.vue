<template>
  <div class="p-4 flex gap-2 group items-center relative border rounded-lg shadow-xs">
    <!-- Title Skeleton -->
    <div class="flex-grow items-center truncate relative">
      <USkeleton class="h-5 w-2/5" />
    </div>
    
    <!-- Stats and Menu Skeleton -->
    <div class="flex items-center gap-4 relative text-sm">
      <!-- Last Updated Skeleton -->
      <USkeleton class="h-4 w-24 hidden md:inline" />
      <USkeleton class="h-4 w-2 hidden md:inline" />

      <!-- Views Skeleton -->
      <div class="flex items-center gap-1">
        <USkeleton class="h-5 w-5" />
        <USkeleton class="h-4 w-6" />
      </div>
      
      <!-- Submissions Skeleton -->
      <div class="flex items-center gap-1">
        <USkeleton class="h-5 w-5" />
        <USkeleton class="h-4 w-6" />
      </div>
      
      <!-- Extra Menu Skeleton -->
      <USkeleton class="h-8 w-8" />
    </div>
  </div>
</template> 