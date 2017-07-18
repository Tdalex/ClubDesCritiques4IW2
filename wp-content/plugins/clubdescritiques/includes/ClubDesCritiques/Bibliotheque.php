<?php
/**
 * Club Des Critiques Extension
 *
 * @package clubdescritiques
 */

namespace ClubDesCritiques;

use ClubDesCritiques\Utilisateur as Utilisateur;

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

		// post_type = echange
        register_post_type(
            'Echange',
            array(
                'labels' => array(
                    'name' => __('Echanges', 'clubdescritiques'),
                    'singular_name' => __('Echange', 'clubdescritiques'),
                    'all_items' => __('Tous les echanges', 'clubdescritiques'),
                    'add_new_item' => __("Ajout d'un echange", 'clubdescritiques'),
                    'search_items' => __('Recherche dans les echanges', 'clubdescritiques'),
                    'not_found' => __('Aucun echange trouve.', 'clubdescritiques')
                ),
                'public' => true,
                'show_ui' => true, // passer à false pour ne pas y accéder en BO, mais sera accessible en front
                'show_in_nav_menus' => false,
                'show_in_admin_bar' => false,
                'supports' => array('title', 'author'),
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
                'supports' => array('title', 'editor', 'author'),
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
                'supports' => array('title', 'author'),
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

		// taxonomy = type
        $args = array(
            'labels' => array(
                'name' => __('types', 'taxonomy general name'),
                'singular_name' => __('type', 'taxonomy general name'),
                'all_items' => __('all types', 'aubertetduval'),
                'add_new_item' => __('Add type', 'aubertetduval'),
                'search_items' => __('Search in type', 'aubertetduval'),
                'not_found' => __('No type found.', 'aubertetduval')
            ),
            'public'                => false,
            'hierarchical'          => false,
            'show_ui'               => true,
            'show_in_nav_menus'     => false,
            'show_admin_column'     => true,
            'query_var'             => true,
            'show_in_rest'          => true
        );
        register_taxonomy('exchange_type', array('echange'), $args);

		 // taxonomy = format
        $args = array(
            'labels' => array(
                'name' => __('Formats', 'taxonomy general name'),
                'singular_name' => __('Format', 'taxonomy general name'),
                'all_items' => __('All format', 'aubertetduval'),
                'add_new_item' => __('Add format', 'aubertetduval'),
                'search_items' => __('Search in format', 'aubertetduval'),
                'not_found' => __('No format found.', 'aubertetduval')
            ),
            'public'                => false,
            'hierarchical'          => false,
            'show_ui'               => true,
            'show_in_nav_menus'     => false,
            'show_admin_column'     => true,
            'query_var'             => true,
            'show_in_rest'          => true
        );
        register_taxonomy('bibliotheque_format', array('bibliotheque'), $args);
    }

	/**
    * Return the brands
    * @param  WP_REST_Request $value
    * @return Json response
    */
    public static function getBibliotheque($params){
        global $wpdb;
        foreach($params as $key => $value){
            if(!is_array($params[$key])){
                $params[$key] = sanitize_text_field($value);
            }else{
                foreach($params[$key] as $k => $val)
                    $params[$key][$k] = sanitize_text_field($val);
            }
        }

        $limit  = 12;
        $offset = 0;
        $where  = array();
        $having = array();
        $join   = '';
		$auteurId = array();

        $query      = "SELECT DISTINCT ". $wpdb->posts .".ID, ".$wpdb->posts .".post_title  FROM ". $wpdb->posts;
        $countQuery = "SELECT COUNT(*) as count FROM(SELECT ".  $wpdb->posts .".ID  FROM ". $wpdb->posts;

        //set OFFSET
        if(isset($params['offset']) && !empty($params['offset']))
            $offset        = $params['offset'];

        //set limit
        if(isset($params['limit']) && !empty($params['limit']))
            $limit         = $params['limit'];

        // filter by title, author or meta
		if(isset($params['keywords']) && !empty($params['keywords'])){
			$search_query = 'SELECT ID FROM wp_posts WHERE post_type = "auteurs" AND post_title LIKE %s';
			$like = '%'.$params['keywords'].'%';
			$results = $wpdb->get_results($wpdb->prepare($search_query, $like), ARRAY_N);
			foreach($results as $key => $array){
				 $auteurId[] = 'a:1:{i:0;s:2:"'.$array[0].'";}';
			}

            $join         .= " INNER JOIN ". $wpdb->postmeta ." as metaA ON (". $wpdb->posts .".ID = metaA.post_id)";
            $needle        = array(' ','-','/');
            $keywords      = str_replace($needle, '%',$params['keywords']);
            $firstWhere    = '( '.$wpdb->posts .".post_title LIKE '%". $keywords ."%' OR metaA.meta_value LIKE '%". $keywords ."%' )";
			if(!empty($auteurId)){
				$join     .= " INNER JOIN ". $wpdb->postmeta ." as metaB ON (". $wpdb->posts .".ID = metaB.post_id)";
				$auteurId  = implode('\',\'', $auteurId);
				$firstWhere .= " OR metaB.meta_value IN ('". $auteurId ."')";
			}
			$where[]   = $firstWhere .")";
        }
        // filter by genre
        if(isset($params['genre']) && !empty($params['genre'])){
            if(is_array($params['genre']))
                $params['genre'] = implode(',', $params['genre']);

            $join         .= " LEFT JOIN ". $wpdb->term_relationships ." as taxA ON (". $wpdb->posts .".ID = taxA.object_id)";
            $where[]       = " taxA.term_taxonomy_id IN (". $params['genre'] ."))";
            if(isset($params['genre']))
                $having[]  = " count(distinct taxA.term_taxonomy_id)=". count(explode(',',$params['genre']));
        }

		// filter by format
        if(isset($params['format']) && !empty($params['format'])){
            if(is_array($params['format']))
                $params['format'] = implode(',', $params['format']);

            $join         .= " LEFT JOIN ". $wpdb->term_relationships ." as taxB ON (". $wpdb->posts .".ID = taxB.object_id)";
            $where[]       = " taxB.term_taxonomy_id IN (". $params['format'] ."))";
            if(isset($params['format']))
                $having[]  = " count(distinct taxB.term_taxonomy_id)=". count(explode(',',$params['format']));
        }

        // filter by note
        // if(isset($params['note']) && !empty($params['note'])){
            // $join         .= " INNER JOIN ". $wpdb->postmeta ." as metaC ON (". $wpdb->posts .".ID = metaC.post_id)";
            // if(is_array($params['note']))
                // $params['note'] = implode("','",$params['note']);
            // $where[]       = " metaC.meta_key LIKE '%notation%' AND metaC.meta_value >= ". $params['note'] .")";
            // $having[]      = " count(metaC.meta_value)=". count(explode(',',$params['note']));
        // }

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
        $query .= $join ." WHERE (". $wpdb->posts .".post_type = 'bibliotheque' AND ". $wpdb->posts .".post_status = 'publish' AND ". $where ." GROUP BY ". $wpdb->posts .".ID  ". $having ." LIMIT ". $offset .", ". $limit;

        //return query if asked
        if(isset($params['query']))
            return $query;

        //get result
        $result = $wpdb->get_results($query);

        //send response
        if(!empty($result)){
			$response['nb_products'] = 0;
            //create count query
            $countQuery .= $join ." WHERE (". $wpdb->posts .".post_type = 'product' AND ". $wpdb->posts .".post_status = 'publish' AND ". $where ." GROUP BY ". $wpdb->posts .".ID  ". $having .") as T";
            
			if(isset($params['note']) && !empty($params['note'])){
				$response['products']    = array();
				foreach($result as $r){				
					$averageNote = Utilisateur::getAverageNote($r->ID);
					if($averageNote['average'] >= $params['note'] && $averageNote['total'] > 0){
						$response['nb_products']++;
						$response['products'][]    = $r;							
					}
				}
			}else{
				$response['nb_products'] = $wpdb->get_results($countQuery)[0]->count;
				$response['products']    = $result;
            }
			return $response;
        }
        $response['nb_products'] = 0;
        $response['products']    = array();
        return $response;
    }

	public static function getAuthorBiblio($authorId, $max = -1){
		$args = array(
			'posts_per_page'   => $max,
			'meta_key'         => 'author',
			'meta_value'       => "a:1:{i:0;s:2:\"". $authorId ."\";}",
			'post_type'        => 'bibliotheque',
			'post_status'      => 'publish',
		);

		$posts = get_posts( $args );
		if(!empty($posts))
			return $posts;
		return false;
	}
}

