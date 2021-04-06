module.exports = {
    roots: ['<rootDir>/js-tests'],
    moduleFileExtensions: ['js', 'jsx', 'json', 'vue'],
    testRegex: 'js-tests/.*.spec.js$',
    transform: {
        '^.+\\.js$': '<rootDir>/node_modules/babel-jest',
        '.*\\.(vue)$': '<rootDir>/node_modules/vue-jest',
    },
    setupFiles: ['<rootDir>/js-tests/config.js'],
    globals: {
        __PATH_PREFIX__: '',
        'ts-jest': {
            diagnostics: false,
        },
    },
}
