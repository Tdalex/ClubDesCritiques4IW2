<?php
/*
 * Template name: user profil
 */
 
use ClubDesCritiques\Utilisateur as Utilisateur;

// get user ID
$path       = array_filter(explode("/", $_SERVER['REQUEST_URI']));
$user_id = end($path);
$user    = get_user_by('ID', $user_id);

//404 if not product
if(!is_object($user)){
    global $wp_query;
    $wp_query->set_404();
    status_header( 404 );
    get_template_part( 404 ); exit();
}

$userMeta = get_user_meta( $user->ID );

if(isset($_POST['type']) && $_POST['type'] == 'modifyUser'){
	Utilisateur::modifyUserInfo($_POST);
}elseif(isset($_POST['type']) && $_POST['type'] == 'modifyContact'){
	if(	Utilisateur::isContact($user->ID)){
		Utilisateur::modifyContact($user->ID, 'remove');		
	}else{
		Utilisateur::modifyContact($user->ID, 'add');
	}	
}

$userMeta = get_user_meta( $user->ID );
get_header(); 
?>


<!-- <form action="" method="POST">
	<input type='hidden' name='type' value='modifyUser'><input>
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
			<img src="<?php echo get_field('photo', 'user_'.$user->ID); ?>">
		</div>
		<div class="col-md-6 col-xs-12">
			<h2><?php echo strtoupper($user->user_lastname).' '.ucfirst(strtolower ($user->user_firstname));?></h2>
			<p><?php echo $user->user_email;?></p>
			<p><?php echo $userMeta['description'][0] ?></p>
		</div>
		<div class="row col-md-3 div_menu_right">
			<div class="menu_right">
				<?php if(is_user_logged_in() && $user->ID == wp_get_current_user()->ID){ ?>
				<div class="row">
					<div class="menu_flottant">
						<ul>
							<a href="#"><li>Modifier mes informations</li></a>
							<a href="#"><li>Modifier mon mot de passe</li></a>
							<a href="#"><li>Gérer ses contacts</li></a>
						</ul>
					</div>
				</div>
				<?php } ?>
				<?php if(is_user_logged_in() && $user->ID != wp_get_current_user()->ID){ ?>
					<div class="row">
						<form action="" method='POST'>
						<?php if(Utilisateur::isContact($user->ID)){ ?>
							<button type='submit' name='type' value='modifyContact'>Enlever Contact</button>	
						<?php }else{ ?>
							<button type='submit' name='type' value='modifyContact'>Ajouter Contact</button>
						<?php } ?>
						</form>	
					</div><br>
					<div class="row">
							<a href="#contact"><button>Contacter</button></a>
					</div><br>
				<?php } ?>
				<div class="row">
					<h2>Liste contact</h2>
					<div class="menu_flottant">
					<?php foreach(get_field('contact', 'user_'.$user->ID) as $contact){ ?>
						<ul>
							<a href='<?php echo get_permalink(get_page_by_title('utilisateur')).$contact['ID']; ?>'><li><?php echo $contact['user_firstname']. ' ' .$contact['user_lastname'];;?></li></a>
						</ul>
					<?php } ?>
					</div>
				</div>
				</div>
		</div>
	</div>	
<br>
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