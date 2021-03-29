module.exports = {
    plugins: [
        // npm packages: eslint eslint-plugin-vue
        "vue"
    ],
    extends: [
        "plugin:vue/base",
        "plugin:vue/essential",
        "plugin:vue/strongly-recommended",
        "plugin:vue/recommended"
    ],
    rules: {
        "vue/component-definition-name-casing": ["error", "kebab-case"]
    }
}
