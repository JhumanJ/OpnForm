<template>
  <div class="mx-auto mb-12 max-w-7xl px-6 lg:px-8">
    <div class="mx-auto max-w-2xl text-center">
      <h2 class="text-lg font-semibold leading-8 tracking-tight text-blue-500">
        Single or multi-page forms
      </h2>
      <p
        class="mt-2 text-3xl font-semibold tracking-tight text-neutral-900 sm:text-4xl"
      >
        Discover our beautiful templates
      </p>
      <p class="mt-3 px-8 text-center text-lg text-neutral-400">
        If you need inspiration, checkout our templates.
      </p>
    </div>
    <div class="my-3 flex justify-center">
      <NuxtLink :to="{ name: 'templates' }">
        See all templates
        <svg
          class="h-4 w-4 inline"
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 20 20"
          fill="currentColor"
          aria-hidden="true"
        >
          <path
            fill-rule="evenodd"
            d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
            clip-rule="evenodd"
          />
        </svg>
      </NuxtLink>
    </div>

    <div
      v-if="sliderTemplates.length > 0"
      class="w-full inline-flex flex-nowrap overflow-hidden [mask-image:_linear-gradient(to_right,transparent_0,_black_128px,_black_calc(100%-128px),transparent_100%)]"
    >
      <ul
        ref="templates-slider"
        class="flex justify-center md:justify-start animate-infinite-scroll"
      >
        <li
          v-for="(template) in sliderTemplates"
          :key="template.id"
          class="mx-4 w-72 h-auto"
        >
          <single-template :template="template" />
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
import SingleTemplate from "../templates/SingleTemplate.vue"

export default {
  components: { SingleTemplate },
  setup() {
    const { list } = useTemplates()
    const { data: templates } = list({ limit: 10 })

    return {
      sliderTemplates: computed(() => templates.value ?? []),
    }
  },

  watch: {
    sliderTemplates: {
      deep: true,
      handler() {
        this.$nextTick(() => {
          this.setInfinite()
        })
      },
    },
  },

  methods: {
    setInfinite() {
      const ul = this.$refs["templates-slider"]
      if (ul) {
        ul.insertAdjacentHTML("afterend", ul.outerHTML)
        ul.nextSibling.setAttribute("aria-hidden", "true")
      }
    },
  },
}
</script>
