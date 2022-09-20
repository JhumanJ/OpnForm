<template>
  <div id="features" class="px-4 mx-auto sm:max-w-xl md:max-w-full lg:max-w-screen-xl md:px-24 lg:px-8">
    <div v-if="!featuresOnly" :class="{'mb-10 md:mb-12':!featuresOnly }" class="max-w-xl md:mx-auto sm:text-center lg:max-w-2xl ">
      <div>
        <p
          class="inline-block px-3 py-px mb-4 text-xs font-semibold tracking-wider text-nt-blue uppercase rounded-full bg-nt-blue-lighter"
        >
          100% Free
        </p>
      </div>
      <h2
        class="max-w-lg mb-6 font-sans text-3xl font-bold leading-none tracking-tight text-gray-900 dark:text-gray-100 sm:text-4xl md:mx-auto"
      >
        <span class="relative inline-block">
          <svg viewBox="0 0 52 24" fill="currentColor"
               class="text-nt-blue-light absolute top-0 left-0 z-0 hidden w-32 -mt-8 -ml-20 text-blue-gray-100 lg:w-32 lg:-ml-28 lg:-mt-10 sm:block"
          >
            <defs>
              <pattern id="27df4f81-c854-45de-942a-fe90f7a300f9" x="0" y="0" width=".135" height=".30">
                <circle cx="1" cy="1" r=".7" />
              </pattern>
            </defs>
            <rect fill="url(#27df4f81-c854-45de-942a-fe90f7a300f9)" width="52" height="24" />
          </svg>
          <span class="relative">The</span>
        </span>
        easiest way to create forms for free
      </h2>
      <p class="text-base text-gray-700 dark:text-gray-300 md:text-lg">
        You've been paying too much for too long. OpnForm is the first open-source form builder. Need a contact
        form? Doing a survey? Create a form in 3 minutes and start receiving submissions.
      </p>
    </div>
    <div class="grid max-w-screen-lg gap-8 row-gap-10 mx-auto md:grid-cols-2">
      <div class="flex flex-col max-w-md sm:mx-auto sm:flex-row">
        <div class="mr-4">
          <div class="flex items-center justify-center w-12 h-12 mb-4 rounded-full bg-nt-blue-lighter">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-nt-blue" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14v6m-3-3h6M6 10h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2zm10 0h2a2 2 0 002-2V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2zM6 20h2a2 2 0 002-2v-2a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2z" />
            </svg>
          </div>
        </div>
        <div>
          <h6 class="mb-3 text-xl font-bold leading-5">
            Infinite Number of Fields
          </h6>
          <p class="mb-3 text-sm dark:text-gray-100 text-gray-900">
            There are no limits on the number of input fields in your forms. Organize fields and decide which are required.
          </p>
        </div>
      </div>
      <div class="flex flex-col max-w-md sm:mx-auto sm:flex-row">
        <div class="mr-4">
          <div class="flex items-center justify-center w-12 h-12 mb-4 rounded-full bg-nt-blue-lighter">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-nt-blue" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
              />
            </svg>
          </div>
        </div>
        <div>
          <h6 class="mb-3 text-xl font-bold leading-5">
            Infinite Number of Forms
          </h6>
          <p class="mb-3 text-sm dark:text-gray-100 text-gray-900">
            You can create as many forms as you need. Forms everywhere, for everything!
          </p>
        </div>
      </div>
      <div class="flex flex-col max-w-md sm:mx-auto sm:flex-row">
        <div class="mr-4">
          <div class="flex items-center justify-center w-12 h-12 mb-4 rounded-full bg-nt-blue-lighter">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-nt-blue" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
        <div>
          <h6 class="mb-3 text-xl font-bold leading-5">
            Infinite Responses
          </h6>
          <p class="mb-3 text-sm dark:text-gray-100 text-gray-900">
            All of you forms can have unlimited responses, no need to worry about quotas and other stressful metrics.
          </p>
        </div>
      </div>
      <div class="flex flex-col max-w-md sm:mx-auto sm:flex-row">
        <div class="mr-4">
          <div class="flex items-center justify-center w-12 h-12 mb-4 rounded-full bg-nt-blue-lighter">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-nt-blue" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
          </div>
        </div>
        <div>
          <h6 class="mb-3 text-xl font-bold leading-5">
            Notifications
          </h6>
          <p class="mb-3 text-sm dark:text-gray-100 text-gray-900">
            Receive notifications directly in Slack or in your mailbox whenever your from has a new submission (if you want to).
          </p>
        </div>
      </div>
      <div class="flex flex-col max-w-md sm:mx-auto sm:flex-row">
        <div class="mr-4">
          <div class="flex items-center justify-center w-12 h-12 mb-4 rounded-full bg-nt-blue-lighter">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-nt-blue" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
            </svg>
          </div>
        </div>
        <div>
          <h6 class="mb-3 text-xl font-bold leading-5">
            Integrate Anywhere
          </h6>
          <p class="mb-3 text-sm dark:text-gray-100 text-gray-900">
            You can integrate your form anywhere: on your website, or even within a Notion Page.
          </p>
        </div>
      </div>
      <div class="flex flex-col max-w-md sm:mx-auto sm:flex-row">
        <div class="mr-4">
          <div class="flex items-center justify-center w-12 h-12 mb-4 rounded-full bg-nt-blue-lighter">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"  class="w-6 h-6 text-nt-blue" >
              <path stroke-linecap="round" stroke-linejoin="round" d="M4.098 19.902a3.75 3.75 0 005.304 0l6.401-6.402M6.75 21A3.75 3.75 0 013 17.25V4.125C3 3.504 3.504 3 4.125 3h5.25c.621 0 1.125.504 1.125 1.125v4.072M6.75 21a3.75 3.75 0 003.75-3.75V8.197M6.75 21h13.125c.621 0 1.125-.504 1.125-1.125v-5.25c0-.621-.504-1.125-1.125-1.125h-4.072M10.5 8.197l2.88-2.88c.438-.439 1.15-.439 1.59 0l3.712 3.713c.44.44.44 1.152 0 1.59l-2.879 2.88M6.75 17.25h.008v.008H6.75v-.008z" />
            </svg>

          </div>
        </div>
        <div>
          <h6 class="mb-3 text-xl font-bold leading-5">
            Customize Everything
          </h6>
          <p class="mb-3 text-sm dark:text-gray-100 text-gray-900">
            Change form themes, change texts, colors, add images, add custom thank you pages and much more.
          </p>
        </div>
      </div>
      <div class="flex flex-col max-w-md sm:mx-auto sm:flex-row">
        <div class="mr-4">
          <div class="flex items-center justify-center w-12 h-12 mb-4 rounded-full bg-nt-blue-lighter">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-nt-blue" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </div>
        </div>
        <div>
          <h6 class="mb-3 text-xl font-bold leading-5">
            File Uploads
          </h6>
          <p class="mb-3 text-sm dark:text-gray-100 text-gray-900">
            Easily add file upload inputs to your forms. Uploaded files are securely stored for you. Up to 5mb!
          </p>
        </div>
      </div>
      <div class="flex flex-col max-w-md sm:mx-auto sm:flex-row">
        <div class="mr-4">
          <div class="flex items-center justify-center w-12 h-12 mb-4 rounded-full bg-nt-blue-lighter">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-nt-blue" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
          </div>
        </div>
        <div>
          <h6 class="mb-3 text-xl font-bold leading-5">
            Advanced features
          </h6>
          <p class="mb-3 text-sm dark:text-gray-100 text-gray-900">
            Form logic, URL pre-fill, hidden fields, unique submission id, form password, webhooks, custom code, closing date, etc. It's all there!
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {

  props: {
    featuresOnly: {
      type: Boolean,
      default: false
    }
  },
  data: () => ({}),

  methods: {}
}
</script>
