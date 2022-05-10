let mix = require( 'laravel-mix' )

mix.setPublicPath( 'dist' )
   .js( 'resources/js/field.js', 'js' )
   .webpackConfig( {
                       resolve: {
                           alias: {
                               '@local': path.resolve( __dirname, 'resources/js/' ),
                               '@nova': path.resolve( __dirname, 'vendor/laravel/nova/resources/js/' ),
                               '@': path.resolve( __dirname, 'vendor/laravel/nova/resources/js/' ),
                           },
                       }
                   } )
