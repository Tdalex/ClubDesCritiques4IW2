<?php
/*
 * Template name: my profil
 */
 
use ClubDesCritiques\Utilisateur as Utilisateur;

if(!is_user_logged_in()){
	Utilisateur::redirect('/');
}

$user = wp_get_current_user();

if(isset($_POST)){
	Utilisateur::modifyUserInfo($_POST);
}

$userMeta = get_user_meta( $user->ID );

get_header(); 
?>


<!-- <form action="" method="POST">
	Email: <input type='text' name='user_email' value='<?php echo $user->user_email;?>'></input><br>
	Mot de passe: <input type='password' name='password'></input><br>
	Vérification mot de passe: <input type='password' name='passwordCheck'></input><br>
	<br>
	Prénom: <input type='text' name='first_name' value='<?php echo $user->user_firstname;?>'></input><br>
	Nom de famille: <input type='text' name='last_name' value='<?php echo $user->user_lastname ;?>'></input><br>
	Description: <textarea name='description' ><?php echo $userMeta['description'][0] ?></textarea><br>
	<br>
	<button type='submit'>Modifier mes infos</button>
</form> -->




<div class="container">
	<div class="row title_profil">
		<h1>Profil Utilisateur</h1>
	</div>
	<div class="row content_profil">
		<div class="col-md-3 col-xs-12">
			<img src="http://lorempixel.com/400/400/">
		</div>
		<div class="col-md-6 col-xs-12">
			<h2><?php echo strtoupper($user->user_lastname).' '.ucfirst(strtolower ($user->user_firstname));?></h2>
			<p><?php echo $user->user_email;?></p>
			<p>Curabitur vehicula scelerisque tincidunt. Donec hendrerit venenatis leo, a congue dolor pellentesque at. Proin at orci quis velit feugiat accumsan pellentesque quis lectus. Fusce sollicitudin molestie arcu, non eleifend mauris rhoncus ac. Ut velit justo, ultrices vitae commodo sed, pellentesque nec libero. Nunc in aliquet justo, et tempus neque. Morbi nec orci magna. Donec convallis, lorem ut viverra convallis, diam ipsum pretium massa, eu malesuada libero tortor eget felis. Maecenas erat sem, iaculis sed consequat sed, pretium eu erat. Vivamus volutpat malesuada ex, convallis pellentesque magna rhoncus nec. Nulla pulvinar, lorem a hendrerit tristique, ex velit bibendum neque, sit amet sollicitudin odio felis vitae nisl. Sed placerat elit orci, id vulputate ante mattis sed. Vestibulum eget mollis dolor. Pellentesque tellus urna, lobortis in iaculis nec, aliquet vitae enim. Aenean condimentum fermentum semper.</p>
		</div>
		<div class="row col-md-3 div_menu_right">
			<div class="menu_right">
			<div class="row">
				<div class="menu_flottant">
					<ul>
						<a href="#"><li>Modifier mes informations</li></a>
						<a href="#"><li>Modifier mon mot de passe</li></a>
						<a href="#"><li>Gérer ses contacts</li></a>
					</ul>
				</div>
			</div>
			<div class="row">
					<a href="#contact"><button>Contacter</button></a>
			</div>
			</div>
		</div>
	</div>	

	<div class="row exchange">
		<h2>Pour échanger</h2>
	</div>


	<div class="row">

		<div class="col-md-2 col-xs-6 ">
			<a href=#>
				<img class="img-responsive" src="http://lorempixel.com/400/400/" />
				<h3>Théorie de l'information et du codage</h3>
				<p>Olivier Rioul</p>
			</a>
		</div>
		<div class="col-md-2 col-md-offset-1 col-xs-6">
			<a href=#>
				<img class="img-responsive" src="http://lorempixel.com/400/400/" />
				<h3>Théorie de l'information et du codage</h3>
				<p>Olivier Rioul</p>
			</a>
		</div>
		<div class=" col-md-2 col-md-offset-1 col-xs-6">
			<a href=#>
				<img class="img-responsive" src="http://lorempixel.com/400/400/" />
				<h3>Théorie de l'information et du codage</h3>
				<p>Olivier Rioul</p>
			</a>
		</div>
	</div>

	<div class="row separator">
		<div class="col-md-9">
			<hr />
		</div>
	</div>

	<div class="row">
		<div class="contact_profil" id="contact">
			<h2>Contactez <?php echo ucfirst(strtolower ($user->user_lastname)).' '.ucfirst(strtolower ($user->user_firstname));?></h2>
		</div>

		<div class="row form-group form_contact">
			<div class="col-md-3 col-xs-12">
				<label for="name">Nom :</label>
			</div>
			<div class="col-md-3 col-md-offset-1 col-xs-12">
				<input type="text" name="name" id="name" class="form-control" />
			</div>
		</div>

		<div class="row form-group form_contact">
			<div class="col-md-3 col-xs-12">
				<label for="email">Email :</label>
			</div>
			<div class="col-md-3 col-md-offset-1 col-xs-12">
				<input type="email" name="email" id="email" class="form-control" />
			</div>
		</div>

		<div class="row form-group form_contact">
			<div class="col-md-3 col-xs-12">
				<label for="subject">Sujet :</label>
			</div>
			<div class="col-md-3 col-md-offset-1 col-xs-12">
				<input type="text" name="subject" id="subject" class="form-control" />
			</div>
		</div>
		<div class="row form-group form_contact">
			<div class="col-md-3 col-xs-12">
				<label for="message">Message :</label>
			</div>
			<div class="col-md-3 col-md-offset-1 col-xs-12">
				<textarea class="form-control" rows="10" id="message" name="message"></textarea>
			</div>
		</div>

		<div class="row">
			<div class="col-md-offset-2 input-group-lg">
				<input type="submit" class="btn btn-primary col-md-6 col-xs-12 btn_submit submit_contact" value="Envoyer" />
			</div>
		</div>
		
	</div>





</div>

<?php
get_footer(); 
?>