const mix = require('laravel-mix');

mix.setPublicPath('./public')
    .js('resources/js/app.js', 'app.js')
    .sass('resources/sass/app.scss', 'app.css')
    .options({ processCssUrls: false })
    .sourceMaps()
    .version();
