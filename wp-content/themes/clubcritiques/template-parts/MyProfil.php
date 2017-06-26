<?php
/*
 * Template name: my profil
 */
 
use ClubDesCritiques\Utilisateur as Utilisateur;

$user = wp_get_current_user();

?>


<form action="" method="POST">
	Email: <input type='text' name='email' value='<?php echo $user->user_email;?>'></input><br>
	Mot de passe: <input type='password' name='password'></input><br>
	Vérification mot de passe: <input type='password' name='newPasswordCheck'></input><br>
	<br>
	Prénom: <input type='text' name='firstname' value='<?php echo $user->firstname;?>'></input><br>
	Prénom: <input type='text' name='lastname' value='<?php echo $user->lastname;?>'></input><br>
	
	<button type='submit'>Modifier mes infos</button>
</form>