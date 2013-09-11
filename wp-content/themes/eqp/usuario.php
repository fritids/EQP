<?php
/*
Template Name: Nuevo Perfil de Usuario
*/
 socialFollows();
 
    if( !empty($_POST) && $_POST['usid'] ){ 
        saveUserData(); 
    }

    if($_GET['user']){ $usuario = get_userdata( $_GET['user'] ); }
    else { $usuario = $current_user; }
    
    if( is_user_logged_in() && $current_user->ID == $usuario->ID ) { $logueado = true; }
    else { $logueado = false; }
    
    $facebook = get_user_meta($usuario->ID, 'facebook', true);
    $twitter = get_user_meta($usuario->ID, 'twitter', true);
    
 get_header(); the_post(); ?>

    <section id="content" class="inside" >
            
            <section id="inside-showcased-items">
                <div class="section-header">
                    <div class="userInfoBox clearfix">
                        <div class="usr-avatar-holder grande">
                            <?php echo get_avatar( $usuario->ID, 100 ); ?>
                        </div>
                        <div class="fLeft noMargin userMetaBox">
                            <h1><?php nombre_y_apellido($usuario->ID, true); ?> <?php if( in_array('candidato', (array)$usuario->roles) && get_field( 'partido_candidato', 'user_'.$usuario->ID ) ) { echo '('. get_field( 'partido_candidato', 'user_'.$usuario->ID ) .')'; } ?></h1>
                            <p class="userRegistrationDate">Usuario desde el <?php echo date_i18n( "d F, Y", strtotime($usuario->user_registered) ); ?></p>
                            <div class="usr-description"><?php echo apply_filters('the_content', make_clickable( $usuario->user_description )); ?></div>
                            <div class="usr-contact clearfix">
                                <p>Contactar:</p>
                                <ul>
                                    <?php
                                        if( $facebook ){ echo '<li><a href="'. $facebook .'" title="Facebook"><img src="'.  get_bloginfo('template_directory') .'/css/ui/ico-facebook-xl.png" alt="Facebook"/></a></li>'; }
                                        if( $twitter ){ echo '<li><a href="'. $twitter .'" title="Twitter"><img src="'.  get_bloginfo('template_directory') .'/css/ui/ico-twitter-xl.png" alt="Twitter"/></a></li>'; }
                                        mailUserBtn( $usuario->ID, is_user_logged_in() );
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="userActivity clearfix" >
                        <span class="titlePill">Contenidos Publicados</span>
                        <ul class="clearfix">
                            <li><span class="entry-publish-s"><?php echo countPostOverTime('post', $usuario->ID); ?></span> Entradas</li>
                            <li><span class="photo-publish-s"><?php echo countPostOverTime('post_fotos', $usuario->ID); ?></span> Fotos</li>
                            <li><span class="video-publish-s"><?php echo countPostOverTime('post_videos', $usuario->ID); ?></span> Videos</li>
                            <li><span class="action-publish-s"><?php echo countPostOverTime('post_acciones', $usuario->ID); ?></span> Acciones</li>
                            <li><span class="propuesta-publish-s"><?php echo countPostOverTime('propuestas', $usuario->ID); ?></span> Propuestas</li>
                            <?php if( in_array('candidato', (array)$usuario->roles) ) : ?>
                                <li><span class="propuesta-publish-s"><?php echo countPostOverTime('post_respuestas', $usuario->ID); ?></span> Respuestas</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <?php if( $logueado ) : ?>
                        <a id="editBtn" href="#" title="Editar Datos" class="evt action-ca" data-func="showUserEditForm" data-usid="<?php echo $usuario->ID; ?>">Editar Datos</a>
                    <?php endif; ?>
                </div>   
            </section>
            <section id="searchResultsHolder" class="userPosts userGeneratedContent">
                <?php
                    if( in_array('candidato', (array)$usuario->roles) && !isset($_GET['contenido']) ){
                        $postTypeArg = 'post_respuestas';
                        $format = 'perfilUsuario_respuestas';
                    } else {
                        $postTypeArg = array( 'post', 'post_fotos', 'post_videos', 'post_acciones', 'propuestas' );
                        $format = 'perfilUsuario';
                    }
                    $atts = array(
                        'post_type' => $postTypeArg,
                        'postCount' => 3,
                        'autor' => $usuario->ID,
                        'formato' => $format,
                        'paginate' => true
                    );

                    $resultados = getPostsBlocks($atts);
//                    if( $resultados == true ) {
                        $out = "";
                        if( in_array('candidato', (array)$usuario->roles) ) {
                            $qString_user = isset($_GET['user']) ? '?user='.$_GET['user'] : '';
                            
                            $qString = $_SERVER['QUERY_STRING'] ? '?'. $_SERVER['QUERY_STRING'] : "";
                            if( $qString ){ $qString = $qString. '&contenido=true'; }
                            elseif( $qString == '' ) { $qString = '?contenido=true'; }
                            
                            $respuestasCurrent = 'class="current"';
                            $contenidosCurrent = '';
                            
                            if( isset($_GET['contenido']) ){
                                $respuestasCurrent = '';
                                $contenidosCurrent = 'class="current"';
                            }
                            
                            $out .= '<ul class="menu perfilDeUsuario clearfix">';
                            $out .= '<li '. $respuestasCurrent .'><a href="/perfil-de-usuario/'. $qString_user .'" title="Ver Respuestas Publicadas" rel="contents" >Respuestas Publicadas</a></li>';
                            $out .= '<li '. $contenidosCurrent .'><a href="/perfil-de-usuario/'. $qString .'" title="Ver Contenidos Publicados" rel="contents" >Contenidos Publicados</a></li>';
                            $out .= '</ul>';
                            $out .= '<span id="preTabs" class="clear"></span>';
                        } else {
                            $out .= '<h2>Contenidos Publicados</h2>';
                        }
                        $out .= $resultados ? $resultados : '<em class="noContents">Este Usuario no ha publicado contenidos</em>';
                        echo $out;
//                    }
                ?>
            </section>
            <section class="userVotes userGeneratedContent">
                <?php
                    $qString = $_SERVER['QUERY_STRING'] ? '?'. $_SERVER['QUERY_STRING'] : "";
                    $accionesAdheridas = my_actions($usuario->ID, 3, 0, $logueado);
                    if( $accionesAdheridas ) {
                        echo '<h2>Últimas Acciones Firmadas</h2>';
                        echo '<ul class="article-list regular">';
                        echo $accionesAdheridas;
                        echo '</ul>';
                        echo '<a class="see-more" href="/perfil-de-usuario/acciones-firmadas/'. $qString .'" title="Ver Acciones firmadas por '. nombre_y_apellido($usuario->ID) .'" rel="subsection">Ver Acciones firmadas por '. nombre_y_apellido($usuario->ID) .'</a>';
                    }
                ?>
            </section>
    </section>
</div> <!-- cierra div class="siteWrapper" -->



<?php get_footer()?>