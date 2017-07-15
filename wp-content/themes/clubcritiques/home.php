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
$time_salon = date('F j, Y H:i:s', strtotime($endDate));
?>

<?php
get_header(); ?>

<section>

<?php if(isset($_SESSION['message'])){ ?>
	<div class="alert alert-<?php echo $_SESSION['message']['type'] ?>">
	  <?php echo $_SESSION['message']['text']; ?>
	</div>	
<?php unset($_SESSION['message']);
	} ?>
<div class="hero">


<div class="container">
			<div class="col-md-7 col-md-offset-5 col-xs-10 col-xs-offset-2">
	            <h1 class="page-title"><?php echo get_the_title(); ?></h1>
	            <h2 class="lead blog-description"><?php echo get_the_content(); ?><h2>
	        </div>

            <div class="container">

	<?php if(!is_user_logged_in()){ ?>
		<div class="col-xs-6 col-sm-6">
			<form action="" method="POST" class="login">
			<p>Connectez vous</p>
				 <input type='hidden' name='type' value='login'></input>
				Email:<input type='text' name='email' placeholder="email"></input><br>
				Mot de passe:<input type='password' name='password' placeholder="mot de passe"></input><br>
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
		
	<?php } elseif($_SESSION['activate']) { 
			echo $_SESSION['activate'];
		  } else { ?>
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
				<img class="icone-info" src="<?php echo get_parent_theme_file_uri( '/assets/images/icone_book.png' ); ?>" alt="icone" /><br/>
				<span class="hint-action-title">Découvrez</span>
				<p class="hint-action-description">
					<?php echo get_field('discover', get_the_ID()); ?>
				</p>
			</li>
		</ul>

		<ul class="card">
			<li class="hint-column hint-action">
				<img class="icone-info" src="<?php echo get_parent_theme_file_uri( '/assets/images/icone_star.png' ); ?>" alt="icone" /><br/>
				<span class="hint-action-title">NOTEZ</span>
				<p class="hint-action-description">
					<?php echo get_field('notation', get_the_ID()); ?>
				</p>
			</li>
		</ul>

		<ul class="card">
			<li class="hint-column hint-action">
				<img class="icone-info" src="<?php echo get_parent_theme_file_uri( '/assets/images/icone_share.png' ); ?>" alt="icone" /><br/>
				<span class="hint-action-title">PARTAGEZ</span>
				<p class="hint-action-description">
					<?php echo get_field('share', get_the_ID()); ?>
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
		<h1 class="chat-title">Salons</h1>
		<?php if($nextChat){ 
			$chatProduct = get_field('product', $nextChat->ID)[0]; ?>
				<?php if ($today < $startDate) { ?> 
					<p class="lead blog-description">Le salon ouvrira le <?php echo $start; ?></p> 
				<?php }else{ ?> 

					<div class="row">
						<div class="col-md-2">
							<div class="row">
								<?php if(!$image){ ?> 
									<img class="img-responsive img-livre-accueil " src="<?php echo get_parent_theme_file_uri( '/assets/images/book_defaut.png' ); ?>"> 
								<?php }else{ ?>
									<img class="img-responsive img-livre-accueil " src="<?php echo $image; ?>"></img>
								<?php } ?>
							</div>
						</div>


						<div class="col-md-4">
								<div class="row">
									<h2 class="title"><a class="chat-link" href="<?php echo get_permalink(get_page_by_title('Produit')).$chatProduct->ID; ?>"><?php echo $chatProduct->post_title; ?></a></h2>
								</div>

								<div class="row author author-home">		
									<a href='#'>[Auteur]</a>
									[15/06/2017]
								</div>

							<div class="row">
								<?php if ( $averageNote['total'] > 0){ ?>
									<span class='star_rating'>
									<?php
									if($averageNote['average'] >= 0.5 && $averageNote['average'] < 1.5){
										echo "<span class='note_star'>★</span>★★★★</span>";
									}elseif($averageNote['average'] >= 1.5 && $averageNote['average'] < 2.5){
										echo "<span class='note_star'>★★</span>★★★</span>";
									}elseif($averageNote['average'] >= 2.5 && $averageNote['average'] < 3.5){
										echo "<span class='note_star'>★★★</span>★★</span>";
									}elseif($averageNote['average'] >= 3.5 && $averageNote['average'] < 4.5){
										echo "<span class='note_star'>★★★★</span>★</span>";						
									}elseif($averageNote['average'] >= 4.5){
										echo "<span class='note_star'>★</span>★★★★</span>";
									}else{
										echo '★★★★★</span>';
									} 
									
									if($averageNote['total'] > 1){
										echo "<span>".$averageNote['total']." notes</span>";
									}
									else{
										echo "<span>".$averageNote['total']." note</span>";
									}?>
								<?php }else{ ?>
									<span class="aucune_note">Aucune note</span>
								<?php } ?>
							</div>
						</div>
					
						<div class="col-md-6">
							<div class="row">
								<h2 class="h2-timer">Le salon fini dans :</h2>
							</div>	
							<div class="row timer" id="timer" data-timer="<?php echo $time_salon; ?>">
							  <span class="time"></span>
							  <span class="time"></span>
							  <span class="time"></span>
							  <span class="time"></span>
							</div>
						</div>
					</div>


			<?php if(!is_user_logged_in()){ ?>
					<p class="lead blog-description">Veuillez vous connecter avant de rejoindre le salon</p>
				<?php }elseif(ChatRoom::isUserKicked($nextChat->ID, get_current_user_id())){ ?>
						<p>Vous avez été expulsé du salon</p>
				<?php }elseif(false !== Utilisateur::getNotation($chatProduct->ID, get_current_user_id())){ ?>
					<div class="row col-md-2 col-md-offset-5 join-salon">
					<a class="join-room" href='<?php echo get_permalink($nextChat->ID)?>?changeRoom=true' ><button class="btn">Rejoindre un salon</button></a></div>
					<?php if($userRoom = ChatRoom::getUserRoom($nextChat->ID)){ ?>
						<div class="row col-md-2 col-md-offset-5 join-salon">
							<a class="join-room" href='<?php echo get_permalink($userRoom)?>' ><button class="btn">Rejoindre votre dernier salon</button></a>
						</div>
					<?php } ?>
				<?php }else{ ?>
					<a href='<?php echo get_permalink(get_page_by_title('Produit')).$chatProduct->ID;?>' >Veuillez noter le livre avant de rejoindre un salon</a><br>
				<?php } ?>
			<?php } ?>
			<?php }else{ ?>
				<h1 class="chat-title">Aucun salon programmé pour le moment</h1><br>
			<?php } ?>
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
				<h2>Contactez nous</h2>
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
