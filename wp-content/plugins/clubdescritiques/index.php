<?php
/**
 * Club Des Critiques
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

require WP_CONTENT_DIR. '/themes/clubdescritiques/vendor/autoload.php';

spl_autoload_register(function($class) {
    $namespaces = explode('\\', $class);
    $project = reset($namespaces);

    if ($project == 'ClubDesCritiques') {
        $path = null;
        while (($namespace = current($namespaces)) !== false) {
            $path .= DIRECTORY_SEPARATOR . $namespace;
            next($namespaces);
        }
        $path .= '.php';

        if (file_exists(plugin_dir_path(__FILE__) . 'includes' . $path)) {
            require (plugin_dir_path(__FILE__) . 'includes' .  $path);
        }
    }
});

register_activation_hook(__FILE__, array('ClubDesCritiques\Bibliotheque', 'activate'));


add_action('init', array('ClubDesCritiques\Bibliotheque', 'initCustomTypes'));
add_action('init', array('ClubDesCritiques\Bibliotheque', 'initCustomTaxonomies'));

// init custom WP API endpoint
add_action( 'rest_api_init', function () {
    register_rest_route( 'ClubDesCritiques/', "bibliotheque", array(
        'methods'  => WP_REST_Server::READABLE,
        'callback' => array('ClubDesCritiques\Bibliotheque', 'getBibliothequeAPI'),
    ));
});

//add custom param for WP_QUERY for *like*
add_filter( 'posts_where', array('ClubDesCritiques\Bibliotheque', 'search_posts_where'), 10, 2 );

ClubDesCritiques\Settings::registerHooks();
ClubDesCritiques\Cron\Task\Migration::registerHooks();


