const mix = require('laravel-mix');

mix.webpackConfig({
    output: {
        publicPath: '/vendor/bazar/',
        chunkFilename: '[name].js',
    },
});

mix.setPublicPath('./public')
    .js('resources/js/app.js', 'app.js')
    .vue({ version: 3, runtimeOnly: true })
    .options({ processCssUrls: false })
    .sourceMaps()
    .version();
