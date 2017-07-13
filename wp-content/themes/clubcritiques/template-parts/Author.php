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
$birthdate = date($birthdate);
$deathdate = get_field('deathdate', $author_ID);
$deathdate = date($deathdate);

get_header(); 
?>

<div class="container contain">

	<div class="row">
		<div class="col-md-3">
			<?php if(!$image){ ?> 
				<img class="img-responsive img-livre" style='height:300px; length:300px;' src="<?php echo get_parent_theme_file_uri( '/assets/images/book_defaut.png' ); ?>"> 
			<?php }else{ ?>
				<img class="img-responsive img-livre" style='height:300px; length:300px;' src="<?php echo $photo; ?>"></img>
			<?php } ?>
		</div>

		<div class="col-md-9">
			<div class="row">
				<h1 class="title"><?php echo $author->post_title;?></h1>
			</div>
		</div>

		<div class="col-md-9">
			<div class="row author interligne_big">
				<?php echo $birthdate. ' - ' . $deathdate?>
			</div>
		</div>

		<div class="col-md-9">
			<div class="row description interligne_small">
				<?php echo $descriptionAuthor;?>
			</div>
		</div>

		<div><?php echo $description;?></div>
	</div>

	<div class="row">
		<div class="row">
			<h2 class="cat_h2">Bibliographie</h2>
		</div>
		<div class="row book_author">
			<ul>
				<?php
				foreach(Bibliotheque::getAuthorBiblio($author_ID) as $biblio)
				{ 
				$image = get_field('image', $biblio->ID); ?>
				<li class="row single_book col-md-9">

					<div class="col-md-3">
						<a href='<?php echo get_permalink(get_page_by_title('Produit')).$biblio->ID; ?>'>
							<?php if(!$image){ ?> 
								<img class="img-responsive img-livre" src="<?php echo get_parent_theme_file_uri( '/assets/images/book_defaut.png' ); ?>"> 
							<?php }else{ ?>
								<img class="img-responsive img-livre" src="<?php echo $image ?>"></img>
							<?php } ?>
					</div>
					<div class="col-md-9">
							<div class="row">
								<h3><?php echo $biblio->post_title;?></h3>	
							</div>
						</a>
							<div class="row description">
							<p>
								<?php echo get_field('description', $biblio->ID); ?>
							</p>
							</div>
					</div>
				</li>
				<?php } ?>
			</ul>

	</div>

</div>

<?php
get_footer(); 

?>
