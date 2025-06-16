import Quill from 'quill'

// Self-executing function to patch Quill's prototype
;(function installQuillFixes() {
  const originalGetSemanticHTML = Quill.prototype.getSemanticHTML
  Quill.prototype.getSemanticHTML = function (index = 0, length) {
    const html = originalGetSemanticHTML.call(this, index, length || this.getLength())
    return html.replace(/<p><\/p>/g, '<p><br/></p>')
  }
})()

export default {} 