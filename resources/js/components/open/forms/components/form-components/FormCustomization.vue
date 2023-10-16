<template>
  <collapse class="p-4 w-full border-b" v-model="isCollapseOpen">
    <template #title>
      <h3 class="font-semibold text-lg">
        <svg class="h-5 w-5 inline mr-2 -mt-1 transition-colors" :class="{'text-blue-600':isCollapseOpen, 'text-gray-500':!isCollapseOpen}" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M1.66667 9.99984C1.66667 14.6022 5.39763 18.3332 10 18.3332C11.3807 18.3332 12.5 17.2139 12.5 15.8332V15.4165C12.5 15.0295 12.5 14.836 12.5214 14.6735C12.6691 13.5517 13.5519 12.6689 14.6737 12.5212C14.8361 12.4998 15.0297 12.4998 15.4167 12.4998H15.8333C17.214 12.4998 18.3333 11.3805 18.3333 9.99984C18.3333 5.39746 14.6024 1.6665 10 1.6665C5.39763 1.6665 1.66667 5.39746 1.66667 9.99984Z" stroke="currentColor" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M5.83333 10.8332C6.29357 10.8332 6.66667 10.4601 6.66667 9.99984C6.66667 9.5396 6.29357 9.1665 5.83333 9.1665C5.3731 9.1665 5 9.5396 5 9.99984C5 10.4601 5.3731 10.8332 5.83333 10.8332Z" stroke="currentColor" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M13.3333 7.49984C13.7936 7.49984 14.1667 7.12674 14.1667 6.6665C14.1667 6.20627 13.7936 5.83317 13.3333 5.83317C12.8731 5.83317 12.5 6.20627 12.5 6.6665C12.5 7.12674 12.8731 7.49984 13.3333 7.49984Z" stroke="currentColor" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M8.33333 6.6665C8.79357 6.6665 9.16667 6.29341 9.16667 5.83317C9.16667 5.37293 8.79357 4.99984 8.33333 4.99984C7.8731 4.99984 7.5 5.37293 7.5 5.83317C7.5 6.29341 7.8731 6.6665 8.33333 6.6665Z" stroke="currentColor" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>

        Customization
      </h3>
    </template>

    <select-input name="theme" class="mt-4"
                  :options="[
                    {name:'Mặc định',value:'default'},
                    {name:'Đơn giản',value:'simple'},
                    {name:'Nổi bật nội dung',value:'notion'},
                  ]"
                  :form="form" label="Giao diện/Chủ đề"
    />

    <select-input name="width" class="mt-4"
                  :options="[
                    {name:'Canh giữa',value:'centered'},
                    {name:'Tràn viền',value:'full'},
                  ]"
                  :form="form" label="Định dạng" help="Hữu ích khi dùng tính năng nhúng biểu mẫu"
    />

    <image-input name="cover_picture" class="mt-4"
                 :form="form" label="Ảnh bìa" help="Không khả dụng khi dùng tính năng nhúng biểu mẫu"
                 :required="false"
    />

    <image-input name="logo_picture" class="mt-4"
                 :form="form" label="Logo" help="Không khả dụng khi dùng tính năng nhúng biểu mẫu"
                 :required="false"
    />

    <select-input name="dark_mode" class="mt-4"
                  help="Để xem các thay đổi, hãy lưu biểu mẫu và mở lại"
                  :options="[
                    {name:'Tự động - tuỳ chọn hệ thống của thiết bị',value:'auto'},
                    {name:'Chế độ sáng',value:'light'},
                    {name:'Chế độ tối',value:'dark'}
                  ]"
                  :form="form" label="Chế độ tối"
    />
    <color-input name="color" class="mt-4"
                 :form="form"
                 label="Màu sắc (đối với các nút và đường viền)"
    />
    <toggle-switch-input name="hide_title" :form="form" class="mt-4"
                    label="Ẩn tiêu đề"
    />
    <toggle-switch-input name="no_branding" :form="form" class="mt-4">
      <template #label>
        Loại bỏ thương hiệu e-Form
        <pro-tag class="ml-1" />
      </template>
    </toggle-switch-input>
    <toggle-switch-input name="uppercase_labels" :form="form" class="mt-4"
                    label="Tiêu đề cấu trúc viết hoa"
    />
    <toggle-switch-input name="transparent_background" :form="form" class="mt-4"
                    label="Nền trong suốt" help="Chỉ áp dụng khi biểu mẫu dùng chức năng nhúng"
    />
    <toggle-switch-input name="confetti_on_submission" :form="form" class="mt-4"
                         label="Kích hoạt hiệu ứng bắn hoa giấy khi gửi biểu mẫu thành công"
                         @input="onChangeConfettiOnSubmission"
    />
    <toggle-switch-input name="auto_save" :form="form"
                         label="Tự động lưu phản hồi biểu mẫu"
                         help="Sẽ lưu dữ liệu trong trình duyệt, nếu người dùng không gửi biểu mẫu thì lần sau sẽ tự động điền trước dữ liệu đã nhập trước đó"
    />
  </collapse>
</template>

<script>
import Collapse from '../../../../common/Collapse.vue'
import ProTag from '../../../../common/ProTag.vue'

export default {
  components: { Collapse, ProTag },
  props: {
  },
  data () {
    return {
      isMounted: false,
      isCollapseOpen: true
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

  mounted() {
    this.isMounted = true
  },

  methods: {
    onChangeConfettiOnSubmission(val) {
      this.$set(this.form, 'confetti_on_submission', val)
      if(this.isMounted && val){
        this.playConfetti()
      }
    },
    openChat () {
      window.$crisp.push(['do', 'chat:show'])
      window.$crisp.push(['do', 'chat:open'])
    },
  }
}
</script>
