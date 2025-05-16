import { reactive, nextTick } from 'vue'
import Quill from 'quill'

// Core imports
const ParchmentEmbed = Quill.import('parchment').EmbedBlot
const Delta = Quill.import('delta')
const Parchment = Quill.import('parchment')

/**
 * Utility to remove BOM and other zero-width characters from a string.
 */
function cleanString(str) {
  if (typeof str !== 'string') return ''
  return str.replace(/\uFEFF/g, '').replace(/\s+/g, ' ').trim()
}

export default function registerMentionExtension(QuillInstance) {
  /**
   * MentionBlot - Embeds a mention as a non-editable element
   */
  if (!QuillInstance.imports['formats/mention']) {
    class MentionBlot extends ParchmentEmbed {
      static blotName = 'mention'
      static tagName = 'SPAN'
      static scope = Parchment.Scope.INLINE
      
      // Match any span with a 'mention' attribute
      static matches(domNode) {
        return (
          domNode instanceof HTMLElement && 
          domNode.tagName === this.tagName && 
          domNode.hasAttribute('mention')
        )
      }

      static create(value) {
        const node = document.createElement(this.tagName)
        node.setAttribute('contenteditable', 'false')
        
        const data = (typeof value === 'object' && value !== null) ? value : { field: {}, fallback: '' }
        data.field = (typeof data.field === 'object' && data.field !== null) ? data.field : {}
        
        const fieldName = cleanString(data.field.name)
        const fallbackText = cleanString(data.fallback)
        const displayText = fieldName || fallbackText || 'mention'

        node.setAttribute('mention', 'true')
        node.setAttribute('mention-field-id', data.field.id || '')
        node.setAttribute('mention-field-name', fieldName)
        node.setAttribute('mention-fallback', fallbackText)

        const textNode = document.createTextNode(displayText)
        node.appendChild(textNode)
        
        return node
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

      static formats(domNode) {
        return MentionBlot.value(domNode)
      }

      length() {
        return 1
      }
    }

    // Register the blot with Quill
    QuillInstance.register('formats/mention', MentionBlot)
  }

  // Add clipboard matcher for handling mentions in pasted/loaded HTML
  if (QuillInstance.clipboard && typeof QuillInstance.clipboard.addMatcher === 'function') {
    QuillInstance.clipboard.addMatcher('span[mention]', (node, delta) => {
      if (node.hasAttribute('mention')) {
        const mentionData = {
          field: {
            id: node.getAttribute('mention-field-id') || '',
            name: node.getAttribute('mention-field-name') || ''
          },
          fallback: node.getAttribute('mention-fallback') || ''
        }
        
        return new Delta().insert({ mention: mentionData })
      }
      
      return delta
    })
  }

  /**
   * MentionModule - Handles mention UI integration with Quill
   */
  if (!QuillInstance.imports['modules/mention']) {
    class MentionModule {     
      constructor(quill, options = {}) {
        this.quill = quill
        this.options = options
        
        // Reactive state for the UI component
        this.state = reactive({
          open: false,
          onInsert: null,
          onCancel: null
        })
        
        this.setupMentions()
      }
      
      setupMentions() {
        const toolbar = this.quill.getModule('toolbar')
        if (toolbar) {
          toolbar.addHandler('mention', () => {
            const range = this.quill.getSelection()
            if (range) {
              this.state.open = true
              this.state.onInsert = (mentionData) => this.insertMention(mentionData, range.index)
              this.state.onCancel = () => {
                this.state.open = false
              }
            }
          })
        }
      }
      
      insertMention(mentionData, index) {
        if (!mentionData || typeof mentionData.field !== 'object' || mentionData.field === null) {
          console.error("Invalid mention data for insertion:", mentionData)
          return
        }
        
        this.state.open = false
        
        // Handle selection
        const selection = this.quill.getSelection()
        if (selection && selection.length > 0) {
          this.quill.deleteText(selection.index, selection.length, QuillInstance.sources.USER)
          index = selection.index
        }
        
        // Prepare clean data for the blot
        const blotData = {
          field: {
            id: mentionData.field.id || '',
            name: cleanString(mentionData.field.name)
          },
          fallback: cleanString(mentionData.fallback)
        }

        // Insert mention as embed using the blotData
        this.quill.insertEmbed(index, 'mention', blotData, QuillInstance.sources.USER)
        
        // Move cursor after mention
        nextTick(() => {
          this.quill.focus()
          this.quill.setSelection(index + 1, 0, QuillInstance.sources.SILENT)
        })
      }
    }
    
    // Register the module
    QuillInstance.register('modules/mention', MentionModule)
  }
  
  // Patch getSemanticHTML to handle non-breaking spaces
  if (typeof Quill.prototype.getSemanticHTML === 'function') {
    if (!Quill.prototype.getSemanticHTML.isPatched) {
      const originalGetSemanticHTML = Quill.prototype.getSemanticHTML
      Quill.prototype.getSemanticHTML = function(index = 0, length) { 
        const currentLength = this.getLength()
        const sanitizedIndex = Math.max(0, index)
        const sanitizedLength = Math.max(0, Math.min(length ?? (currentLength - sanitizedIndex), currentLength - sanitizedIndex))
        if (sanitizedIndex >= currentLength && currentLength > 0) { 
          return originalGetSemanticHTML.call(this, 0, 0) 
        }
        const html = originalGetSemanticHTML.call(this, sanitizedIndex, sanitizedLength)
        return html.replace(/&nbsp;|\u00A0/g, ' ')
      }
      Quill.prototype.getSemanticHTML.isPatched = true
    }
  }
  
  // Return reactive state for component binding
  return reactive({
    open: false,
    onInsert: null,
    onCancel: null
  })
}