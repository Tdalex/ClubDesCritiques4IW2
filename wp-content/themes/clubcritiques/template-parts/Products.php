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

<?php
 get_header();
?>


<div class="container">

	<div class="row">
		<?php if(isset($_SESSION['message'])){ ?>
			<div class="alert alert-<?php echo $_SESSION['message']['type'] ?>">
			  <?php echo $_SESSION['message']['text']; ?>
			</div>	
		<?php unset($_SESSION['message']);
			} ?>

		<div class="col-sm-3 col-sm-offset-1 blog-sidebar">
		<p>Nombre de Produits correspondant à votre recherche: <?php echo $countProducts; ?></p>
			<form action="" method="POST">
				Titre ou auteur : <input type='text' name='keywords' value='<?php echo $_POST['keywords'] ?>'></input><br>
				<br><label>Genre</label>

				<?php foreach($genres as $genre){ ?>
					<?php if(isset($_POST['genre']) && in_array($genre->term_id ,$_POST['genre'])){ ?>
						<input type="checkbox" checked name="genre[]" value="<?php echo $genre->term_id ?>"><?php echo $genre->name ?><br>
					<?php }else{ ?>
						<input type="checkbox" name="genre[]" value="<?php echo $genre->term_id ?>"><?php echo $genre->name ?><br>
					<?php } ?>
				<?php } ?>
				
				<br><label>Format</label>
				<?php foreach($formats as $format){ ?>
					<?php if(isset($_POST['format']) && in_array($format->term_id ,$_POST['format'])){ ?>
						<input type="checkbox" checked name="format[]" value="<?php echo $format->term_id ?>"><?php echo $format->name ?><br>
					<?php }else{ ?>
						<input type="checkbox" name="format[]" value="<?php echo $format->term_id ?>"><?php echo $format->name ?><br>
					<?php } ?>
				<?php } ?>
				<br><label>Note minimale</label>
				<select name='note' id="note">
					<option></option>
					<?php for($i=0;$i<=5;$i++){ ?>
						<?php if(isset($_POST['note']) && $i==$_POST['note']){ ?>
							<option selected value="<?php echo $i; ?>"><?php echo $i; ?></option>
						<?php }else{ ?>
							<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
						<?php } ?>
					<?php } ?>
				</select><br>
				<br><button name='type' value='search' type='submit'>rechercher</button>
			</form>
		</div><!-- /.blog-sidebar -->

		<div class="col-sm-8">
		<?php foreach($products as $product){ ?>
			<div class="col-xs-6 col-lg-4">
				<a href="<?php echo get_permalink(get_page_by_title('Produit')).$product->ID; ?>">
				<?php if(!get_field('image', $product->ID)){ ?> 
					<img src="<?php echo get_parent_theme_file_uri( '/assets/images/book_defaut.png' ); ?>"> 
                <?php }else{ ?>
					<img src="<?php echo get_field('image', $product->ID); ?>">
				<?php } ?>
				</img></a>
				<span class="title_book"><?php echo $product->post_title; ?></span>
				
				<a href="<?php echo get_permalink(get_page_by_title('Auteur')).get_field('author',$product->ID)[0]->ID; ?>">
					<span class="author_book"><?php echo get_field('author',$product->ID)[0]->post_title; ?></span>
				</a><br>
				<?php  $averageNote = Utilisateur::getAverageNote($product->ID);
				if ( $averageNote['total'] > 0){ ?>
					<span class='star_rating'>
					<?php
					if($averageNote['average'] >= 0.5 && $averageNote['average'] < 1.5){
						echo "<span class='note_star'>★</span>★★★★</span>";
					}elseif($averageNote['average'] >= 1.5 && $averageNote['average'] < 2.5){
						echo "<span class='note_star'>★★</span>★★★</span>";
					}elseif($averageNote['average'] >= 2.5 && $averageNote['average'] < 3.5){
						echo "<span class='note_star'>★★★</span>★★</span>";
					}elseif($averageNote['average'] >= 3.5 && $averageNote['average'] < 4.5){
						echo "<span class='note_star'>★★★★</span>★</span>";						
					}elseif($averageNote['average'] >= 4.5){
						echo "<span class='note_star'>★</span>★★★★</span>";
					}else{
						echo '★★★★★</span>';
					} ?>
				<?php }else{ ?>
					<span>Aucune note</span>
				<?php } ?>
				<p><a class="btn btn-default" href="<?php echo get_permalink(get_page_by_title('Produit')).$product->ID; ?>" role="button">plus d'infos &raquo;</a></p>
			</div><!--/.col-xs-6.col-lg-4-->
		<?php } ?>
			</div>


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
	</div>