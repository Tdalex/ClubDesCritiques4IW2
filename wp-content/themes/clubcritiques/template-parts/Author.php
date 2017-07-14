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

<div class="container-fluid">
	<div class="row header_page">
		<div class="container">
			<div class="row content_profil">
				<div class="col-md-3">
					<?php if(!$image){ ?> 
						<img src="<?php echo get_parent_theme_file_uri( '/assets/images/avatar_defaut.png' ); ?>" 
						class=" img-responsive profil_img" alt="photo profil" />
					<?php }else{ ?>
						<img src="<?php echo $photo; ?>" class="profil_img" alt="photo profil">
					<?php } ?>
				</div>
				<div class="col-md-6 col-xs-12 content_myProfil">
					<h2><?php echo $author->post_title;?></h2>
					<div class="author interligne_big">
						<?php echo $birthdate. ' - ' . $deathdate?>
					</div>
					<div class="description interligne_small">
						<?php echo $descriptionAuthor;?>
					</div>
				</div>
				<div><?php echo $description;?></div>
			</div>
		</div>
	</div>

	
	<div class="container">
		<div class="row">
			<h2 class="cat_h2 ">Bibliographie</h2>
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
								<img class="img-responsive img_biblio" src="<?php echo get_parent_theme_file_uri( '/assets/images/book_defaut.png' ); ?>"> 
							<?php }else{ ?>
								<img class="img-responsive img_biblio" src="<?php echo $image ?>"></img>
							<?php } ?>
					</div>
					<div class="col-md-9 biblio_content">
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
