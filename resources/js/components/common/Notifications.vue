<template>
  <div class="fixed top-16 bottom-24 right-0 flex px-4 items-start justify-end z-10">
    <notification v-slot="{ notifications, close }">
      <div class="relative" v-for="notification in notifications" :key="notification.id">
        <div
          v-if="notification.type==='success'"
          class="flex max-w-sm w-full mx-auto bg-white shadow-md rounded-lg overflow-hidden mt-4"
        >
          <div class="flex justify-center items-center w-12 bg-green-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 fill-current text-white" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
          </div>

          <div class="-mx-3 py-2 px-4">
            <div class="mx-3">
              <span class="text-green-500 font-semibold">{{notification.title}}</span>
              <p class="text-gray-600 text-sm">{{notification.text}}</p>
            </div>
          </div>
        </div>
        <div
          v-if="notification.type==='info'"
          class="flex max-w-sm w-full mx-auto bg-white shadow-md rounded-lg overflow-hidden mt-4"
        >
          <div class="flex justify-center items-center w-12 bg-blue-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 fill-current text-white" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>
          </div>

          <div class="-mx-3 py-2 px-4">
            <div class="mx-3">
              <span class="text-blue-500 font-semibold">{{notification.title}}</span>
              <p class="text-gray-600 text-sm">T{{notification.text}}</p>
            </div>
          </div>
        </div>
        <div
          v-if="notification.type==='error'"
          class="flex max-w-sm w-full mx-auto bg-white shadow-md rounded-lg overflow-hidden mt-4"
        >
          <div class="flex justify-center items-center w-12 bg-red-500">
            <svg
              class="h-6 w-6 fill-current text-white"
              viewBox="0 0 40 40"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                d="M20 3.33331C10.8 3.33331 3.33337 10.8 3.33337 20C3.33337 29.2 10.8 36.6666 20 36.6666C29.2 36.6666 36.6667 29.2 36.6667 20C36.6667 10.8 29.2 3.33331 20 3.33331ZM21.6667 28.3333H18.3334V25H21.6667V28.3333ZM21.6667 21.6666H18.3334V11.6666H21.6667V21.6666Z"
              />
            </svg>
          </div>

          <div class="-mx-3 py-2 px-4">
            <div class="mx-3">
              <span class="text-red-500 font-semibold">{{notification.title}}</span>
              <p class="text-gray-600 text-sm">{{notification.text}}</p>
            </div>
          </div>
        </div>
        <div
          class="flex max-w-sm w-full mx-auto bg-white shadow-md rounded-lg overflow-hidden mt-4"
          v-if="notification.type==='warning'"
        >
          <div class="flex justify-center items-center w-12 bg-yellow-500">
            <svg
              class="h-6 w-6 fill-current text-white"
              viewBox="0 0 40 40"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                d="M20 3.33331C10.8 3.33331 3.33337 10.8 3.33337 20C3.33337 29.2 10.8 36.6666 20 36.6666C29.2 36.6666 36.6667 29.2 36.6667 20C36.6667 10.8 29.2 3.33331 20 3.33331ZM21.6667 28.3333H18.3334V25H21.6667V28.3333ZM21.6667 21.6666H18.3334V11.6666H21.6667V21.6666Z"
              />
            </svg>
          </div>

          <div class="-mx-3 py-2 px-4">
            <div class="mx-3">
              <span class="text-yellow-500 font-semibold">{{notification.title}}</span>
              <p class="text-gray-600 text-sm">{{notification.text}}</p>
            </div>
          </div>
        </div>
        <button @click="close(notification.id)" class="absolute top-0 bottom-4 right-0 px-2 py-2 cursor-pointer">
          <svg
            class="fill-current h-6 w-6 text-gray-300 hover:text-gray-500"
            role="button"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 20 20"
          >
            <title>Close</title>
            <path
              d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"
            />
          </svg>
        </button>
      </div>
    </notification>
  </div>
</template>

<script>
export default {
  name: 'Notifications',

  data() {
    return {}
  },

  computed: {},

  mounted() {
  },

  methods: {}
}
</script>
