import { defineStore } from "pinia"
import clonedeep from "clone-deep"
import { generateUUID } from "~/lib/utils.js"

const defaultBlockNames = {
  text: "Your name",
  date: "Date",
  url: "Link",
  phone_number: "Phone Number",
  number: "Number",
  rating: "Rating",
  scale: "Scale",
  slider: "Slider",
  email: "Email",
  checkbox: "Checkbox",
  select: "Select",
  multi_select: "Multi Select",
  files: "Files",
  signature: "Signature",
  "nf-text": "Text Block",
  "nf-page-break": "Page Break",
  "nf-divider": "Divider",
  "nf-image": "Image",
  "nf-code": "Code Block",
}

export const useWorkingFormStore = defineStore("working_form", {
  state: () => ({
    content: null,

    // Field being edited
    selectedFieldIndex: null,
    showEditFieldSidebar: null,
    showAddFieldSidebar: null,
    blockForm: null,
    draggingNewBlock: false,
  }),
  actions: {
    set(form) {
      this.content = form
    },
    setProperties(properties) {
      this.content.properties = [...properties]
    },
    openSettingsForField(index) {
      // If field is passed, compute index
      if (typeof index === "object") {
        index = this.content.properties.findIndex(
          (prop) => prop.id === index.id,
        )
      }
      this.selectedFieldIndex = index
      this.showEditFieldSidebar = true
      this.showAddFieldSidebar = false
    },
    closeEditFieldSidebar() {
      this.selectedFieldIndex = null
      this.showEditFieldSidebar = false
      this.showAddFieldSidebar = false
    },
    openAddFieldSidebar(index) {
      // If field is passed, compute index
      if (index !== null && typeof index === "object") {
        index = this.content.properties.findIndex(
          (prop) => prop.id === index.id,
        )
      }
      this.selectedFieldIndex = index
      this.showAddFieldSidebar = true
      this.showEditFieldSidebar = false
    },
    closeAddFieldSidebar() {
      this.selectedFieldIndex = null
      this.showAddFieldSidebar = false
      this.showEditFieldSidebar = false
    },
    reset() {
      this.content = null
      this.selectedFieldIndex = null
      this.showEditFieldSidebar = null
      this.showAddFieldSidebar = null
    },

    resetBlockForm() {
      this.blockForm = useForm({
        type: null,
        name: null,
      })

    },

    prefillDefault(data) {
      if (data.type === "nf-text") {
        data.content = "<p>This is a text block.</p>"
      } else if (data.type === "nf-page-break") {
        data.next_btn_text = "Next"
        data.previous_btn_text = "Previous"
      } else if (data.type === "nf-code") {
        data.content =
          '<div class="text-blue-500 italic">This is a code block.</div>'
      } else if (data.type === "signature") {
        data.help = "Draw your signature above"
      }
      return data
    },

    addBlock(type, index = null) {
      this.blockForm.type = type
      this.blockForm.name = defaultBlockNames[type]
      const newBlock = this.prefillDefault(this.blockForm.data())
      newBlock.id = generateUUID()
      newBlock.hidden = false
      if (["select", "multi_select"].includes(this.blockForm.type)) {
        newBlock[this.blockForm.type] = { options: [] }
      }
      if (this.blockForm.type === "rating") {
        newBlock.rating_max_value = 5
      }
      if (this.blockForm.type === "scale") {
        newBlock.scale_min_value = 1
        newBlock.scale_max_value = 5
        newBlock.scale_step_value = 1
      }
      if (this.blockForm.type === "slider") {
        newBlock.slider_min_value = 0
        newBlock.slider_max_value = 50
        newBlock.slider_step_value = 1
      }
      newBlock.help_position = "below_input"
      if (
        (this.selectedFieldIndex === null || this.selectedFieldIndex === undefined) &&
        (index === null || index === undefined)
      ) {
        const newFields = clonedeep(this.content.properties)
        newFields.push(newBlock)
        this.content.properties = newFields
        this.openSettingsForField(
          this.content.properties.length - 1,
        )
      } else {
        const fieldIndex = typeof index === "number" ? index : this.selectedFieldIndex + 1
        const newFields = clonedeep(this.content.properties)
        newFields.splice(fieldIndex, 0, newBlock)
        this.content.properties = newFields
        this.openSettingsForField(fieldIndex)
      }
    },

    moveField(oldIndex, newIndex) {
      const newFields = clonedeep(this.content.properties)
      const field = newFields.splice(oldIndex, 1)[0]
      newFields.splice(newIndex, 0, field)
      this.content.properties = newFields
    }
  },
  history: {}
})
