<?php
/*
 * Template name: singleProduct
 */

use ClubDesCritiques\Bibliotheque as Bibliotheque;
use ClubDesCritiques\Utilisateur as Utilisateur;

// get product ID
$path       = array_filter(explode("/", $_SERVER['REQUEST_URI']));
$product_ID = end($path);
$product    = get_post($product_ID);
//404 if not product
if(!is_object($product) || $product->post_type != 'bibliotheque'){
    global $wp_query;
    $wp_query->set_404();
    status_header( 404 );
    get_template_part( 404 ); exit();
}

if(isset($_POST['type']) && $_POST['type'] == 'comment'){
	Utilisateur::postComment($product_ID, $_POST);
}elseif(isset($_POST['type']) && $_POST['type'] == 'deleteComment'){
	Utilisateur::deleteComment($product_ID);
}elseif(isset($_POST['type']) && $_POST['type'] == 'give'){
	Utilisateur::prepareExchange($product_ID, 'donner');
}elseif(isset($_POST['type']) && $_POST['type'] == 'take'){
	Utilisateur::prepareExchange($product_ID, 'recevoir');	
}elseif(isset($_POST['type']) && $_POST['type'] == 'deleteExchange'){
	Utilisateur::deleteExchange($product_ID);	
}

//product
$author = get_field('author', $product_ID)[0];
$published_date = get_field('published_date', $product_ID);
$published_date = date($published_date);

$description    = get_field('description', $product_ID);
$original_title = get_field('original_title', $product_ID);
$image = get_field('image', $product_ID);

//exchanges
$exchanges = Utilisateur::getProductExchange($product_ID);

//auteur
$sexe      = get_field('sexe', $author->ID);
$photo     = get_field('photo', $author->ID);
$descriptionAuthor = get_field('description', $author->ID);

$birthdate = get_field('birthdate', $author->ID);
$birthdate = date($birthdate);

$deathdate = get_field('deathdate', $author->ID);
$deathdate = date($deathdate);

$userNote = 0;

$userExchange = array();
if($userId = get_current_user_id()){
    $userNote = Utilisateur::getNotation($product_ID, $userId);
	$userExchange = Utilisateur::getUserExchange($product_ID, $userId);
	$exchangeType = wp_get_post_terms($userExchange->ID, 'exchange_type')[0]->slug;
}

$comments    = Utilisateur::getProductComments($product_ID);
$averageNote = Utilisateur::getAverageNote($product_ID);

//random suggested books
$args = array(
	'posts_per_page'   => 5,
	'orderby'          => 'rand',
	'post_type'        => 'bibliotheque',
	'post_status'      => 'publish',
	'suppress_filters' => true 
);

$suggestedProducts = get_posts( $args );
for($i=0; $i<4; $i++){
	if($product_ID != $suggestedProducts[$i]->ID){
		$suggestedProductsTemp[] = $suggestedProducts[$i];
	}
}
if(count($suggestedProductsTemp)<4){
	$suggestedProductsTemp[] = $suggestedProducts[4];
}

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
				<div class="col-md-3">
				<div class="row">
					<?php if(!$image){ ?> 
						<img class="img-responsive img-livre" src="<?php echo get_parent_theme_file_uri( '/assets/images/book_defaut.png' ); ?>"> 
					<?php }else{ ?>
						<img class="img-responsive img-livre" src="<?php echo $image; ?>"></img>
					<?php } ?>
				</div>
				
				<div class="row btn-echanges">
					<?php if(is_user_logged_in()){ ?>
						<div class="col-md-12">
							<form method='POST' action=''>
								<?php if(empty($userExchange) || $exchangeType == 'recevoir'){ ?>
									<button type='submit' class="btn" value='give' name='type'>Donner</button>
								<?php } ?>
								<?php if(empty($userExchange) || $exchangeType == 'donner'){ ?>
									<button type='submit' class="btn" value='take' name='type'>Recevoir</button>
								<?php } ?>
								<?php if(!empty($userExchange)){ ?>
									<button type='submit' class="btn" value='deleteExchange' name='type'>Annuler</button>
								<?php } ?>
							</form>
						</div>
					<?php } ?>
				</div>
				</div>
				
				<div class="col-md-9">
					<div class="row">
						<h1 class="title"><?php echo $product->post_title; ?></h1>
					</div>

					<div class="row author">		
						<a href='<?php echo get_permalink(get_page_by_title('Auteur')).$author->ID; ?>'><?php echo $author->post_title;?></a>
						<?php echo $published_date; ?>
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
						<span>Aucune note</span>
					<?php } ?>
					</div>

					<div class="row description">
					<h2>Résumé:</h2>
					<?php echo $description; ?>
					</div>
				</div>
		</div>
	</div>

	<div class="container">
		<div class="row">
			<h2 class="cat_h2">Autres suggestions</h2>
		</div>

		<div class="row">
		<?php foreach($suggestedProductsTemp as $sp){ ?>
			<div class="col-md-3 suggestions col-xs-6">
				<a href="<?php echo get_permalink(get_page_by_title('Produit')).$sp->ID; ?>">
					<div class="suggestions-img">
						<?php if(!get_field('image', $sp->ID)){ ?> 
							<img class="img-responsive"  src="<?php echo get_parent_theme_file_uri( '/assets/images/book_defaut.png' ); ?>"> 
						<?php }else{ ?>
							<img class="img-responsive"  src="<?php echo get_field('image', $sp->ID); ?>"></img>
						<?php } ?>
					</div>
					<h3><?php echo $sp->post_title; ?></h3>
				</a>				
				<a href="<?php echo get_permalink(get_page_by_title('Auteur')).get_field('author', $sp->ID)[0]->ID; ?>">
					<p><?php echo get_field('author', $sp->ID)[0]->post_title; ?></p>
				</a>
			</div>
		<?php } ?>
		</div>
