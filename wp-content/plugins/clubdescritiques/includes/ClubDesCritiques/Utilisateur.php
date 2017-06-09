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
	
	public function register($request){
		if(empty($request))
			return false;
		
		//generate random password
		$password = wp_generate_password(8, false); 
		$user = wp_create_user($request['email'], $password, $request['email']);
		
		$object  = 'Bienvenue � Club Des Critiques';
		$message = 'Bienvenue � Club Des Critiques ' . $request['email'] . ', <br> Afin de valider votre compte, veuillez vous connecter avec ce mot de passe: <br><br> ' . $password . ' <br><br> il vous sera ensuite demand� de le modifier.<br><br><br> Cordialement.';
		$headers[] = 'From: no_reply';
		
		return wp_mail($request['email'], $object, $message, $headers);
	}
	
	public function activateAccount($newPassword){
		if(count($newPassword)<6)
			return 'Veuillez entrer un mot de passe plus long';
		
		if($request['newPassword'] != $request['newPasswordCheck'])
			return 'Veuillez valider la v�rification de mot de passe';
			
		$user_id = get_current_user_id();
		wp_set_password($newPassword, $user_id);
		update_field('activated', true, 'user_'.$user_id);
		return true;
	}
	
	public function modifyUserInfo($request){
		$user_id = get_current_user_id();
		$request['userInfo']['ID'] = $user_id;
		
		//change password
		if(!empty($request['newPassword']) && $request['newPassword'] == $request['newPasswordCheck'])
			wp_set_password($newPassword, $user_id);
			
		return wp_update_user($request['userInfo']);
	}
}
