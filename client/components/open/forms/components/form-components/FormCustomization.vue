<template>
  <editor-options-panel name="Customization" :already-opened="true">
    <template #icon>
      <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M1.66667 9.99984C1.66667 14.6022 5.39763 18.3332 10 18.3332C11.3807 18.3332 12.5 17.2139 12.5 15.8332V15.4165C12.5 15.0295 12.5 14.836 12.5214 14.6735C12.6691 13.5517 13.5519 12.6689 14.6737 12.5212C14.8361 12.4998 15.0297 12.4998 15.4167 12.4998H15.8333C17.214 12.4998 18.3333 11.3805 18.3333 9.99984C18.3333 5.39746 14.6024 1.6665 10 1.6665C5.39763 1.6665 1.66667 5.39746 1.66667 9.99984Z" stroke="currentColor" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M5.83333 10.8332C6.29357 10.8332 6.66667 10.4601 6.66667 9.99984C6.66667 9.5396 6.29357 9.1665 5.83333 9.1665C5.3731 9.1665 5 9.5396 5 9.99984C5 10.4601 5.3731 10.8332 5.83333 10.8332Z" stroke="currentColor" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M13.3333 7.49984C13.7936 7.49984 14.1667 7.12674 14.1667 6.6665C14.1667 6.20627 13.7936 5.83317 13.3333 5.83317C12.8731 5.83317 12.5 6.20627 12.5 6.6665C12.5 7.12674 12.8731 7.49984 13.3333 7.49984Z" stroke="currentColor" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M8.33333 6.6665C8.79357 6.6665 9.16667 6.29341 9.16667 5.83317C9.16667 5.37293 8.79357 4.99984 8.33333 4.99984C7.8731 4.99984 7.5 5.37293 7.5 5.83317C7.5 6.29341 7.8731 6.6665 8.33333 6.6665Z" stroke="currentColor" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </template>

    <select-input name="theme" class="mt-4"
                  :options="[
                    {name:'Default',value:'default'},
                    {name:'Simple',value:'simple'},
                    {name:'Notion',value:'notion'},
                  ]"
                  :form="form" label="Form Theme"
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

    <color-input name="color" class="mt-4"
                 :form="form"
                 label="Color (for buttons & inputs border)"
    />
    <toggle-switch-input name="hide_title" :form="form" class="mt-4"
                         label="Hide Title"
    />
    <toggle-switch-input name="no_branding" :form="form" class="mt-4">
      <template #label>
        Remove OpnForm Branding
        <pro-tag class="ml-1" />
      </template>
    </toggle-switch-input>
    <toggle-switch-input name="show_progress_bar" :form="form" class="mt-4"
                         label="Show progress bar"
    />
    <toggle-switch-input name="uppercase_labels" :form="form" class="mt-4"
                         label="Uppercase Input Labels"
    />
    <toggle-switch-input name="transparent_background" :form="form" class="mt-4"
                         label="Transparent Background" help="Only applies when form is embedded"
    />
    <toggle-switch-input name="confetti_on_submission" :form="form" class="mt-4"
                         label="Confetti on successful submisison"
                         @update:model-value="onChangeConfettiOnSubmission"
    />
    <toggle-switch-input name="auto_save" :form="form"
                         label="Auto save form response"
                         help="Will save data in browser, if user not submit the form then next time will auto prefill last entered data"
    />
  </editor-options-panel>
</template>

<script setup>
import { useWorkingFormStore } from '../../../../../stores/working_form'
import EditorOptionsPanel from '../../../editors/EditorOptionsPanel.vue'
import ProTag from '~/components/global/ProTag.vue'

const workingFormStore = useWorkingFormStore()
const form = storeToRefs(workingFormStore).content
const isMounted =  ref(false)
const crisp = useCrisp()
const confetti = useConfetti()

onMounted(() => {
  isMounted.value = true
})

const onChangeConfettiOnSubmission = (val) => {
  form.confetti_on_submission = val
  if (isMounted.value && val) {
    confetti.play()
  }
}
</script>
