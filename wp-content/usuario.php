<?php
/*
Template Name: Perfil de Usuario
*/
?>
<?php //socialFollows(); ?>
<?php

    if( $_GET['newUser'] == 'true' && !is_user_logged_in() && !$_POST ) {
        crearUsuario($_GET); 
        wp_redirect(home_url() . '/perfil-de-usuario'); exit;
    }
    
    if( $_POST && $_POST['usid'] ){ 
        saveUserData(); 
        $dataSaved = '<div class="message success" >Sus datos han sido guardados con éxito.</div>';
    }

    if($_GET['user']){ $usuario = get_userdata( $_GET['user'] ); }
    else { $usuario = $current_user; }
    
    if( is_user_logged_in() && $current_user->ID == $usuario->ID ) { $logueado = true; }
    else { $logueado = false; }
    
    $facebook = get_user_meta($usuario->ID, 'facebook', true);
    $twitter = get_user_meta($usuario->ID, 'twitter', true);
    $licencia = get_user_meta($usuario->ID, 'licencia', true);
    
?>
<?php get_header(); the_post();?>

<section id="content" class="inside">
            
            <section id="inside-showcased-items">
                <div class="section-header">
                    <h1 class="pseudo-breadcrumb eqp">
                        <?php echo breadcrumb(); ?>
                    </h1>
                    <?php waysToConnect(); ?>
                </div>   
            </section>
            <section id="userContainer" class="single-content-holder">
                <?php if( $_GET['newUser'] == 'true' && !is_user_logged_in() && !$_POST ) :  // cuando el usuario llega por primera vez despues de registrarse ?>
                    
                    <div id="usrInfoShow" class="usr-profile-header clearfix">
                        <h2>¡Gracias por registrarte en El Quinto Poder!</h2>
                        <p>Se ha enviado un e-mail a tu correo con tu contraseña</p>
                        <p>para poder editar tus datos debes</p>
                        <a href="#" class="goHome evt" data-func="showLogin" >Ingresar</a>
                    </div>
                
                <?php else : ?>
                
                    <?php echo $dataSaved; ?>
                    <?php if( $logueado ) : ?>
                    <div id="editUserData" class="mini-call-to-action">
                        <a href="#" title="Editar Datos" class="evt" data-func="editUsrData">Editar Datos</a>
                    </div>
                    <?php endif; ?>

                    <div id="usrInfoShow" class="usr-profile-header clearfix">
                        <div class="usr-avatar-holder">
                            <?php echo get_avatar( $usuario->ID, 40 ); ?>
                        </div>
                        <div class="usr-profile-info" >
                            <h2><?php nombre_y_apellido($usuario->ID, true); ?></h2>
                            <p>Usuario desde el <?php echo date_i18n( "d F, Y", strtotime($usuario->user_registered) ); ?></p>
                            <p class="usr-description"><?php echo $usuario->user_description; ?></p>
                            <?php if ( $facebook || $twitter ) : ?>
                                <div class="usr-contact">
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
                    </div>

                    <?php if( $logueado ) : ?>
                    <div id="usrInfoEdit" class="usr-profile-header">
                        <form id="usrEditForm" method="post" action="" enctype="multipart/form-data">
                            <div class="usr-avatar-holder">
                                <?php echo get_avatar( $usuario->ID, 40 ); ?>
                                <a id="changeAvatarAction" href="#" title="Cambiar Foto" class="evt" data-func="ChangeAvatar">Cambiar Foto</a>
                                <input id="uploadAvatar" type="file" name="simple-local-avatar" >
                            </div>
                            <div class="usr-profile-info">
                                <div id="usrGeneralInfo">                        
                                    <label for="usrFirstName" >Nombre</label>
                                    <input type="text" name="usrFirstName" autocomplete="off" value="" placeholder="<?php echo $usuario->first_name; ?>" >

                                    <label for="usrLastName" >Apellido</label>
                                    <input type="text" name="usrLastName" autocomplete="off" value="" placeholder="<?php echo $usuario->last_name; ?>" >
                                    
                                    <label for="usrEmail" >E-mail</label>
                                    <input type="email" name="usrEmail" autocomplete="off" value="" placeholder="<?php if( !twitterExampleEmail($usuario->ID) ) { echo $usuario->user_email; } ?>" >

                                    <label for="usrDescription" >Descripción</label>
                                    <textarea name="usrDescription" autocomplete="off" placeholder="<?php echo $usuario->user_description; ?>"></textarea>
                                </div>

                                <div id="usrLoginInfo">
                                    <h2>Datos de Acceso</h2>
                                    <label for="usrPass" >Contraseña</label>
                                    <input type="password" autocomplete="off" name="usrPass" value="" placeholder="Escribe tu nueva contraseña" >

                                    <label for="usrPassRepeat" >Repite tu contraseña</label>
                                    <input type="password" autocomplete="off" name="usrPassRepeat" value="" placeholder="Repite tu nueva contraseña" >
                                </div>

                                <div id="usrContactInfo">
                                    <h2>Contacto</h2>
                                    <p>Agrega tu Pefíl en Facebook y tu Timeline en Twitter de modo que los otros usuarios de El Quinto Poder puedan contactarte por otras vías.</p>
                                    <ul>
                                        <li>
                                            <label for="usrFacebook" ><img src="<?php bloginfo('template_directory') ?>/css/ui/ico-facebook-xl.png" alt="Facebook"/></label>
                                            <input id="usrFacebook" type="text" name="usrFacebook" value="" placeholder="<?php echo $facebook; ?>" >
                                        </li>
                                        <li>
                                            <label for="usrTwitter" ><img src="<?php bloginfo('template_directory') ?>/css/ui/ico-twitter-xl.png" alt="Twitter"/></label>
                                            <input id="usrTwitter" type="text" name="usrTwitter" value="" placeholder="<?php echo $twitter; ?>" >
                                        </li>
                                    </ul>
                                </div>

                                <div id="usrActions">
                                    <h2>Acciones a las que adhieres</h2>
                                    <ul>
                                        <?php echo my_actions($usuario->ID, true); ?>
                                    </ul>
                                </div>
                            </div>
                            <input type="hidden" name="usid" value="<?php echo $usuario->ID; ?>">
                        </form>
                    </div>
                    <?php endif; ?>
                
                <?php endif; ?>

                </section>
                <?php// if( $_GET['newUser'] != 'true' ) :  // cuando el usuario llega por primera vez despues de registrarse ?>
    
                    <?php

                        $args = array(
                            'post_type' => 'post',
                            'offset' => 0,
                            'postCount' => 8,
                            'formato' => 'listaChica',
                            'orden' => 'date',
                            'showName' => true,
                            'autor' => $usuario->ID
                        );
                        $entradas = getPostsBlocks($args);

                        $args = array(
                            'post_type' => 'post_fotos',
                            'offset' => 0,
                            'postCount' => 8,
                            'categoria' => false,
                            'typeClass' => 'pict-type',
                            'perfil' => 'usuario',
                            'orden' => 'date',
                            'showName' => true,
                            'autor' => $usuario->ID
                        );
                        $fotos = getPostsBlocks($args);


                        $args = array(
                            'post_type' => 'post_videos',
                            'offset' => 0,
                            'postCount' => 8,
                            'categoria' => false,
                            'typeClass' => 'video-type',
                            'perfil' => 'usuario',
                            'orden' => 'date',
                            'showName' => true,
                            'autor' => $usuario->ID
                        );
                        $videos = getPostsBlocks($args);

                        $args = array(
                            'post_type' => 'post_acciones',
                            'offset' => 0,
                            'formato' => 'listaAcciones',
                            'typeClass' => 'action-type',
                            'perfil' => 'usuario',
                            'postCount' => 8,
                            'showName' => true,
                            'autor' => $usuario->ID
                        );
                        $myActions = getPostsBlocks($args);

                        $myActionsVotes = my_actions_blocks($usuario->ID);

                    ?>

                    <section id="user-content" class="single-content-holder">
                        <?php if ( $entradas || $fotos || $videos || $myActions || $myActionsVotes ) : ?>            
                        <h2 class="label">Contenidos Publicados</h2>
                        <?php endif; ?>

                        <?php if ($entradas) : ?>
                        <section class="the-more entries">
                            <h2 class="label channels-label entry-published-s"><a href="/entradas" title="Entrada">Entradas</a></h2>
                            <ul class="article-list regular">
                                <?php echo $entradas; ?>
                            </ul>
                            <a class="see-more evt" data-author="<?php echo $usuario->ID ?>" data-tab="noTab" data-cat="" data-type="post" title="Ver más Post" data-order="masNuevos" data-func="cargarPost" href="#">Ver más Entradas</a>
                        </section>
                        <?php endif; ?>

                        <?php if ($fotos) : ?>
                        <section class="the-more fotos">
                            <h2 class="label channels-label photo-published-s"><a href="/fotos" title="Fotos">Fotos</a></h2>
                            <ul class="article-list vertical">
                                <?php echo $fotos; ?>
                            </ul>
                            <a class="see-more evt" data-author="<?php echo $usuario->ID ?>" data-tab="noTab" data-cat="" data-type="post_fotos" title="Ver más Fotos" data-order="masNuevos" data-func="cargarPost" href="#">Ver más Fotos</a>
                        </section>
                        <?php endif; ?>

                        <?php if ($videos) : ?>
                        <section class="the-more videos">
                            <h2 class="label channels-label video-published-s"><a href="/videos" title="Videos">Videos</a></h2>
                            <ul class="article-list vertical">
                                <?php echo $videos; ?>
                            </ul>
                            <a class="see-more evt" data-author="<?php echo $usuario->ID ?>" data-tab="noTab" data-cat="" data-type="post_videos" title="Ver más Videos" data-order="masNuevos" data-func="cargarPost" href="#">Ver más Videos</a>
                        </section>
                        <?php endif; ?>

                        <?php if ($myActions) : ?>
                        <section class="the-more actions">
                            <h2 class="label channels-label action-published-s"><a href="/acciones-home" title="Acciones Iniciadas">Acciones Iniciadas</a></h2>
                            <ul class="article-list regular">
                                <?php echo $myActions; ?>
                            </ul>
                            <a class="see-more evt" data-author="<?php echo $usuario->ID ?>" data-tab="noTab" data-cat="" data-type="post_acciones" title="Ver más Acciones" data-order="masNuevos" data-func="cargarPost" href="#">Ver más Acciones</a>
                            
                        </section>
                        <?php endif; ?>
                        <?php if ($myActionsVotes) : ?>
                        <section class="the-more actions">
                            <h2 class="label channels-label action-published-s"><a href="/acciones-home" title="Acciones a las que ha adherido">Acciones a las que ha adherido</a></h2>
                            <ul class="article-list regular">
                                <?php echo $myActionsVotes; ?>
                            </ul>
                            <a class="see-more evt" data-author="<?php echo $usuario->ID ?>" data-tab="noTab" data-cat="" data-type="post_acciones" title="Ver más Acciones" data-order="masNuevos" data-func="cargarPost" href="#">Ver más Acciones</a>
                        </section>
                        <?php endif; ?>
                    <?php// endif; ?>
                
                
            </section>
    
    </section>
</div>



<?php get_footer()?>