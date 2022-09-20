<template>
  <div>
    <div class="mt-6 flex flex-col">
      <div class="w-full md:max-w-3xl md:mx-auto px-4 md:pt-16">
        <h1 class="sm:text-5xl">
          Privacy Policy
        </h1>
        <notion-page page-id="9c97349ceda7455aab9b341d1ff70f79" />
      </div>
    </div>
    <open-form-footer />
  </div>
</template>

<script>
import NotionPage from '../../components/open/NotionPage'
import OpenFormFooter from '../../components/pages/OpenFormFooter'

export default {
  components: { OpenFormFooter, NotionPage },
  layout: 'default',

  metaInfo () {
    return { title: 'Privacy Policy' }
  },

  data: () => ({
  }),

  computed: {},

  mounted () {
  }
}
</script>
