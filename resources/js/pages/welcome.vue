<template>
  <div>
    <div class="flex flex-col bg-gray-50 dark:bg-notion-dark">
      <div class="w-full bg-gradient-to-b from-white to-gray-100">
        <div class="w-full md:mx-auto flex flex-wrap mb-10 -mb-32 md:-mb-52 lg:-mb-72 md:pt-10 relative max-w-5xl">
          <div
            class="flex flex-col justify-center w-full text-center lg:pr-10 p-6 lg:p-10 relative z-10"
          >
            <h1 dusk="title" class="text-4xl lg:text-5xl">
              The open-source <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-blue-600">form builder</span><br>
            </h1>
            <h3 class="mt-6 text-xl text-gray-600 dark:text-gray-400 max-w-xl mx-auto">
              Create beautiful forms and share them anywhere. It takes seconds, you don't need to know how to code
              and <span
              class="font-medium"
            >it's free</span>.
            </h3>
            <div class="mt-6 flex justify-center">
              <v-button class="mr-1" :to="{ name: 'register' }" :arrow="true">
                Create a form for FREE
              </v-button>
            </div>
            <div class="justify-center flex gap-2 mt-10">
              <div class="flex items-center text-gray-400 text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                     stroke="currentColor" class="w-4 h-4 mr-1">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                </svg>
                <span>Unlimited forms</span>
              </div>
              <div class="flex items-center text-gray-400 text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                     stroke="currentColor" class="w-4 h-4 mr-1">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                </svg>
                <span>
                  Unlimited fields
                </span>
              </div>
              <div class="flex text-gray-400 text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                     stroke="currentColor" class="w-4 h-4 mr-1">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                </svg>
                <span>Unlimited responses</span>
              </div>
            </div>
          </div>
          <div class="w-full mt-8 relative px-6 mx-auto max-w-4xl sm:px-10 lg:px-0 z-10 flex items-center justify-center">
            <img loading="lazy" class="w-full shadow-2xl rounded-xl block max-w-2xl lg:max-w-5xl"
                 :src="asset('img/pages/welcome/product-cover.jpg')" alt="cover-product">
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-notion-dark-light pt-32 md:pt-52 lg:pt-72 pb-8">
        <div class="md:max-w-5xl md:mx-auto w-full">
          <features class="pt-20 pb-8"/>
        </div>
      </div>

      <div class="bg-gray-50 dark:bg-notion-dark py-8">
        <div class="md:max-w-5xl md:mx-auto w-full">
          <div class="my-5 text-center">
            <h3 class="font-semibold text-3xl">And many more features</h3>
            <p class="w-full mt-2 mb-8">
              OpnForm makes form building easy and comes with powerful features.
            </p>
            <div class="grid md:grid-cols-3 mt-10 mb-5">
              <div class="flex font-semibold my-3">
                <svg class="w-5 h-5 mr-2 text-nt-blue" viewBox="0 0 23 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M10.5 3.5H17.8C18.9201 3.5 19.4802 3.5 19.908 3.71799C20.2843 3.90973 20.5903 4.21569 20.782 4.59202C21 5.01984 21 5.57989 21 6.7V8C21 8.93188 21 9.39783 20.8478 9.76537C20.6448 10.2554 20.2554 10.6448 19.7654 10.8478C19.3978 11 18.9319 11 18 11M12.5 18.5H5.2C4.0799 18.5 3.51984 18.5 3.09202 18.282C2.71569 18.0903 2.40973 17.7843 2.21799 17.408C2 16.9802 2 16.4201 2 15.3V14C2 13.0681 2 12.6022 2.15224 12.2346C2.35523 11.7446 2.74458 11.3552 3.23463 11.1522C3.60218 11 4.06812 11 5 11M9.8 13.5H13.2C13.48 13.5 13.62 13.5 13.727 13.4455C13.8211 13.3976 13.8976 13.3211 13.9455 13.227C14 13.12 14 12.98 14 12.7V9.3C14 9.01997 14 8.87996 13.9455 8.773C13.8976 8.67892 13.8211 8.60243 13.727 8.5545C13.62 8.5 13.48 8.5 13.2 8.5H9.8C9.51997 8.5 9.37996 8.5 9.273 8.5545C9.17892 8.60243 9.10243 8.67892 9.0545 8.773C9 8.87996 9 9.01997 9 9.3V12.7C9 12.98 9 13.12 9.0545 13.227C9.10243 13.3211 9.17892 13.3976 9.273 13.4455C9.37996 13.5 9.51997 13.5 9.8 13.5ZM17.3 21H20.7C20.98 21 21.12 21 21.227 20.9455C21.3211 20.8976 21.3976 20.8211 21.4455 20.727C21.5 20.62 21.5 20.48 21.5 20.2V16.8C21.5 16.52 21.5 16.38 21.4455 16.273C21.3976 16.1789 21.3211 16.1024 21.227 16.0545C21.12 16 20.98 16 20.7 16H17.3C17.02 16 16.88 16 16.773 16.0545C16.6789 16.1024 16.6024 16.1789 16.5545 16.273C16.5 16.38 16.5 16.52 16.5 16.8V20.2C16.5 20.48 16.5 20.62 16.5545 20.727C16.6024 20.8211 16.6789 20.8976 16.773 20.9455C16.88 21 17.02 21 17.3 21ZM2.3 6H5.7C5.98003 6 6.12004 6 6.227 5.9455C6.32108 5.89757 6.39757 5.82108 6.4455 5.727C6.5 5.62004 6.5 5.48003 6.5 5.2V1.8C6.5 1.51997 6.5 1.37996 6.4455 1.273C6.39757 1.17892 6.32108 1.10243 6.227 1.0545C6.12004 1 5.98003 1 5.7 1H2.3C2.01997 1 1.87996 1 1.773 1.0545C1.67892 1.10243 1.60243 1.17892 1.5545 1.273C1.5 1.37996 1.5 1.51997 1.5 1.8V5.2C1.5 5.48003 1.5 5.62004 1.5545 5.727C1.60243 5.82108 1.67892 5.89757 1.773 5.9455C1.87996 6 2.01997 6 2.3 6Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Form logic
              </div>
              <div class="flex font-semibold my-3">
                <svg class="w-5 h-5 mr-2 text-nt-blue" viewBox="0 0 23 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M1.5 11H21.5M1.5 11C1.5 16.5228 5.97715 21 11.5 21M1.5 11C1.5 5.47715 5.97715 1 11.5 1M21.5 11C21.5 16.5228 17.0228 21 11.5 21M21.5 11C21.5 5.47715 17.0228 1 11.5 1M11.5 1C14.0013 3.73835 15.4228 7.29203 15.5 11C15.4228 14.708 14.0013 18.2616 11.5 21M11.5 1C8.99872 3.73835 7.57725 7.29203 7.5 11C7.57725 14.708 8.99872 18.2616 11.5 21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                URL pre-fill
              </div>
              <div class="flex font-semibold my-3">
                <svg class="w-5 h-5 mr-2 text-nt-blue" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M7.99999 1L4.99999 19M16 1L13 19M19 6H2M18 14H1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Unique submission ID
              </div>
              <div class="flex font-semibold my-3">
                <svg class="w-5 h-5 mr-2 text-nt-blue" viewBox="0 0 23 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M10.2429 3.09232C10.6494 3.03223 11.0686 3 11.5004 3C16.6054 3 19.9553 7.50484 21.0807 9.28682C21.2169 9.5025 21.285 9.61034 21.3231 9.77667C21.3518 9.90159 21.3517 10.0987 21.3231 10.2236C21.2849 10.3899 21.2164 10.4985 21.0792 10.7156C20.7793 11.1901 20.3222 11.8571 19.7165 12.5805M6.22432 4.71504C4.06225 6.1817 2.59445 8.21938 1.92111 9.28528C1.78428 9.50187 1.71587 9.61016 1.67774 9.77648C1.6491 9.9014 1.64909 10.0984 1.67771 10.2234C1.71583 10.3897 1.78393 10.4975 1.92013 10.7132C3.04554 12.4952 6.39541 17 11.5004 17C13.5588 17 15.3319 16.2676 16.7888 15.2766M2.50042 1L20.5004 19M9.3791 7.87868C8.8362 8.42157 8.50042 9.17157 8.50042 10C8.50042 11.6569 9.84356 13 11.5004 13C12.3288 13 13.0788 12.6642 13.6217 12.1213" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Hidden fields
              </div>
              <div class="flex font-semibold my-3">
                <svg class="w-5 h-5 mr-2 text-nt-blue" viewBox="0 0 19 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M14.5 8V6C14.5 3.23858 12.2614 1 9.5 1C6.73858 1 4.5 3.23858 4.5 6V8M9.5 12.5V14.5M6.3 19H12.7C14.3802 19 15.2202 19 15.862 18.673C16.4265 18.3854 16.8854 17.9265 17.173 17.362C17.5 16.7202 17.5 15.8802 17.5 14.2V12.8C17.5 11.1198 17.5 10.2798 17.173 9.63803C16.8854 9.07354 16.4265 8.6146 15.862 8.32698C15.2202 8 14.3802 8 12.7 8H6.3C4.61984 8 3.77976 8 3.13803 8.32698C2.57354 8.6146 2.1146 9.07354 1.82698 9.63803C1.5 10.2798 1.5 11.1198 1.5 12.8V14.2C1.5 15.8802 1.5 16.7202 1.82698 17.362C2.1146 17.9265 2.57354 18.3854 3.13803 18.673C3.77976 19 4.61984 19 6.3 19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Form password
              </div>
              <div class="flex font-semibold my-3">
                <svg class="w-5 h-5 mr-2 text-nt-blue" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M16.5 13C14.8431 13 13.5 14.3431 13.5 16C13.5 17.6569 14.8431 19 16.5 19C18.1569 19 19.5 17.6569 19.5 16C19.5 14.3431 18.1569 13 16.5 13ZM16.5 13V6C16.5 5.46957 16.2893 4.96086 15.9142 4.58579C15.5391 4.21071 15.0304 4 14.5 4H11.5M4.5 7C6.15685 7 7.5 5.65685 7.5 4C7.5 2.34315 6.15685 1 4.5 1C2.84315 1 1.5 2.34315 1.5 4C1.5 5.65685 2.84315 7 4.5 7ZM4.5 7V19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Webhooks
              </div>
              <div class="flex font-semibold my-3">
                <svg class="w-5 h-5 mr-2 text-nt-blue" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M13 13L16 10L13 7M8 7L5 10L8 13M6.3 19H14.7C16.3802 19 17.2202 19 17.862 18.673C18.4265 18.3854 18.8854 17.9265 19.173 17.362C19.5 16.7202 19.5 15.8802 19.5 14.2V5.8C19.5 4.11984 19.5 3.27976 19.173 2.63803C18.8854 2.07354 18.4265 1.6146 17.862 1.32698C17.2202 1 16.3802 1 14.7 1H6.3C4.61984 1 3.77976 1 3.13803 1.32698C2.57354 1.6146 2.1146 2.07354 1.82698 2.63803C1.5 3.27976 1.5 4.11984 1.5 5.8V14.2C1.5 15.8802 1.5 16.7202 1.82698 17.362C2.1146 17.9265 2.57354 18.3854 3.13803 18.673C3.77976 19 4.61984 19 6.3 19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Custom code
              </div>
              <div class="flex font-semibold my-3">
                <svg class="w-5 h-5 mr-2 text-nt-blue" viewBox="0 0 21 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M19.5 7H1.5M14.5 1V4M6.5 1V4M10.5 17V11M7.5 14H13.5M6.3 21H14.7C16.3802 21 17.2202 21 17.862 20.673C18.4265 20.3854 18.8854 19.9265 19.173 19.362C19.5 18.7202 19.5 17.8802 19.5 16.2V7.8C19.5 6.11984 19.5 5.27976 19.173 4.63803C18.8854 4.07354 18.4265 3.6146 17.862 3.32698C17.2202 3 16.3802 3 14.7 3H6.3C4.61984 3 3.77976 3 3.13803 3.32698C2.57354 3.6146 2.1146 4.07354 1.82698 4.63803C1.5 5.27976 1.5 6.11984 1.5 7.8V16.2C1.5 17.8802 1.5 18.7202 1.82698 19.362C2.1146 19.9265 2.57354 20.3854 3.13803 20.673C3.77976 21 4.61984 21 6.3 21Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Closing date
              </div>
              <div class="flex font-semibold my-3">
                <svg class="w-5 h-5 mr-2 text-nt-blue" viewBox="0 0 19 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M9.5 3C10.0523 3 10.5 2.55228 10.5 2C10.5 1.44772 10.0523 1 9.5 1C8.94772 1 8.5 1.44772 8.5 2C8.5 2.55228 8.94772 3 9.5 3Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M9.5 10C10.0523 10 10.5 9.55228 10.5 9C10.5 8.44772 10.0523 8 9.5 8C8.94772 8 8.5 8.44772 8.5 9C8.5 9.55228 8.94772 10 9.5 10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M9.5 17C10.0523 17 10.5 16.5523 10.5 16C10.5 15.4477 10.0523 15 9.5 15C8.94772 15 8.5 15.4477 8.5 16C8.5 16.5523 8.94772 17 9.5 17Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M16.5 3C17.0523 3 17.5 2.55228 17.5 2C17.5 1.44772 17.0523 1 16.5 1C15.9477 1 15.5 1.44772 15.5 2C15.5 2.55228 15.9477 3 16.5 3Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M16.5 10C17.0523 10 17.5 9.55228 17.5 9C17.5 8.44772 17.0523 8 16.5 8C15.9477 8 15.5 8.44772 15.5 9C15.5 9.55228 15.9477 10 16.5 10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M16.5 17C17.0523 17 17.5 16.5523 17.5 16C17.5 15.4477 17.0523 15 16.5 15C15.9477 15 15.5 15.4477 15.5 16C15.5 16.5523 15.9477 17 16.5 17Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M2.5 3C3.05228 3 3.5 2.55228 3.5 2C3.5 1.44772 3.05228 1 2.5 1C1.94772 1 1.5 1.44772 1.5 2C1.5 2.55228 1.94772 3 2.5 3Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M2.5 10C3.05228 10 3.5 9.55228 3.5 9C3.5 8.44772 3.05228 8 2.5 8C1.94772 8 1.5 8.44772 1.5 9C1.5 9.55228 1.94772 10 2.5 10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M2.5 17C3.05228 17 3.5 16.5523 3.5 16C3.5 15.4477 3.05228 15 2.5 15C1.94772 15 1.5 15.4477 1.5 16C1.5 16.5523 1.94772 17 2.5 17Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                And much more...
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="pt-20 pb-5 text-center bg-white dark:bg-notion-dark-light">
        <h3 class="font-semibold text-3xl">See what people are saying</h3>
        <p class="w-full mt-2 mb-8">
          These are the stories of our customers who have joined us with great pleasure when using this crazy feature.
        </p>
        <testimonials/>
      </div>

      <div class="w-full bg-blue-900 p-12 md:p-24 text-center">
        <h4 class="font-semibold text-3xl text-white">Take your forms to the next level</h4>
        <p class="text-gray-300 my-8">No trial. Generous, unlimited free plan.</p>
        <div class="mt-6 flex justify-center">
          <v-button :to="{ name: 'register' }" v-track.welcome_create_form_click :arrow="true" color="blue">
            Create a form for FREE
          </v-button>
        </div>
        <div class="flex justify-center mt-6">
          <a target="_blank" href="" class="mr-4">
            <svg class="w-6 h-6 text-white" viewBox="0 0 24 20" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M7.55016 19.7502C16.6045 19.7502 21.5583 12.2469 21.5583 5.74211C21.5583 5.53117 21.5536 5.31554 21.5442 5.1046C22.5079 4.40771 23.3395 3.5445 24 2.55554C23.1025 2.95484 22.1496 3.21563 21.1739 3.32898C22.2013 2.71315 22.9705 1.74572 23.3391 0.606011C22.3726 1.1788 21.3156 1.58286 20.2134 1.80085C19.4708 1.01181 18.489 0.48936 17.4197 0.314295C16.3504 0.13923 15.2532 0.321295 14.2977 0.832341C13.3423 1.34339 12.5818 2.15495 12.1338 3.14156C11.6859 4.12816 11.5754 5.23486 11.8195 6.29054C9.86249 6.19233 7.94794 5.68395 6.19998 4.79834C4.45203 3.91274 2.90969 2.66968 1.67297 1.14976C1.0444 2.23349 0.852057 3.51589 1.13503 4.73634C1.418 5.95678 2.15506 7.02369 3.19641 7.72023C2.41463 7.69541 1.64998 7.48492 0.965625 7.10617V7.1671C0.964925 8.30439 1.3581 9.40683 2.07831 10.287C2.79852 11.1672 3.80132 11.7708 4.91625 11.9952C4.19206 12.1934 3.43198 12.2222 2.69484 12.0796C3.00945 13.0577 3.62157 13.9131 4.44577 14.5266C5.26997 15.14 6.26512 15.4808 7.29234 15.5015C5.54842 16.8714 3.39417 17.6144 1.17656 17.6109C0.783287 17.6103 0.390399 17.5861 0 17.5387C2.25286 18.984 4.87353 19.7516 7.55016 19.7502Z" fill="currentColor"/>
            </svg>
          </a>
          <a target="_blank" href="" class="mr-4">
            <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M24 12C24 5.37258 18.6274 0 12 0C5.37258 0 0 5.37258 0 12C0 17.9895 4.3882 22.954 10.125 23.8542V15.4688H7.07812V12H10.125V9.35625C10.125 6.34875 11.9166 4.6875 14.6576 4.6875C15.9701 4.6875 17.3438 4.92188 17.3438 4.92188V7.875H15.8306C14.34 7.875 13.875 8.80008 13.875 9.75V12H17.2031L16.6711 15.4688H13.875V23.8542C19.6118 22.954 24 17.9895 24 12Z" fill="currentColor"/>
            </svg>
          </a>
          <a target="_blank" href="" class="mr-4">
            <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" clip-rule="evenodd" d="M12 0C5.3724 0 0 5.3808 0 12.0204C0 17.3304 3.438 21.8364 8.2068 23.4252C8.8068 23.5356 9.0252 23.1648 9.0252 22.8456C9.0252 22.5612 9.0156 21.804 9.0096 20.802C5.6712 21.528 4.9668 19.1904 4.9668 19.1904C4.422 17.8008 3.6348 17.4312 3.6348 17.4312C2.5452 16.6872 3.7176 16.7016 3.7176 16.7016C4.9212 16.7856 5.5548 17.94 5.5548 17.94C6.6252 19.776 8.364 19.2456 9.0468 18.9384C9.1572 18.162 9.4668 17.6328 9.81 17.3328C7.146 17.0292 4.344 15.9972 4.344 11.3916C4.344 10.08 4.812 9.006 5.5788 8.166C5.4552 7.8624 5.0436 6.6396 5.6964 4.986C5.6964 4.986 6.7044 4.662 8.9964 6.2172C9.97532 5.95022 10.9853 5.81423 12 5.8128C13.02 5.8176 14.046 5.9508 15.0048 6.2172C17.2956 4.662 18.3012 4.9848 18.3012 4.9848C18.9564 6.6396 18.5436 7.8624 18.4212 8.166C19.1892 9.006 19.6548 10.08 19.6548 11.3916C19.6548 16.0092 16.848 17.0256 14.1756 17.3232C14.6064 17.694 14.9892 18.4272 14.9892 19.5492C14.9892 21.1548 14.9748 22.452 14.9748 22.8456C14.9748 23.1672 15.1908 23.5416 15.8004 23.424C18.19 22.6225 20.2672 21.0904 21.7386 19.0441C23.2099 16.9977 24.001 14.5408 24 12.0204C24 5.3808 18.6264 0 12 0Z" fill="currentColor"/>
            </svg>
          </a>
        </div>
      </div>

      <open-form-footer class="dark:border-t border-t"/>

    </div>
  </div>
</template>

<script>
import {mapGetters} from 'vuex'
import Features from '~/components/pages/welcome/Features'
import OpenFormFooter from '../components/pages/OpenFormFooter'
import Testimonials from '../components/pages/welcome/Testimonials'

export default {
  components: {Testimonials, OpenFormFooter, Features},

  layout: 'default',

  metaInfo() {
    return {title: 'Create beautiful & open-source forms for free'}
  },

  data: () => ({
    title: window.config.appName
  }),

  mounted() {
  },

  methods: {
    // openCrisp () {
    //   window.$crisp.push(['do', 'chat:show'])
    //   window.$crisp.push(['do', 'chat:open'])
    //   window.$crisp.push(['do', 'message:send', ['text', "Hi! I'd like to learn more about OpnForm"]])
    // }
  },

  computed: {
    ...mapGetters({
      authenticated: 'auth/check'
    })
  }
}
</script>

<style lang="scss" scoped>

.customer-logo-container {
  max-width: 130px;
  width: 100%;
}

@screen md {
  #macbook-video {
    position: absolute;
    max-width: 84.8% !important;
    right: 0px;
    top: 6.8%;
  }
}
</style>
