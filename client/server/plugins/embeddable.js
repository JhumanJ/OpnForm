export default defineNitroPlugin(nitroApp => {
  nitroApp.hooks.hook('render:response', (response, { event }) => {
    const routePath= event.context.params._
    if (!routePath.startsWith('forms/')) {
      // Only allow embedding of forms
      response.headers['X-Frame-Options'] = 'sameorigin'
    }
  })
})
