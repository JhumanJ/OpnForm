<template>
  <div
    id="public-form"
    class="flex flex-col"
  >
    <div v-if="form && !isIframe && (form.logo_picture || form.cover_picture)">
      <div v-if="form.cover_picture">
        <div
          id="cover-picture"
          class="max-h-56 w-full overflow-hidden flex items-center justify-center"
        >
          <img
            alt="Form Cover Picture"
            :src="form.cover_picture"
            class="w-full"
          >
        </div>
      </div>
      <div
        v-if="form.logo_picture"
        class="w-full p-5 relative mx-auto"
        :class="{'pt-20':!form.cover_picture, 'md:w-3/5 lg:w-1/2 md:max-w-2xl': form.width === 'centered', 'max-w-7xl': (form.width === 'full' && !isIframe) }"
        :style="{ 'direction': form?.layout_rtl ? 'rtl' : 'ltr' }"
      >
        <img
          alt="Logo Picture"
          :src="form.logo_picture"
          :class="{'top-5':!form.cover_picture, '-top-10':form.cover_picture}"
          class="w-20 h-20 object-contain absolute transition-all"
        >
      </div>
    </div>
    <div
      class="w-full mx-auto px-4"
      :class="{'mt-6':!isIframe, 'md:w-3/5 lg:w-1/2 md:max-w-2xl': form && (form.width === 'centered'), 'max-w-7xl': (form && form.width === 'full' && !isIframe)}"
    >
      <div v-if="!formLoading && !form">
        <NotFoundForm />
      </div>
      <div v-else-if="formLoading">
        <p class="text-center mt-6 p-4">
          <loader class="h-6 w-6 text-nt-blue mx-auto" />
        </p>
      </div>
      <template v-else>
        <OpenCompleteForm
          ref="openCompleteForm"
          :form="form"
          class="mb-10"
          :dark-mode="darkMode"
          :mode="FormMode.LIVE"
          @password-entered="passwordEntered"
        />
      </template>
    </div>
  </div>
</template>

<script setup>
import OpenCompleteForm from "~/components/open/forms/OpenCompleteForm.vue"
import sha256 from 'js-sha256'
import { onBeforeRouteLeave } from 'vue-router'
import {
  disableDarkMode,
  handleDarkMode,
  handleTransparentMode,
  focusOnFirstFormElement,
  useDarkMode
} from '~/lib/forms/public-page'
import { FormMode } from "~/lib/forms/FormModeStrategy.js"

const crisp = useCrisp()
const formsStore = useFormsStore()
const darkMode = useDarkMode()
const isIframe = useIsIframe()
const formLoading = computed(() => formsStore.loading)
const slug = useRoute().params.slug
const form = computed(() => formsStore.getByKey(slug))
const { t } = useI18n()

const openCompleteForm = ref(null)

const passwordEntered = function (password) {
  const cookie = useCookie('password-' + slug, {
    maxAge: 60 * 60 * 7,
    sameSite: 'none',
    secure: true
  })
  cookie.value = sha256(password)
  nextTick(() => {
    loadForm().then(() => {
      if (form.value?.is_password_protected) {
        openCompleteForm.value.addPasswordError(t('forms.invalid_password'))
      }
    })
  })
}

const loadForm = async (setup=false) => {
  if (formsStore.loading || (form.value && !form.value.is_password_protected)) return Promise.resolve()
  const event = useRequestEvent()

  if (setup) {
    const {data, error} = await formsStore.publicLoad(slug)
    if (error.value) {
      console.error(`Error loading form [${slug}]:`,error.value)
      formsStore.stopLoading()
      setResponseStatus(event, 404, 'Page Not Found')
      return
    }
    formsStore.save(data.value)
  } else {
    try {
      const data = await formsStore.publicFetch(slug)
      formsStore.save(data)
    } catch {
      formsStore.stopLoading()
      setResponseStatus(event, 404, 'Page Not Found')
      return
    }
  }
  formsStore.stopLoading()

  // Adapt page to form: colors, custom code etc
  handleDarkMode(form.value?.dark_mode)
  handleTransparentMode(form.value?.transparent_background)

  // Remove 'hidden' class from html tag if present
  nextTick(() => {
    if (import.meta.client) {
      window.document.documentElement.classList.remove('hidden')
    }
  })
}

