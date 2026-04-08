const Encore = require('@symfony/webpack-encore');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')

    // Copie les assets TinyMCE (skins, themes, icons, plugins) dans public/build/tinymce
    .copyFiles([
        { from: './node_modules/tinymce', to: 'tinymce/[path][name].[ext]' }
    ])

    .addEntry('app', './assets/app.js')
    .addEntry('frontStyle', './assets/styles/front/main.scss')
    .addEntry('adminStyle', './assets/styles/admin/main.scss')
    .addEntry('frontScript', './assets/scripts/front/main.js')
    .addEntry('adminScript', './assets/scripts/admin/main.js')

    .enableStimulusBridge('./assets/controllers.json')
    .splitEntryChunks()
    .enableSingleRuntimeChunk()

    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())

    .configureBabel((config) => {
        config.plugins.push('@babel/plugin-proposal-class-properties');
    })
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })

    .enableSassLoader()
;

module.exports = Encore.getWebpackConfig();
