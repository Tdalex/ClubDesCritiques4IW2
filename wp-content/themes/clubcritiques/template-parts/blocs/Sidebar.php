<?php
/*
 * Template name: sidebar
 */

?>

<?php if(!is_user_logged_in()){ ?>
	<form action="" method="POST" class="login">
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
	<form action="" method="POST" class="logout">
		<input type='hidden' name='type' value='logout'></input>
		<button type='submit'>se deconnecter</button>
	</form>	
<?php } ?>
