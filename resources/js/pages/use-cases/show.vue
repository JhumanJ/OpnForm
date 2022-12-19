<template>
  <div>
    <div class="mt-6 flex flex-col">
      <div class="w-full md:max-w-3xl md:mx-auto px-4">
        <breadcrumb :path="breadcrumbs" />
        <p v-if="(useCase === null || !useCase)">
          Use case does not exist.
        </p>
        <div v-else>
          <div class="flex flex-wrap items-center mt-6 mb-4">
            <div class="flex w-full items-center">
              <h2 class="text-nt-blue text-3xl font-bold flex-grow">
                {{ useCase.title }}
              </h2>
            </div>
          </div>
          <div class="mb-10">
            <div class="w-full shadow-xl rounded-lg my-5 max-h-72 flex items-center justify-center overflow-hidden">
              <img :src="metaImage" alt="Feature cover image" class="w-full object-cover">
            </div>
            <div class="mt-10" v-html="useCase.body" />

            <div v-if="Object.keys(useCase.links).length > 0">
              <p class="font-semibold mt-5">
                Related Links
              </p>
              <ul class="list-disc list-inside">
                <li v-for="(value, name) in useCase.links" :key="name">
                  <a :href="value" target="_blank">{{ name }}</a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
    <open-form-footer class="mt-8 border-t"/>
  </div>
</template>

<script>
import use_cases from '../../../data/use_cases.json'
import OpenFormFooter from '../../components/pages/OpenFormFooter'
import Breadcrumb from '../../components/common/Breadcrumb'

export default {
  components: { Breadcrumb, OpenFormFooter },
  layout: 'default',

  data: () => ({
    useCases: use_cases
  }),

  computed: {
    metaTitle () {
      return this.useCase ? this.useCase.title : 'Use Case'
    },
    metaImage () {
      return this.asset('img/pages/use_cases/' + this.useCase.image_url)
    },
    useCase () {
      return this.useCases.find(item => item.slug === this.$route.params.slug)
    },
    breadcrumbs () {
      if (!this.useCase) {
        return [{ route: { name: 'use_cases' }, label: 'Use Cases' }]
      }
      return [{ route: { name: 'use_cases' }, label: 'Use Cases' }, { label: this.useCase.title }]
    }
  },

  mounted () {
  }
}
</script>
