<?php
/*
 * Template name: user profil
 */
 
use ClubDesCritiques\Utilisateur as Utilisateur;

// get user ID
$path       = array_filter(explode("/", $_SERVER['REQUEST_URI']));
$user_id = end($path);
$user    = get_user_by('ID', $user_id);
$exchanges = Utilisateur::getAllUserExchange($user_id);

//404 if not product
if(!is_object($user)){
    global $wp_query;
    $wp_query->set_404();
    status_header( 404 );
    get_template_part( 404 ); exit();
}

$userMeta = get_user_meta( $user->ID );

if(isset($_POST['type']) && $_POST['type'] == 'modifyContact'){
	if(	Utilisateur::isContact($user->ID)){
		Utilisateur::modifyContact($user->ID, 'remove');		
	}else{
		Utilisateur::modifyContact($user->ID, 'add');
	}	
}

$userMeta = get_user_meta( $user->ID );
get_header(); 
?>

<div class="container-fluid">
	<div class="row header_page">
		<div class="container">
			<?php if(isset($_SESSION['message'])){ ?>
				<div class="alert alert-<?php echo $_SESSION['message']['type'] ?>">
				  <?php echo $_SESSION['message']['text']; ?>
				</div>	
			<?php unset($_SESSION['message']);
				} ?>
			<div class="row content_profil">
				<div class="col-md-3 col-xs-12">
					<?php if(!get_field('photo', 'user_'.$user->ID)){ ?> 
						<img src="<?php echo get_parent_theme_file_uri( '/assets/images/avatar_defaut.png' ); ?>" 
						class=" img-responsive profil_img" alt="avatar" />
					<?php }else{ ?>
						<img src="<?php echo get_field('photo', 'user_'.$user->ID);?>" class="profil_img">
					<?php } ?>
				</div>
				<div class="col-md-6 col-xs-12 content_myProfil">
					<h2><?php echo strtoupper($user->user_lastname).' '.ucfirst(strtolower ($user->user_firstname));?></h2>
					<p><?php echo $user->user_email;?></p>
					<p><?php echo $userMeta['description'][0] ?></p>
				</div>
				<div class="row col-md-3 div_menu_right">
					<div class="menu_right">
						<?php if(is_user_logged_in() && $user->ID != wp_get_current_user()->ID){ ?>
							<div class="row">
								<form action="" method='POST'>
								<?php if(Utilisateur::isContact($user->ID)){ ?>
									<button type='submit' name='type' value='modifyContact'>Supprimer des contacts</button>	
								<?php }else{ ?>
									<button type='submit' name='type' value='modifyContact'>Ajouter Contact</button>
								<?php } ?>
								</form>	
							</div><br>
							<div class="row">
									<a href="#contact"><button>Contacter</button></a>
							</div><br>
						<?php } ?>
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
	</div>

	<?php if (isset($exchanges['give']) || isset($exchanges['take'])){ ?>
	<div class="container">
		<div class="bg_page_profil row">
			<h1>L'ESPACE TROC</h1>
			<p class="col-md-10 col-md-offset-1">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dolores officia aut vel facilis ducimus doloremque harum, dicta laborum repellat exercitationem id adipisci culpa, commodi quisquam quis nobis fuga libero eligendi!
			</p>
		</div>
		<div class="row exchange">
			<?php if(isset($exchanges['take'])){ ?>
				<h2><?php echo ucfirst(strtolower ($user->user_firstname));?> souhaite recevoir ce(s) livre(s) :</h2>
				<div class="row">
					<?php foreach($exchanges['take'] as $exchange){ ?>
						<?php $exchangeProduct = get_field('product', $exchange->ID)[0]; ?>
						<div class="col-md-3 col-xs-6 suggestions">
							<a href='<?php echo get_permalink(get_page_by_title('utilisateur')).$exchangeProduct->ID; ?>'>
								<div class="suggestions-img">
									<?php if(!get_field('image', $exchangeProduct->ID)){ ?> 
										<img class="img-responsive" src="<?php echo get_parent_theme_file_uri( '/assets/images/book_defaut.png' ); ?>"> 
									<?php }else{ ?>
										<img class="img-responsive" src="<?php echo get_field('image', $exchangeProduct->ID)	; ?>"></img>
									<?php } ?>
								</div>
								<h3><?php echo $exchangeProduct->post_title; ?></h3>
								<p><?php echo get_field('author',$exchangeProduct->ID)[0]->post_title; ?></p>
							</a>
						</div>
					<?php } ?>
				</div>
			<?php }?>
			<?php if (isset($exchanges['give'])){ ?>
				<h2><?php echo ucfirst(strtolower ($user->user_firstname));?> possède ce(s) livre(s) :</h2>
				<div class="row">
					<?php foreach($exchanges['give'] as $exchange){ ?>
						<?php $exchangeProduct = get_field('product', $exchange->ID)[0]; ?>
						<div class="col-md-3 col-xs-6 suggestions">
							<a href='<?php echo get_permalink(get_page_by_title('utilisateur')).$exchangeProduct->ID; ?>'>	
								<div class="suggestions-img">
									<?php if(!get_field('image', $exchangeProduct->ID)){ ?> 
										<img class="img-responsive" src="<?php echo get_parent_theme_file_uri( '/assets/images/book_defaut.png' ); ?>"> 
									<?php }else{ ?>
										<img class="img-responsive" src="<?php echo get_field('image', $exchangeProduct->ID); ?>"></img>
									<?php } ?>
								</div>
								<h3><?php echo $exchangeProduct->post_title; ?></h3>
								<p><?php echo get_field('author',$exchangeProduct->ID)[0]->post_title; ?></p>
							</a>
						</div>
					<?php } ?>
				</div>
			<?php }?>
		</div>
	</div>
	<?php } ?>

		<div class="container contact-user" id="contact">
			<div class="contact_profil">
				<h2>Contactez <?php echo ucfirst(strtolower ($user->user_lastname)).' '.ucfirst(strtolower ($user->user_firstname));?></h2>
			</div>

			<div class="form-contact col-md-6 col-md-offset-3">

				<div class="row form-group form_contact">
					<div class="">
						<label for="name">Nom :</label>
					</div>
					<div class="">
						<input type="text" name="name" id="name" class="form-control" />
					</div>
				</div>

				<div class="row form-group form_contact">
					<div class="">
						<label for="email">Email :</label>
					</div>
					<div class="">
						<input type="email" name="email" id="email" class="form-control" />
					</div>
				</div>

				<div class="row form-group form_contact">
					<div class="">
						<label for="subject">Sujet :</label>
					</div>
					<div class="">
						<input type="text" name="subject" id="subject" class="form-control" />
					</div>
				</div>
				<div class="row form-group form_contact">
					<div class="">
						<label for="message">Message :</label>
					</div>
					<div class="">
						<textarea class="form-control" rows="10" id="message" name="message"></textarea>
					</div>
				</div>

				<div class="row">
					<div class="input-group-lg">
						<button type="submit" class="btn btn_submit submit_contact">Envoyer</button>
					</div>
				</div>
			</div>
		</div>
			
		
</div>
	

<?php
get_footer(); 
?>