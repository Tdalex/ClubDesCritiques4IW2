<?php
/*
 * Template name: my profil
 */
 
use ClubDesCritiques\Utilisateur as Utilisateur;

if(!is_user_logged_in()){
	Utilisateur::redirect('/');
}

$user = wp_get_current_user();
$exchanges = Utilisateur::getAllUserExchange($user->ID);
$userMeta = get_user_meta( $user->ID );

get_header(); 
?>

<div class="container-fluid">
	<div class="row header_page">
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
						<?php if(is_user_logged_in() && $user->ID == wp_get_current_user()->ID){ ?>
						<div class="row">
							<h2>Paramètres</h2>
							<div class="menu_flottant">
								<ul>
									<a href='<?php echo get_permalink(get_page_by_title('Modifier profil')); ?>'><li>Modifier mes informations</li></a>
									<a href="#"><li>Gérer ses contacts</li></a>
								</ul>
							</div>
						</div>
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
			<?php echo get_field('exchange_description', get_the_ID()); ?>
		</div>
		<div class="row exchange">	
			<?php if(isset($exchanges['take'])){ ?>
				<h2>Je souhaite recevoir ces livres :</h2>
				<div class="row">
					<?php foreach($exchanges['take'] as $exchange){ ?>
						<?php $exchangeProduct = get_field('product', $exchange->ID)[0]; ?>
						<div class="col-md-3 col-xs-6 suggestions">
							<a href='<?php echo get_permalink(get_page_by_title('Produit')).$exchangeProduct->ID; ?>'>	
								<div class="suggestions-img">
									<?php if(!get_field('image', $exchangeProduct->ID)){ ?> 
										<img class="img-responsive" src="<?php echo get_parent_theme_file_uri( '/assets/images/book_defaut.png' ); ?>"> 
									<?php }else{ ?>
										<img class="img-responsive" src="<?php echo get_field('image', $exchangeProduct->ID)	; ?>"></img>
									<?php } ?>
								</div>
								<h3><?php echo $exchangeProduct->post_title; ?></h3>
							</a>
							<a href="<?php echo get_permalink(get_page_by_title('Auteur')).get_field('author',$exchangeProduct->ID)[0]->ID; ?>">
								<p><?php echo get_field('author',$exchangeProduct->ID)[0]->post_title; ?></p>
							</a>
						</div>
					<?php } ?>
				</div>
			<?php }?>
			<?php if (isset($exchanges['give'])){ ?>
				<h2>Je possède ces livres :</h2>
				<div class="row">
					<?php foreach($exchanges['give'] as $exchange){ ?>
						<?php $exchangeProduct = get_field('product', $exchange->ID)[0]; ?>
						<div class="col-md-3 col-xs-6 suggestions">
							<a href='<?php echo get_permalink(get_page_by_title('Produit')).$exchangeProduct->ID; ?>'>	
								<div class="suggestions-img">
									<?php if(!get_field('image', $exchangeProduct->ID)){ ?> 
										<img class="img-responsive" src="<?php echo get_parent_theme_file_uri( '/assets/images/book_defaut.png' ); ?>"> 
									<?php }else{ ?>
										<img class="img-responsive" src="<?php echo get_field('image', $exchangeProduct->ID); ?>"></img>
									<?php } ?>
								</div>
								<h3><?php echo $exchangeProduct->post_title; ?></h3>
							</a>
							<a href="<?php echo get_permalink(get_page_by_title('Auteur')).get_field('author',$exchangeProduct->ID)[0]->ID; ?>">
								<p><?php echo get_field('author',$exchangeProduct->ID)[0]->post_title; ?></p>
							</a>
						</div>
					<?php } ?>
				</div>
			<?php }?>
		</div>
	</div>
	<?php } ?>

</div>

<?php
get_footer(); 
?>