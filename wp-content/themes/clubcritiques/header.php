<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage Club_Critiques
 * @since 1.0
 * @version 1.0
 */
 
use ClubDesCritiques\Utilisateur as Utilisateur;
use ClubDesCritiques\ChatRoom as ChatRoom;

if(isset($_POST['type']) && $_POST['type'] == 'contactSend'){
	echo sendContactForm($_POST);
}elseif(isset($_POST) && $_POST['type'] == 'register'){
	echo Utilisateur::register($_POST);
}elseif(isset($_POST) && $_POST['type'] == 'login'){
	echo Utilisateur::login($_POST);
}elseif(isset($_POST) && $_POST['type'] == 'logout'){
	wp_logout();
	echo Utilisateur::redirect($_SERVER['REQUEST_URI']);
}elseif(isset($_POST) && $_POST['type'] == 'activate'){
	echo Utilisateur::activateAccount($_POST);
}

?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js no-svg">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script>
    <!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div class="site header-nightsky">
    <a class="skip-link screen-reader-text" href="#content"><?php _e('Skip to content', 'clubcritiques'); ?></a>

    <header id="masthead" class="site-header" role="banner">

        <?php if (has_nav_menu('top')) : ?>
        <nav class="navbar navbar-default">
                <a class="navbar-brand" href="/"><img id="logo_header" src="<?php echo get_parent_theme_file_uri( '/assets/images/logo.png' ); ?>" width="200" height="75" ></a>
                <div class="collapse navbar-collapse" id="myNavbar">
					<?php get_search_form(); ?>
					<ul class="nav navbar-nav navbar-right">
						<?php get_template_part('template-parts/navigation/navigation', 'top'); ?>
					</ul>
                </div>
        </nav>

        <!-- <div class="hero">
            <h1 class="page-title">La puissance du bouche à oreille</h1>
            <p class="lead blog-description"><?php echo $post_homepage->post_content ?><p>
            <div class="btn btn-primary">Learn more about us</div>
            <div class="btn btn-primary">Contact us</div>
            </div> -->
</div>
<?php endif; ?>

</header><!-- #masthead -->

	<?php
	// If a regular post or page, and not the front page, show the featured image.
	if ( has_post_thumbnail() && ( is_single() || ( is_page() && ! clubcritiques_is_frontpage() ) ) ) :
		echo '<div class="single-featured-image-header">';
		the_post_thumbnail( 'clubcritiques-featured-image' );
		echo '</div><!-- .single-featured-image-header -->';
	endif;
	?>

	<div class="site-content-contain">
		<div id="content" class="site-content">
