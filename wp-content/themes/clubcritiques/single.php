<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage Club_Critiques
 * @since 1.0
 * @version 1.0
 */

get_header(); ?>
<?php if('chat-room' == get_post_type()){
	include(get_stylesheet_directory().'/template-parts/ChatRoom.php');
}else{
?>
<div class="wrap">
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php
				/* Start the Loop */
				while ( have_posts() ) : the_post();

					get_template_part( 'template-parts/post/content', get_post_format() );

					// If comments are open or we have at least one comment, load up the comment template.
					if ( comments_open() || get_comments_number() ) :
						comments_template();
					endif;

					the_post_navigation( array(
						'prev_text' => '<span class="screen-reader-text">' . __( 'Previous Post', 'clubcritiques' ) . '</span><span aria-hidden="true" class="nav-subtitle">' . __( 'Previous', 'clubcritiques' ) . '</span> <span class="nav-title"><span class="nav-title-icon-wrapper">' . clubcritiques_get_svg( array( 'icon' => 'arrow-left' ) ) . '</span>%title</span>',
						'next_text' => '<span class="screen-reader-text">' . __( 'Next Post', 'clubcritiques' ) . '</span><span aria-hidden="true" class="nav-subtitle">' . __( 'Next', 'clubcritiques' ) . '</span> <span class="nav-title">%title<span class="nav-title-icon-wrapper">' . clubcritiques_get_svg( array( 'icon' => 'arrow-right' ) ) . '</span></span>',
					) );

				endwhile; // End of the loop.
			?>

		</main><!-- #main -->
	</div><!-- #primary -->
	<?php get_sidebar(); ?>
</div><!-- .wrap -->
<?php
} ?>
<?php get_footer();
