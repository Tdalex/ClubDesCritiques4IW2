<?php
/*
 * Template name: Author detail
 */

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
$birthdate = get_field('birthdate', $author_ID);
$sexe      = get_field('sexe', $author_ID);
$deathdate = get_field('deathdate', $author_ID);
$photo     = get_field('photo', $author_ID);
$descriptionAuthor = get_field('description', $author_ID);
?>

<div><?php echo $author->post_title;?></div>
<div><?php echo $birthdate . ' --- ' . $deathdate;?></div>
<img style='height:50px; length:50px;'src='<?php echo $photo;?>'></img>
<div><?php echo $descriptionAuthor;?></div>
<div><?php echo $sexe;?></div>
<div><?php echo $description;?></div>