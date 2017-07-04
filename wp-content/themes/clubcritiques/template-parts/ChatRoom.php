<?php
/*
 * Template name: chat room
 */

use ClubDesCritiques\Bibliotheque as Bibliotheque;
use ClubDesCritiques\Utilisateur as Utilisateur;

// get product ID
// $path       = array_filter(explode("/", $_SERVER['REQUEST_URI']));
// $product_ID = end($path);
$product_ID = 50;
$product    = get_post($product_ID);

//404 if not product
if(!is_object($product) || $product->post_type != 'bibliotheque'){
    global $wp_query;
    $wp_query->set_404();
    status_header( 404 );
    get_template_part( 404 ); exit();
}

//product
$author = get_field('author', $product_ID)[0];
$published_date = get_field('published_date', $product_ID);
$published_date = new DateTime($published_date);

$description    = get_field('description', $product_ID);
$original_title = get_field('original_title', $product_ID);
$image = get_field('image', $product_ID);

$userNote = 0;

if($userId = get_current_user_id()){
    $userNote = Utilisateur::getNotation($product_ID, $userId);
}

$averageNote = Utilisateur::getAverageNote($product_ID);

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

		<div class="col-md-9">
			<div class="row">
				<h1 class="title"><?php echo $product->post_title; ?></h1>
			</div>

			<div class="row author">
				<a target='_blank' href='<?php echo get_permalink(get_page_by_title('Auteur')).$author->ID; ?>'><?php echo $author->post_title;?></a>
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
<br>
	<div class='chat-box'>
		<?php
			the_content();
		?>
	</div>
</div>

<?php
get_footer();
?>