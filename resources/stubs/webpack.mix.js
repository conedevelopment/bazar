mix.js('resources/js/vendor/bazar/app.js', 'public/vendor/bazar')
    .vue({ version: 2 })
    .sass('resources/sass/vendor/bazar/app.scss', 'public/vendor/bazar')
    .options({ processCssUrls: false })
    .sourceMaps()
    .version();
