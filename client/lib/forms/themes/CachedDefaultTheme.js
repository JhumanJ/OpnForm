import ThemeBuilder from './ThemeBuilder.js'

const CachedDefaultTheme = (function() {
  let instance

  function createInstance() {
    const themeBuilder = new ThemeBuilder()
    return themeBuilder.getAllComponents()
  }

  return {
    getInstance: function() {
      if (!instance) {
        instance = createInstance()
      }
      return instance
    }
  }
})()

export default CachedDefaultTheme
