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
$time_start_salon = date('F j, Y H:i:s', strtotime($startDate));
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
					<div class="row">
						<div class="col-md-3">
							<div class="row">
								<a href="#">
									<?php if(!$image){ ?> 
										<div style="background-image: url(<?php echo get_parent_theme_file_uri( '/assets/images/book_defaut.png' ); ?>)" class="book-salon"></div>
									<?php }else{ ?>
										<div class="book-salon" style="background-image: url(<?php echo $image; ?>"></div>
									<?php } ?>
								</a>
							</div>
						</div>


						<div class="col-md-3">
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
								<h2 class="h2-timer">Le salon commence dans :</h2>
							</div>	
							<div class="row timer" id="timer" data-timer="<?php echo $time_start_salon; ?>">
							  <span class="time"></span>
							  <span class="time"></span>
							  <span class="time"></span>
							  <span class="time"></span>
							</div>
						</div>
					</div>

				<?php }else{ ?> 

					<div class="row">
						<div class="col-md-3">
							<div class="row">
								<?php if(!$image){ ?> 
									<div class="book-salon" style=" background-image: url(<?php echo get_parent_theme_file_uri( '/assets/images/book_defaut.png' ); ?>)"></div>
								<?php }else{ ?>
									<div class="book-salon" style=" background-image: url(<?php echo $image; ?>)"></div>
								<?php } ?>
							</div>
						</div>


						<div class="col-md-3">
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
						<a class="join-room" href='<?php echo get_permalink($nextChat->ID)?>?changeRoom=true' ><button class="btn">Rejoindre le salon</button></a>
					</div>
					<?php if($userRoom = ChatRoom::getUserRoom($nextChat->ID)){ ?>
						<div class="row col-md-2 col-md-offset-5 join-salon text-center">
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
		<h1>Livres du moment</h1>
		<?php foreach($products as $product){ ?>
			<div class="col-md-2 col-xs-4">
				<?php if(!get_field('image', $product->ID)){ ?>
					<a href="<?php echo get_permalink(get_page_by_title('Produit')).$product->ID; ?>"><div class="book-moment" style="background-image: url(<?php echo get_parent_theme_file_uri( '/assets/images/book_defaut.png' ); ?>)"></div></a>
				<?php }else{ ?>
					<a href="<?php echo get_permalink(get_page_by_title('Produit')).$product->ID; ?>"><div class="book-moment" style="background-image: url(<?php echo get_field('image', $product->ID); ?>)"></div></a>
				<?php } ?>
				<p class="title_book"><a  class="link-book" href="<?php echo get_permalink(get_page_by_title('Produit')).$product->ID; ?>"><?php echo $product->post_title; ?></a></p>
			</div>
		<?php } ?>
	</div>
</div>
</section>



<!-- FIN SECTION LIVRES -->

<!-- SECTION CONTACT -->


<section>
<div class="div_contact">
<div class="container">
    <div class="row">
        <div class="col-lg-12 title_contact">
            <h1>Contactez nous</h1>
        </div>
    </div>
	<div class="row">
        <div class="col-lg-12">
            <form name="sentMessage">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Votre Nom *" id="name" required="">
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Votre Prénom *" id="surname" required="">
                        </div>
                        <div class="form-group">
                            <input type="email" class="form-control" placeholder="Votre Email *" id="email" required="">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <textarea class="form-control" rows="6" placeholder="Votre Message *" id="message" required=""></textarea>
                        </div>
                    </div>
                    <div class="col-lg-12 text-center button-submit">
                        <button type="submit" class="btn">Envoyer</button>
                    </div>
                </div>
            </form>
        </div>	

	</div>

</div>
</section>

<!-- FIN SECTION CONTACT -->

<?php get_footer();
?>
