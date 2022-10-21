<template>
  <collapse class="py-3 w-full border-b" :default-value="true">
    <template #title>
      <h3 class="font-semibold text-lg">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline text-blue-600 mr-2 -mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
        </svg>
        Customization
        <pro-tag />
      </h3>
    </template>

    <select-input name="theme" class="mt-4"
                  :options="[
                    {name:'Default',value:'default'},
                    {name:'Simple',value:'simple'},
                    {name:'Notion',value:'notion'},
                  ]"
                  :form="form" label="Form Theme"
    />
    <div class="-mt-3 mb-3 text-gray-400 dark:text-gray-500">
      <small>
        Need another theme? <a href="#" @click.prevent="openChat">Send us some suggestions!</a>
      </small>
    </div>

    <select-input name="width" class="mt-4"
                  :options="[
                    {name:'Centered',value:'centered'},
                    {name:'Full Width',value:'full'},
                  ]"
                  :form="form" label="Form Width" help="Useful when embedding your form"
    />

    <image-input name="cover_picture" class="mt-4"
                 :form="form" label="Cover Picture" help="Not visible when form is embedded"
                 :required="false"
    />

    <image-input name="logo_picture" class="mt-4"
                 :form="form" label="Logo" help="Not visible when form is embedded"
                 :required="false"
    />

    <select-input name="dark_mode" class="mt-4"
                  help="To see changes, save your form and open it"
                  :options="[
                    {name:'Auto - use Device System Preferences',value:'auto'},
                    {name:'Light Mode',value:'light'},
                    {name:'Dark Mode',value:'dark'}
                  ]"
                  :form="form" label="Dark Mode"
    />
    <color-input name="color" class="mt-4"
                 :form="form"
                 label="Color (for buttons & inputs border)"
    />
    <checkbox-input name="hide_title" :form="form" class="mt-4"
                    label="Hide Title"
    />
    <checkbox-input name="no_branding" :form="form" class="mt-4"
                    label="Remove OpnForm Branding"
    />
    <checkbox-input name="uppercase_labels" :form="form" class="mt-4"
                    label="Uppercase Input Labels"
    />
    <checkbox-input name="transparent_background" :form="form" class="mt-4"
                    label="Transparent Background" help="Only applies when form is embedded"
    />
  </collapse>
</template>

<script>
import Collapse from '../../../../common/Collapse'
import ProTag from '../../../../common/ProTag'

export default {
  components: { Collapse, ProTag },
  props: {
  },
  data () {
    return {
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
  },

  methods: {
    openChat () {
      window.$crisp.push(['do', 'chat:show'])
      window.$crisp.push(['do', 'chat:open'])
    }
  }
}
</script>
