module.exports = {
    root: true,
    extends: ["@nuxt/eslint-config"],
    parser: "vue-eslint-parser",
    env: {
        browser: true,
        node: true,
    },
    rules: {
        "vue/require-default-prop": "off",
        "vue/no-mutating-props": "off",
        semi: ["error", "never"],
        "vue/no-v-html": "off",
        "prefer-rest-params": "off",
        "vue/valid-template-root": "off",
        "no-undef": "off",
        "no-unused-vars": ["error", { "argsIgnorePattern": "^_" }],
        "@typescript-eslint/no-unused-vars": ["error", { "argsIgnorePattern": "^_" }]
    },
};
