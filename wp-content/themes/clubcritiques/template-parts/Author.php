<?php
/*
 * Template name: Author detail
 */
 
use ClubDesCritiques\Bibliotheque as Bibliotheque;

// get author ID
$path       = array_filter(explode("/", $_SERVER['REQUEST_URI']));
$author_ID = end($path);
$author    = get_post($author_ID);

//404 if not product
if(!is_object($author) || $author->post_type != 'auteurs'){
    global $wp_query;
    $wp_query->set_404();
    status_header( 404 );
    get_template_part( 404 ); exit();
}

//auteur
$sexe      = get_field('sexe', $author_ID);
$photo     = get_field('photo', $author_ID);
$descriptionAuthor = get_field('description', $author_ID);
$birthdate = get_field('birthdate', $author_ID);
$birthdate = new DateTime($birthdate);
$deathdate = get_field('deathdate', $author_ID);
$deathdate = new DateTime($deathdate);
?>

<div><?php echo $author->post_title;?></div>
<div><?php echo $birthdate->format('j/m/Y') . ' --- ' . $deathdate->format('j/m/Y')?></div>
<img style='height:50px; length:50px;'src='<?php echo $photo;?>'></img>
<div><?php echo $descriptionAuthor;?></div>
<div><?php echo $sexe;?></div>
<div><?php echo $description;?></div>

<p>Bibliographie</p>
<?php
foreach(Bibliotheque::getAuthorBiblio($author_ID) as $biblio){ ?>
	<a href='<?php echo get_permalink(get_page_by_title('Produit')).$biblio->ID; ?>'><?php echo $biblio->post_title;?></a><br>
<?php }
?>