</div>

<?php if (isset($exchanges['give']) || isset($exchanges['take'])){ ?>
<div class="bg_page row" id="troc">
	<h1 class="h1_troc">L'ESPACE TROC</h1>
		<?php echo get_field('exchange_description', get_the_ID()); ?>
</div>
<div class="container exchanges">
	<?php if(isset($exchanges['take'])){ ?>
		<h2 class="troc_h2 col-md-5 col-md-offset-3">Ces utilisateurs souhaitent recevoir ce livre :</h2>
			<?php foreach($exchanges['take'] as $exchange){ ?>
				<div class="user_troc">
					<div class="col-md-2 col-md-offset-3 col-xs-12 img_troc">
		              <a href='<?php echo get_permalink(get_page_by_title('utilisateur')).$commentAuthor->ID; ?>'>
						<?php if(!get_field('photo',  'user_'.$exchangeAuthor->ID)){ ?> 
							<div style="background-image: url(<?php echo get_parent_theme_file_uri( '/assets/images/avatar_defaut.png' ); ?>)" 
							class="avatar_sp avatar_echange img-responsive"></div>
						<?php }else{ ?>
							<div class="img-responsive avatar_sp avatar_echange"
								style="background-image: url(<?php echo get_field('photo',  'user_'.$exchangeAuthor->ID); ?>)"></div>
						<?php } ?>
		              </a>
					</div>
					<div class="col-md-3 name_troc">
						<?php $exchangeAuthor = get_user_by('ID', $exchange->post_author); ?>
							<h2><a href='<?php echo get_permalink(get_page_by_title('utilisateur')).$exchangeAuthor->ID; ?>'>
								<?php echo ucfirst(strtolower($exchangeAuthor->user_firstname)) .' '. ucfirst(strtolower($exchangeAuthor->user_lastname)); ?>
								</a></h2>	
					</div>
				</div>
			<?php } ?>
	<?php }?>

	<?php if (isset($exchanges['give'])){ ?>
		<h2 class="troc_h2 col-md-5 col-md-offset-3">Ces utilisateurs possèdent ce livre</h2>
			<?php foreach($exchanges['give'] as $exchange){ ?>
				<?php $exchangeAuthor = get_user_by('ID', $exchange->post_author); ?>
				<div class="user_troc">
					<div class="col-md-2 col-md-offset-3 img_troc">
						<a href='<?php echo get_permalink(get_page_by_title('utilisateur')).$exchangeAuthor->ID; ?>'>
						<?php if(!get_field('photo', 'user_'.$exchangeAuthor->ID)){ ?> 
							<img src="<?php echo get_parent_theme_file_uri( '/assets/images/avatar_defaut.png' ); ?>" 
							class="avatar_sp avatar_echange img-responsive" alt="avatar" />
						<?php }else{ ?>
							<img class="avatar_sp avatar_echange img-responsive"  src="<?php echo get_field('photo',  'user_'.$exchangeAuthor->ID); ?>" alt="avatar"></img>
						<?php } ?>							
						</a>
					</div>
					<div class="col-md-3 name_troc">
						<h2><a href='<?php echo get_permalink(get_page_by_title('utilisateur')).$exchangeAuthor->ID; ?>'>
							<?php echo $exchangeAuthor->user_firstname .' '. $exchangeAuthor->user_lastname; ?>
						</a></h2>
					</div>
				</div>
			<?php } ?>
<?php }}?>
</div>		

	<div class="container">
		<h2 class="cat_h2">Commentaires</h2>

		<div class="container">
			<div class="col-md-11 comments">
			<?php if(is_user_logged_in()){ ?>		
				<div class="row">
					<form method='POST' action=''>
					<div class="rate">
						<input type="radio" id="star5" name="userNote" value="5" <?php if($userNote==5){echo"checked";}?>/><label for="star5" title="text">5 stars</label>
						<input type="radio" id="star4" name="userNote" value="4" <?php if($userNote==4){echo"checked";}?>/><label for="star4" title="text">4 stars</label>
						<input type="radio" id="star3" name="userNote" value="3" <?php if($userNote==3){echo"checked";}?>/><label for="star3" title="text">3 stars</label>
						<input type="radio" id="star2" name="userNote" value="2" <?php if($userNote==2){echo"checked";}?>/><label for="star2" title="text">2 stars</label>
						<input type="radio" id="star1" name="userNote" value="1" <?php if($userNote==1){echo"checked";}?>/><label for="star1" title="text">1 star</label>
					</div>

					<?php if( Utilisateur::getUserComment($product_ID)){
						$userComment = Utilisateur::getUserComment($product_ID); ?>
						<textarea name='comment' class="send_commentaire" placeholder="Ajouter un commentaire..."><?php echo strip_tags($userComment->post_content); ?></textarea>
					
						<div class="btn_submit_com">
							<button type='submit' value='deleteComment' class="btn" name='type'>annuler</button>&nbsp;&nbsp;
							<button type='submit' value='comment' class="btn" name='type'> commenter</button>
						</div>
						<?php }else{ ?>
							<textarea name='comment'></textarea>
							<div class="btn_submit_com">
								<button type='submit' value='comment' class="btn" name='type'> commenter</button>
							</div>
						<?php } ?>
					</div>
					</form>
				</div>
			<?php } ?>				
			</div>			
			<?php foreach($comments as $comment){ ?>
				<?php $commentAuthor = get_user_by('ID', $comment->post_author); ?>
				<div class="row div_comment">
					<div class="col-md-11 comments">
						<div class="col-md-2 ">
			              <a href='<?php echo get_permalink(get_page_by_title('utilisateur')).$commentAuthor->ID; ?>'>
							<?php if(!get_field('photo', 'user_'.$commentAuthor->ID)){ ?> 
								<div class="avatar_sp img-responsive" style="background-image: url(<?php echo get_parent_theme_file_uri( '/assets/images/avatar_defaut.png' ); ?>)"></div>
							<?php }else{ ?>
								<div class="avatar_sp img-responsive" style="background-image: url(<?php echo get_field('photo', 'user_'.$commentAuthor->ID); ?>)"></div>
							<?php } ?>
			              </a>
						</div>
						
						<div class="col-md-10">
							<div class="row">
								<h2>
									<div class="row">
										<a href='<?php echo get_permalink(get_page_by_title('utilisateur')).$commentAuthor->ID; ?>'><?php echo ucfirst(strtolower($commentAuthor->user_firstname)) .' '. ucfirst(strtolower($commentAuthor->user_lastname)); ?>
										</a> 
										<?php

										switch (Utilisateur::getNotation($product_ID, $commentAuthor->ID)) {
											case '1':
												echo "<span class='star_rating_small'>
														<span class='note_star'>★</span>★★★★
													  </span>";
												break;
											case '2':
												echo "<span class='star_rating_small'>
														<span class='note_star'>★★</span>★★★
													  </span>";
												break;
											case '3':
												echo "<span class='star_rating_small'>
														<span class='note_star'>★★★</span>★★
													  </span>";
												break;
											case '4':
												echo "<span class='star_rating_small'>
														<span class='note_star'>★★★★</span>★
													  </span>";
												break;
											case '5':
												echo "<span class='star_rating_small'>
														<span class='note_star'>★★★★★</span>
													  </span>";
												break;
											
											default:
												echo "<span class='star_rating_small'>
														★★★★★
													  </span>";
												break;
										}
										?>
									</div>
								</h2>
							</div>
							<div class="row commentaire_block">
								<?php  echo $comment->post_content; ?>
							</div>							
						</div>						
					</div>
				</div><br>
			<?php } ?>
		</div>
	</div>
</div>
<?php
get_footer(); 
?>