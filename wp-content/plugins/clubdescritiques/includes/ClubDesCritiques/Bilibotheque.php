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

    /**
    * Return the brands
    * @param  WP_REST_Request $value
    * @return Json response
    */
    public static function getBibliothequeAPI($request){
        $params = $request->get_params();
        global $wpdb;

        $limit      = 10;
        $offset     = 0;
        $where      = array();
        $having     = array();
        $join       = '';

        $query      = "SELECT DISTINCT ". $wpdb->posts .".ID, ".$wpdb->posts .".post_title  FROM ". $wpdb->posts;
        $countQuery = "SELECT COUNT(*) as count FROM(SELECT ".  $wpdb->posts .".ID  FROM ". $wpdb->posts;

        //set OFFSET
        if(isset($params['offset']) && !empty($params['offset']))
            $offset        = $params['offset'];

        //set limit
        if(isset($params['limit']) && !empty($params['limit']))
            $limit         = $params['limit'];

        // filter by theme
        if(isset($params['theme']) && !empty($params['theme'])){
            $join         .= " LEFT JOIN ". $wpdb->term_relationships ." AS taxA ON (". $wpdb->posts .".ID = taxA.object_id)";
            $where[]       = "taxA.term_taxonomy_id IN (". $params[''] ."))";
            if(isset($params['exact_market']))
                $having[]  = " count(distinct taxA.term_taxonomy_id)=". count(explode(',',$params['theme']));
        }

        // filter by title or meta
        if(isset($params['search']) && !empty($params['search'])){
            $join         .= " INNER JOIN ". $wpdb->postmeta ." as metaA ON (". $wpdb->posts .".ID = metaA.post_id)";
            $needle        = array(' ','-','/');
            $search        = str_replace($needle, '%',$params['search']);
            $where[]       = $wpdb->posts .".post_title LIKE '%". $search ."%' OR metaA.meta_value LIKE '%". $search ."%')";
        }

        //merge filters
        if(!empty($where)){
            $where   = implode($where, ' AND (');
        }else{
            $where   = '1=1)';
        }

        //merge having
        if(!empty($having)){
            $having  = 'having'. implode($having, ' AND ');
        }else{
            $having  = '';
        }

        // create QUERY
        $query .= $join ." WHERE ". $wpdb->posts .".post_type = 'product' AND (". $where ." GROUP BY ". $wpdb->posts .".ID  ". $having ." LIMIT ". $offset .", ". $limit;

        //return query if asked
        if(isset($params['query']))
            return $query;

        //get result
        $result = $wpdb->get_results($query);

        //send response
        if(!empty($result)){

            //create count query
            $countQuery .= $join ." WHERE ". $wpdb->posts .".post_type = 'product' AND (". $where ." GROUP BY ". $wpdb->posts .".ID  ". $having .") as T";
            $response['nb_products'] = $wpdb->get_results($countQuery)[0]->count;

            $response['items'] = $result;

            return wp_send_json_success($response);
        }

        return wp_send_json_error();
    }
}
