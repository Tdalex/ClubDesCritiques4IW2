<?php
/*
 * Template name: my profil
 */
 
use ClubDesCritiques\Utilisateur as Utilisateur;

$user = wp_get_current_user();

if(isset($_POST)){
	Utilisateur::modifyUserInfo($_POST);
}
?>


<form action="" method="POST">
	Email: <input type='text' name='user_email' value='<?php echo $user->user_email;?>'></input><br>
	Mot de passe: <input type='password' name='password'></input><br>
	Vérification mot de passe: <input type='password' name='passwordCheck'></input><br>
	<br>
	Prénom: <input type='text' name='first_name' value='<?php echo $user->user_firstname;?>'></input><br>
	Nom de famille: <input type='text' name='last_name' value='<?php echo $user->user_lastname ;?>'></input><br>
	<br>
	<button type='submit'>Modifier mes infos</button>
</form>