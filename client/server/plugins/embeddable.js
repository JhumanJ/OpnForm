export default defineNitroPlugin(nitroApp => {
  nitroApp.hooks.hook('render:response', (response, { event }) => {
    const routePath = event.node?.req?.url || event.node?.req?.originalUrl
    // const routePath= event.context.params._
    if (routePath && !routePath.startsWith('/forms/')) {
      // Only allow embedding of forms
      response.headers['X-Frame-Options'] = 'sameorigin'
    }

    delete response.headers['x-powered-by']
  })
})
