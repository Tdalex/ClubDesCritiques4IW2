<?php
/*
 * Template name: chat room
 */

use ClubDesCritiques\Bibliotheque as Bibliotheque;
use ClubDesCritiques\Utilisateur as Utilisateur;
use ClubDesCritiques\ChatRoom as ChatRoom;

$chatRoom = get_post();
ChatRoom::selectBestRoom(get_the_ID());

$product = get_field('product', get_the_ID())[0];

//404 if chat room not now
$startDate = get_field('start_date', get_the_ID());
$endDate   = get_field('end_date', get_the_ID());
$today     = date('Y-m-d H:i:s');


if ($today > $endDate or $today < $startDate) {
    global $wp_query;
    $wp_query->set_404();
    status_header( 404 );
    get_template_part( 404 ); exit();
}

//product
$author = get_field('author', $product->ID)[0];
$published_date = get_field('published_date', $product->ID);
$published_date = new DateTime($published_date);

$description    = get_field('description', $product->ID);
$original_title = get_field('original_title', $product->ID);
$image = get_field('image', $product->ID);

$userNote = 0;

if($userId = get_current_user_id()){
    $userNote = Utilisateur::getNotation($product->ID, $userId);
}

$averageNote = Utilisateur::getAverageNote($product->ID);

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