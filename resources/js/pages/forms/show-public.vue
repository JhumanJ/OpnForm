<template>
  <div class="flex flex-col">
    <div v-if="form && !isIframe && (form.logo_picture || form.cover_picture)">
      <div v-if="form.cover_picture">
        <div id="cover-picture" class="max-h-56 w-full overflow-hidden flex items-center justify-center">
          <img alt="Form Cover Picture" :src="form.cover_picture" class="w-full">
        </div>
      </div>
      <div v-if="form.logo_picture" class="w-full p-5 relative mx-auto"
           :class="{'pt-20':!form.cover_picture, 'md:w-3/5 lg:w-1/2 md:max-w-2xl': form.width === 'centered', 'max-w-7xl': (form.width === 'full' && !isIframe) }"
      >
        <img alt="Logo Picture" :src="form.logo_picture"
             :class="{'top-5':!form.cover_picture, '-top-10':form.cover_picture}"
             class="w-20 h-20 object-contain absolute left-5 transition-all"
        >
      </div>
    </div>
    <div class="w-full mx-auto px-4"
         :class="{'mt-6':!isIframe, 'md:w-3/5 lg:w-1/2 md:max-w-2xl': form && (form.width === 'centered'), 'max-w-7xl': (form && form.width === 'full' && !isIframe)}"
    >
      <div v-if="!formLoading && !form">
        <h1 class="mt-6" v-text="'Whoops'" />
        <p class="mt-6">
          Unfortunately we could not find this form. It may have been deleted by it's author.
        </p>
        <p class="mb-10 mt-4">
          <router-link :to="{name:'welcome'}">
            Create your form for free with OpnForm
          </router-link>
        </p>
      </div>
      <div v-else-if="formLoading">
        <p class="text-center mt-6 p-4">
          <loader class="h-6 w-6 text-nt-blue mx-auto" />
        </p>
      </div>
      <template v-else>
        <div v-if="recordLoading">
          <p class="text-center mt-6 p-4">
            <loader class="h-6 w-6 text-nt-blue mx-auto" />
          </p>
        </div>
        <open-complete-form v-show="!recordLoading" ref="open-complete-form" :form="form" class="mb-10"
                            @password-entered="passwordEntered"
        />
      </template>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import { computed } from 'vue'
import { useFormsStore } from '../../stores/forms'
import { useRecordsStore } from '../../stores/records'
import OpenCompleteForm from '../../components/open/forms/OpenCompleteForm.vue'
import sha256 from 'js-sha256'
import { onBeforeRouteLeave } from 'vue-router'
import { disableDarkMode, handleDarkMode, handleTransparentMode } from '~lib/forms/public-page'
import { focusOnFirstFormElement } from '../../../../client/lib/forms/public-page'

function loadForm (slug) {
  const formsStore = useFormsStore()
  if (formsStore.loading) return
  formsStore.startLoading()
  return axios.get('/api/forms/' + slug).then((response) => {
    const form = response.data
    formsStore.set([response.data])

    // Custom code injection
    if (form.custom_code) {
      const scriptEl = document.createRange().createContextualFragment(form.custom_code)
      document.head.append(scriptEl)
    }

    handleDarkMode(form)
    handleTransparentMode(form)

    formsStore.stopLoading()
  }).catch(() => {
    formsStore.stopLoading()
  })
}

export default {
  components: { OpenCompleteForm },

  setup () {
    const crisp = useCrisp()
    crisp.hideChat()

    onBeforeRouteLeave((to, from) => {
      crisp.showChat()
      disableDarkMode()
    })

    const formsStore = useFormsStore()
    const recordsStore = useRecordsStore()

    const passwordEntered = function (password) {
      useCookie('password-' + this.form.slug, { maxAge: { expires: 60 * 60 * 7 }, sameSite: false, secure: true }).value = sha256(password)
      loadForm(this.formSlug).then(() => {
        if (this.form.is_password_protected) {
          this.$refs['open-complete-form'].addPasswordError('Invalid password.')
        }
      })
    }

    const slug = useRoute()

    return {
      formsStore,
      passwordEntered,
      submitted: ref(false),
      isIframe: useIsIframe(),
      form: computed(() => formsStore.getByKey()),
      formLoading: computed(() => formsStore.loading),
      recordLoading: computed(() => recordsStore.loading)
    }
  },

  computed: {
    formSlug () {
      return this.$route.params.slug
    },
    form () {
      return this.formsStore.getBySlug(this.formSlug)
    }
    // metaTitle () {
    //   if (this.form && this.form.is_pro && this.form.seo_meta.page_title) {
    //     return this.form.seo_meta.page_title
    //   }
    //   return this.form ? this.form.title : 'Create beautiful forms'
    // },
    // metaTemplate () {
    //   if (this.form && this.form.is_pro && this.form.seo_meta.page_title) {
    //     // Disable template if custom SEO title
    //     return '%s'
    //   }
    //   return null
    // },
    // metaDescription () {
    //   if (this.form && this.form.is_pro && this.form.seo_meta.page_description) {
    //     return this.form.seo_meta.page_description
    //   }
    //   return (this.form && this.form.description) ? this.form.description.substring(0, 160) : null
    // },
    // metaImage () {
    //   if (this.form && this.form.is_pro && this.form.seo_meta.page_thumbnail) {
    //     return this.form.seo_meta.page_thumbnail
    //   }
    //   return (this.form && this.form.cover_picture) ? this.form.cover_picture : null
    // },
    // metaTags () {
    //   return (this.form && this.form.can_be_indexed) ? [] : [{ name: 'robots', content: 'noindex' }]
    // }
  },

  mounted () {
    loadForm(this.formSlug).then(() => {
      if (this.isIframe) return
      focusOnFirstFormElement()
    })
  }
}
</script>
