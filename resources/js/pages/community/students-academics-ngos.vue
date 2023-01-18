<template>
  <div>
    <div class="mt-6 flex flex-col">
      <div class="w-full md:max-w-3xl md:mx-auto px-4 md:pt-16 pb-10">
        <h1 class="sm:text-5xl mb-4">
          OpnForm Discount for Students, Academics and NGOs
        </h1>
        <notion-page page-id="c65a499d39834e0b8978556a8d7af867" />
      </div>
    </div>
    <open-form-footer />
  </div>
</template>

<script>
import OpenFormFooter from '../../components/pages/OpenFormFooter.vue'

export default {
  components: { OpenFormFooter },
  layout: 'default',

  props: {
    metaTitle: { type: String, default: 'OpnForm Discount for Students, Academics and NGOs' },
    metaDescription: { type: String, default: 'If you are a student, an academic of if you work for a NGO we are happy to offer you a 40% discount on your OpnForm Pro subscription.' }
  },

  data: () => ({
  }),

  computed: {},

  mounted () {
  }
}
</script>
