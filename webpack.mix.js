let mix = require('laravel-mix');
/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.styles([
        'public/asset/bower_components/bootstrap/dist/css/bootstrap.min.css',
        'public/asset/dist/css/AdminLTE.css',
        'public/asset/dist/css/skins/skin-black.css',
        'public/asset/bower_components/morris.js/morris.css',
        'public/asset/bower_components/jvectormap/jquery-jvectormap.css',
        'public/asset/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css',
        'public/asset/bower_components/bootstrap-daterangepicker/daterangepicker.css',
        'public/asset/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css',
        'public/asset/hotsnackbar.css'


    ], 'public/css/everythingSangitCSS.css')

.combine([
        'public/asset/bower_components/jquery/dist/jquery.min.js',
        'public/asset/bower_components/jquery-ui/jquery-ui.min.js',
        'public/asset/bower_components/bootstrap/dist/js/bootstrap.min.js',
        'public/asset/bower_components/morris.js/morris.min.js',
        'public/asset/bower_components/jquery-sparkline/dist/jquery.sparkline.min.js',
        'public/asset/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js',
        'public/asset/plugins/jvectormap/jquery-jvectormap-world-mill-en.js',
        'public/asset/bower_components/jquery-knob/dist/jquery.knob.min.js',
        'public/asset/bower_components/moment/min/moment.min.js',
        'public/asset/bower_components/bootstrap-daterangepicker/daterangepicker.js',
        'public/asset/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js',
        'public/asset/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js',
        'public/asset/bower_components/jquery-slimscroll/jquery.slimscroll.min.js',
        'public/asset/bower_components/fastclick/lib/fastclick.js',
        'public/asset/hotsnackbar.js',
        'public/asset/dist/js/adminlte.min.js'

    ], 'public/js/everythingSangitJS.js');
