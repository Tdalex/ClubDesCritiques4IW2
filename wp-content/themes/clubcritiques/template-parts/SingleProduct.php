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

//product
$author = get_field('author', $product_ID)[0];
$published_date = get_field('published_date', $product_ID);
$published_date = new DateTime($published_date);

$description    = get_field('description', $product_ID);
$original_title = get_field('original_title', $product_ID);
$image = get_field('image', $product_ID);

//auteur
$sexe      = get_field('sexe', $author->ID);
$photo     = get_field('photo', $author->ID);
$descriptionAuthor = get_field('description', $author->ID);

$birthdate = get_field('birthdate', $author->ID);
$birthdate = new DateTime($birthdate);

$deathdate = get_field('deathdate', $author->ID);
$deathdate = new DateTime($deathdate);

$userNote = 0;

if(isset($_POST['userNote'])){
	$userNote = $_POST['userNote'];
	Utilisateur::ChangeNotation($product_ID, $userNote);
}elseif($userId = get_current_user_id()){
	$userNote = Utilisateur::getNotation($product_ID, $userId);
}		

$averageNote = Utilisateur::getAverageNote($product_ID);
?>


<h1><?php echo $product->post_title; ?></h1>
<?php if ($averageNote['total'] > 0){ ?>
	<p>Moyenne des notes: <?php echo $averageNote['average']; ?>/5</p>
	<p>Nombre total de notes: <?php echo $averageNote['total']; ?></p>
<?php }else{ ?>
	<p>Aucune note</p>
<?php } 

if(get_current_user_id()){ ?>
<p>Votre note:</p> 
<form action="" method='POST'>
	<p>
		<select name='userNote' id="userNote">
			<?php for($i=0;$i<=5;$i++){ ?>
				<?php if($i==$userNote){ ?>
					<option selected value="<?php echo $i; ?>"><?php echo $i; ?></option> 
				<?php }else{ ?>
					<option value="<?php echo $i; ?>"><?php echo $i; ?></option> 
				<?php } ?>
			<?php } ?>
		</select>/5
	</p>
	<button type='submit'>Changer ma note</button>
</form>
<?php } ?>

<h2><?php echo $original_title; ?></h2>
<img style='height:50px; length:50px;'src='<?php echo $image;?>'></img>
<div><?php echo $description;?></div>
<div><?php echo $published_date->format('j/m/Y'); ?></div>
<br><br>
<a href='<?php echo get_permalink(get_page_by_title('Auteur')).$author->ID; ?>'><?php echo $author->post_title;?></a>
<div><?php echo $birthdate->format('j/m/Y') . ' --- ' . $deathdate->format('j/m/Y')?></div>
<img style='height:50px; length:50px;'src='<?php echo $photo;?>'></img>
<div><?php echo $descriptionAuthor;?></div>
<div><?php echo $sexe;?></div>
<div><?php echo $description;?></div>