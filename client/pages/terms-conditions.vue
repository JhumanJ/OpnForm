<template>
  <div>
    <div class="mt-6 flex flex-col">
      <div class="w-full md:max-w-3xl md:mx-auto px-4 md:pt-16">
        <h1 class="sm:text-5xl">
          Terms & Conditions
        </h1>
        <NotionPage page-id="246420da2834480ca04047b0c5a00929" />
      </div>
    </div>
    <open-form-footer />
  </div>
</template>

<script>
import OpenFormFooter from '../components/pages/OpenFormFooter.vue'

export default {
  components: { OpenFormFooter },
  layout: 'default',

  data: () => ({
    metaTitle: 'Terms & Conditions',
  }),

  computed: {},

  mounted () {
  }
}
</script>
