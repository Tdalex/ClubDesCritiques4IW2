<?php
/**
 * Template for displaying search forms in Club Critiques
 *
 * @package WordPress
 * @subpackage Club_Critiques
 * @since 1.0
 * @version 1.0
 */
?>

<?php $unique_id = esc_attr( uniqid( 'search-form-' ) ); ?>

<form role="search" method="post" class="search-form" action="<?php echo get_permalink(21); ?>">
	<label for="<?php echo $unique_id; ?>">
		<span class="screen-reader-text"><?php echo _x( 'Search for:', 'label', 'clubcritiques' ); ?></span>
	</label>
	<input type="search" id="<?php echo $unique_id; ?>" class="search-field" placeholder="<?php echo esc_attr_x( 'Auteur, Titre, &hellip;', 'placeholder', 'clubcritiques' ); ?>" value="<?php echo get_search_query(); ?>" name="keywords" />
	<button type="submit"  name="type" value="search" class="search-submit"><?php echo clubcritiques_get_svg( array( 'icon' => 'search' ) ); ?><span class="screen-reader-text"><?php echo _x( 'Search', 'submit button', 'clubcritiques' ); ?></span></button>
</form>
