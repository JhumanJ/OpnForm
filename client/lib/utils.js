export const hash = (str, seed = 0) => {
  let h1 = 0xdeadbeef ^ seed,
    h2 = 0x41c6ce57 ^ seed;
  for (let i = 0, ch; i < str.length; i++) {
    ch = str.charCodeAt(i);
    h1 = Math.imul(h1 ^ ch, 2654435761);
    h2 = Math.imul(h2 ^ ch, 1597334677);
  }

  h1 = Math.imul(h1 ^ (h1 >>> 16), 2246822507) ^ Math.imul(h2 ^ (h2 >>> 13), 3266489909);
  h2 = Math.imul(h2 ^ (h2 >>> 16), 2246822507) ^ Math.imul(h1 ^ (h1 >>> 13), 3266489909);

  return 4294967296 * (2097151 & h2) + (h1 >>> 0);
}

/*
 * Url and domain related utils
 */

/**
 * Returns the appUrl with the given path appended.
 * @param path
 * @returns {string}
 */
export const appUrl = (path = '/') => {
  let baseUrl = useRuntimeConfig().public.appUrl
  if (!baseUrl) {
    console.warn('appUrl not set in runtimeConfig')
    return path
  }

  if (path.startsWith('/')) {
    path = path.substring(1)
  }

  if (!baseUrl.endsWith('/')) {
    baseUrl += '/'
  }

  return baseUrl + path
}

/**
 * SSR compatible function to get current host
 * @param path
 * @returns {string}
 */
export const getHost = function () {
  if (process.server) {
    return getDomain(useNuxtApp().ssrContext?.event.context.siteConfigNitroOrigin) || useNuxtApp().ssrContext?.event.node.req.headers.host
  } else {
    return window.location.host
  }
}

/**
 * Extract domain from url
 * @param url
 * @returns {*}
 */
export const getDomain = function (url) {
  if (url.includes('localhost')) return 'localhost'
  try {
    if (!url.startsWith('http')) url = 'https://' + url
    return (new URL(url)).hostname
  } catch (e) {
    return url
  }
}

/**
 * Returns true if the app is running on a custom domain, false otherwise.
 * @returns {boolean}
 */
export const customDomainUsed = function() {
  const config = useRuntimeConfig()
  const appDomain = getDomain(config.public.appUrl)
  const host = getHost()

  return host !== appDomain && getDomain(host) !== appDomain
}
