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
$published_date = new DateTime($published_date);

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
$birthdate = new DateTime($birthdate);

$deathdate = get_field('deathdate', $author->ID);
$deathdate = new DateTime($deathdate);

$userNote = 0;

if($userId = get_current_user_id()){
    $userNote = Utilisateur::getNotation($product_ID, $userId);
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

<div class="container">
	<div class="row">

		<div class="col-md-3">
		<?php if(!$image){ ?> 
			<img class="img-responsive" style='height:300px; length:300px;' src="https://pictures.abebooks.com/isbn/9782070543588-fr.jpg"> 
		<?php }else{ ?>
			<img class="img-responsive" style='height:300px; length:300px;' src="<?php echo $image; ?>"></img>
		<?php } ?>
		</div>
		
		<?php if(is_user_logged_in()){ ?>
			<div class="col-md-3">
				<form method='POST' action=''>
					<button type='submit' value='give' name='type'>Donner</button>
					<button type='submit' value='take' name='type'>Recevoir</button>
					<button type='submit' value='deleteExchange' name='type'>Annuler</button>
				</form>
			</div>
		<?php } ?>
		
		<div class="col-md-9">
			<div class="row">
				<h1 class="title"><?php echo $product->post_title; ?></h1>
			</div>

			<div class="row author">		
				<a href='<?php echo get_permalink(get_page_by_title('Auteur')).$author->ID; ?>'><?php echo $author->post_title;?></a>
				<?php echo $published_date->format('j/m/Y'); ?>
			</div>

			<div class="row">
				<div class="rating"><!--
				   --><a href="#5" title="Donner 5 étoiles">★</a><!--
				   --><a href="#4" title="Donner 4 étoiles">★</a><!--
				   --><a href="#3" title="Donner 3 étoiles">★</a><!--
				   --><a href="#2" title="Donner 2 étoiles">★</a><!--
				   --><a href="#1" title="Donner 1 étoile">★</a>
				</div>
				<span><?php echo $averageNote['total']; ?> notations</span>
			</div>

			<div class="row description">
			<?php echo $description; ?>
			</div>
		</div>
	</div>

	<div class="row">
		<h2 class="cat_h2">Autres suggestions</h2>
	</div>

	<div class="row">
	<?php foreach($suggestedProductsTemp as $sp){ ?>
		<div class="col-md-3 suggestions col-xs-6">
			<a href="<?php echo get_permalink(get_page_by_title('Produit')).$sp->ID; ?>">
				<?php if(!get_field('image', $sp->ID)){ ?> 
					<img class="img-responsive"  src="https://pictures.abebooks.com/isbn/9782070543588-fr.jpg"> 
				<?php }else{ ?>
					<img class="img-responsive"  src="<?php echo get_field('image', $sp->ID); ?>"></img>
				<?php } ?>
				<h3><?php echo $sp->post_title; ?></h3>
				<p><?php echo get_field('author', $sp->ID)[0]->post_title; ?></p>
			</a>
		</div>
	<?php } ?>
	</div>
	
	<?php if(is_user_logged_in()){ ?>
	
	<div class="row">
		<form method='POST' action=''>
			Note: <select name='userNote' id="userNote">
				<?php for($i=0;$i<=5;$i++){ ?>
					<?php if($i==$userNote){ ?>
						<option selected value="<?php echo $i; ?>"><?php echo $i; ?></option> 
					<?php }else{ ?>
						<option value="<?php echo $i; ?>"><?php echo $i; ?></option> 
					<?php } ?>
				<?php } ?>
			</select>/5<br>
			Commentaire: 
			<?php if( Utilisateur::getUserComment($product_ID)){
			$userComment = Utilisateur::getUserComment($product_ID); ?>
			<textarea name='comment'><?php echo strip_tags($userComment->post_content); ?></textarea>
			<button type='submit' value='deleteComment' name='type'>supprimer</button>
			<?php }else{ ?>
			<textarea name='comment'></textarea>
			<?php } ?>
			<button type='submit' value='comment' name='type'>commenter</button>
			
		</form>
	</div>
	<?php } ?>
	<br>
	<h2>Commentaires</h2>
	<div class="row">
		<?php foreach($comments as $comment){ ?>
			<?php $commentAuthor = get_user_by('ID', $comment->post_author); ?>
			<div class="col-md-3 comments col-xs-6">
				<h2><a href='<?php echo get_permalink(get_page_by_title('utilisateur')).$commentAuthor->ID; ?>'><?php echo $commentAuthor->user_firstname .' '. $commentAuthor->user_lastname; ?></a> <span><?php echo Utilisateur::getNotation($product_ID, $commentAuthor->ID); ?>/5</span></h2>
				<?php  echo $comment->post_content; ?>
			</div>
		<?php } ?>
	</div>
	
	
	<br>
	<?php if (isset($exchanges['give']) || isset($exchanges['take'])){ ?>
	<h2>Ces personnes souhaitent echanger</h2>
	<?php if(isset($exchanges['take'])){ ?>
		<h3>Recevoir</h3>
		<div class="row">
			<?php foreach($exchanges['take'] as $exchange){ ?>
				<?php $exchangeAuthor = get_user_by('ID', $exchange->post_author); ?>
				<div class="col-md-3 comments col-xs-6">
					<h2><a href='<?php echo get_permalink(get_page_by_title('utilisateur')).$exchangeAuthor->ID; ?>'><?php echo $exchangeAuthor->user_firstname .' '. $exchangeAuthor->user_lastname; ?></a></h2>
				</div>
			<?php } ?>
		</div>
	<?php }?>
	<?php if (isset($exchanges['give'])){ ?>
		<h3>Donner</h3>
		<div class="row">
			<?php foreach($exchanges['give'] as $exchange){ ?>
				<?php $exchangeAuthor = get_user_by('ID', $exchange->post_author); ?>
				<div class="col-md-3 comments col-xs-6">
					<h2><a href='<?php echo get_permalink(get_page_by_title('utilisateur')).$exchangeAuthor->ID; ?>'><?php echo $exchangeAuthor->user_firstname .' '. $exchangeAuthor->user_lastname; ?></a></h2>
				</div>
			<?php } ?>
		</div>
	<?php }}?>
</div>

<?php
get_footer(); 
?>