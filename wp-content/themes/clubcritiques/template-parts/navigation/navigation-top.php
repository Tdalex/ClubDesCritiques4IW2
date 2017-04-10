<?php
/**
 * Displays top navigation
 *
 * @package WordPress
 * @subpackage Club_Critiques
 * @since 1.0
 * @version 1.0
 */

?>
<nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php _e( 'Top Menu', 'clubcritiques' ); ?>">
	<button class="menu-toggle" aria-controls="top-menu" aria-expanded="false"><?php echo clubcritiques_get_svg( array( 'icon' => 'bars' ) ); echo clubcritiques_get_svg( array( 'icon' => 'close' ) ); _e( 'Menu', 'clubcritiques' ); ?></button>
	<?php wp_nav_menu( array(
		'theme_location' => 'top',
		'menu_id'        => 'top-menu',
	) ); ?>

	<?php if ( ( clubcritiques_is_frontpage() || ( is_home() && is_front_page() ) ) && has_custom_header() ) : ?>
		<a href="#content" class="menu-scroll-down"><?php echo clubcritiques_get_svg( array( 'icon' => 'arrow-right' ) ); ?><span class="screen-reader-text"><?php _e( 'Scroll down to content', 'clubcritiques' ); ?></span></a>
	<?php endif; ?>
</nav><!-- #site-navigation -->
