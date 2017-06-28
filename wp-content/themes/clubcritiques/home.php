<?php
/*
 * Template name: Home
 */

use ClubDesCritiques\Bibliotheque as Bibliotheque;
use ClubDesCritiques\Utilisateur as Utilisateur;

$args = array(
	'posts_per_page'   => 6,
	'offset'           => 0,
	'orderby'          => 'ID',
	'order'            => 'DESC',
	'post_type'        => 'bibliotheque',
	'post_status'      => 'publish',
	'suppress_filters' => true 
);
$lastProducts = get_posts( $args );

?>

<?php
get_header(); ?>

    <div class="container">

        <div class="row row-offcanvas row-offcanvas-right">

            <div class="col-xs-12 col-sm-9">

                <div class="wrap">
                    <h1 class="page-title">Hello world!</h1>
                    <p class="lead blog-description">Quanta autem vis amicitiae sit, ex hoc intellegi maxime potest, quod ex
                        infinita societate generis humani, quam conciliavit ipsa natura, ita contracta res est et adducta in angustum ut omnis caritas aut inter duos aut inter paucos iungeretur.

                        Nisi mihi Phaedrum, inquam, tu mentitum aut Zenonem putas, quorum utrumque audivi, cum mihi nihil sane praeter sedulitatem probarent, omnes mihi Epicuri sententiae satis notae sunt. atque eos, quos nominavi, cum Attico nostro frequenter audivi, cum miraretur ille quidem utrumque, Phaedrum autem etiam amaret, cotidieque inter nos ea, quae audiebamus, conferebamus, neque erat umquam controversia, quid ego intellegerem, sed quid probarem.</p>
                </div>

                <div class="row">
                    <h2 class="blog-post-title">Livres du moment</h2>
					<?php foreach($lastProducts as $product){ ?>
						<div class="col-xs-6 col-lg-4">
							<img src="<?php echo get_field('image', $product->ID); ?>"></img>
							<p class="title_book"><?php echo $product->post_title; ?></p>
							<p><a class="btn btn-default" href="<?php echo get_permalink(get_page_by_title('Produit')).$product->ID; ?>" role="button">plus d'infos &raquo;</a></p>
						</div><!--/.col-xs-6.col-lg-4-->
					<?php } ?>
                </div><!--/row-->
            </div><!--/.col-xs-12.col-sm-9-->

            <div class="col-xs-6 col-sm-3 sidebar-offcanvas" id="sidebar">
                <div class="list-group">
                    <?php include(get_stylesheet_directory().'/template-parts/blocs/sidebar.php'); ?>
                </div>
            </div><!--/.sidebar-offcanvas-->
        </div><!--/row-->

        <div class="contact-section">
			<h2>Contact Us</h2>
			<p>Feel free to shout us by feeling the contact form or visiting our social network sites like Fackebook,Whatsapp,Twitter.</p>
			<div class="row">
				<div class="col-md-8 col-md-offset-2">
					<form class="form-horizontal">
						<div class="form-group">
							<label for="exampleInputName2">Name</label>
							<input type="text" class="form-control" id="exampleInputName2" placeholder="Jane Doe">
						</div>
						<div class="form-group">
							<label for="exampleInputEmail2">Email</label>
							<input type="email" class="form-control" id="exampleInputEmail2" placeholder="jane.doe@example.com">
						</div>
						<div class="form-group ">
							<label for="exampleInputText">Your Message</label>
							<textarea  class="form-control" placeholder="Description"></textarea>
						</div>
						<button type="submit" class="btn btn-default">Send Message</button>
					</form>


				</div>
			</div>
        </div>

    </div><!--/.container-->


<?php get_footer();
?>