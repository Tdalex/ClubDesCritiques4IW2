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
		$user = wp_get_current_user();

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

		wp_set_password($request['newPassword'], $user->ID);
		//field 'activated'
		update_field('field_5938214cbbae4', array("true"), 'user_'.$user->ID);
		$object    = 'Activation du compte terminé';
		$message   = 'Bonjour,<br><br>Votre compte a bien été activé,<br> en esperant vous voir tres bientôt<br> Cordialement, <br><BR> Le club des critiques';
		$headers[] = 'From: '. NO_REPLY;
		wp_mail($user->user_email , $object, $message, $headers);
		$_SESSION['login_msg'] = 'Votre compte a bien été activé';
		wp_set_current_user($user->ID);
		wp_set_auth_cookie($user->ID);
		do_action( 'wp_login', $user->user_login );

		return self::redirect($_SERVER['REQUEST_URI']);

	}

	public static function modifyUserInfo($request){
		$user = wp_get_current_user();
		$request['ID'] = $user->ID;
		$lastEmail = $user->user_email;
		//change password
		if(!empty($request['password'])){
			if(strlen($request['password'])>=6){
				if($request['password'] == $request['passwordCheck']){
					wp_set_password($request['password'], $user_id);
					$object    = 'Modification de votre mot de passe';
					$message   = 'Bonjour,<br><br>Votre mot de passe vient d\'êtres modifié,<br> En cas de problème veuillez nous contacter au plus tôt, sinon veuillez ignorer ce message.<br> Cordialement, <br><BR> Le club des critiques';
					$headers[] = 'From: '. NO_REPLY;
					wp_mail($user->user_email , $object, $message, $headers);
				}else{
					echo 'Veuillez valider la vérification de mot de passe';
				}
			}else{
				echo 'Veuillez entrer un mot de passe plus long';
			}
		}
		// if(!empty($_FILES['photo']){
			// update_field('photo', $_FILES['photo'], 'user_'.$user->ID)
		// }
		unset($request['password']);
		unset($request['passwordCheck']);
		$request = array_filter($request);

		if(isset($request['email']) && $lastEmail != $request['user_email']){
			$object    = 'Modification de votre email';
			$message   = 'Bonjour,<br><br>Votre email vient d\'êtres modifié vers '. $request['user_email'] .', En cas de problème veuillez nous contacter au plus tôt,<br> sinon veuillez ignorer ce message.<br> Cordialement, <br><BR> Le club des critiques';
			$headers[] = 'From: '. NO_REPLY;
			wp_mail($lastEmail , $object, $message, $headers);
		}
		return wp_update_user($request);
	}

	public static function postComment($idProduct, $request){
		$user_id = get_current_user_id();
		$comment = self::getUserComment($idProduct);

		self::ChangeNotation($idProduct, $request['userNote']);

		if(is_object($comment)){
			$update = array('ID' => $comment->ID, 'post_content' => $request['comment']);
			wp_update_post($update);
		}else{
			$productTitle = get_the_title($idProduct);
			$post   	  = array('post_author' => $user_id, 'post_content' => $request['comment'], 'post_type' => 'commentaire','post_status' => 'publish', 'post_title' => $productTitle.'_'.$user_id);

			$idComment = wp_insert_post($post, $user_id);
			update_field('field_593a461c598a5', $idProduct, $idComment);
		}
		return self::redirect($_SERVER['REQUEST_URI']);
	}

	public static function getProductComments($idProduct){
		$args = array(
			'posts_per_page'   => -1,
			'meta_value'       => "a:1:{i:0;s:2:\"".$idProduct."\";}",
			'post_type'        => 'commentaire',
			'post_status'      => 'publish'
		);

		return get_posts($args);
	}

	public static function getUserComment($idProduct, $user_id = null){
		if($user_id === null)
			$user_id = get_current_user_id();

		$args = array(
			'posts_per_page'   => -1,
			'meta_value'       => "a:1:{i:0;s:2:\"".$idProduct."\";}",
			'post_type'        => 'commentaire',
			'post_status'      => 'publish',
			'post_author'	   => $user_id
		);
		$comment = get_posts($args);
		if(isset($comment[0])){
			return $comment[0];
		}
		return array();
	}

	public static function deleteComment($productId, $userId = null){
		if($userId === null)
			$userId = get_current_user_id();

		$comment = self::getUserComment($productId, $userId);
		if(is_object($comment))
			wp_delete_post($comment->ID);
		return self::redirect($_SERVER['REQUEST_URI']);
	}

	public static function getNotation($idProduct, $user_id){
		$args = array(
			'posts_per_page'   => -1,
			'meta_value'       => "a:1:{i:0;s:2:\"".$idProduct."\";}",
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
			'meta_value'       => "a:1:{i:0;s:2:\"".$idProduct."\";}",
			'post_type'        => 'notation',
			'post_status'      => 'publish'
		);

		$nbNotation = 0;
		$average    = 0;
		$totalNote  = 0;
		$notations  = get_posts($args);
		if(!empty($notations)){
			foreach($notations as $notation){
				$totalNote += get_field('note', $notation);
				$nbNotation++;
			}
		}
		if($nbNotation > 0)
			$average = $totalNote/$nbNotation;

		return array('average' => $average, 'total' => $nbNotation);
	}

	public static function ChangeNotation($idProduct, $note){
		$user_id	  = get_current_user_id();
		$productTitle = get_the_title($idProduct);

		$args = array(
			'posts_per_page'   => -1,
			'meta_value'       => "a:1:{i:0;s:2:\"".$idProduct."\";}",
			'post_type'        => 'notation',
			'author'	  	   => $user_id,
			'post_status'      => 'publish'
		);
		$existNote = get_posts($args);
		if(empty($existNote)){
			$post = array('post_author' => $user_id, 'post_type' => 'notation', 'post_title' => $productTitle.'_'.$user_id, 'post_status' => 'publish');

			$idNotation = wp_insert_post($post, $user_id);
			// product noted
			update_field('field_593a597075cb8', array($idProduct) , $idNotation);
		}else{
			$idNotation = $existNote[0];
		}
		// note
		update_field('field_593a5956855e4', $note, $idNotation);

		return true;
	}

	public static function isContact($idContact){
		$user_id = get_current_user_id();
		$myContact = get_field('contact', 'user_'.$user_id) ? get_field('contact', 'user_'.$user_id) : array();

		foreach($myContact as $key => $mc){
			if($idContact == $mc['ID']){
				return true;
			}
		}
		return false;
	}

	public static function ModifyContact($idContact, $type = 'add'){
		$user_id = get_current_user_id();
		$friendContact = get_field('contact', 'user_'.$idContact) ? get_field('contact', 'user_'.$idContact) : array();
		$myContact = get_field('contact', 'user_'.$user_id) ? get_field('contact', 'user_'.$user_id) : array();
		$already = false;
		if($type == 'add'){
			foreach($friendContact as $fc){
				$friendContactTemp[] = $fc['ID'];
				if($user_id == $fc['ID']){
					$already = true;
					break;
				}
			}
			if(!$already){
				$friendContactTemp[] = $user_id;
				update_field('field_5954b2cf2206c', $friendContactTemp , 'user_'.$idContact);
				$already = false;
			}
			foreach($myContact as $mc){
				$myContactTemp[] = $mc['ID'];
				if($idContact == $mc['ID']){
					$already = true;
				}
			}
			if(!$already){
				$myContactTemp[] = $idContact;
				update_field('field_5954b2cf2206c', $myContactTemp , 'user_'.$user_id);
			}
		}else{
			foreach($friendContact as $key => $fc){
				if($user_id != $fc['ID']){
					$friendContactTemp[] = $fc['ID'];
				}
			}
			update_field('field_5954b2cf2206c', $friendContactTemp , 'user_'.$idContact);
			foreach($myContact as $key => $mc){
				if($idContact != $mc['ID']){
					$myContactTemp[] = $fc['ID'];
				}
			}
			update_field('field_5954b2cf2206c', $myContactTemp , 'user_'.$user_id);
		}
		return self::redirect($_SERVER['REQUEST_URI']);
	}

	public static function getProductExchange($idProduct){
		$args = array(
			'posts_per_page'   => -1,
			'meta_value'       => "a:1:{i:0;s:2:\"".$idProduct."\";}",
			'post_type'        => 'echange',
			'post_status'      => 'publish'
		);
		$exchanges = array();
		foreach(get_posts($args) as $exchange){
			if(wp_get_post_terms($exchange->ID, 'exchange_type')[0]->slug == 'donner'){
				$exchanges['give'][] = $exchange;
			}else{
				$exchanges['take'][] = $exchange;
			}
		}
		return $exchanges;
	}

	public static function getUserExchange($idProduct, $user_id = null){
		if($user_id === null)
			$user_id = get_current_user_id();

		$args = array(
			'posts_per_page'   => -1,
			'meta_value'       => "a:1:{i:0;s:2:\"".$idProduct."\";}",
			'post_type'        => 'echange',
			'post_status'      => 'publish',
			'post_author'	   => $user_id
		);
		$exchange = get_posts($args);

		if(isset($exchange[0])){
			return $exchange[0];
		}
		return array();

	}

	public static function getAllUserExchange($user_id = null){
		if($user_id === null)
			$user_id = get_current_user_id();

		$args = array(
			'posts_per_page'   => -1,
			'post_type'        => 'echange',
			'post_status'      => 'publish',
			'post_author'	   => $user_id
		);

		foreach(get_posts($args) as $exchange){
			if(wp_get_post_terms($exchange->ID, 'exchange_type')[0]->slug == 'donner'){
				$exchanges['give'][] = $exchange;
			}else{
				$exchanges['take'][] = $exchange;
			}
		}
		return $exchanges;

	}

	public static function prepareExchange($idProduct, $type){
		$user_id  = get_current_user_id();
		$exchange = self::getUserExchange($idProduct);

		$term = get_term_by( 'slug', $type, 'exchange_type');
		if(is_object($term)){
			if(is_object($exchange)){
				wp_set_object_terms( $exchange->ID, $term->term_id, 'exchange_type');
			}else{
				$productTitle = get_the_title($idProduct);
				$post   	  = array('post_author' => $user_id, 'post_type' => 'echange','post_status' => 'publish', 'post_title' => $productTitle.'_'.$user_id);

				$idExchange = wp_insert_post($post, $user_id);
				update_field('field_59562915ed401', $idProduct, $idExchange);
				wp_set_object_terms( $idExchange, $term->term_id, 'exchange_type');
			}
		}
		return self::redirect($_SERVER['REQUEST_URI']);
	}

	public static function deleteExchange($productId, $userId = null){
		if($userId === null)
			$userId = get_current_user_id();

		$exchange = self::getUserExchange($productId);
		if(is_object($exchange))
			wp_delete_post($exchange->ID);

		return self::redirect($_SERVER['REQUEST_URI']);
	}
}
