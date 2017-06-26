<?php
/*
 * Template name: sidebar
 */
 
use ClubDesCritiques\Utilisateur as Utilisateur;

if(isset($_POST) && $_POST['type'] == 'register'){
	echo Utilisateur::register($_POST);
}elseif(isset($_POST) && $_POST['type'] == 'login'){
	echo Utilisateur::login($_POST);
}elseif(isset($_POST) && $_POST['type'] == 'logout'){
	wp_logout();
	Utilisateur::redirect($_SERVER['REQUEST_URI']);
}elseif(isset($_POST) && $_POST['type'] == 'activate'){
	echo Utilisateur::activateAccount($_POST);
}
?>

<?php if(!is_user_logged_in()){ ?>
	<form action="" method="POST">
		<input type='hidden' name='type' value='login'></input>
		email:<input type='text' name='email'></input><br>
		password:<input type='password' name='password'></input><br>
		<button type='submit'>se connecter</button>
	</form>

	<form action="" method="POST">
		<input type='hidden' name='type' value='register'></input>
		email:<input type='text' name='email'></input><br>
		<button type='submit'>s'inscrire</button>
	</form>
<?php }else{ ?>
	<form action="" method="POST">
		<input type='hidden' name='type' value='logout'></input>
		<button type='submit'>se deconnecter</button>
	</form>	
<?php } ?>
