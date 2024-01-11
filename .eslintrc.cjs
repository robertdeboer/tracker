module.exports = {
    extends: [
        // add more generic rule sets here, such as:
        // 'eslint:recommended',
        'plugin:vue/vue3-recommended',
        // 'plugin:vue/recommended' // Use this if you are using Vue.js 2.x.
        "plugin:vue/vue3-strongly-recommended",
        'plugin:@typescript-eslint/recommended',
        'prettier'
    ],
    rules: {
        // override/add rules settings here, such as:
        // 'vue/no-unused-vars': 'error',
        'vue/multi-word-component-names': 0
    },
    parser: 'vue-eslint-parser',
    "parserOptions": {
        "parser": {
            "ts": "@typescript-eslint/parser",
            "js": "@typescript-eslint/parser",
            "<template>": "espree"
        }
    },
    plugins: ['@typescript-eslint'],
    root: true,
    env: {
        "es6": true
    }
}
