<?php
/*
 * Template name: edit profil
 */
 
use ClubDesCritiques\Utilisateur as Utilisateur;

if(!is_user_logged_in()){
	Utilisateur::redirect('/');
}

$user = wp_get_current_user();

if(isset($_POST['type']) && $_POST['type'] == 'modifyUser'){
	Utilisateur::modifyUserInfo($_POST, $_FILES);
}

$userMeta = get_user_meta( $user->ID );
get_header(); 
?>

<div class="container">
	<div class="row title_profil">
		<h1><?php echo get_the_title();  ?></h1>
	</div>
	<?php if(isset($_SESSION['message'])){ ?>
		<div class="alert alert-<?php echo $_SESSION['message']['type'] ?>">
		  <?php echo $_SESSION['message']['text']; ?>
		</div>	
	<?php unset($_SESSION['message']);
		} ?>
	<div class="row content_profil">
		<form action="" method="POST" enctype="multipart/form-data">
			<div class="col-md-3 col-xs-12">
				<?php if(!get_field('photo', 'user_'.$user->ID)){ ?> 
					<img src="<?php echo get_parent_theme_file_uri( '/assets/images/avatar_defaut.png' ); ?>" 
					class="avatar_sp img-responsive" alt="avatar" />
				<?php }else{ ?>
					<img src="<?php echo get_field('photo', 'user_'.$user->ID);?>">
				<?php } ?><br>
				Changer de photo:<input type="file" name="photo" accept="image/*"></input>
			</div>
			<div class="col-md-6 col-xs-12">
					<input type='hidden' name='type' value='modifyUser'></input>
					Email: <input type='text' name='user_email' value='<?php echo $user->user_email;?>'></input><br>
					Mot de passe: <input type='password' name='password'></input><br>
					Vérification mot de passe: <input type='password' name='passwordCheck'></input><br>
					<br>
					Prénom: <input type='text' name='first_name' value='<?php echo $user->user_firstname;?>'></input><br>
					Nom de famille: <input type='text' name='last_name' value='<?php echo $user->user_lastname ;?>'></input><br>
					Description: <textarea name='description' ><?php echo $userMeta['description'][0] ?></textarea><br>
					<br>
					<button type='submit'>Modifier mes infos</button>
			</div>
		</form>
		<div class="row col-md-3 div_menu_right">
			<div class="menu_right">
				<?php if(get_field('contact', 'user_'.$user->ID)){ ?>
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
				<?php } ?>
			</div>
		</div>
	</div>	
</div>

<?php
get_footer(); 
?>