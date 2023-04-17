<template>
  <div class="fixed top-0 bottom-24 right-0 flex px-4 items-start justify-end z-50 pointer-events-none">
    <notification v-slot="{ notifications, close }">
      <div class="relative pointer-events-auto" v-for="notification in notifications" :key="notification.id">
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
              <span class="text-green-500 font-semibold pr-6">{{notification.title}}</span>
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
              <span class="text-blue-500 font-semibold pr-6">{{notification.title}}</span>
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
              <span class="text-red-500 font-semibold pr-6">{{notification.title}}</span>
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
              <span class="text-yellow-500 font-semibold pr-6">{{notification.title}}</span>
              <p class="text-gray-600 text-sm">{{notification.text}}</p>
            </div>
          </div>
        </div>
        <div
          class="flex max-w-sm w-full mx-auto bg-white shadow-md rounded-lg overflow-hidden mt-4"
          v-if="notification.type==='confirm'"
        >
          <div class="flex justify-center items-center w-12 bg-blue-500">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 text-white">
              <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm11.378-3.917c-.89-.777-2.366-.777-3.255 0a.75.75 0 01-.988-1.129c1.454-1.272 3.776-1.272 5.23 0 1.513 1.324 1.513 3.518 0 4.842a3.75 3.75 0 01-.837.552c-.676.328-1.028.774-1.028 1.152v.75a.75.75 0 01-1.5 0v-.75c0-1.279 1.06-2.107 1.875-2.502.182-.088.351-.199.503-.331.83-.727.83-1.857 0-2.584zM12 18a.75.75 0 100-1.5.75.75 0 000 1.5z" clip-rule="evenodd" />
            </svg>
          </div>

          <div class="-mx-3 py-2 px-4">
            <div class="mx-3">
              <span class="text-blue-500 font-semibold pr-6">{{notification.title}}</span>
              <p class="text-gray-600 text-sm">{{notification.text}}</p>
              <div class="w-full flex gap-2 mt-1">
                <v-button color="blue" size="small" @click.prevent="notification.success();close(notification.id)">Yes</v-button>
                <v-button color="gray" shade="light" size="small" @click.prevent="notification.failure();close(notification.id)">No</v-button>
              </div>
            </div>
          </div>
        </div>
        <button @click="close(notification.id)" class="absolute top-0 right-0 px-2 py-2 cursor-pointer">
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