await loadForm(true)

onMounted(() => {
  crisp.hideChat()
  document.body.classList.add('public-page')
  if (form.value) {
    handleDarkMode(form.value?.dark_mode)
    handleTransparentMode(form.value?.transparent_background)

    // Remove 'hidden' class from html tag if present
    nextTick(() => {
      if (import.meta.client) {
        window.document.documentElement.classList.remove('hidden')
      }
    })

    if (import.meta.client) {
      if (form.value.custom_code) {
        const scriptEl = document.createRange().createContextualFragment(form.value.custom_code)
        try {
          document.head.append(scriptEl)
        } catch (e) {
          console.error('Error appending custom code', e)
        }
      }
      if (!isIframe && form.value?.auto_focus) focusOnFirstFormElement()
    }
  }
})

onBeforeRouteLeave(() => {
  document.body.classList.remove('public-page')
  crisp.showChat()
  disableDarkMode()
})

const pageMeta = computed(() => {
  if (form.value && form.value.is_pro && form.value.seo_meta) {
    return form.value.seo_meta
  }
  return {}
})

const getFontUrl = computed(() => {
  if(!form.value || !form.value.font_family) return null
  const family = form.value.font_family.replace(/ /g, '+')
  return `https://fonts.googleapis.com/css?family=${family}:wght@400,500,700,800,900&display=swap`
})

const headLinks = computed(() => {
  const links = []
  if (form.value && form.value.font_family) {
    links.push({
        rel: 'stylesheet',
        href: getFontUrl.value
    })
  }
  if (pageMeta.value.page_favicon) {
    links.push({
        rel: 'icon', type: 'image/x-icon',
        href: pageMeta.value.page_favicon
    })
    links.push({
        rel: 'apple-touch-icon',
        type: 'image/png',
        href: pageMeta.value.page_favicon
    })
    links.push({
      rel: 'shortcut icon',
      href: pageMeta.value.page_favicon
    })
  }
  return links
})
    
useOpnSeoMeta({
  title: () => {
    if (pageMeta.value.page_title) {
      return pageMeta.value.page_title
    }
    return form.value ? form.value.title : 'Create beautiful forms'
  },
  description: () => {
    if (pageMeta.value.page_description) {
      return pageMeta.value.page_description
    }
    return null
  },
  ogImage: () => {
    if (pageMeta.value.page_thumbnail) {
      return pageMeta.value.page_thumbnail
    }
    return (form.value && form.value?.cover_picture) ? form.value?.cover_picture : null
  },
  robots: () => {
    return (form.value && form.value?.can_be_indexed) ? null : 'noindex, nofollow'
  }
})

const getHtmlClass = computed(() => {
  return {
    dark: form.value?.dark_mode === 'dark',
    hidden: form.value?.dark_mode === 'auto' && import.meta.server,
  }
})

useHead({
  htmlAttrs: {
    dir: () => form.value?.layout_rtl ? 'rtl' : 'ltr',
    class: getHtmlClass.value,
    lang: () => form.value?.language || 'en'
  },
  titleTemplate: (titleChunk) => {
    if (pageMeta.value.page_title) {
      // Disable template if custom SEO title
      return titleChunk
    }
    return titleChunk ? `${titleChunk} - OpnForm` : 'OpnForm'
  },
  link: headLinks.value,
  meta: pageMeta.value.page_favicon ? [
    {
      name: 'apple-mobile-web-app-capable',
      content: 'yes'
    },
    {
      name: 'apple-mobile-web-app-status-bar-style',
      content: 'black-translucent'
    },
  ] : {},
  script: [{ src: '/widgets/iframeResizer.contentWindow.min.js' }]
})
</script>
