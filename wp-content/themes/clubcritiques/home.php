<?php
/*
 * Template name: Home
 */

use ClubDesCritiques\Bibliotheque as Bibliotheque;
use ClubDesCritiques\Utilisateur as Utilisateur;
use ClubDesCritiques\ChatRoom as ChatRoom;

$args = array(
	'posts_per_page'   => 6,
	'offset'           => 0,
	'orderby'          => 'ID',
	'order'            => 'DESC',
	'post_type'        => 'bibliotheque',
	'post_status'      => 'publish',
	'suppress_filters' => true
);
$products = get_posts( $args );

//next Chat
setlocale (LC_TIME, 'fr_FR.utf8','fra'); 
$nextChat  = ChatRoom::getNextChat();
$startDate = get_field('start_date', $nextChat->ID);
$endDate   = get_field('end_date',  $nextChat->ID);
$today     = date('Y-m-d H:i:s');
$start = strftime('%A %d %B à %Hh%M',strtotime($start));
$end   = strftime('%A %d %B à %Hh%M',strtotime($endDate));

?>

<?php
get_header(); ?>

<section>

<div class="hero">

<?php if(isset($_SESSION['message'])){ ?>
			<div class="alert alert-<?php echo $_SESSION['message']['type'] ?>">
			  <?php echo $_SESSION['message']['text']; ?>
			</div>	
		<?php unset($_SESSION['message']);
			} ?>

<div class="container">
            <h1 class="page-title">La puissance du bouche à oreille</h1>
            <h2 class="lead blog-description"><?php echo get_the_content() ?><h2>
            <div class="container">

            <?php if(!is_user_logged_in()){ ?>

            <div class="col-xs-6 col-sm-6">

	<form action="" method="POST" class="login">
	<p>Connectez vous</p>
		 <input type='hidden' name='type' value='login'></input>
		email:<input type='text' name='email' placeholder="email"></input><br>
		password:<input type='password' name='password' placeholder="mot de passe"></input><br>
		<button type='submit'>se connecter</button>
	</form>
		</div>
	
		<div class="col-xs-6 col-sm-6">
		<p>Inscrivez vous</p>
	<form action="" method="POST">
		<input type='hidden' name='type' value='register'></input>
		Email:<input required='required' type='text' name='email' placeholder="email"></input><br>
		<button type='submit'>S'inscrire</button>
	</form>
	</div>
	
<?php } else { ?>
		<form action="" method="POST" class="logout">
		<input type='hidden' name='type' value='logout'></input>
		<button type='submit'>se deconnecter</button>
	</form>	
<?php } ?>

</div> <!-- Container -->
       </div>
</section>

<!-- FIN SECTION INTRO -->

<!-- SECTION INFO -->

<section>

<div class="more_info">
        <div class="container">
        	<div class="row">
        		<ul class="card">
			<li class="hint-column hint-action">
				<span class="hint-action-icon discover"></span>
				<span class="hint-action-title">Découvrez</span>
				<p class="hint-action-description">
					Nous organisons votre bouche à oreille culturel. Découvrez des livres qui correspondent à vos goûts.
				</p>
			</li>
		</ul>

		<ul class="card">
			<li class="hint-column hint-action">
				<span class="hint-action-icon rate-home"></span>
				<span class="hint-action-title">NOTEZ</span>
				<p class="hint-action-description">
					Evaluez les livres que vous avez lus. Classez-les selon vos critères ou donnez votre avis détaillé dans une critique.
				</p>
			</li>
		</ul>

		<ul class="card">
			<li class="hint-column hint-action">
				<span class="hint-action-icon share"></span>
				<span class="hint-action-title">PARTAGEZ</span>
				<p class="hint-action-description">
					Faites découvrir vos coups de coeur et vos coups de gueule à vos amis, conseillez leur ce qu'ils pourront aimer.


				</p>
			</li>
		</ul>
        	</div>
        </div>    
</div>

</section>

<!-- FIN SECTION INFO -->

<!-- SECTION CHAT -->

<section>

