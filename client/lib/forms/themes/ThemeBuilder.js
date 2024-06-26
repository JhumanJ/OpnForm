import {twMerge} from 'tailwind-merge'
import {themes} from './form-themes.js'

export const sizes = ['sm', 'md', 'lg']

class ThemeBuilder {
  constructor(theme = 'default', options = {}) {
    this.theme = themes[theme] || themes.default
    this.size = options.size || 'md'
    this.borderRadius = options.borderRadius || 'small'
  }

  extractSizedClasses(baseConfig) {
    if (typeof baseConfig === 'object' &&
      sizes.every((size) => baseConfig[size])) {
      return baseConfig[this.size]
    }

    return baseConfig
  }

  mergeNestedClasses(baseConfig, componentConfig) {
    const mergedConfig = {}

    const allKeys = new Set([
      ...Object.keys(baseConfig),
      ...Object.keys(componentConfig),
    ])

    allKeys.forEach((key) => {
      const baseValue = this.extractSizedClasses(baseConfig[key])
      const componentValue = this.extractSizedClasses(componentConfig[key])

      if (key === 'borderRadius') {
        // Special case for border radius
        const borderRadiusClass = baseConfig.borderRadius?.[this.borderRadius] || ''
        mergedConfig[key] = twMerge(borderRadiusClass, componentValue)
      } else if (
        typeof baseValue === 'object' &&
        baseValue !== null &&
        !Array.isArray(baseValue)) {
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
