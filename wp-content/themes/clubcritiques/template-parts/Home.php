<?php
/*
 * Template name: Home
 */
 ?>

<?php


/*
* Bloc Flexible - Slider
*
*
*/
if( have_posts() ): while( have_posts() ): the_post();
    if( have_rows('blocs') ):

        while ( have_rows('blocs') ) : the_row();

        $layout = get_row_layout();
        $path = get_template_directory() . '/templates/blocs/' . $layout . '.php';
        
        if (file_exists($path)) {
            require $path;
        }

        endwhile;

    endif;
endwhile; endif;
?>