<div class="next-chat">
    <div class="container">
        <div class="row row-offcanvas row-offcanvas-right">
            <div class="col-xs-12 col-sm-12">
                <div class="chatHeader">
					<?php if($nextChat){ 
						$chatProduct = get_field('product', $nextChat->ID)[0]; ?>
							<?php if ($today < $startDate) { ?> 
								<p class="lead blog-description">Le salon ouvrira le <?php echo $start; ?></p> 
							<?php }else{ ?> 
								<h1 class="chat-title"><?php echo $nextChat->post_title ?></h1>
								<p class="lead blog-description">Prochain Chat sur <a class="chat-link" href="<?php echo get_permalink(get_page_by_title('Produit')).$chatProduct->ID; ?>"><?php echo $chatProduct->post_title; ?></a></p>
								<?php echo get_field('description', $nextChat->ID); ?>								
								<p class="lead blog-description">Le salon fermera le <?php echo $end; ?></p> 
								<?php if(!is_user_logged_in()){ ?>
									<p class="lead blog-description">Veuillez vous connecter avant de rejoindre le salon</p>
								<?php }elseif(ChatRoom::isUserKicked($nextChat->ID, get_current_user_id())){ ?>
										<p>Vous avez été expulsé du salon</p>
								<?php }elseif(false !== Utilisateur::getNotation($chatProduct->ID, get_current_user_id())){ ?>
									<a class="join-room" href='<?php echo get_permalink($nextChat->ID)?>?changeRoom=true' >Rejoindre un salon</a><br>
									<?php if($userRoom = ChatRoom::getUserRoom($nextChat->ID)){ ?>
										<a class="join-room" href='<?php echo get_permalink($userRoom)?>' >Rejoindre votre dernier salon</a><br>
									<?php } ?>
								<?php }else{ ?>
									<a href='<?php echo get_permalink(get_page_by_title('Produit')).$chatProduct->ID;?>' >Veuillez noter le livre avant de rejoindre un salon</a><br>
								<?php } ?>
							<?php } ?>
					<?php }else{ ?>
						<h1 class="chat-title">Aucun salon programmé pour le moment</h1><br>
					<?php } ?>
                </div>
           </div>
    </div>    
</div>

</section>

<!-- FIN SECTION CHAT -->

<!-- SECTION LIVRES -->

<section>

<div class="livres-moment">
	<div class="container">
		<div class="row row-offcanvas row-offcanvas-right">
			<div class="col-xs-12 col-sm-12">
				<h2 class="blog-post-title">Livres du moment</h2>
				<?php foreach($products as $product){ ?>
					<div class="col-xs-6 col-lg-2">
						<?php if(!get_field('image', $product->ID)){ ?>
							<a href="<?php echo get_permalink(get_page_by_title('Produit')).$product->ID; ?>"><img src="<?php echo get_parent_theme_file_uri( '/assets/images/book_defaut.png' ); ?>"></img></a>
						<?php }else{ ?>
							<a href="<?php echo get_permalink(get_page_by_title('Produit')).$product->ID; ?>"><img src="<?php echo get_field('image', $product->ID); ?>"></img></a>
						<?php } ?>
						<p class="title_book"><a  class="link-book" href="<?php echo get_permalink(get_page_by_title('Produit')).$product->ID; ?>"><?php echo $product->post_title; ?></a></p>
						<p><a class="btn btn-primary" href="<?php echo get_permalink(get_page_by_title('Produit')).$product->ID; ?>" role="button">plus d'infos &raquo;</a></p>
					</div><!--/.col-xs-6.col-lg-4-->
				<?php } ?>
			</div>
		</div>    
	</div>
</div>
</section>

<!-- FIN SECTION LIVRES -->

<!-- SECTION CONTACT -->

<section>

<div class="contact-section">
    <div class="container">
        <div class="row ">
            <div class="col-xs-12 col-sm-12">
				<h2>Contact Us</h2>
				<p class="description_contact">Feel free to shout us by feeling the contact form or visiting our social network sites like Fackebook,Whatsapp,Twitter.</p>
				<div class="row">
					<div class="col-md-12">
						<form class="form-horizontal">
							<div class="form-group">
								<label for="exampleInputName2">Name</label>
								<input type="text" class="form-control" id="exampleInputName2" placeholder="Jane Doe">
							</div>
							<div class="form-group">
								<label for="exampleInputEmail2">Email</label>
								<input type="email" class="form-control" id="exampleInputEmail2" placeholder="jane.doe@example.com">
							</div>
							<div class="form-group ">
								<label for="exampleInputText">Your Message</label>
								<textarea  class="form-control" placeholder="Description"></textarea>
							</div>
							<button type="submit" class="btn btn-default">Send Message</button>
						</form>
					</div>
				</div>
			</div>    
		</div>
	</div>
</div>
</section>

<!-- FIN SECTION CONTACT -->

<?php get_footer();
?>
