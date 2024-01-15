<template>
    <!--  Forgot password modal  -->
    <modal :show="show" @close="close" max-width="lg">
      <template #icon>
        <template v-if="isMailSent">
          <svg width="56" height="56" viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect class="text-blue-50" width="56" height="56" rx="28" fill="currentColor"/>
            <path d="M16.3333 22.1666L25.859 28.8346C26.6304 29.3746 27.016 29.6446 27.4356 29.7492C27.8061 29.8415 28.1937 29.8415 28.5643 29.7492C28.9838 29.6446 29.3695 29.3746 30.1408 28.8346L39.6666 22.1666M21.9333 37.3333H34.0666C36.0268 37.3333 37.0069 37.3333 37.7556 36.9518C38.4141 36.6163 38.9496 36.0808 39.2851 35.4223C39.6666 34.6736 39.6666 33.6935 39.6666 31.7333V24.2666C39.6666 22.3064 39.6666 21.3264 39.2851 20.5777C38.9496 19.9191 38.4141 19.3837 37.7556 19.0481C37.0069 18.6666 36.0268 18.6666 34.0666 18.6666H21.9333C19.9731 18.6666 18.993 18.6666 18.2443 19.0481C17.5857 19.3837 17.0503 19.9191 16.7147 20.5777C16.3333 21.3264 16.3333 22.3064 16.3333 24.2666V31.7333C16.3333 33.6935 16.3333 34.6736 16.7147 35.4223C17.0503 36.0808 17.5857 36.6163 18.2443 36.9518C18.993 37.3333 19.9731 37.3333 21.9333 37.3333Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </template>
        <template v-else>
          <svg width="56" height="56" viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect class="text-blue-50" width="56" height="56" rx="28" fill="currentColor"/>
            <path d="M33.8333 24.4999C33.8333 23.9028 33.6055 23.3057 33.1499 22.8501C32.6943 22.3945 32.0972 22.1667 31.5 22.1667M31.5 31.5C35.366 31.5 38.5 28.366 38.5 24.5C38.5 20.634 35.366 17.5 31.5 17.5C27.634 17.5 24.5 20.634 24.5 24.5C24.5 24.8193 24.5214 25.1336 24.5628 25.4415C24.6309 25.948 24.6649 26.2013 24.642 26.3615C24.6181 26.5284 24.5877 26.6184 24.5055 26.7655C24.4265 26.9068 24.2873 27.046 24.009 27.3243L18.0467 33.2866C17.845 33.4884 17.7441 33.5893 17.6719 33.707C17.608 33.8114 17.5608 33.9252 17.5322 34.0442C17.5 34.1785 17.5 34.3212 17.5 34.6065V36.6333C17.5 37.2867 17.5 37.6134 17.6272 37.863C17.739 38.0825 17.9175 38.261 18.137 38.3728C18.3866 38.5 18.7133 38.5 19.3667 38.5H22.1667V36.1667H24.5V33.8333H26.8333L28.6757 31.991C28.954 31.7127 29.0932 31.5735 29.2345 31.4945C29.3816 31.4123 29.4716 31.3819 29.6385 31.358C29.7987 31.3351 30.052 31.3691 30.5585 31.4372C30.8664 31.4786 31.1807 31.5 31.5 31.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </template>
      </template>
      <template #title>
        <template v-if="isMailSent">Check your email</template>
        <template v-else>Forgot password?</template>
      </template>
      <template v-if="isMailSent">
        <div class="text-center">We sent a password reset link to <br/><span>{{form.email}}</span></div>
        <div class="w-full p-4 text-center">
          <span class="mt-4">Didn't receive the email? <a href="#" class="ml-1" @click.prevent="send">Click to resend</a></span>
        </div>
      </template>
      <template v-else>
        <div class="text-center">No worries, we'll send you reset instructions.</div>
        <form @submit.prevent="send" @keydown="form.onKeydown($event)" class="p-4">
          <text-input name="email" :form="form" label="Email" placeholder="Your email address" :required="true" />

          <div class="w-full mt-6">
            <v-button :loading="form.busy" class="w-full my-3">Reset password</v-button>
          </div>
        </form>
      </template>
      <div class="w-full text-center">
        <a href="#" @click.prevent="close" class="text-xs hover:underline text-gray-500 sm:text-sm hover:text-gray-700">
          <svg class="inline mr-1" width="15" height="14" viewBox="0 0 15 14" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M13.3334 6.99996H1.66669M1.66669 6.99996L7.50002 12.8333M1.66669 6.99996L7.50002 1.16663" stroke="#475467" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          Back to log in
        </a>
      </div>
    </modal>
  </template>

  <script>
  export default {
    name: 'ForgotPasswordModal',
    components: { },
    props: {
      show: {
        type: Boolean,
        required: true
      }
    },
    data: () => ({
      isMailSent: false,
      form: useForm({
        email: ''
      })
    }),
    methods: {
      async send () {
        const { data } = await this.form.post('/password/email')
        this.isMailSent = true
      },
      close () {
        this.$emit('close')
        this.isMailSent = false
      }
    }
  }
  </script>
