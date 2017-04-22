<?php
/**
 * Club Des Critiques Extension
 *
 * @package clubdescritiques
 */

namespace ClubDesCritiques;


class Bibliotheque
{
    public static function activate()
    {
        global $wpdb;

        
    }

    public static function initCustomTypes()
    {

        // post_type = bibliotheque
        register_post_type(
            'bibliotheque',
            array(
                'labels' => array(
                    'name' => __('bibliotheques', 'clubdescritiques'),
                    'singular_name' => __('bibliotheque', 'clubdescritiques'),
                    'all_items' => __('All bibliotheque', 'clubdescritiques'),
                    'add_new_item' => __('Add bibliotheque', 'clubdescritiques'),
                    'search_items' => __('Search in bibliotheque', 'clubdescritiques'),
                    'not_found' => __('No bibliotheque found.', 'clubdescritiques')
                ),
                'public' => true,
                'show_ui' => true, // passer à false pour ne pas y accéder en BO, mais sera accessible en front
                'show_in_nav_menus' => false,
                'show_in_admin_bar' => false,
                'supports' => array('title'),
                'can_export' => true,
                'show_in_rest'  => true,
                'custom_metadata' => true,
            )
        );
		
		 // post_type = Authors
        register_post_type(
            'authors',
            array(
                'labels' => array(
                    'name' => __('authors', 'clubdescritiques'),
                    'singular_name' => __('author', 'clubdescritiques'),
                    'all_items' => __('All authors', 'clubdescritiques'),
                    'add_new_item' => __('Add author', 'clubdescritiques'),
                    'search_items' => __('Search in authors', 'clubdescritiques'),
                    'not_found' => __('No author found.', 'clubdescritiques')
                ),
                'public' => true,
                'show_ui' => true, // passer à false pour ne pas y accéder en BO, mais sera accessible en front
                'show_in_nav_menus' => false,
                'show_in_admin_bar' => false,
                'supports' => array('title'),
                'can_export' => true,
                'show_in_rest'  => true,
                'custom_metadata' => true,
            )
        );

        flush_rewrite_rules(false);
    }

    public static function initCustomTaxonomies()
    {
        // taxonomy = category
        $args = array(
            'labels' => array(
                'name' => __('Genres', 'taxonomy general name'),
                'singular_name' => __('Genre', 'taxonomy general name'),
                'all_items' => __('All genre', 'aubertetduval'),
                'add_new_item' => __('Add genre', 'aubertetduval'),
                'search_items' => __('Search in genre', 'aubertetduval'),
                'not_found' => __('No genre found.', 'aubertetduval')
            ),
            'public'                => false,
            'hierarchical'          => false,
            'show_ui'               => true,
            'show_in_nav_menus'     => false,
            'show_admin_column'     => true,
            'query_var'             => true,
            'show_in_rest'          => true
        );
        register_taxonomy('bibliotheque_genre', array('genre'), $args);
    }
    
    /*public static function loadTextDomain()
    {
        load_plugin_textdomain('uimmcrm', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }*/

    /**
    * add custom param for WP_QUERY for *like*
    */
    public static function search_posts_where( $where, &$wp_query )
    {
        global $wpdb;
        if ( $search_like = $wp_query->get( 'search' ) ) {
            //title
            $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( $wpdb->esc_like( $search_like ) ) . '%\'';

            //meta
            // $where .= ' OR ( ' . $wpdb->postmeta . '.meta_value LIKE \'%' . esc_sql( $wpdb->esc_like( $search_like ) ) . '%\' )';
        }
        return $where;
    }

    /**
    * Return the brands
    * @param  WP_REST_Request $value
    * @return Json response
    */
    public static function getBibliothequeAPI($request){
        $params = $request->get_params();

        $args['posts_per_page']  = -1;
        $args['post_type']       = 'product';

        foreach($params as $key => $value){
            switch($key){
                case 'market':
                    $args['tax_query'] =
                        array(
                            array(
                                'taxonomy' => 'product_market',
                                'field'    => 'term_id',
                                'terms'    => $value,
                            )
                        );
                    break;

                case 'search':

                    // title like value
                    $args[$key] = $value;

                    // keyword + mechanical properties like search
                    // $args['meta_query'] = 
                    //     array(
                    //         'relation'      => 'OR',
                    //         array(
                    //             // 'key'       => 'keyword',
                    //             'value'     => $value,
                    //             'compare'   => 'LIKE',
                    //         ),
                    //         // array(
                    //         //     'key'       => 'mechanical_properties_%_value',
                    //         //     'value'     => $value,
                    //         //     'compare'   => 'LIKE',
                    //         // )
                    //     );

                    break;

                default:
                    $args[$key] = $value;
                    break;
            }
        }

        $wp = new \WP_Query($args);
        if($wp !== null && !empty($wp->posts)){
            // return $wp->request;
            $response['total'] = $wp->post_count;
            $response['body']  = $wp->posts;
            return $response;
        }
        return false;
    }
}
