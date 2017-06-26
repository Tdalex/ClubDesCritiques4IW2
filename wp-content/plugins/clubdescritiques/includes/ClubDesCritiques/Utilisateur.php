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
	
	public static function redirect($url = false){
		if(!$url)
			$url = get_home_url();
        echo '<script type="text/javascript">window.location = "' . $url . '"</script>';
        return true;
    }
	
	public static function register($request){
		if(empty($request))
			return false;
		
		//generate random password
		
		$user =	get_user_by('email', $request['email']);
		
		if(!$user){
			$password  = wp_generate_password(8, false); 
			$user      = wp_create_user($request['email'], $password, $request['email']);
			$object    = 'Bienvenue à Club Des Critiques';
			$message   = 'Bienvenue à Club Des Critiques ' . $request['email'] . ', <br> Afin de valider votre compte, veuillez <a href="'. home_url() .'">vous connecter</a> avec ce mot de passe: <br><br> ' . $password . ' <br><br> il vous sera ensuite demandé de le modifier.<br><br><br> Cordialement, <br> Le club des critiques';
			$headers[] = 'From: '. NO_REPLY;
			wp_mail($request['email'], $object, $message, $headers);
		}else{
			return 'email deja utilise';
		}
		$_SESSION['login_msg'] = 'un email vous a ete envoye';
		return self::redirect($_SERVER['REQUEST_URI']);
	}
	
	public static function login($request){
		if(empty($request))
			return false;
		
		//get user by email
		$user =	get_user_by('email', $request['email']);
		if($user){
			$user = wp_authenticate($user->user_login, $request['password']);
			if ( $user->ID ){
				wp_set_current_user($user->ID);
				wp_set_auth_cookie($user->ID);
				do_action( 'wp_login', $user->user_login );
				if(!get_field('activated', 'user_'.$user->ID)){
					$activate = "
					<form action='' method='POST'>
						<input type='hidden' name='type' value='activate'></input>
						password:<input type='password' name='newPassword'></input><br>
						confirm password:<input type='password' name='newPasswordCheck'></input><br>
						<button type='submit'>modifier  mot de passe</button>
					</form>";
					return $activate;
				}else{
					$_SESSION['login_msg'] = 'bienvenue';
					return self::redirect($_SERVER['REQUEST_URI']);
				}
			}else{
				return 'email ou mot de passe non valide';
			}
		}else{
			return 'email ou mot de passe non valide';
		}
	}
	
	public static function activateAccount($request){
		$error = false;
		
		if(strlen($request['newPassword'])<6){
			echo 'Veuillez entrer un mot de passe plus long';
			$error = true;
		}
		
		if($request['newPassword'] != $request['newPasswordCheck']){
			echo 'Veuillez valider la vérification de mot de passe';
			$error = true;
		}
		
		if($error){
			$activate = "
			<form action='' method='POST'>
				<input type='hidden' name='type' value='activate'></input>
				password:<input type='password' name='newPassword'></input><br>
				confirm password:<input type='password' name='newPasswordCheck'></input><br>
				<button type='submit'>modifier  mot de passe</button>
			</form>";
			return $activate;
		}
		
		$user = wp_get_current_user();
		wp_set_password($request['newPassword'], $user->ID);
		update_field('activated', true, 'user_'.$user->ID);
		$object    = 'Activation du compte terminé';
		$message   = 'Bonjour,<br><br>Votre compte a bien été activé,<br> en esperant vous voir tres bientôt<br> Cordialement, <br><BR> Le club des critiques';
		$headers[] = 'From: '. NO_REPLY;
		wp_mail($user->user_email , $object, $message, $headers);	
		$_SESSION['login_msg'] = 'Votre compte a bien été activé'
		wp_set_current_user($user->ID);
		wp_set_auth_cookie($user->ID);
		do_action( 'wp_login', $user->user_login );

		return self::redirect($_SERVER['REQUEST_URI']);
			
	}
	
	public static function modifyUserInfo($request){
		$user_id = get_current_user_id();
		$request['userInfo']['ID'] = $user_id;
		
		//change password
		if(!empty($request['newPassword']) && $request['newPassword'] == $request['newPasswordCheck'])
			wp_set_password($newPassword, $user_id);
			
		return wp_update_user($request['userInfo']);
	}
	
	public static function postComment($idProduct, $content){
		$user_id	  = get_current_user_id();
		$productTitle = get_the_title($idProduct);
		$post   	  = array('post_author' => $user_id, 'post_type' => 'commentaire', 'post_title' => $productTitle.'_'.$user_id.'_');
		
		$idComment = wp_insert_post($post, $user_id);
		update_field('commented_product', $idComment, $idProduct);
		update_field('comment', $idComment, $content);
		return true;
	}
	
	public static function editComment($idComment, $content){
		update_field('comment', $idComment, $content);
		return true;
	}
	
	public static function deleteComment($idComment){
		wp_delete_post($idComment);
		return true;
	}
	
	public static function getNotation($idProduct, $user_id){
		$args = array(
			'posts_per_page'   => -1,
			'meta_value'       => $idProduct,
			'post_type'        => 'notation',
			'author'	  	   => $user_id,
			'post_status'      => 'publish'
		);
		$notation = get_posts($args);
		if(!empty($notation)){
			return get_field('note', $notation[0]->ID);
		}else{
			return 'aucune note donn�e';
		}
	}
	
	public static function getAverageNote($idProduct){
		$args = array(
			'posts_per_page'   => -1,
			'meta_value'       => $idProduct,
			'post_type'        => 'notation',
			'post_status'      => 'publish'
		);
		
		$nbNotation = 0;
		$totalNote  = 0;
		$notations  = get_posts($args);
		if(!empty($notations)){
			foreach($notations as $notation){
				$totalNote += get_field('note', $notation);
				$nbNotation++;
			}
		}
		return array('average' => $totalNote/$nbNotation, 'total' => $nbNotation);
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
		
		if(empty($existNote)){
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
