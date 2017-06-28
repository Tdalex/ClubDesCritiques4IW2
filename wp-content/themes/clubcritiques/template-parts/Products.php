<?php
/*
 * Template name: Products
 */

use ClubDesCritiques\Bibliotheque as Bibliotheque;
use ClubDesCritiques\Utilisateur as Utilisateur;
 
if(isset($_POST['type']) && $_POST['type'] == 'search'){
	$response = Bibliotheque::getBibliotheque($_POST);
	$page = $_POST['page'];
	
	$products = $response['products'];
	$countProducts = $response['nb_products'];
}else{
	$page = 1;
	$args = array(
		'posts_per_page'   => 6,
		'offset'           => 0,
		'orderby'          => 'ID',
		'order'            => 'DESC',
		'post_type'        => 'bibliotheque',
		'post_status'      => 'publish',
		'suppress_filters' => true 
	);
	$products = get_posts( $args );
	
	$args = array(
		'posts_per_page'   => -1,
		'offset'           => 0,
		'post_type'        => 'bibliotheque',
		'post_status'      => 'publish',
		'suppress_filters' => true 
	);
	$countProducts = count(get_posts( $args ));
}		

$genres = get_terms( array(
			'taxonomy' => 'bibliotheque_genre',
			'hide_empty' => false,
		) );	
	
$formats = get_terms( array(
			'taxonomy' => 'bibliotheque_format',
			'hide_empty' => false,
		) );		
?>

<form action="" method="POST">
	<input type='hidden' name='type' value='search'></input>
	Titre ou auteur : <input type='text' name='keywords' value='<?php echo $_POST['keywords'] ?>'></input><br>
	<label>Genre</label><br>
	<?php foreach($genres as $genre){ ?>
		<?php if(isset($_POST['genre']) && in_array($genre->term_id ,$_POST['genre'])){ ?>
			<input type="checkbox" checked name="genre[]" value="<?php echo $genre->term_id ?>"><?php echo $genre->name ?><br>
		<?php }else{ ?>
			<input type="checkbox" name="genre[]" value="<?php echo $genre->term_id ?>"><?php echo $genre->name ?><br>
		<?php } ?>
	<?php } ?>
	<label>Format</label><br>
	<?php foreach($formats as $format){ ?>
		<?php if(isset($_POST['format']) && in_array($format->term_id ,$_POST['format'])){ ?>
			<input type="checkbox" checked name="format[]" value="<?php echo $format->term_id ?>"><?php echo $format->name ?><br>
		<?php }else{ ?>
			<input type="checkbox" name="format[]" value="<?php echo $format->term_id ?>"><?php echo $format->name ?><br>
		<?php } ?>
	<?php } ?>
	<label>Note minimale</label>
	<select name='note' id="note">
		<option></option> 
		<?php for($i=0;$i<=5;$i++){ ?>
			<?php if(isset($_POST['note']) && $i==$_POST['note']){ ?>
				<option selected value="<?php echo $i; ?>"><?php echo $i; ?></option> 
			<?php }else{ ?>
				<option value="<?php echo $i; ?>"><?php echo $i; ?></option> 
			<?php } ?>
		<?php } ?>
	</select>
	<br><button type='submit'>rechercher</button>



	<p>Nombre de Produits correspondant a votre recherche: <?php echo $countProducts; ?></p>
	<div class="row">
		<?php foreach($products as $product){ ?>
			<div class="col-xs-6 col-lg-4">
				<!-- <img src="<?php echo get_field('image', $product->ID); ?>"></img> -->
				<p class="title_book"><?php echo $product->post_title; ?></p>
				<p><a class="btn btn-default" href="<?php echo get_permalink(get_page_by_title('Produit')).$product->ID; ?>" role="button">plus d'infos &raquo;</a></p>
			</div><!--/.col-xs-6.col-lg-4-->
		<?php } ?>
	</div><!--/row-->

  
	<div class="align-center">
		<?php //pagination
			$total      = $countProducts;
			$limit 		= 12;
			$maxPage    = floor($total / $limit)+1;
			$pagination = '';
			$delimiter  = ' ';
			$start      = $page >= 10 ? $page-5 : 1;
			$end        = $maxPage - $start > 10 ? $start + 10 : $maxPage;

			if ( $total > $limit ) {
				if ( $page > 1 ) {
					$pagination .= "<button name='page' type='submit' value='" . ($page - 1) . "'>précedent</button><span> | </span>";
				}

				if ( $start > 1 ) {
					$pagination .= '<button name="page" type="submit" value="1">1</button>';
					$p = 10;
					while($p < $start){						
						$pagination .= '<span> , </span><button name="page" type="submit" value="'. $p .'">'. $p .'</button></a>';
						$p += 10;
					}
					$pagination .= '<span> ... </span>';
				}

				for ( $i=$start; $i <= $end; $i++ ) {
					if ( $i == $page ) {
						$pagination .= '<span>'.$i.'</span>';
					}else{
						$pagination .= '<button name="page" type="submit" value="'. $i .'">'. $i .'</button>';
					}
					$pagination .= $delimiter;
				}
				if ( $maxPage - $end > 10 ) {
					$pagination .= '<span> ... </span>';
					$p = floor(($end + 10) / 10) * 10;
					while($p < $maxPage){
						$pagination .= '<button name="page" type="submit" value="'. $p .'">'. $p .'</button><span> , </span>';
						$p += 10;
					}
					$pagination .= '<button name="page" type="submit" value="'. $maxPage .'">'. $maxPage .'</button>';
				}

				if ( $page < $total/$limit ) {
					$pagination .= '<span> | </span><button name="page" type="submit" value="'. ($page + 1) .'">suivant</button>';
				}
			}
			echo $pagination;
		?>
	</div>
</form>