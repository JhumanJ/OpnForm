export default defineNuxtRouteMiddleware((from, to, next) => {
    const runtimeConfig = useRuntimeConfig()
    if (runtimeConfig.public?.selfHostMode) {
        if (from.name == 'register' && from.query?.email && from.query?.invite_token) {
            return
        }
        if (from.name == 'ai-form-builder' && runtimeConfig.public?.aiFeaturesEnabled) {
            return
        }
        return navigateTo({ name: "home" })
    }
})