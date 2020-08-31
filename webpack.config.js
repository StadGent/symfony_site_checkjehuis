var Encore = require('@symfony/webpack-encore');

Encore
    .disableSingleRuntimeChunk()
    .setOutputPath('public/build/')
    // This is specific to the city of Ghent implementation of this app. Change
    // this to /build if this website is running in your web root.
    .setPublicPath('/build')
    .setManifestKeyPrefix('build/')
    .configureFilenames({
        css: '[name]-[contenthash].css',
        js: '[name]-[contenthash].js'
    })
    .copyFiles({
        from: './assets/images',
        to: 'images/[path][name].[ext]'
    })
    .copyFiles({
        from: './assets/datepicker',
        to: 'datepicker/[path][name].[ext]'
    })
    .addStyleEntry('css/app', './assets/css/app.scss')
    .addEntry('js/app', './assets/js/app.js')
    .addEntry('js/ckeditor', './assets/js/ckeditor.js')
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .enableSassLoader()
;

module.exports = Encore.getWebpackConfig();
