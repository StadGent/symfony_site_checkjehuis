var Encore = require('@symfony/webpack-encore');

Encore
    .disableSingleRuntimeChunk()
    .setOutputPath('public/build/')
    .setPublicPath('/build')
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
