export default defineNuxtRouteMiddleware((from) => {
    const runtimeConfig = useRuntimeConfig()
    if (runtimeConfig.public?.selfHosted) {
        if (from.name == 'register' && from.query?.email && from.query?.invite_token) {
            return
        }
        if (from.name == 'ai-form-builder' && runtimeConfig.public?.aiFeaturesEnabled) {
            return
        }
        return navigateTo({ name: "home" })
    }
})