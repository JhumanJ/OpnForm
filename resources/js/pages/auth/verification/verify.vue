<template>
  <div class="row">
    <div class="col-lg-8 m-auto  px-4">
      <h1 class="my-6">
        {{ $t('verify_email') }}
      </h1>
      <template v-if="success">
        <div class="alert alert-success" role="alert">
          {{ success }}
        </div>

        <router-link :to="{ name: 'login' }" class="btn btn-primary">
          {{ $t('login') }}
        </router-link>
      </template>
      <template v-else>
        <div class="alert alert-danger" role="alert">
          {{ error || $t('failed_to_verify_email') }}
        </div>

        <router-link :to="{ name: 'verification.resend' }" class="small float-right">
          {{ $t('resend_verification_link') }}
        </router-link>
      </template>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import SeoMeta from '../../../mixins/seo-meta.js'

const qs = (params) => Object.keys(params).map(key => `${key}=${params[key]}`).join('&')

export default {
  async beforeRouteEnter (to, from, next) {
    try {
      const { data } = await axios.post(`/api/email/verify/${to.params.id}?${qs(to.query)}`)

      next(vm => {
        vm.success = data.status
      })
    } catch (e) {
      next(vm => {
        vm.error = e.response.data.status
      })
    }
  },

  middleware: 'guest',
  mixins: [SeoMeta],
  
  data: () => ({
    metaTitle: 'Verify Email',
    error: '',
    success: ''
  })
}
</script>
