<template>
    <modal :show="show" max-width="lg" @close="emit('close')">
        <open-form :theme="theme" :loading="false" :show-hidden="true" :form="form" :fields="form.properties" @submit="updateForm" :default-data-form="submission">
        <template #submit-btn="{submitForm}">
                <v-button :loading="loading" class="mt-2 px-8 mx-1" @click.prevent="submitForm">
                Update Submission
                </v-button>
            </template>
        </open-form>
    </modal>
</template>
<script setup>
import {ref, defineProps, defineEmits, onMounted } from 'vue'
import OpenForm from '../forms/OpenForm.vue';
import { themes } from '~/lib/forms/form-themes.js'
const props = defineProps({
  show: { type: Boolean, required: true },
  form: { type: Object, required: true },
  theme:{type:Object, default:themes.default},
  submission:{type:Object}
})

let loading = ref(false)

const emit = defineEmits(['close', 'updated'])
const updateForm =  (form, onFailure) =>{
    loading.value  = true
    form.put('/open/forms/' + props.form.id + '/submissions/'+props.submission.id).then((res) => {
        useAlert().success(res.message)
        loading.value  = false
        emit('close')
        emit('updated', res.data.data)

      }).catch((error) => {
        console.error(error)
        loading.value = false
        onFailure()
      })
    
}

</script>