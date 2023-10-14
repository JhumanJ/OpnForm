<template>
  <transition name="fade">
    <div v-if="form.successful" class="bg-green-200 border-green-600 text-green-600 border-l-4 p-4 relative rounded-lg"
         role="alert">
      <button v-if="dismissible"
              type="button"
              @click.prevent="dismiss()"
              class="absolute right-2 top-0 -mr-1 flex-shrink-0 flex p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 sm:-mr-2">
        <span class="sr-only">
            Dismiss
        </span>
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="h-6 w-6 text-green-500"
             viewBox="0 0 1792 1792">
          <path
            d="M1490 1322q0 40-28 68l-136 136q-28 28-68 28t-68-28l-294-294-294 294q-28 28-68 28t-68-28l-136-136q-28-28-28-68t28-68l294-294-294-294q-28-28-28-68t28-68l136-136q28-28 68-28t68 28l294 294 294-294q28-28 68-28t68 28l136 136q28 28 28 68t-28 68l-294 294 294 294q28 28 28 68z">
          </path>
        </svg>
      </button>
      <p class="font-bold">
        Success
      </p>
      <div v-html="message"/>
    </div>
  </transition>
</template>

<script>
import Alert from './Alert.js'

export default {
  name: 'AlertSuccess',
  extends: Alert,
  props: {
    message: { type: String, default: '' }
  }
}
</script>
