import {twMerge} from 'tailwind-merge'
import {themes} from './form-themes.js'

class ThemeBuilder {
  constructor(theme = 'default', options = {}) {
    this.theme = themes[theme] || themes.default
    this.size = options.size || 'sm'
    this.borderRadius = options.borderRadius || 'small'
  }

  mergeNestedClasses(baseConfig, componentConfig) {
    const mergedConfig = {}

    const allKeys = new Set([
      ...Object.keys(baseConfig),
      ...Object.keys(componentConfig),
    ])

    allKeys.forEach((key) => {
      const baseValue = baseConfig[key]
      const componentValue = componentConfig[key]

      if (key === 'size') {
        const sizeClass = baseConfig.size?.[this.size] || ''
        mergedConfig[key] = twMerge(sizeClass, componentValue)
      } else if (key === 'borderRadius') {
        const borderRadiusClass = baseConfig.borderRadius?.[this.borderRadius] || ''
        mergedConfig[key] = twMerge(borderRadiusClass, componentValue)
      } else if (typeof baseValue === 'object' && baseValue !== null && !Array.isArray(baseValue)) {
        mergedConfig[key] = this.mergeNestedClasses(baseValue, componentValue || {})
      } else {
        mergedConfig[key] = twMerge(baseValue || '', componentValue || '')
      }
    })

    return mergedConfig
  }

  getComponentTheme(componentName = 'default') {
    const baseComponentConfig = this.theme.default || {}
    const componentConfig = this.theme[componentName] || {}
    return this.mergeNestedClasses(baseComponentConfig, componentConfig)
  }

  // Get all components classes for the selected theme
  getAllComponents() {
    const allComponents = {}

    Object.keys(this.theme).forEach((componentName) => {
      allComponents[componentName] = this.getComponentTheme(componentName)
    })

    return allComponents
  }
}

export default ThemeBuilder
