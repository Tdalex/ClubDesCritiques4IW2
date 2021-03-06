<?php
/*
 * Template name: chat room
 */

use ClubDesCritiques\Bibliotheque as Bibliotheque;
use ClubDesCritiques\Utilisateur as Utilisateur;
use ClubDesCritiques\ChatRoom as ChatRoom;

$chatRoom = get_post();
$product = get_field('product', get_the_ID())[0];

$token = get_field('invitation_token', get_the_ID());
if(!$token){
	$token = wp_generate_password( 8, false );
	update_field('field_5963361fc991f',$token, get_the_ID());
}

$startDate = get_field('start_date', get_the_ID());
$endDate   = get_field('end_date', get_the_ID());
$today     = date('Y-m-d H:i:s');

if(!is_user_logged_in() || false === Utilisateur::getNotation($product->ID, get_current_user_id()) || true === ChatRoom::isUserKicked(get_the_ID(), get_current_user_id())){
	Utilisateur::redirect('/');
}
$userId 	= get_current_user_id();
$user_meta  = get_userdata($userId);
$user_role  = $user_meta->roles[0]; 

ChatRoom::cleanCurrentUsers(get_the_ID());
if(isset($_GET['kick']) && !empty($_GET['kick']) && $user_role == 'administrator'){	
	$kickedUser = get_user_by('id',$_GET['kick']);
	$kickedName = strtoupper($kickedUser->user_lastname)." ".ucfirst(strtolower ($kickedUser->user_firstname));
	$kicked = ChatRoom::kickUser(get_the_ID(), $_GET['kick']);	
	ChatRoom::joinChatRoom(get_the_ID());
	if($kicked === true){
		$_SESSION['message'] = array('type' => 'info', 'text' => $kickedName. " a bien été expulsé du salon");
	}
}elseif(isset($_GET['token']) && $_GET['token'] == $token){
	ChatRoom::joinChatRoom(get_the_ID());	
}elseif(isset($_GET['changeRoom']) && $_GET['changeRoom'] == true){
	ChatRoom::changeRoom(get_the_ID());
}elseif(!ChatRoom::isUserInRoom(get_the_ID())){
	ChatRoom::selectBestRoom(get_the_ID());
}else{
	ChatRoom::joinChatRoom(get_the_ID());
}

//404 if chat room not now
if ($today > $endDate or $today < $startDate) {
    global $wp_query;
    $wp_query->set_404();
    status_header( 404 );
    get_template_part( 404 ); exit();
}

//product
$author = get_field('author', $product->ID)[0];
$published_date = get_field('published_date', $product->ID);
$published_date = DateTime::createFromFormat('d-m-Y', $published_date);

$description    = get_field('description', $product->ID);
$original_title = get_field('original_title', $product->ID);
$image = get_field('image', $product->ID);

$userNote = Utilisateur::getNotation($product->ID, $userId);
$averageNote = Utilisateur::getAverageNote($product->ID);

get_header();
?>

<div class="container-fluid">
	<div class="row header_page">
		<div class="container">
				<div class="row">
					<div class="col-md-12">
						<h1 class="title title_margin title_salon" >Salon <?php echo get_field('room_number', get_the_ID())." : <a target='_blank' href='".getTemplateUrl('SingleProduct').$product->ID."'>".$product->post_title."</a> (<a target='_blank'  href='".getTemplateUrl('Author').$author->ID."'>".$author->post_title."</a>)"; ?></h1>
					</div>
				</div>

				<div class="row description_livre">
					<div class="col-md-3">
						<?php if(!$image){ ?> 
							<img class="img-responsive img-livre" src="<?php echo get_parent_theme_file_uri( '/assets/images/book_defaut.png' ); ?>"> 
						<?php }else{ ?>
							<img class="img-responsive img-livre" src="<?php echo $image; ?>"></img>
						<?php } ?>
					</div>
					<div class="col-md-9">
						<div class="row description">
						<p>Résumé du livre</p>
						</div>
					</div>
				</div>
		</div>
	</div>

		<div class="row chatroom">
			<?php if(isset($_SESSION['message']) && !isset($_POST['type'])){ ?>
				<div class="alert alert-<?php echo $_SESSION['message']['type'] ?>">
				  <?php echo $_SESSION['message']['text']; ?>
				</div>	
			<?php unset($_SESSION['message']);
				} ?>
			<div id='message'></div>
			<div class='chat-box col-md-7'>
				<?php
					the_content();
				?>
			</div>
			<?php if('administrator' == $user_role){ ?>
			<div class='col-md-5'>
				<h3>lien d'invitation</h3>
				<p><?php echo get_permalink().'?token='.$token ?></p>
			</div>
			<?php } ?>
			
			<div class='col-md-5'>
				<table class="table table-striped table-salon">
					<thead>
				        <tr>
				            <th>Liste des membres dans le salon</th>
				            <th>Action</th>
				        </tr>
				    </thead>
				    <tbody id='current-user-table'>
				    </tbody>
				</table>
			</div>
		</div>
	</div>

<?php
get_footer();
?>