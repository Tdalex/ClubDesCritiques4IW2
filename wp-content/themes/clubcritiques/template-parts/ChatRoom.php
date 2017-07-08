<?php
/*
 * Template name: chat room
 */

use ClubDesCritiques\Bibliotheque as Bibliotheque;
use ClubDesCritiques\Utilisateur as Utilisateur;

$chatRoom  = get_post();

// $product = get_field('product', get_the_ID())[0];
$product = get_post(50);

//404 if chat room not now
$startDate = get_field('start_date', get_the_ID());
$startDate = new DateTime($startDate);
$endDate   = get_field('end_date', get_the_ID());
$endDate = new DateTime($endDate);
$today = new DateTime();

if ($today > $endDate or $today < $startDate) {
    global $wp_query;
    $wp_query->set_404();
    status_header( 404 );
    get_template_part( 404 ); exit();
}

//product
$author = get_field('author', $product->ID)[0];
$published_date = get_field('published_date', $product->ID);
$published_date = new DateTime($published_date);

$description    = get_field('description', $product->ID);
$original_title = get_field('original_title', $product->ID);
$image = get_field('image', $product->ID);

$userNote = 0;

if($userId = get_current_user_id()){
    $userNote = Utilisateur::getNotation($product->ID, $userId);
}

$averageNote = Utilisateur::getAverageNote($product->ID);

get_header();
?>

<div class="container">
	<div class="row">
		<div class="col-md-12">
			<h1 class="title title_margin" >Salon : Titre (Auteur) - Nbr de participants</h1>
		</div>
	</div>
<br>
	<div class="row chatroom">
		<div class='chat-box col-md-7'>
			<?php
				the_content();
			?>
		</div>

		<div class='col-md-5'>
			<table class="table table-striped table-salon">
				<thead>
			        <tr>
			            <th>Liste des membres dans le salon</th>
			            <th>Action</th>
			        </tr>
			    </thead>
			    <tbody>
			    	<tr>
			    		<td>Nom Prénom 1</td>
			    		<td>Signaler | Contacter</td>
			    	</tr>
			    	<tr>
			    		<td>Nom Prénom 1</td>
			    		<td>Signaler | Contacter</td>
			    	</tr>

			    	<tr>
			    		<td>Nom Prénom 1</td>
			    		<td>Signaler | Contacter</td>
			    	</tr>

			    	<tr>
			    		<td>Nom Prénom 1</td>
			    		<td>Signaler | Contacter</td>
			    	</tr>
			    </tbody>
			</table>
		</div>
	</div>
</div>

<?php
get_footer();
?>