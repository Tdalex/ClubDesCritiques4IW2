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
			return self::redirect(strtok($_SERVER["REQUEST_URI"],'?'));

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
			$_SESSION['message'] = array('type' => 'danger', 'text' => 'email deja utilise');
			return self::redirect(strtok($_SERVER["REQUEST_URI"],'?'));
		}
		$_SESSION['message'] = array('type' => 'success', 'text' => 'un email comportant vos identifiants vous a ete envoyé');
		return self::redirect(strtok($_SERVER["REQUEST_URI"],'?'));
	}

	public static function login($request){
		if(empty($request))
			return self::redirect(strtok($_SERVER["REQUEST_URI"],'?'));

		//get user by email
		$user =	get_user_by('email', $request['email']);
		if($user){
			$user = wp_authenticate($user->user_login, $request['password']);
			if ( $user->ID ){
				wp_set_current_user($user->ID);
				wp_set_auth_cookie($user->ID);
				do_action( 'wp_login', $user->user_login );

				if(!get_field('activated', 'user_'.$user->ID)){
					$activate = '
					<div class="modal-backdrop in"></div>
					<div class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="channelModal" style="display:block;">
						<div class="modal-dialog modal-lg" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<h4 class="modal-title" id="channelModal">Bienvenue sur Club des Critiques, <br>Afin de bénécifier d\'une experience optimale, veuillez remplir les champs ci-dessous.</h4>
								</div>
								<div class="modal-body modal-password">
								   <form action="" method="POST">
											<input type="hidden" name="type" value="activate"></input>
											Prénom:<input required="required" type="text" name="firstname"></input><br>
											Nom de famille:<input required="required" type="text" name="lastname"></input><br>
											Mot de passe:<input type="password" name="newPassword"></input><br>
											Confirmation de mot de passe:<input type="password" name="newPasswordCheck"></input><br>
											<button type="submit">modifier  mot de passe</button>
										</form>
								</div>
							</div>
						</div>
					</div>';
					
					$_SESSION['activate'] = $activate;
					return self::redirect(strtok($_SERVER["REQUEST_URI"],'?'));
				}else{
					return self::redirect(strtok($_SERVER["REQUEST_URI"],'?'));
				}
			}else{
				$_SESSION['message'] = array('type' => 'danger', 'text' => 'email ou mot de passe non valide');
				return self::redirect(strtok($_SERVER["REQUEST_URI"],'?'));
			}
		}else{
			$_SESSION['message'] = array('type' => 'danger', 'text' => 'email ou mot de passe non valide');
			return self::redirect(strtok($_SERVER["REQUEST_URI"],'?'));
		}
	}

	public static function activateAccount($request){
		$error = false;
		$user = wp_get_current_user();

		if(strlen($request['newPassword'])<6){
			$_SESSION['message'] = array('type' => 'danger', 'text' => 'Veuillez entrer un mot de passe plus long');
			$error = true;
		}

		if($request['newPassword'] != $request['newPasswordCheck']){
			$_SESSION['message'] = array('type' => 'danger', 'text' => 'Veuillez valider la vérification de mot de passe');
			$error = true;
		}

		if($error){
			return self::redirect(strtok($_SERVER["REQUEST_URI"],'?'));
		}
		unset($_SESSION['activate']);
		wp_update_user(array('ID' => $user->ID, 'first_name' => $request['firstname'], 'last_name' => $request['lastname']));
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

		return self::redirect(strtok($_SERVER["REQUEST_URI"],'?'));

	}

	public static function modifyUserInfo($request, $file = array()){
		$user = wp_get_current_user();
		$request['ID'] = $user->ID;
		$lastEmail = $user->user_email;
		
		//change photo
		if(!empty($file) && isset($file['photo'])){
			$photo = $file['photo'];
			
			if ( ! function_exists( 'wp_handle_upload' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
			}

			$upload_overrides = array( 'test_form' => false );
			$movefile = wp_handle_upload( $photo, $upload_overrides );

			if ( $movefile && ! isset( $movefile['error'] ) ) {
				$filename = $movefile['file'];

				$wp_upload_dir = wp_upload_dir();
				$attachment = array(
					'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ), 
					'post_mime_type' => $movefile['type'],
					'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
					'post_content'   => '',
					'post_status'    => 'inherit'
				);

				$attach_id = wp_insert_attachment( $attachment, $filename);

				require_once( ABSPATH . 'wp-admin/includes/image.php' );

				$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
				wp_update_attachment_metadata( $attach_id, $attach_data );
;
				update_field('field_5954b1a910314', $attach_id, 'user_'.$user->ID);
			} else {
				$_SESSION['message'] = array('type' => 'danger', 'text' => "Une erreur est survenue pendant le téléchargement de l'image");
			}
		}
		
		
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
					$_SESSION['message'] = array('type' => 'danger', 'text' => 'Veuillez valider la vérification de mot de passe');
				}
			}else{
				$_SESSION['message'] = array('type' => 'danger', 'text' => 'Veuillez entrer un mot de passe plus long');
			}
		}
		unset($request['password']);
		unset($request['photo']);
		unset($request['passwordCheck']);
		$request = array_filter($request);

		if(isset($request['email']) && $lastEmail != $request['user_email']){
			$object    = 'Modification de votre email';
			$message   = 'Bonjour,<br><br>Votre email vient d\'êtres modifié vers '. $request['user_email'] .', En cas de problème veuillez nous contacter au plus tôt,<br> sinon veuillez ignorer ce message.<br> Cordialement, <br><BR> Le club des critiques';
			$headers[] = 'From: '. NO_REPLY;
			wp_mail($lastEmail , $object, $message, $headers);
		}
		wp_update_user($request);
		return self::redirect(strtok($_SERVER["REQUEST_URI"],'?'));
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
		return self::redirect(strtok($_SERVER["REQUEST_URI"],'?'));
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
		$comment = array();
		
		$args = array(
			'posts_per_page'   => -1,
			'meta_value'       => "a:1:{i:0;s:2:\"".$idProduct."\";}",
			'post_type'        => 'commentaire',
			'post_status'      => 'publish',
		);
		
		foreach( get_posts($args) as $c){
			if($c->post_author == $user_id){
				$comment = $c;
				break;
			}
		}
		
		if(!empty($comment)){
			return $comment;
		}
		return array();
	}

	public static function deleteComment($productId, $userId = null){
		if($userId === null)
			$userId = get_current_user_id();

		$comment = self::getUserComment($productId, $userId);
		if(is_object($comment)){
			wp_delete_post($comment->ID);
			$_SESSION['message'] = array('type' => 'success', 'text' => 'Votre commentaire a bien été supprimé');
		}else{
			$_SESSION['message'] = array('type' => 'danger', 'text' => 'une erreur est survenue');
		}
		
		return self::redirect(strtok($_SERVER["REQUEST_URI"],'?'));
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
			return false;
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
		$contactUser = get_user_by('id',$idContact);
		$contactName = strtoupper($contactUser->user_lastname)." ".ucfirst(strtolower ($contactUser->user_firstname));
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
			$_SESSION['message'] = array('type' => 'success', 'text' => $contactName.' a bien été ajouté à votre liste des contacts');
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
			$_SESSION['message'] = array('type' => 'success', 'text' => $contactName.' a bien été supprimé à votre liste des contacts');
		}
		return self::redirect(strtok($_SERVER["REQUEST_URI"],'?'));
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
		$_SESSION['message'] = array('type' => 'success', 'text' => "L'oeuvre a été ajouté à votre liste d'echange");
		return self::redirect(strtok($_SERVER["REQUEST_URI"],'?'));
	}

	public static function deleteExchange($productId, $userId = null){
		if($userId === null)
			$userId = get_current_user_id();

		$exchange = self::getUserExchange($productId);
		if(is_object($exchange))
			wp_delete_post($exchange->ID);
		
		$_SESSION['message'] = array('type' => 'success', 'text' => "L'oeuvre a été enlevé de votre liste d'echange");
		return self::redirect(strtok($_SERVER["REQUEST_URI"],'?'));
	}
	
	public static function searchUser($search){
		$search = explode(' ', $search);
		$users = array();
		foreach($search as $search_string){	
			$args  =  array ( 
				'meta_query' => array(
					'relation' => 'OR',
					array(
						'key'     => 'first_name',
						'value'   => $search_string,
						'compare' => 'LIKE'
					),
					array(
						'key'     => 'last_name',
						'value'   => $search_string,
						'compare' => 'LIKE'
					)
				)
			);
				
			$wp_user_query = new \WP_User_Query($args);
			$wp_user_query->query();

			$results = $wp_user_query->get_results();
			foreach($results as $r){
				if(!in_array($r->data->ID, $users)){
					$users[] = $r->data->ID;
				}
			}
		}
		return $users;
	}
}
