const mix = require( 'laravel-mix' );

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js( 'resources/js/app.js', 'public/js' )
   .copy( 'resources/js/global.js', 'public/js' )
   .copy( 'resources/js/triggers.js', 'public/js' )
   .copy( 'resources/js/helpers.js', 'public/js' )
   .copy( 'resources/js/globalEventListener.js', 'public/js' )
   .copy( 'resources/js/dynamic_triggers.js', 'public/js' )
   // .copy( 'resources/js/toggleHiddenNavigation.js', 'public/js' )
   // .copy( 'resources/js/login_as_admin.js', 'public/js' )
   // .copy( 'resources/js/sweetalert.min.js', 'public/js' )
   .postCss( 'resources/css/my_app.css', 'public/css' )
   .postCss( 'resources/css/custom-ltr.css', 'public/css' )
   .postCss( 'resources/css/login.css', 'public/css' )
   .postCss( 'resources/css/Nunito.css', 'public/css' )
   .copy( 'resources/images/*', 'public/images' )
   .copy( 'resources/images/quill', 'public/images/quill' )
   .copyDirectory( 'resources/fonts', 'public/fonts' )
   .postCss( 'resources/css/app.css', 'public/css', [
       //
   ] );

// mix.sass( 'resources/scss/custom.scss', 'public/nova/nova-theme/custom.css' )
//    .options( {
//                  processCssUrls: false,
//              } )
//    .copy( 'node_modules/font-awesome/fonts', 'public/fonts' );
mix.copy( 'node_modules/font-awesome/fonts', 'public/fonts' );

mix.sass( 'resources/scss/custom-rtl.scss', 'public/css/custom-rtl.css' )
   .options( {
                 processCssUrls: false,
             } );

mix.sass( 'resources/scss/sidebar-icons.scss', 'public/css/sidebar-icons.css' )
   .options( {
                 processCssUrls: false,
             } );
