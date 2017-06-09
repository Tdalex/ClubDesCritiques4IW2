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
            'Bibliotheque',
            array(
                'labels' => array(
                    'name' => __('Bibliotheques', 'clubdescritiques'),
                    'singular_name' => __('Bibliotheque', 'clubdescritiques'),
                    'all_items' => __('Toutes les bibliotheques', 'clubdescritiques'),
                    'add_new_item' => __("Ajout d'une bibliotheque", 'clubdescritiques'),
                    'search_items' => __('Recherche dans la bibliotheque', 'clubdescritiques'),
                    'not_found' => __('Aucune bibliotheque trouvee.', 'clubdescritiques')
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
		
		// post_type = commentaire
        register_post_type(
            'Commentaire',
            array(
                'labels' => array(
                    'name' => __('Commentaires', 'clubdescritiques'),
                    'singular_name' => __('Commentaire', 'clubdescritiques'),
                    'all_items' => __('Tout les commentaires', 'clubdescritiques'),
                    'add_new_item' => __("Ajout d'un commentaire", 'clubdescritiques'),
                    'search_items' => __('Recherche dans les commentaires', 'clubdescritiques'),
                    'not_found' => __('Aucun commentaire trouvee.', 'clubdescritiques')
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
            'Auteurs',
            array(
                'labels' => array(
                    'name' => __('Auteurs', 'clubdescritiques'),
                    'singular_name' => __('Auteur', 'clubdescritiques'),
                    'all_items' => __('Tout les auteurs', 'clubdescritiques'),
                    'add_new_item' => __('Ajouter un auteur', 'clubdescritiques'),
                    'search_items' => __('Rechercher dans les auteurs', 'clubdescritiques'),
                    'not_found' => __('Aucun auteur trouve.', 'clubdescritiques')
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
		
		 // post_type = notation
        register_post_type(
            'Notation',
            array(
                'labels' => array(
                    'name' => __('Notations', 'clubdescritiques'),
                    'singular_name' => __('Notation', 'clubdescritiques'),
                    'all_items' => __('Toutes les notations', 'clubdescritiques'),
                    'add_new_item' => __('Ajouter une notation', 'clubdescritiques'),
                    'search_items' => __('Rechercher dans les notations', 'clubdescritiques'),
                    'not_found' => __('Aucune notation trouve.', 'clubdescritiques')
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
        register_taxonomy('bibliotheque_genre', array('bibliotheque'), $args);
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
