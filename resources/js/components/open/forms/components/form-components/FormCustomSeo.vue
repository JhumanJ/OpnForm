<template>
  <collapse class="p-5 w-full border-b" v-model="isCollapseOpen">
    <template #title>
      <h3 id="v-step-2" class="font-semibold text-lg">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
             class="h-5 w-5 inline -ml-1 mr-2 -mt-1 transition-colors" :class="{'text-blue-600':isCollapseOpen, 'text-gray-500':!isCollapseOpen}"
        >
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"
          />
        </svg>

        Link Settings - SEO
        <pro-tag />
      </h3>
    </template>
    <p class="mt-4 text-gray-500 text-sm">
      Customize the image and text that appear when you share your form on other sites (Open Graph).
    </p>
    <text-input v-model="form.seo_meta.page_title" name="page_title" class="mt-4"
                label="Page Title" help="Under or approximately 60 characters"
    />
    <text-area-input v-model="form.seo_meta.page_description" name="page_description" class="mt-4"
                     label="Page Description" help="Between 150 and 160 characters"
    />
    <image-input v-model="form.seo_meta.page_thumbnail" name="page_thumbnail" class="mt-4"
                 label="Page Thumbnail Image" help="Also know as og:image - 1200 X 630"
    />
  </collapse>
</template>

<script>
import Collapse from '../../../../common/Collapse.vue'
import ProTag from '../../../../common/ProTag.vue'

export default {
  components: { Collapse, ProTag },
  props: {},
  data () {
    return {
      isCollapseOpen: false
    }
  },
  computed: {
    form: {
      get () {
        return this.$store.state['open/working_form'].content
      },
      /* We add a setter */
      set (value) {
        this.$store.commit('open/working_form/set', value)
      }
    }
  },
  watch: {},
  mounted () {
    ['page_title', 'page_description', 'page_thumbnail'].forEach((keyname) => {
      if (this.form.seo_meta[keyname] === undefined) {
        this.form.seo_meta[keyname] = null
      }
    })
  },
  methods: {}
}
</script>
