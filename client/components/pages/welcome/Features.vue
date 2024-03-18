<template>
  <div id="features" class="px-4 mx-auto sm:max-w-xl md:max-w-full lg:max-w-screen-xl md:px-24 lg:px-8">

    <div class="mb-16 max-w-xl md:mx-auto sm:text-center lg:max-w-2xl ">
      <h2
        class="mb-6 font-sans text-4xl font-semibold leading-none tracking-tight text-gray-900 dark:text-gray-100 sm:text-4xl md:mx-auto">
        The easiest way to create forms. <br/>
        Generous unlimited <span class="text-nt-blue">free plan.</span>
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
          img: '/img/pages/welcome/step-1.jpg'
        },
        {
          title: 'Share',
          description: 'Your form has a unique link that you can share everywhere. Send the link, or even embed the form on your website.',
          features: [
            'Share the link to your form',
            'Embed the form on your website'
          ],
          img: '/img/pages/welcome/step-2.jpg'
        },
        {
          title: 'Get Results',
          description: 'Receive your form submissions. Receive notifications, send confirmations. Export submissions and check your form analytics.',
          features: [
            'Unlimited form submissions for free',
            'Easily export submissions as CSV',
            'Views & Submissions Analytics'
          ],
          img: '/img/pages/welcome/step-3.jpg'
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
                 sizes="320px sm:530px"
                 :src="step.img" alt="product-feature-image"/>
      </div>
    </div>

    <div class="grid md:grid-cols-3 mt-20">
      <div class="mb-8 md:mr-10">
        <svg class="w-10 h-10 text-nt-blue" viewBox="0 0 44 42" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M26.0006 40H18.0006M2.58828 9.63978C2.55958 6.73709 4.12455 4.02649 6.6527 2.59999M41.405 9.6398C41.4337 6.7371 39.8687 4.0265 37.3406 2.60001M34.0006 14C34.0006 10.8174 32.7363 7.76516 30.4859 5.51472C28.2354 3.26428 25.1832 2 22.0006 2C18.818 2 15.7658 3.26428 13.5153 5.51472C11.2649 7.76516 10.0006 10.8174 10.0006 14C10.0006 20.1804 8.44154 24.4119 6.69993 27.2108C5.23085 29.5717 4.49631 30.7522 4.52325 31.0815C4.55307 31.4461 4.63032 31.5852 4.92415 31.8032C5.18951 32 6.38578 32 8.7783 32H35.2229C37.6154 32 38.8117 32 39.077 31.8032C39.3709 31.5852 39.4481 31.4461 39.4779 31.0815C39.5049 30.7522 38.7703 29.5718 37.3013 27.2108C35.5597 24.4119 34.0006 20.1804 34.0006 14Z" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <h3 class="my-3 font-semibold">Notifications</h3>
        <p>Receive notifications directly in Slack or in your mailbox whenever your from has a new submission (if you
          want to).</p>
      </div>
      <div class="mb-8 md:mr-10">
        <svg class="w-10 h-10 text-nt-blue" viewBox="0 0 45 44" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M21.6666 2H27.0666C30.4269 2 32.1071 2 33.3906 2.65396C34.5195 3.2292 35.4374 4.14708 36.0127 5.27606C36.6666 6.55953 36.6666 8.23969 36.6666 11.6V32.4C36.6666 35.7603 36.6666 37.4405 36.0127 38.7239C35.4374 39.8529 34.5195 40.7708 33.3906 41.346C32.1071 42 30.4269 42 27.0666 42H14.2666C10.9063 42 9.22615 42 7.94268 41.346C6.81371 40.7708 5.89583 39.8529 5.32059 38.7239C4.66663 37.4405 4.66663 35.7603 4.66663 32.4V31M28.6666 24H19.6666M28.6666 16H21.6666M28.6666 32H12.6666M8.66663 18V7C8.66663 5.34315 10.0098 4 11.6666 4C13.3235 4 14.6666 5.34315 14.6666 7V18C14.6666 21.3137 11.9803 24 8.66663 24C5.35292 24 2.66663 21.3137 2.66663 18V10" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <h3 class="my-3 font-semibold">File Uploads</h3>
        <p>Easily add file upload inputs to your forms. Uploaded files are securely stored for you. Up to 5mb!</p>
      </div>
      <div class="mb-8 md:mr-10">
        <svg class="w-10 h-10 text-nt-blue" viewBox="0 0 45 44" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M2.33331 22C2.33331 33.0457 11.2876 42 22.3333 42C25.647 42 28.3333 39.3137 28.3333 36V35C28.3333 34.0712 28.3333 33.6067 28.3846 33.2168C28.7391 30.5244 30.8578 28.4058 33.5502 28.0513C33.9401 28 34.4045 28 35.3333 28H36.3333C39.647 28 42.3333 25.3137 42.3333 22C42.3333 10.9543 33.379 2 22.3333 2C11.2876 2 2.33331 10.9543 2.33331 22Z" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M12.3333 24C13.4379 24 14.3333 23.1046 14.3333 22C14.3333 20.8954 13.4379 20 12.3333 20C11.2287 20 10.3333 20.8954 10.3333 22C10.3333 23.1046 11.2287 24 12.3333 24Z" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M30.3333 16C31.4379 16 32.3333 15.1046 32.3333 14C32.3333 12.8954 31.4379 12 30.3333 12C29.2287 12 28.3333 12.8954 28.3333 14C28.3333 15.1046 29.2287 16 30.3333 16Z" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M18.3333 14C19.4379 14 20.3333 13.1046 20.3333 12C20.3333 10.8954 19.4379 10 18.3333 10C17.2287 10 16.3333 10.8954 16.3333 12C16.3333 13.1046 17.2287 14 18.3333 14Z" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
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
