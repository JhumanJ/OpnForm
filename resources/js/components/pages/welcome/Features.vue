<template>
  <div id="features" class="px-4 mx-auto sm:max-w-xl md:max-w-full lg:max-w-screen-xl md:px-24 lg:px-8">

    <div class="mb-16 max-w-xl md:mx-auto sm:text-center lg:max-w-2xl ">
      <h2
        class="mb-6 font-sans text-4xl font-semibold leading-none tracking-tight text-gray-900 dark:text-gray-100 sm:text-4xl md:mx-auto">
        The easiest way to create forms. <br/>
        And it's <span class="text-nt-blue">100% free!</span>
      </h2>
      <p class="text-base text-gray-700 dark:text-gray-300 md:text-lg">
        Need a contact form? Doing a survey? Create a form in 2 minutes and start receiving submissions.
      </p>
    </div>

    <div class="flex flex-wrap items-center mt-16" :class="{'md:flex-row-reverse':index%2==1}" v-for="(step,index) in [
        {
          title: 'Create',
          description: 'Create a form in 2 minutes. More than 10 input types, images, logic and much more.',
          features: [
            'Build a simple form in minutes.',
            'No coding needed.'
          ],
          img: 'img/pages/welcome/step-1.jpg'
        },
        {
          title: 'Share',
          description: 'Velit officia consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud amet.\n',
          features: [
            'Build a simple form in minutes.',
            'No coding needed.'
          ],
          img: 'img/pages/welcome/step-2.jpg'
        },
        {
          title: 'Get Results',
          description: 'Create a form in 2 minutes. More than 10 input types, images, logic and much more.',
          features: [
            'Build a simple form in minutes.',
            'No coding needed.'
          ],
          img: 'img/pages/welcome/step-3.jpg'
        }
      ]" :key="step.title">
      <div class="w-full md:w-1/2 lg:w-5/12" :class="{'md:pl-4':index%2==1, 'md:pr-4':index%2==0}">
        <svg v-if="step.title=='Create'" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg"
             stroke="currentColor" class="w-10 h-10 text-nt-blue">
          <path
            d="M22 7.99997H13.6C10.2397 7.99997 8.55953 7.99997 7.27606 8.65393C6.14708 9.22917 5.2292 10.1471 4.65396 11.276C4 12.5595 4 14.2397 4 17.6V34.4C4 37.7603 4 39.4404 4.65396 40.7239C5.2292 41.8529 6.14708 42.7708 7.27606 43.346C8.55953 44 10.2397 44 13.6 44H30.4C33.7603 44 35.4405 44 36.7239 43.346C37.8529 42.7708 38.7708 41.8529 39.346 40.7239C40 39.4404 40 37.7603 40 34.4V26M15.9999 32H19.349C20.3274 32 20.8166 32 21.2769 31.8894C21.6851 31.7915 22.0753 31.6298 22.4331 31.4105C22.8368 31.1632 23.1827 30.8173 23.8745 30.1255L43 11C44.6569 9.34311 44.6569 6.65682 43 4.99997C41.3431 3.34311 38.6569 3.34311 37 4.99996L17.8745 24.1255C17.1827 24.8173 16.8368 25.1632 16.5894 25.5668C16.3701 25.9247 16.2085 26.3149 16.1105 26.723C15.9999 27.1834 15.9999 27.6726 15.9999 28.6509V32Z"
            stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <svg v-else-if="step.title=='Share'" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg"
             stroke="currentColor" class="w-10 h-10 text-nt-blue">
          <path
            d="M17.18 27.02L30.84 34.98M30.82 13.02L17.18 20.98M42 10C42 13.3137 39.3137 16 36 16C32.6863 16 30 13.3137 30 10C30 6.68629 32.6863 4 36 4C39.3137 4 42 6.68629 42 10ZM18 24C18 27.3137 15.3137 30 12 30C8.68629 30 6 27.3137 6 24C6 20.6863 8.68629 18 12 18C15.3137 18 18 20.6863 18 24ZM42 38C42 41.3137 39.3137 44 36 44C32.6863 44 30 41.3137 30 38C30 34.6863 32.6863 32 36 32C39.3137 32 42 34.6863 42 38Z"
            stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <svg v-else-if="step.title=='Share'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke-width="1.5" stroke="currentColor" class="w-10 h-10 text-nt-blue">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
        </svg>
        <svg v-else-if="step.title=='Get Results'" class="w-10 h-10 text-nt-blue" viewBox="0 0 48 48" fill="none"
             xmlns="http://www.w3.org/2000/svg">
          <path
            d="M42 42H9.2C8.07989 42 7.51984 42 7.09202 41.782C6.71569 41.5903 6.40973 41.2843 6.21799 40.908C6 40.4802 6 39.9201 6 38.8V6M40 16L32.1623 24.3653C31.8652 24.6823 31.7167 24.8409 31.5375 24.9228C31.3794 24.9951 31.2051 25.0249 31.0319 25.0093C30.8357 24.9916 30.6429 24.8915 30.2574 24.6913L23.7426 21.3087C23.3571 21.1085 23.1643 21.0084 22.9681 20.9907C22.7949 20.9751 22.6206 21.0049 22.4625 21.0772C22.2833 21.1591 22.1348 21.3177 21.8377 21.6347L14 30"
            stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>


        <h4 class="my-5 text-2xl font-medium">{{ index + 1 }}. {{ step.title }}</h4>
        <p class="dark:text-white">
          {{ step.description }}
        </p>
        <div class="mb-8">
          <div class="flex mt-4" v-for="feature in step.features" :key="feature">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                 stroke="currentColor" class="w-4 h-4 mt-1 mr-2 text-nt-blue">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
            </svg>
            {{ feature }}
          </div>
        </div>
      </div>
      <div class="w-full md:w-1/2 lg:w-7/12 flex items-center justify-center relative w-full"
           :class="{'md:pr-8':index%2==1, 'md:pl-8':index%2==0}">
        <img loading="lazy" class="block rounded-2xl w-full"
             :src="asset(step.img)" alt="cover-product">
      </div>
    </div>

    <div class="grid md:grid-cols-3 mt-20">
      <div class="mb-8 md:mr-10">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
             class="w-10 h-10 text-nt-blue">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0M3.124 7.5A8.969 8.969 0 015.292 3m13.416 0a8.969 8.969 0 012.168 4.5"/>
        </svg>
        <h3 class="my-3 font-semibold">Notifications</h3>
        <p>Receive notifications directly in Slack or in your mailbox whenever your from has a new submission (if you
          want to).</p>
      </div>
      <div class="mb-8 md:mr-10">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
             class="w-10 h-10 text-nt-blue">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
        </svg>
        <h3 class="my-3 font-semibold">File Uploads</h3>
        <p>Easily add file upload inputs to your forms. Uploaded files are securely stored for you. Up to 5mb!</p>
      </div>
      <div class="mb-8 md:mr-10">
        <svg class="w-10 h-10 text-nt-blue" viewBox="0 0 49 48" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path
            d="M4.33331 24C4.33331 35.0457 13.2876 44 24.3333 44C27.647 44 30.3333 41.3137 30.3333 38V37C30.3333 36.0712 30.3333 35.6067 30.3846 35.2168C30.7391 32.5244 32.8578 30.4058 35.5502 30.0513C35.9401 30 36.4045 30 37.3333 30H38.3333C41.647 30 44.3333 27.3137 44.3333 24C44.3333 12.9543 35.379 4 24.3333 4C13.2876 4 4.33331 12.9543 4.33331 24Z"
            stroke="#155EEF" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
          <path
            d="M14.3333 26C15.4379 26 16.3333 25.1046 16.3333 24C16.3333 22.8954 15.4379 22 14.3333 22C13.2287 22 12.3333 22.8954 12.3333 24C12.3333 25.1046 13.2287 26 14.3333 26Z"
            stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
          <path
            d="M32.3333 18C33.4379 18 34.3333 17.1046 34.3333 16C34.3333 14.8954 33.4379 14 32.3333 14C31.2287 14 30.3333 14.8954 30.3333 16C30.3333 17.1046 31.2287 18 32.3333 18Z"
            stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
          <path
            d="M20.3333 16C21.4379 16 22.3333 15.1046 22.3333 14C22.3333 12.8954 21.4379 12 20.3333 12C19.2287 12 18.3333 12.8954 18.3333 14C18.3333 15.1046 19.2287 16 20.3333 16Z"
            stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>

        <h3 class="my-3 font-semibold">Customize Everything</h3>
        <p>Change form themes, change texts, colors, add images, add custom thank you pages and much more.</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {

  props: {},
  data: () => ({}),

  methods: {}
}
</script>
