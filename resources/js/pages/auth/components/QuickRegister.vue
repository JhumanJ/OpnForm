<template>
  <div>
    <!--  Login modal  -->
    <modal :show="showLoginModal" @close="showLoginModal=false" max-width="lg">
      <template #icon>
        <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path
            d="M12 8V16M8 12H16M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12Z"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </template>
      <template #title>
        Login to OpnForm
      </template>
      <div class="px-4">
        <login-form :isQuick="true" @openRegister="openRegister" @afterQuickLogin="afterQuickLogin" />
      </div>
    </modal>


    <!--  Register modal  -->
    <modal :show="showRegisterModal" @close="$emit('close')" max-width="lg">
      <template #icon>
        <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path
            d="M12 8V16M8 12H16M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12Z"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </template>
      <template #title>
        Create an account
      </template>
      <div class="px-4">
        <register-form :isQuick="true" @openLogin="openLogin" @afterQuickLogin="afterQuickLogin" />
      </div>
    </modal>
    
  </div>
</template>

<script>
import LoginForm from './LoginForm'
import RegisterForm from './RegisterForm'

export default {
  name: 'QuickRegister',
  components: {
    LoginForm,
    RegisterForm
  },
  props: {
    showRegisterModal: {
      type: Boolean,
      required: true
    }
  },

  data: () => ({
    showLoginModal: false,
  }),

  mounted() {
  },

  methods: {
    openLogin(){
      this.showLoginModal = true
      this.$emit('close')
    },
    openRegister(){
      this.showLoginModal = false
      this.$emit('reopen')
    },
    afterQuickLogin(){
      this.showLoginModal = false
      this.$emit('afterLogin')
    }
  }
}
</script>
