<?php
/*
 * Template Name: Nuevo Perfil de Usuario
 */
get_header(); the_post();

    if( isset($_GET['user']) && is_numeric($_GET['user']) ){ $usuario = get_userdata( intval( $_GET['user'] ) ); }
    elseif( is_user_logged_in() ){
        global $current_user;
        // carga informacion en la variable global $current_user
        get_currentuserinfo();
        $usuario = $current_user;
    }
    // else resdirect ??
    
    $twitter = get_user_meta($usuario->ID, 'twitter', true);
    $facebook = get_user_meta($usuario->ID, 'facebook', true);

?>
        <article>
            <header class="topEntry">
                <div class="row topEntryWrap">
                    <?php echo mobile_breadcrumbs(); ?>
                    <h1 class="perfilTitle column10">
                        <?php the_title(); ?>
                    </h1>
                    <div class="row">
                        <div class="column2 clearfix">
                            <div class="userPerfil">
                                <?php echo get_simple_local_avatar($usuario->ID, 100); ?>   
                            </div>
                            <aside class="userContact">
                                <p class="contUser">Contactar Usuario</p>
                                <ul>      
                                    <li><a class="userMail evt" data-func="contactarUsuario" data-usid="<?php echo $usuario->ID; ?>" href="#">Mensaje</a></li>
                                    <?php
                                        if( $twitter ){ echo '<li><a class="userTwit" href="http://twitter.com/'. $twitter .'" title="Ir a perfil de twitter" rel="external">Twitter</a></li>'; }
                                        if( $facebook ){ echo '<li><a class="userFace" href="'. $facebook .'" title="Ir a perfil de facebook" rel="external">Facebook</a></li>'; }
                                    ?>
                                </ul>
                            </aside>
                        </div>
                        <div class="infoPerfil column10 last">
                            <h2><?php echo nombre_y_apellido( $usuario->ID ); ?></h2>
                            <p class="since">
                                Usuario desde el <?php echo date_i18n( "d F, Y", strtotime($usuario->user_registered) ); ?>
                            <p>
                            <div class="userDesc">
                                <?php echo apply_filters('the_content', make_clickable( $usuario->user_description )); ?>
                            </div>
                            
                            <p class="publiCount">Este usuario ha publicado</p>
                            
                            <ul class="publiCounter">
                                <li class="entryCount"><?php echo mobile_get_user_activity( $usuario->ID, 'post'); ?> Entradas</li>
                                <li class="fotoCount"><?php echo mobile_get_user_activity( $usuario->ID, 'post_fotos'); ?> Fotos</li>
                                <li class="videoCount"><?php echo mobile_get_user_activity( $usuario->ID, 'post_videos'); ?> Videos</li>
                                <li class="accioCount"><?php echo mobile_get_user_activity( $usuario->ID, 'post_acciones'); ?> Acciones</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </header>
            <div class="row entryWrap">
                <div id="holder-column" class="column2">
                    <div id="user-content-type-buttons" class="scroller" >
                        <p class="contUser">Actividad</p>
                        <button class="user-activity-type active evt" data-func="loadUserActivityType" data-contentType="publicado" data-usid="<?php echo $usuario->ID; ?>" title="Ver contenidos publicados" >Contenidos<br>publicados</button>
                        <button class="user-activity-type evt" data-func="loadUserActivityType" data-contentType="porLeer" data-usid="<?php echo $usuario->ID; ?>" title="Ver contenidos por leer" >Contenidos<br>por leer</button>
                        <button class="user-activity-type evt" data-func="loadUserActivityType" data-contentType="leido" data-usid="<?php echo $usuario->ID; ?>" title="Ver contenidos leídos" >Contenidos<br>leídos</button>
                    </div>
                </div>
                <div id="user-content-holder" class="publiContent column10 last">
                    <h2 id="user-content-title">CONTENIDOS PUBLICADOS</h2>
                    <ul id="user-content-list" >
                        <?php mobile_list_contents(array(
                            'autor' => $usuario->ID,
                            'items' => 10,
                            'echo' => true
                        )); ?>
                    </ul>
                    <a id="see-more-content" class="verMas evt" data-func="loadMoreUserContent" data-usid="<?php echo $usuario->ID; ?>" data-offset="10" >Cargar más</a>
                </div>
            </div>
        </article>
     <?php get_footer(); ?>