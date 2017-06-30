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

<div class="container">
	<div class="row title_profil">
		<h1><?php echo get_the_title();  ?></h1>
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

<?php if (isset($exchanges['give']) || isset($exchanges['take'])){ ?>
	<div class="row separator">
		<div class="col-md-9">
			<hr />
		</div>
	</div>
	<div class="row exchange">
		<h2>Pour échanger</h2>
		<div class="row">
		<?php if(isset($exchanges['take'])){ ?>
			<h3>Recevoir</h3>
			<div class="row">
				<?php foreach($exchanges['take'] as $exchange){ ?>
					<?php $exchangeProduct = get_field('product', $exchange->ID)[0]; ?>
					<div class="col-md-2 col-xs-6 ">
							<a href='<?php echo get_permalink(get_page_by_title('utilisateur')).$exchangeProduct->ID; ?>'></a>
							<?php if(!get_field('image', $exchangeProduct->ID)){ ?> 
								<img class="img-responsive" src="https://pictures.abebooks.com/isbn/9782070543588-fr.jpg"> 
							<?php }else{ ?>
								<img class="img-responsive" src="<?php echo get_field('image', $exchangeProduct->ID)	; ?>"></img>
							<?php } ?>
							<h3><?php echo $exchangeProduct->post_title; ?></h3>
							<p><?php echo get_field('author',$exchangeProduct->ID)[0]->post_title; ?></p>
						</a>
					</div>
				<?php } ?>
			</div>
		<?php }?>
		<?php if (isset($exchanges['give'])){ ?>
			<h3>Donner</h3>
			<div class="row">
				<?php foreach($exchanges['give'] as $exchange){ ?>
					<?php $exchangeProduct = get_field('product', $exchange->ID)[0]; ?>
					<div class="col-md-2 col-xs-6 ">
							<a href='<?php echo get_permalink(get_page_by_title('utilisateur')).$exchangeProduct->ID; ?>'></a>
							<?php if(!get_field('image', $exchangeProduct->ID)){ ?> 
								<img class="img-responsive" src="https://pictures.abebooks.com/isbn/9782070543588-fr.jpg"> 
							<?php }else{ ?>
								<img class="img-responsive" src="<?php echo get_field('image', $exchangeProduct->ID); ?>"></img>
							<?php } ?>
							<h3><?php echo $exchangeProduct->post_title; ?></h3>
							<p><?php echo get_field('author',$exchangeProduct->ID)[0]->post_title; ?></p>
						</a>
					</div>
				<?php } ?>
			</div>
		<?php }?>
	</div>
<?php } ?>

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