<template>
  <div class="flex flex-col min-h-full mt-6">
    <div class="w-full flex-grow md:w-4/5 lg:w-2/3 md:mx-auto md:max-w-4xl px-4">
      <div>
        <div class="flex flex-wrap items-center mt-6 mb-4">
          <h2 class="text-nt-blue text-3xl font-bold flex-grow">
            Use Cases
          </h2>
        </div>
        <div class="mb-4">
          <div v-if="useCases && useCases.length"
               class="grid max-w-3xl grid-cols-1 mx-auto text-center sm:text-left sm:grid-cols-2 gap-y-8 gap-x-8 lg:gap-x-20">
            <div class="relative group" v-for="(useCase, index) in useCases" :key="index">
              <div class="overflow-hidden rounded-lg aspect-w-16 aspect-h-9">
                <img class="object-cover w-full h-full transition-all duration-300 transform group-hover:scale-125"
                :src="asset('img/pages/use_cases/'+useCase.image_url)" alt=""/>
              </div>
              <p class="text-xl font-bold text-gray-900 font-pj">{{ useCase.title }}</p>
              <router-link :to="{params:{slug:useCase.slug},name:'use_cases.show'}" title="">
                <span class="absolute inset-0" aria-hidden="true"></span>
              </router-link>
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


export default {
  components: {OpenFormFooter},
 
  data() {
    return {
      metaTitle: "Use Cases",
      useCases: use_cases

    }
  },

  mounted() {},

  methods: {},

  computed: {}
}
</script>
