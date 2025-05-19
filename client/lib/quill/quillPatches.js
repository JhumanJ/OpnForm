import Quill from 'quill'

// Self-executing function to patch Quill's prototype
;(function installQuillFixes() {
  // Store the original method
  const originalGetSemanticHTML = Quill.prototype.getSemanticHTML

  // Override the getSemanticHTML method
  Quill.prototype.getSemanticHTML = function(index = 0, length) {
    // Call the original method
    const html = originalGetSemanticHTML.call(this, index, length || this.getLength())
    
    // Apply fixes:
    return html
      // 1. Replace &nbsp; with regular spaces
      .replace(/&nbsp;/g, ' ')
      // 2. Fix line breaks by replacing empty paragraphs with paragraphs containing <br/>
      .replace(/<p><\/p>/g, '<p><br/></p>')
  }
})()

export default {} 