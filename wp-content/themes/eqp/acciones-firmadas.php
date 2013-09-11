<?php
/*
Template Name: Acciones Firmadas
*/
?>
<?php

    if($_GET['user']){ $usuario = get_userdata( $_GET['user'] ); }
    else { $usuario = $current_user; }
    
    if( is_user_logged_in() && $current_user->ID == $usuario->ID ) { $logueado = true; }
    else { $logueado = false; }
    
    $facebook = get_user_meta($usuario->ID, 'facebook', true);
    $twitter = get_user_meta($usuario->ID, 'twitter', true);
    $licencia = get_user_meta($usuario->ID, 'licencia', true);
    
?>
<?php get_header(); the_post(); ?>

    <section id="content" class="inside" >
            
            <section id="inside-showcased-items">
                <div class="section-header">
                    <div class="userInfoBox clearfix">
                        <div class="usr-avatar-holder grande">
                            <?php echo get_avatar( $usuario->ID, 100 ); ?>
                        </div>
                        <div class="fLeft noMargin userMetaBox">
                            <h1><?php nombre_y_apellido($usuario->ID, true); ?></h1>
                            <p class="userRegistrationDate">Usuario desde el <?php echo date_i18n( "d F, Y", strtotime($usuario->user_registered) ); ?></p>
                            <div class="usr-description"><?php echo apply_filters('the_content', make_clickable( $usuario->user_description )); ?></div>
                            <?php if ( $facebook || $twitter ) : ?>
                                <div class="usr-contact clearfix">
                                    <p>Contactar:</p>
                                    <ul>
                                        <?php
                                            if( $facebook ){ echo '<li><a href="'.$facebook.'" title="Facebook"><img src="'.  get_bloginfo('template_directory') .'/css/ui/ico-facebook-xl.png" alt="Facebook"/></a></li>'; }
                                            if( $twitter ){ echo '<li><a href="'.$twitter.'" title="Twitter"><img src="'.  get_bloginfo('template_directory') .'/css/ui/ico-twitter-xl.png" alt="Twitter"/></a></li>'; }
                                            ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="userActivity" >
                            <span class="titlePill">Contenidos Publicados</span>
                            <ul class="clearfix">
                                <li><span class="entry-publish-s"><?php echo countPostOverTime('post', $usuario->ID); ?></span> Entradas</li>
                                <li><span class="photo-publish-s"><?php echo countPostOverTime('post_fotos', $usuario->ID); ?></span> Fotos</li>
                                <li><span class="video-publish-s"><?php echo countPostOverTime('post_videos', $usuario->ID); ?></span> Videos</li>
                                <li><span class="action-publish-s"><?php echo countPostOverTime('post_acciones', $usuario->ID); ?></span> Acciones</li>
                            </ul>
                        </div>
                    </div>
                    <?php if( $logueado ) : ?>
                        <a id="editBtn" href="#" title="Editar Datos" class="evt action-ca" data-func="showUserEditForm" data-usid="<?php echo $usuario->ID; ?>">Editar Datos</a>
                    <?php endif; ?>
                </div>   
            </section>
            <section class="userVotes userGeneratedContent">
                <?php
                    $accionesAdheridas = my_actions($usuario->ID, 10, 0, $logueado);
                    if( $accionesAdheridas == true ) {
                        echo '<h2>Últimas Acciones Firmadas</h2>';
                        echo '<ul class="article-list regular">';
                        echo $accionesAdheridas;
                        echo '</ul>';
                        echo '<a class="see-more evt" data-func="showMoreUserVotes" data-usid="'. $usuario->ID .'" data-logued="'. $logueado .'" data-offset="0" href="#" title="Cargar Más" rel="subsection">Cargar más</a>';
                    }
                ?>
            </section>
    </section>
</div> <!-- cierra div class="siteWrapper" -->



<?php get_footer()?>