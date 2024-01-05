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
        <h1 class="mt-6" v-text="'Whoops'"/>
        <p class="mt-6">
          Unfortunately we could not find this form. It may have been deleted by it's author.
        </p>
        <p class="mb-10 mt-4">
          <router-link :to="{name:'index'}">
            Create your form for free with OpnForm
          </router-link>
        </p>
      </div>
      <div v-else-if="formLoading">
        <p class="text-center mt-6 p-4">
          <loader class="h-6 w-6 text-nt-blue mx-auto"/>
        </p>
      </div>
      <template v-else>
        <div v-if="recordLoading">
          <p class="text-center mt-6 p-4">
            <loader class="h-6 w-6 text-nt-blue mx-auto"/>
          </p>
        </div>
        <open-complete-form v-show="!recordLoading" ref="open-complete-form" :form="form" class="mb-10"
                            @password-entered="passwordEntered"
        />
      </template>
    </div>
  </div>
</template>

<script setup>
import {computed} from 'vue'
import OpenCompleteForm from '../../components/open/forms/OpenCompleteForm.vue'
import sha256 from 'js-sha256'
import {onBeforeRouteLeave} from 'vue-router'
import {disableDarkMode, handleDarkMode, handleTransparentMode, focusOnFirstFormElement} from '~/lib/forms/public-page'

const crisp = useCrisp()
const formsStore = useFormsStore()
const recordsStore = useRecordsStore()

const isIframe = useIsIframe()
const formLoading = computed(() => formsStore.loading)
const recordLoading = computed(() => recordsStore.loading)
const slug = useRoute().params.slug
const form = computed(() => formsStore.getByKey(slug))
const submitted = ref(false)

crisp.hideChat()
onBeforeRouteLeave((to, from) => {
  crisp.showChat()
  disableDarkMode()
})

const passwordEntered = function (password) {
  useCookie('password-' + slug, {
    maxAge: {expires: 60 * 60 * 7},
    sameSite: false,
    secure: true
  }).value = sha256(password)
  loadForm(slug).then(() => {
    if (form.value?.is_password_protected) {
      this.$refs['open-complete-form'].addPasswordError('Invalid password.')
    }
  })
}

const loadForm = async () => {
  if (formsStore.loading || form.value) return Promise.resolve()
  const {data, error} = await formsStore.publicLoad(slug)
  if (error.value) {
    formsStore.stopLoading()
    return
  }
  formsStore.save(data.value)
  formsStore.stopLoading()

  // Adapt page to form: colors, custom code etc
  handleDarkMode(form.value)
  handleTransparentMode(form.value)

  if (process.server) return
  if (form.value.custom_code) {
    const scriptEl = document.createRange().createContextualFragment(form.value.custom_code)
    document.head.append(scriptEl)
  }
  if (!isIframe) focusOnFirstFormElement()
}

onMounted(() => {
  loadForm(slug)
})

await loadForm(slug)

useOpnSeoMeta({
  title: () => {
    if (form && form.value.is_pro && form.value.seo_meta.page_title) {
      return form.value.seo_meta.page_title
    }
    return form.value ? form.value.title : 'Create beautiful forms'
  },
  description () {
    if (form && form.value.is_pro && form.value.seo_meta.page_description) {
      return form.value.seo_meta.page_description
    }
    return (form && form.value.description) ? form.value.description.substring(0, 160) : null
  },
  ogImage () {
    if (form && form.value.is_pro && form.value.seo_meta.page_thumbnail) {
      return form.value.seo_meta.page_thumbnail
    }
    return (form && form.value.cover_picture) ? form.value.cover_picture : null
  },
  robots () {
    return (form && form.value.can_be_indexed) ? null : 'noindex, nofollow'
  }
})
useHead({
  titleTemplate: (titleChunk) => {
    if (form && form.value.is_pro && form.value.seo_meta.page_title) {
      // Disable template if custom SEO title
      return titleChunk
    }
    return titleChunk ? `${titleChunk} - OpnForm` : 'OpnForm';
  }
})
</script>
