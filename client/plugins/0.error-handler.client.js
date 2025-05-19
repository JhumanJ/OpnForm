export default defineNuxtPlugin((nuxtApp) => {
const router = useRouter()

    router.onError((error) => {
        if (
        error.message.includes('Failed to fetch dynamically imported module') ||
        error.message.includes('Failed to load resource')
        ) {
        window.location.reload()
        }
    })

    nuxtApp.hook('app:error', (error) => {
        if (
        error.message.includes('Loading chunk') ||
        error.message.includes('Failed to load resource')
        ) {
        window.location.reload()
        }
    })
})