import routes from './routes'
import { createWebHistory, createRouter } from 'vue-router'
import * as Sentry from '@sentry/vue'
import { useAppStore } from '../stores/app'
import { defineComponent, nextTick } from 'vue'

// The middleware for every page of the application.
const globalMiddleware = ['check-auth', 'custom-domains']

// Load middleware modules dynamically.
const requireContext = import.meta.glob('../middleware/**/*.js', { eager: true })
const routeMiddleware = resolveMiddleware(
  requireContext
)

const router = createCustomRouter()

export default router

function createCustomRouter () {
  const router = createRouter({
    scrollBehavior,
    history: createWebHistory(),
    routes
  })

  router.beforeEach(beforeEach)
  router.afterEach(afterEach)

  return router
}

async function getMatchedComponents (to) {
  return resolveComponents(to.matched.map((record) => {
    const component = record.components.default
    return typeof component === 'function' ? defineComponent(component) : component
  }))
}

/**
 * Global router guard.
 *
 * @param {Route} to
 * @param {Route} from
 * @param {Function} next
 */
async function beforeEach (to, from, next) {
  const appStore = useAppStore()

  // Sentry tracking
  if (window.config.sentry_dsn) {
    Sentry.configureScope((scope) => scope.setTransactionName(to?.name || 'Unknown route name'))
  }

  let components = []

  // External redirect
  if (to.matched.some((record) => record.meta.externalUrl)) {
    const url = to.meta.externalUrl
    window.location.replace(url)
    return
  }

  try {
    // Get the matched components and resolve them.
    components = await getMatchedComponents(to)
  } catch (error) {
    if (/^Loading( CSS)? chunk (\d)+ failed\./.test(error.message)) {
      window.location.reload(true)
      return
    }
  }

  if (components.length === 0) {
    return next()
  }

  // Start the loading bar.
  if (components[components.length - 1].loading !== false) {
    nextTick(() => appStore.loaderStart())
  }

  // Get the middleware for all the matched components.
  const middleware = getMiddleware(components)

  // Call each middleware.
  callMiddleware(middleware, to, from, (...args) => {
    // Set the application layout only if "next()" was called with no args.
    if (args.length === 0) {
      if (components[0].layout) {
        appStore.setLayout(components[0].layout)
      } else if (components[0].default && components[0].default.layout) {
        appStore.setLayout(components[0].default.layout)
      } else {
        appStore.setLayout(null)
      }
    }

    next(...args)
  })
}

/**
 * Global after hook.
 *
 * @param {Route} to
 * @param {Route} from
 * @param {Function} next
 */
async function afterEach (to, from, next) {
  const appStore = useAppStore()
  nextTick(() => appStore.loaderFinish())
}

/**
 * Call each middleware.
 *
 * @param {Array} middleware
 * @param {Route} to
 * @param {Route} from
 * @param {Function} next
 */
function callMiddleware (middleware, to, from, next) {
  const appStore = useAppStore()
  const stack = middleware.reverse()

  const _next = (...args) => {
    // Stop if "_next" was called with an argument or the stack is empty.
    if (args.length > 0 || stack.length === 0) {
      if (args.length > 0) {
        appStore.loaderFinish()
      }

      return next(...args)
    }

    const {
      middleware,
      params
    } = parseMiddleware(stack.pop())

    if (typeof middleware === 'function') {
      middleware(to, from, _next, params)
    } else if (routeMiddleware[middleware]) {
      routeMiddleware[middleware](to, from, _next, params)
    } else {
      throw Error(`Undefined middleware [${middleware}]`)
    }
  }

  _next()
}

/**
 * @param  {String|Function} middleware
 * @return {Object}
 */
function parseMiddleware (middleware) {
  if (typeof middleware === 'function') {
    return { middleware }
  }

  const [name, params] = middleware.split(':')

  return { middleware: name, params }
}

/**
 * Merge the the global middleware with the components middleware.
 *
 * @param  {Array} components
 * @return {Array}
 */
function getMiddleware (components) {
  const middleware = [...globalMiddleware]

  components.forEach(component => {
    let compMiddleware
    if (component.middleware) {
      compMiddleware = component.middleware
    } else if (component.default && component.default.middleware) {
      compMiddleware = component.default.middleware
    }

    if (compMiddleware) {
      if (Array.isArray(compMiddleware)) {
        middleware.push(...compMiddleware)
      } else {
        middleware.push(compMiddleware)
      }
    }
  })

  return middleware
}

/**
 * Scroll Behavior
 *
 * @link https://router.vuejs.org/en/advanced/scroll-behavior.html
 *
 * @param  {Route} to
 * @param  {Route} from
 * @param  {Object|undefined} savedPosition
 * @return {Object}
 */
function scrollBehavior (to, from, savedPosition) {
  if (savedPosition) {
    return savedPosition
  }

  if (to.hash) {
    return { selector: to.hash }
  }
  return {}
}

/**
 * @param  {Object} requireContext
 * @return {Object}
 */
function resolveMiddleware (requireContext) {
  const middlewares = {}
  Object.keys(requireContext)
    .map(file =>
      [file.match(/[^/]*(?=\.[^.]*$)/)[0], requireContext[file]]
    ).forEach(([name, middleware]) => {
      middlewares[name] = middleware.default || middleware
    })
  return middlewares
}

/**
 * Resolve async components.
 *
 * @param  {Array} components
 * @return {Array}
 */
function resolveComponents (components) {
  return Promise.all(components.map(component => {
    return typeof component === 'function' ? component() : component
  }))
}
