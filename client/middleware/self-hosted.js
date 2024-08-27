export default defineNuxtRouteMiddleware((from, to, next) => {
    const route = useRoute()
    if (useFeatureFlag('self_hosted')) {
        if (from.name === 'register' && route.query?.email && route.query?.invite_token) {
            return
        }
        if (from.name === 'ai-form-builder' && useFeatureFlag('ai_features')) {
            return
        }
        return navigateTo({ name: "index" })
    }
})
