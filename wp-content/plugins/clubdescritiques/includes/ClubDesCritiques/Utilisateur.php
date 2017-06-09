<?php
/**
 * Club Des Critiques Extension
 *
 * @package clubdescritiques
 */

namespace ClubDesCritiques;


class Utilisateur{
    public static function activate()
    {
        global $wpdb;

        
    }
	
	public static function register($request){
		if(empty($request))
			return false;
		
		//generate random password
		$password = wp_generate_password(8, false); 
		$user = wp_create_user($request['email'], $password, $request['email']);
		
		$object  = 'Bienvenue à Club Des Critiques';
		$message = 'Bienvenue à Club Des Critiques ' . $request['email'] . ', <br> Afin de valider votre compte, veuillez vous connecter avec ce mot de passe: <br><br> ' . $password . ' <br><br> il vous sera ensuite demandé de le modifier.<br><br><br> Cordialement.';
		$headers[] = 'From: no_reply';
		
		return wp_mail($request['email'], $object, $message, $headers);
	}
	
	public static function activateAccount($newPassword){
		if(count($newPassword)<6)
			return 'Veuillez entrer un mot de passe plus long';
		
		if($request['newPassword'] != $request['newPasswordCheck'])
			return 'Veuillez valider la vérification de mot de passe';
			
		$user_id = get_current_user_id();
		wp_set_password($newPassword, $user_id);
		update_field('activated', true, 'user_'.$user_id);
		return true;
	}
	
	public static function modifyUserInfo($request){
		$user_id = get_current_user_id();
		$request['userInfo']['ID'] = $user_id;
		
		//change password
		if(!empty($request['newPassword']) && $request['newPassword'] == $request['newPasswordCheck'])
			wp_set_password($newPassword, $user_id);
			
		return wp_update_user($request['userInfo']);
	}
	
	public static function postCommentary($idProduct, $content){
		$user_id	  = get_current_user_id();
		$productTitle = get_the_title($idProduct);
		$post   	  = array('post_author' => $user_id, 'post_type' => 'commentaire', 'post_title' => $productTitle.'_'.$user_id.'_');
		
		$idCommentary = wp_insert_post($post, $user_id);
		update_field('commented_product', $idProduct, $idCommentary);
		update_field('commentary', $idProduct, $content);
		return true;
	}
	
	public static function ChangeNotation($idProduct, $note){
		$user_id	  = get_current_user_id();
		$productTitle = get_the_title($idProduct);
		
		$args = array(
			'posts_per_page'   => -1,
			'meta_value'       => $idProduct,
			'post_type'        => 'notation',
			'author'	  	   => $user_id,
			'post_status'      => 'publish'
		);
		$existNote 	  = get_posts($args);
		if(empty($existNote){
			$post   	  = array('post_author' => $user_id, 'post_type' => 'notation', 'post_title' => $productTitle.'_'.$user_id);
			
			$idNotation = wp_insert_post($post, $user_id);
			update_field('product_noted', $idNotation, $idProduct);
		}else{
			$idNotation = $existNote[0];
		}
		update_field('note', $idNotation, $note);
		return true;
	}
}
