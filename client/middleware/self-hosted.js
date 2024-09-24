export default defineNuxtRouteMiddleware((from, to) => {
    if (useFeatureFlag('self_hosted')) {
        if (from.name === 'register' && to.query?.email && to.query?.invite_token) {
            return
        }
        if (from.name === 'ai-form-builder' && useFeatureFlag('ai_features')) {
            return
        }
        return navigateTo({ name: "index" })
    }
})
