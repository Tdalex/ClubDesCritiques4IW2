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
                $nextChat = ChatRoom::getNextChat();
                if($nextChat){
                    $chatProduct = get_field('product', $nextChat->ID)[0];
                    if(!is_user_logged_in()){ ?>
                    <?php }elseif(ChatRoom::isUserKicked($nextChat->ID, get_current_user_id())){ ?>
                    <?php }elseif(false !== Utilisateur::getNotation($chatProduct->ID, get_current_user_id())){ ?>
                        <a href='<?php echo get_permalink($nextChat->ID)?>?changeRoom=true'>Rejoindre un salon</a><br>
                        <?php if($userRoom = ChatRoom::getUserRoom($nextChat->ID)){ ?>
                            <a href='<?php echo get_permalink($userRoom)?>'>Rejoindre votre dernier salon</a><br>
                        <?php }?>
                    <?php }else{ ?>
                        <a href='<?php echo getTemplateUrl('SingleProduct').$chatProduct->ID;?>' >Veuillez noter le livre avant de rejoindre un salon</a><br>
                    <?php }?>
                <?php } ?>
            </li>
            <li>
                <?php if(!is_user_logged_in()){ ?>
                    <!-- Trigger the modal with a button -->
                    <button type="button" class="btn-login" data-toggle="modal" data-target="#myModal">Connexion / Inscription</button>

                    <!-- Modal -->
                    <div id="myModal" class="modal fade" role="dialog">
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
