const mix = require('laravel-mix');

mix.setPublicPath('./public')
    .js('resources/js/app.js', 'app.js')
    .sass('resources/sass/app.scss', 'app.css')
    .options({ processCssUrls: false })
    .sourceMaps();

// Symlinking...
// ln -s /.../packages/conedevelopment/bazar/public/app.js /.../public/vendor/bazar/app.js
// ln -s /.../packages/conedevelopment/bazar/public/app.css /.../public/vendor/bazar/app.css
// ln -s /.../packages/conedevelopment/bazar/resources/img /.../public/vendor/bazar/
