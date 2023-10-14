<template>
  <Dropdown v-if="Object.keys(locales).length > 1"
            dropdown-class="origin-top-right absolute right-0 mt-2 w-20 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5">
    <template #trigger="{toggle}">
      <a class="text-gray-300 hover:text-gray-800 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium" href="#" role="button" @click.prevent="toggle"
      >
        {{ locales[locale] }}
      </a>
    </template>

    <a v-for="(value, key) in locales" :key="key" class="block block text-center px-4 py-2 text-md text-gray-700 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-100 dark:hover:text-white dark:hover:bg-gray-600 flex items-center z-10" href="#"
       @click.prevent="setLocale(key)"
    >
      {{ value }}
    </a>
  </Dropdown>
</template>

<script>
import { mapGetters } from 'vuex'
import { loadMessages } from '~/plugins/i18n.js'
import Dropdown from './common/Dropdown.vue'

export default {
  components: { Dropdown },
  computed: mapGetters({
    locale: 'lang/locale',
    locales: 'lang/locales'
  }),

  methods: {
    setLocale (locale) {
      if (this.$i18n.locale !== locale) {
        loadMessages(locale)

        this.$store.dispatch('lang/setLocale', { locale })
      }
    }
  }
}
</script>
