import { reactive } from 'vue'
import Quill from 'quill'
const Inline = Quill.import('blots/inline')
const Clipboard = Quill.import('modules/clipboard')

export default function registerMentionExtension(Quill) {
  // Extend Clipboard to handle pasted content
  class MentionClipboard extends Clipboard {
    convert(html) {
      const delta = super.convert(html)
      const processedDelta = delta.ops.reduce((newDelta, op) => {
        if (op.attributes && op.attributes.mention) {
          const mentionData = op.attributes.mention
          let isValid = false
          // Check for nested structure
          if (
            mentionData &&
            typeof mentionData === 'object' &&
            mentionData.field &&
            typeof mentionData.field === 'object' &&
            mentionData.field.id
          ) {
            isValid = true
          } else if (
            mentionData &&
            typeof mentionData === 'object' &&
            mentionData['mention-field-id']
          ) {
            // Transform flat structure to nested structure
            op.attributes.mention = {
              field: {
                id: mentionData['mention-field-id'],
                name: mentionData['mention-field-name'] || '',
              },
              fallback: mentionData['mention-fallback'] || '',
            }
            isValid = true
          }
          if (!isValid) {
            delete op.attributes.mention
          }
        }
        newDelta.push(op)
        return newDelta
      }, [])
      return processedDelta
    }
  }
  Quill.register('modules/clipboard', MentionClipboard, true)

  class MentionBlot extends Inline {
    static blotName = 'mention'
    static tagName = 'SPAN'

    static create(data) {
      // Only create mention if we have valid data
      if (!data || !data.field || !data.field.id) {
        return null
      }
      let node = super.create()
      MentionBlot.setAttributes(node, data)
      return node
    }

    static setAttributes(node, data) {
      // Only set attributes if we have valid data
      if (!data || !data.field || !data.field.id) {
        return
      }
      node.setAttribute('contenteditable', 'false')
      node.setAttribute('mention', 'true')
      node.setAttribute('mention-field-id', data.field.id || '')
      node.setAttribute('mention-field-name', data.field.name || '')
      node.setAttribute('mention-fallback', data.fallback || '')
      node.textContent = data.field.name || ''
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
      formats['mention'] = MentionBlot.value(this.domNode)
      return formats
    }

    static value(domNode) {
      return {
        field: {
          id: domNode.getAttribute('mention-field-id') || '',
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