export default defineNuxtRouteMiddleware((from, to, next) => {
    const runtimeConfig = useRuntimeConfig()
    const route = useRoute()
    if (runtimeConfig.public?.selfHostMode) {
        if (from.name == 'register' && route.query?.email && route.query?.invite_token) {
            return
        }
        if (from.name == 'ai-form-builder' && runtimeConfig.public?.aiFeaturesEnabled) {
            return
        }
        return navigateTo({ name: "home" })
    }
})