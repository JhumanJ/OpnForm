import { reactive } from 'vue'
import Quill from 'quill'
const Inline = Quill.import('blots/inline')

export default function registerMentionExtension(Quill) {
  class MentionBlot extends Inline {
    static blotName = 'mention'
    static tagName = 'SPAN'

    static create(data) {
      let node = super.create()
      MentionBlot.setAttributes(node, data)
      return node
    }

    static setAttributes(node, data) {
      node.setAttribute('contenteditable', 'false')
      node.setAttribute('mention', 'true')

      if (data && typeof data === 'object') {
        node.setAttribute('mention-field-id', data.field?.nf_id || '')
        node.setAttribute('mention-field-name', data.field?.name || '')
        node.setAttribute('mention-fallback', data.fallback || '')
        node.textContent = data.field?.name || ''
      } else {
        // Handle case where data is not an object (e.g., during undo)
        node.textContent = data || ''
      }
    }

    static formats(domNode) {
      return {
        'mention-field-id': domNode.getAttribute('mention-field-id') || '',
        'mention-field-name': domNode.getAttribute('mention-field-name') || '',
        'mention-fallback': domNode.getAttribute('mention-fallback') || ''
      }
    }

    format(name, value) {
      if (name === 'mention' && value) {
        MentionBlot.setAttributes(this.domNode, value)
      } else {
        super.format(name, value)
      }
    }

    formats() {
      let formats = super.formats()
      formats['mention'] = MentionBlot.formats(this.domNode)
      return formats
    }

    static value(domNode) {
      return {
        field: {
          nf_id: domNode.getAttribute('mention-field-id') || '',
          name: domNode.getAttribute('mention-field-name') || ''
        },
        fallback: domNode.getAttribute('mention-fallback') || ''
      }
    }

    // Override attach to ensure contenteditable is always set
    attach() {
      super.attach()
      this.domNode.setAttribute('contenteditable', 'false')
    }

    length() {
      return 1
    }
  }

  Quill.register(MentionBlot)

  const mentionState = reactive({
    open: false,
    onInsert: null,
    onCancel: null,
  })

  class MentionModule {
    constructor(quill, options) {
      this.quill = quill
      this.options = options

      this.setupMentions()
    }

    setupMentions() {
      const toolbar = this.quill.getModule('toolbar')
      if (toolbar) {
        toolbar.addHandler('mention', () => {
          const range = this.quill.getSelection()
          if (range) {
            mentionState.open = true
            mentionState.onInsert = (mention) => {
              this.insertMention(mention, range.index)
            }
            mentionState.onCancel = () => {
              mentionState.open = false
            }
          }
        })
      }
    }

    insertMention(mention, index) {
      mentionState.open = false

      // Insert the mention
      this.quill.insertEmbed(index, 'mention', mention, Quill.sources.USER)

      // Calculate the length of the inserted mention
      const mentionLength = this.quill.getLength() - index

      nextTick(() => {
        // Focus the editor
        this.quill.focus()

        // Set the selection after the mention
        this.quill.setSelection(index + mentionLength, 0, Quill.sources.SILENT)
      })
    }
  }

  Quill.register('modules/mention', MentionModule)

  return mentionState
}