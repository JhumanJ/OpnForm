// hexToHSL.js functionality
const hexToHSL = (hex) => {
  const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex) || []
  try {
    let r = parseInt(result[1], 16)
    let g = parseInt(result[2], 16)
    let b = parseInt(result[3], 16)
    r /= 255
    g /= 255
    b /= 255
    const max = Math.max(r, g, b),
      min = Math.min(r, g, b)
    let h = 0,
      s,
      l = (max + min) / 2

    if (max === min) {
      h = s = 0 // achromatic
    } else {
      const d = max - min
      s = l > 0.5 ? d / (2 - max - min) : d / (max + min)
      switch (max) {
        case r:
          h = (g - b) / d + (g < b ? 6 : 0)
          break
        case g:
          h = (b - r) / d + 2
          break
        case b:
          h = (r - g) / d + 4
          break
      }
      h /= 6
    }

    return { h: Math.round(h * 360), s: Math.round(s * 100), l: Math.round(l * 100) }
  } catch {
    console.error('Invalid HEX color', hex)
    return { h: 0, s: 0, l: 0 }
  }
}

// hslToHex.js functionality
const hslToHex = ({ h, s, l }) => {
  l /= 100
  const a = (s * Math.min(l, 1 - l)) / 100
  const f = n => {
    const k = (n + h / 30) % 12
    const color = l - a * Math.max(Math.min(k - 3, 9 - k, 1), -1)
    return Math.round(255 * color).toString(16).padStart(2, '0')
  }
  return `#${f(0)}${f(8)}${f(4)}`
}

// generateColor.js functionality
const generateColor = ({ hex, preserve, shades }) => {
  const colorHSL = hexToHSL(hex)
  const obj = {}
  const lightnessDelta = {}

  shades.forEach(({ name, lightness }) => {
    const { h, s, l } = colorHSL
    const hsl = { h, s, l: lightness }
    const hex = hslToHex(hsl)
    obj[name] = hex
    if (preserve) lightnessDelta[name] = Math.abs(l - lightness)
  })

  if (preserve) {
    const [closestShade] = Object.keys(lightnessDelta).sort(
      (a, b) => lightnessDelta[a] - lightnessDelta[b]
    )
    obj[closestShade] = hex
  }

  return obj
}

// tailwindcssPaletteGenerator functionality
const tailwindcssPaletteGenerator = (options) => {
  let colors = []
  let names = ['primary', 'secondary', 'tertiary', 'quaternary', 'quinary', 'senary', 'septenary', 'octonary', 'nonary', 'denary']
  let preserve = true
  let shades = [
    { name: '50', lightness: 98 },
    { name: '100', lightness: 95 },
    { name: '200', lightness: 90 },
    { name: '300', lightness: 82 },
    { name: '400', lightness: 64 },
    { name: '500', lightness: 46 },
    { name: '600', lightness: 33 },
    { name: '700', lightness: 24 },
    { name: '800', lightness: 14 },
    { name: '900', lightness: 7 },
    { name: '950', lightness: 4 }
  ]

  if (typeof options === 'string') options = { colors: [options], names, preserve, shades }
  if (Array.isArray(options)) options = { colors: options, names, preserve, shades }
  if (typeof options === 'object' && !Array.isArray(options)) {
    options = Object.assign({ colors, names, preserve, shades }, options)
  }

  const palette = {}
  options.colors.forEach((hex, i) => {
    const name = options.names[i]
    palette[name] = generateColor({ hex, preserve, shades: options.shades })
  })

  return palette
}

// Exporting the functions
export { generateColor, tailwindcssPaletteGenerator }
