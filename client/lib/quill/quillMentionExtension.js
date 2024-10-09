import Quill from 'quill'

import { reactive, nextTick } from 'vue'




const Embed = Quill.import('blots/embed')




class MentionBlot extends Embed {

  static blotName = "mention"

  static tagName = "span"




  static create(data) {

    const node = super.create()

    node.setAttribute('mention-field-id', data.field.id)

    node.setAttribute('mention-field-name', data.field.name)

    node.setAttribute('mention-fallback', data.fallback)

    node.setAttribute('contenteditable', 'false')

    node.setAttribute('mention', true)




    node.textContent = data.field.name.length > 25 ? `${data.field.name.slice(0, 25)}...` : data.field.name

    return node

  }




  static value(node) {

    return {

      field_id: node.getAttribute('mention-field-id'),

      field_name: node.getAttribute('mention-field-name'),

      fallback: node.getAttribute('mention-fallback'),

    }

  }




  static formats() {

    return true

  }

}




export default function registerMentionExtension(Quill) {

  const mentionState = reactive({

    open: false,

    onInsert: null,

    onCancel: null,

  })




  if (!Quill.imports['modules/mention']) {

    Quill.register(MentionBlot)




    class MentionModule {

      constructor(quill, options) {

        this.quill = quill

        this.options = options

        this.setupMentions()

      }




      setupMentions() {

        this.quill.getModule('toolbar').addHandler('mention', () => {

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




      insertMention(mention, index) {

        this.quill.insertEmbed(index, 'mention', mention)

        this.quill.insertText(index + 1, ' ')

        this.quill.setSelection(index + 2)




        nextTick(() => {

          mentionState.open = false

        })

      }

    }




    Quill.register('modules/mention', MentionModule)

  }




  return mentionState

}