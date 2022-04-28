const mix = require('laravel-mix');

mix.webpackConfig({
    output: {
        publicPath: '/vendor/bazar/',
        chunkFilename: '[name].js',
    },
    externals: {
        vue: 'Vue',
    },
});

mix.setPublicPath('./public')
    .js('resources/js/app.js', 'app.js')
    .vue({ runtimeOnly: true })
    .options({ processCssUrls: false })
    .sourceMaps()
    .version();
