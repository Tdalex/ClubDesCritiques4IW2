<?php
/*
 * Template name: sidebar
 */

?>

<?php if(!is_user_logged_in()){ ?>
	<form action="" method="POST" class="login">
		<input type='hidden' name='type' value='login'></input>
		Email:<input required='required' type='text' name='email'></input><br>
		Mot de passe:<input required='required' type='password' name='password'></input><br>
		<button type='submit'>Se connecter</button>
	</form>

	<form action="" method="POST">
		<input type='hidden' name='type' value='register'></input>
		Email:<input required='required' type='text' name='email'></input><br>
		<button type='submit'>S'inscrire</button>
	</form>
<?php }elseif($_SESSION['activate']){ 
	echo $_SESSION['activate'];
}else{ ?>	
	<form action="" method="POST" class="logout">
		<input type='hidden' name='type' value='logout'></input>
		<button type='submit'>Se deconnecter</button>
	</form>	
<?php } ?>
