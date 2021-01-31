const mix = require('laravel-mix');

mix.webpackConfig({
    output: {
        publicPath: '/vendor/bazar/',
        chunkFilename: '[name].js'
    },
    resolve: {
        alias: {
            'vue$': 'vue/dist/vue.runtime.esm.js'
        }
    },
    externals: {
        moment: 'moment'
    }
});

mix.setPublicPath('./public')
    .js('resources/js/app.js', 'app.js')
    .vue({ version: 2 })
    .sass('resources/sass/app.scss', 'app.css')
    .options({ processCssUrls: false })
    .sourceMaps()
    .version();
