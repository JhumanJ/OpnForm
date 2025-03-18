<template>
  <main class="flex justify-center items-center px-16 w-full max-md:px-5 max-md:max-w-full">
    <section class="flex flex-col mb-4 w-full max-w-[1080px] max-md:mt-10 max-md:max-w-full">
      <div class="text-center mx-auto">
        <img
          src="/img/icons/404.png"
          alt="404 Error"
          class="mx-auto w-1/3"
        >
        <h1 class="text-dark text-2xl md:text-4xl leading-none font-semibold text-center pt-4 md:pt-0 mx-auto md:max-w-full">
          We’re sorry! This Form Is No Longer Here
        </h1>
        <p class="text-gray-600 mt-3">
          It may have been deleted, deactivated, or the form URL might have been regenerated or updated by its owner. But don’t worry—you’ve got plenty of other options!
        </p>
        
        <h3 class="my-8 text-dark text-xl font-semibold text-center md:pt-0 mx-auto md:max-w-full">
          What can you do?
        </h3>
        <div class="mt-8 grid grid-cols-2 md:grid-cols-5 gap-4">
          <NuxtLink
            :to="{ name: 'forms-create' }"
            class="bg-blue-100/50 shadow-md rounded-lg p-4 text-center text-gray-700 hover:bg-blue-100 flex flex-col items-center"
          >
            <Icon
              name="heroicons:check-circle"
              class="w-8 h-8 mb-2 text-nt-blue"
            />
            Start Fresh
          </NuxtLink>
          <NuxtLink
            :to="{ name: 'login' }"
            class="bg-blue-100/50 shadow-md rounded-lg p-4 text-center text-gray-700 hover:bg-blue-100 flex flex-col items-center"
          >
            <Icon
              name="heroicons:list-bullet"
              class="w-8 h-8 mb-2 text-nt-blue"
            />
            Find Your Forms
          </NuxtLink>
          <NuxtLink
            href="#"
            class="bg-blue-100/50 shadow-md rounded-lg p-4 text-center text-gray-700 hover:bg-blue-100 flex flex-col items-center"
            @click="crisp.openHelpdesk()"
          >
            <Icon
              name="heroicons:question-mark-circle"
              class="w-8 h-8 mb-2 text-nt-blue"
            />
            Need Help?
          </NuxtLink>
          <NuxtLink
            :to="{ name: 'index' }"
            class="bg-blue-100/50 shadow-md rounded-lg p-4 text-center text-gray-700 hover:bg-blue-100 flex flex-col items-center"
          >
            <Icon
              name="heroicons:document-magnifying-glass"
              class="w-8 h-8 mb-2 text-nt-blue"
            />
            Explore OpnForm
          </NuxtLink>
          <NuxtLink
            href="#"
            class="bg-blue-100/50 shadow-md rounded-lg p-4 text-center text-gray-700 hover:bg-blue-100 flex flex-col items-center"
            @click="crisp.openChat()"
          >
            <Icon
              name="heroicons:chat-bubble-left-right"
              class="w-8 h-8 mb-2 text-nt-blue"
            />
            Need Assistance?
          </NuxtLink>
        </div>
        
        <div class="mt-10 text-gray-700">
          <h3 class="mt-8 text-dark text-xl font-semibold text-center md:pt-0 mx-auto md:max-w-full">
            Still not sure what to do? Here are more ways to get started!
          </h3>
          <p class="text-sm mt-4">
            No worries! Here are some other options for you:
          </p>
          <ul class="mt-4 space-y-2 text-sm">
            <li>
              Do you want to know why users choose OpnForm? 
              <NuxtLink :to="{ name: 'ai-form-builder' }">
                Learn more here!
              </NuxtLink>
            </li>
            <li>
              Browse Templates
              <NuxtLink :to="{ name: 'templates' }">
                Get inspired with ready-to-use form templates!
              </NuxtLink>
            </li>
            <li>
              Check Out Our Help Center
              <NuxtLink to="https://help.opnform.com/en/">
                Find quick answers to common questions.
              </NuxtLink>
            </li>
          </ul>
        </div>
      </div>
    </section>
  </main>
</template>

<script setup>
const crisp = useCrisp()
</script>
