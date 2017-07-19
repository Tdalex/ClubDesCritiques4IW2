<?php
/**
 * Displays top navigation
 *
 * @package WordPress
 * @subpackage Club_Critiques
 * @since 1.0
 * @version 1.0
 */

use ClubDesCritiques\Utilisateur as Utilisateur;
use ClubDesCritiques\ChatRoom as ChatRoom;

//next Chat
setlocale (LC_TIME, 'fr_FR.utf8','fra'); 
$nextChat  = ChatRoom::getNextChat();
$startDate = get_field('start_date', $nextChat->ID);
$endDate   = get_field('end_date',  $nextChat->ID);
$today     = date('Y-m-d H:i:s');
// $start = strftime('%A %d %B à %Hh%M',strtotime($start));
$end   = strftime('%A %d %B à %Hh%M',strtotime($endDate));
$time_start_salon = date('F j, Y H:i:s', strtotime($startDate));
$time_salon = date('F j, Y H:i:s', strtotime($endDate));
?>
<nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php _e( 'Top Menu', 'clubcritiques' ); ?>">
	<button class="menu-toggle" aria-controls="top-menu" aria-expanded="false"><?php echo clubcritiques_get_svg( array( 'icon' => 'bars' ) ); echo clubcritiques_get_svg( array( 'icon' => 'close' ) ); _e( 'Menu', 'clubcritiques' ); ?></button>
    <div class="menu-header-container">
        <ul id="top-menu" class="menu">
            <?php if(is_user_logged_in()){ ?>
            <li id="menu-item-88" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-88">
                <a href="<?php echo getTemplateUrl('MyProfil') ?>">Mon Profil</a>
            </li>
            <?php } ?>
            <li id="menu-item-89" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-89">
                <a href="<?php echo getTemplateUrl('Products') ?>">Liste Produits</a>
            </li>	
            <li id="menu-chat">
                <?php //next Chat
				if($nextChat){
					if ($today >= $startDate) { ?>
						<button type="button" class="btn-chat" data-toggle="modal" data-target="#salonModal">Salon</button>
            </li>
						 <!-- Modal -->
						<div id="salonModal" class="modal fade" role="dialog">
							<div class="modal-dialog modal-chat">

								<!-- Modal content-->
								<div class="modal-content">
									<div class="modal-header">
										<h1 class="chat-title">Salon</h1>
										<button type="button" class="close" data-dismiss="modal">&times;</button>
									</div>
									<div class="modal-body modal-body-chat">
										<?php if($nextChat){
											$chatProduct   = get_field('product', $nextChat->ID)[0]; 
											$averageNote   = Utilisateur::getAverageNote($chatProduct->ID); 
											$productAuthor = get_field('author', $chatProduct->ID)[0]; 
											$image = get_field('image', $chatProduct->ID); 
											?>
											<div class="row">
												<div class="col-md-3">
													<div class="row">
														<a href="<?php echo getTemplateUrl('SingleProduct').$chatProduct->ID; ?>">
															<?php if(!$image){ ?> 
																<div style="background-image: url(<?php echo get_parent_theme_file_uri( '/assets/images/book_defaut.png' ); ?>)" class="book-salon"></div>
															<?php }else{ ?>
																<div class="book-salon" style="background-image: url(<?php echo $image; ?>"></div>
															<?php } ?>
														</a>
													</div>
												</div>
												<div class="col-md-3">
														<div class="row">
															<h2 class="title"><a class="chat-link-modal" href="<?php echo getTemplateUrl('SingleProduct').$chatProduct->ID; ?>"><?php echo $chatProduct->post_title; ?></a></h2>
														</div>

														<div class="row author author-home-modal">
															<a href='<?php echo getTemplateUrl('Author').$productAuthor->ID; ?>'><?php echo $productAuthor->post_title; ?></a>
															[<?php echo get_field('published_date', $chatProduct->ID); ?>]
														</div>

													<div class="row">
														<?php if ( $averageNote['total'] > 0){ ?>
															<span class='star_rating_modal'>
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
															} 
														}?>
													</div>
												</div>
											<?php if ($today < $startDate) { ?>					
												<div class="col-md-6">
													<div class="row">
														<h2 class="h2-timer-modal">Le salon commence dans :</h2>
													</div>	
													<div class="row timer" id="timer" data-timer="<?php echo $time_start_salon; ?>">
													  <span class="time"></span>
													  <span class="time"></span>
													  <span class="time"></span>
													  <span class="time"></span>
													</div>
												</div>
											</div>

											<?php }else{ ?> 					
												<div class="col-md-6">
													<div class="row">
														<h2 class="h2-timer-modal">Le salon fini dans :</h2>
													</div>	
													<div class="row timer" id="timer" data-timer="<?php echo $time_salon; ?>">
													  <span class="time"></span>
													  <span class="time"></span>
													  <span class="time"></span>
													  <span class="time"></span>
													</div>
												</div>
											</div>

										<?php if(!is_user_logged_in()){ ?>
											<div class="row"><p class="mess_not_connect">Veuillez vous connecter avant de rejoindre le salon</p></div>
												
											<?php }elseif(ChatRoom::isUserKicked($nextChat->ID, get_current_user_id())){ ?>
													<p>Vous avez été expulsé du salon</p>
											<?php }elseif(false !== Utilisateur::getNotation($chatProduct->ID, get_current_user_id())){ ?>
												<div class="row col-md-2 col-md-offset-5 join-salon">
													<a class="join-room" href='<?php echo get_permalink($nextChat->ID)?>?changeRoom=true' ><button class="btn">Rejoindre le salon</button></a>
												</div>
												<?php if($userRoom = ChatRoom::getUserRoom($nextChat->ID)){ ?>
													<div class="row col-md-2 join-salon text-center">
														<a class="join-room" href='<?php echo get_permalink($userRoom)?>' ><button class="btn">Rejoindre votre dernier salon</button></a>
													</div>
												<?php } ?>
											<?php }else{ ?>
												<a href='<?php echo getTemplateUrl('SingleProduct').$chatProduct->ID;?>' >Veuillez noter le livre avant de rejoindre un salon</a><br>
											<?php } ?>
										<?php } ?>
									<?php }else{ ?>
										<h1 class="chat-title">Aucun salon programmé pour le moment</h1><br>
									<?php } ?>
									</div>
								</div>
							</div>
						</div>
					<?php } ?>
				<?php } ?>
            </li>
            <li>
                <?php if(!is_user_logged_in()){ ?>
                    <!-- Trigger the modal with a button -->
                    <button type="button" class="btn-login" data-toggle="modal" data-target="#connectionModal">Connexion / Inscription</button>

                    <!-- Modal -->
                    <div id="connectionModal" class="modal fade" role="dialog">
                        <div class="modal-dialog">

                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                    <div class="col-xs-6 col-sm-6">
                                        <form action="" method="POST" class="login">
                                            <p>Connectez vous</p>
                                            <input type='hidden' name='type' value='login'></input>
                                            Email:<input type='text' name='email' placeholder="email"></input><br>
                                            Mot de passe:<input type='password' name='password' placeholder="mot de passe"></input><br>
                                            <button type='submit'>se connecter</button>
                                        </form>
                                    </div>
                                    <div class="col-xs-6 col-sm-6">
                                        <p>Inscrivez vous</p>
                                        <form action="" method="POST">
                                            <input type='hidden' name='type' value='register'></input>
                                            Email:<input required='required' type='text' name='email' placeholder="email"></input><br>
                                            <button type='submit'>S'inscrire</button>
                                        </form>
                                    </div>
                                </div>
                                </div>
                            </div>

                        </div>
                    </div>

                <?php } elseif($_SESSION['activate']) {
                    echo $_SESSION['activate'];
                } else { ?>
                    <form action="" method="POST" class="logout">
                        <input type='hidden' name='type' value='logout'></input>
                        <button type='submit' class="btn-logout">se deconnecter</button>
                    </form>
                <?php } ?>
            </li>
        </ul>
    </div>

	<?php if ( ( clubcritiques_is_frontpage() || ( is_home() && is_front_page() ) ) && has_custom_header() ) : ?>
		<a href="#content" class="menu-scroll-down"><?php echo clubcritiques_get_svg( array( 'icon' => 'arrow-right' ) ); ?><span class="screen-reader-text"><?php _e( 'Scroll down to content', 'clubcritiques' ); ?></span></a>
	<?php endif; ?>
</nav><!-- #site-navigation -->
