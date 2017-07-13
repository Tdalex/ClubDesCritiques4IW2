<?php
/*
 * Template name: Home
 */

use ClubDesCritiques\Bibliotheque as Bibliotheque;
use ClubDesCritiques\Utilisateur as Utilisateur;
use ClubDesCritiques\ChatRoom as ChatRoom;

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

//next Chat
setlocale (LC_TIME, 'fr_FR.utf8','fra'); 
$nextChat  = ChatRoom::getNextChat();
$startDate = get_field('start_date', $nextChat->ID);
$endDate   = get_field('end_date',  $nextChat->ID);
$today     = date('Y-m-d H:i:s');
$start = strftime('%A %d %B à %Hh%M',strtotime($start));
$end   = strftime('%A %d %B à %Hh%M',strtotime($endDate));
?>

<?php
get_header(); ?>

<section>
<div class="hero">
            <h1 class="page-title">La puissance du bouche à oreille</h1>
            <p class="lead blog-description"><?php echo get_the_content() ?><p>
            <div class="btn btn-primary">Learn more about us</div>
            <div class="btn btn-primary">Contact us</div>
            </div>
</section>
    <div class="container" id="page">
		<?php if(isset($_SESSION['message'])){ ?>
			<div class="alert alert-<?php echo $_SESSION['message']['type'] ?>">
			  <?php echo $_SESSION['message']['text']; ?>
			</div>	
		<?php unset($_SESSION['message']);
			} ?>
        <div class="row row-offcanvas row-offcanvas-right">

            <div class="col-xs-12 col-sm-9">


                <div class="chatHeader">
					<?php if($nextChat){ 
						$chatProduct = get_field('product', $nextChat->ID)[0]; ?>
						<h1 class="chat-title"><?php echo $nextChat->post_title ?></h1>
						<p class="lead blog-description">Prochain Chat sur <a href="<?php echo get_permalink(get_page_by_title('Produit')).$chatProduct->ID; ?>"><?php echo $chatProduct->post_title; ?></a></p>
						<?php echo get_field('description', $nextChat->ID); ?>
						<?php if ($today < $startDate) { ?>
							<p class="lead blog-description">Le salon ouvrira le <?php echo $start; ?></p>
						<?php }elseif(!is_user_logged_in()){ ?>
							<p class="lead blog-description">Veuillez vous connecter avant de rejoindre le salon</p>
						<?php }elseif(ChatRoom::isUserKicked($nextChat->ID, get_current_user_id())){ ?>
								<p>Vous avez été expulsé du salon</p>
						<?php }elseif(false !== Utilisateur::getNotation($chatProduct->ID, get_current_user_id())){ ?>
							<p class="lead blog-description">Le salon fermera le <?php echo $end; ?></p>
							<a href='<?php echo get_permalink($nextChat->ID)?>?changeRoom=true' >Rejoindre un salon</a><br>
							<?php if($userRoom = ChatRoom::getUserRoom($nextChat->ID)){ ?>
								<a href='<?php echo get_permalink($userRoom)?>' >Rejoindre votre dernier salon</a><br>
							<?php }?>
						<?php }else{ ?>
							<a href='<?php echo get_permalink(get_page_by_title('Produit')).$chatProduct->ID;?>' >Veuillez noter le livre avant de rejoindre un salon</a><br>
						<?php }?>
					<?php }else{ ?>
						<h1 class="chat-title">Aucun salon programmé pour le moment</h1><br>
					<?php } ?>
                </div>

                <div class="row">
                    <h2 class="blog-post-title">Livres du moment</h2>
					<?php foreach($products as $product){ ?>
						<div class="col-xs-6 col-lg-4">
							<?php if(!get_field('image', $product->ID)){ ?>
								<a href="<?php echo get_permalink(get_page_by_title('Produit')).$product->ID; ?>"><img src="https://pictures.abebooks.com/isbn/9782070543588-fr.jpg"></img></a>
							<?php }else{ ?>
								<a href="<?php echo get_permalink(get_page_by_title('Produit')).$product->ID; ?>"><img src="<?php echo get_field('image', $product->ID); ?>"></img></a>
							<?php } ?>
							<p class="title_book"><a href="<?php echo get_permalink(get_page_by_title('Produit')).$product->ID; ?>"><?php echo $product->post_title; ?></a></p>
							<p><a class="btn btn-default" href="<?php echo get_permalink(get_page_by_title('Produit')).$product->ID; ?>" role="button">plus d'infos &raquo;</a></p>
						</div><!--/.col-xs-6.col-lg-4-->
					<?php } ?>
                </div><!--/row-->
            </div><!--/.col-xs-12.col-sm-9-->

            <div class="col-xs-6 col-sm-3 sidebar-offcanvas" id="sidebar">
                <div class="list-group">
                    <?php include(get_stylesheet_directory().'/template-parts/blocs/Sidebar.php'); ?>
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