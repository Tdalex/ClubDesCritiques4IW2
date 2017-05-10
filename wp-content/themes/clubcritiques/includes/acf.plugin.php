<?php 

if( !function_exists('is_plugin_active') ) {
            
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    
}

if (is_admin() && is_plugin_active('advanced-custom-fields/acf.php')) {
    /*----------  Auto export ACF changes as JSON  ----------*/
    function wptm_acf_json_save_point( $path ) {
        $path = get_template_directory() . '/acf-json';
        
        $path .= '/' . checkSite();
        
        // return
        return $path;
    }
    add_filter('acf/settings/save_json', 'wptm_acf_json_save_point');
}