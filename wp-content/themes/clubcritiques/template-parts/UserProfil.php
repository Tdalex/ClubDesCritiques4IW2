<?php
/*
 * Template name: user profil
 */

// get user ID
$path       = array_filter(explode("/", $_SERVER['REQUEST_URI']));
$user_id = end($path);
$user    = get_user_by('ID', $user_id);

//404 if not product
if(!is_object($user)){
    global $wp_query;
    $wp_query->set_404();
    status_header( 404 );
    get_template_part( 404 ); exit();
}

$userMeta = get_user_meta( $user->ID );

?>
<h1><?php echo $user->user_firstname. ' '. $user->user_lastname; ?></h1>
Description: <p><?php echo $userMeta['description'][0] ?></p><br>