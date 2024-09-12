export default defineAppConfig({
    ui: {
        primary: process.env.NUXT_THEME_PRIMARY_COLOR !== null ? process.env.NUXT_THEME_PRIMARY_COLOR : 'green',
        gray: 'slate'
    }
})
