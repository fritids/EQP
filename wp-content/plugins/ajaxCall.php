<?php

/*
Plugin Name: Ajax by Ida
Plugin URI: http://ida.cl
Description: Desarrollo de funciones ajax para wordpress
Version: 1.0
Author: Ideas Digitales plicadas
Author URI: http://ida.cl
License: Open Source
*/


function ajax_zone() {
    global $wpdb, $offsetPost;
    
    if( $_POST['func'] == 'getTab' ) {
        if ($_POST['category']) { $cat = $_POST['category']; }
        else { $cat = false; }
        if ($_POST['pType']) { $postType = $_POST['pType']; }
        else { $postType = false; }
       
        
        if($_POST['home'] == 'home'){
            die(get_postTabs($_POST['order'], $cat, $postType, $temas = false));
        }
        else{
            die(get_postTabs($_POST['order'], $cat, $postType, $temas = true));
        }
        
    }
    elseif( $_POST['func'] == 'accionesTabs' ) {
         if ($_POST['category']) { $cat = $_POST['category']; }
        else { $cat = false; }
        
        die(get_ActionTabs($_POST['order'],0,false,$cat));
    }
    elseif( $_POST['func'] == 'getEntradasTabs' ) {
        die(get_entradas_postTabs($_POST['order']));
    }
    elseif( $_POST['func'] == 'retrievePass' ) {
        $user = get_user_by('email', $_POST['email']);
        $newPass = wp_generate_password(8, false);
        
        $userdata = array(
            'ID' => $user->ID,
            'user_pass' => $newPass
        );
        $userCheck = wp_update_user($userdata);
        
        if(!is_wp_error($userCheck)){
        
            $titulo = 'Recuperación de Clave';
            $subtitulo = 'Se te ha asignado una nueva clave para poder ingresar a El Quinto Poder';
            $contenido .= '<p>Tu nombre de usuario es:</p>';
            $contenido .= '<p><strong>'. $user->user_login.'</strong></p>';
            $contenido .= '<p>Tu nueva clave es:</p>';
            $contenido .= '<p><strong>'. $newPass .'</strong></p>';
            $contenido .= '<p><em>Recuerda cambiarla a la brevedad</em></p>';
            $contenido .= '<p>Saludos,</p>';
            $contenido .= '<p>Equipo El Quinto Poder</p>';
            $contenido .= '<p><a href="'. home_url() .'" title="El Quinto poder">'. home_url() .'</a></p>';
            $subject = 'Recuperación de Clave';
            $from = 'noreply@elquintopoder.cl';
            $destino = $user->user_email;

            mailMe($titulo, $subtitulo, $contenido, $subject, $from, $destino);
            
            $out = '<div class="message success"><p>Hemos enviado una nueva clave a tu correo electrónico</p></div>';
            if($_POST['message'] == 'si'){
                $out = 'Hemos enviado una nueva clave a tu correo electrónico';
            }
            die($out);
        }
        else {
            $out = '<div class="message fail"><p>La cuenta de correo electrónico indicada no está registrada en nuestro sitio</p></div>';
            if($_POST['message'] == 'si'){
                $out = 'La cuenta de correo electrónico indicada no está registrada en nuestro sitio';
            }
            die($out);
        }
    }
    elseif( $_POST['func'] == 'notificarComentario' ) {
        
        if( is_user_logged_in() || $_POST['author'] == 0 ) { 
            $autor = wp_get_current_user();
            $autor = nombre_y_apellido($autor->ID);
        }
        else { $autor = $_POST['author']; }
        
        $thepost = get_post($_POST['pid']);
        $postAuthor = get_userdata($thepost->post_author);
        
        if( $thepost->post_type == 'post' ) { $prefix = 'la'; $tipoPost = 'entrada'; }
        elseif( $thepost->post_type == 'post_fotos' ) { $prefix = 'la'; $tipoPost = 'foto'; }
        elseif( $thepost->post_type == 'post_videos' ) { $prefix = 'el'; $tipoPost = 'video'; }
        elseif( $thepost->post_type == 'post_acciones' ) { $prefix = 'la'; $tipoPost = 'acción'; }
        
        $titulo = 'Servicio de Comentarios';
        $subtitulo = 'Uno de sus Contenidos ha sido comentado';
        $contenido = '<p><strong>'. $autor .'</strong> ha comentado en tu '. $tipoPost .' <strong>"'. $thepost->post_title .'"</strong></p>';
        $contenido .= '<p><a href="'. get_permalink($thepost->ID) .'" title="'. $thepost->post_title .'">'. get_permalink($thepost->ID) .'</a></p>';
        $contenido .= '<p>Saludos,</p>';
        $contenido .= '<p>Equipo El Quinto Poder</p>';
        $contenido .= '<p><a href="'. home_url() .'" title="El Quinto poder">'. home_url() .'</a></p>';
        $subject = 'Nuevo Comentario';
        $from = 'comentarios@elquintopoder.cl';
        $destino = $postAuthor->user_email;
        
        mailMe($titulo, $subtitulo, $contenido, $subject, $from, $destino);
        
        $titulo = 'Servicio de Comentarios';
        $subtitulo = 'Un contenido ha sido comentado';
        $contenido = '<p><strong>'. $autor .'</strong> ha comentado en '. $prefix .' '. $tipoPost .' <strong>"'. $thepost->post_title .'"</strong></p>';
        $contenido .= '<p><a href="'. get_permalink($thepost->ID) .'" title="'. $thepost->post_title .'">'. get_permalink($thepost->ID) .'</a></p>';
        $contenido .= '<p>Saludos,</p>';
        $contenido .= '<p>Equipo El Quinto Poder</p>';
        $contenido .= '<p><a href="'. home_url() .'" title="El Quinto poder">'. home_url() .'</a></p>';
        $subject = 'Nuevo Comentario';
        $from = 'comentarios@elquintopoder.cl';
        $destino = get_bloginfo('admin_email');
        
        mailMe($titulo, $subtitulo, $contenido, $subject, $from, $destino);
        mailMe($titulo, $subtitulo, $contenido, $subject, $from, 'c.rodriguez@fdd.cl');
        mailMe($titulo, $subtitulo, $contenido, $subject, $from, 'eabbagliati@fdd.cl');
        mailMe($titulo, $subtitulo, $contenido, $subject, $from, 'minfante@fdd.cl');
        mailMe($titulo, $subtitulo, $contenido, $subject, $from, 'xjara@fdd.cl');
        
        die("$thepost->comment_count");
    }
    elseif ( $_POST['func'] == 'verMasPostPortadas' ) {
        $args = array(
            'post_type' => $_POST['postType'],
            'offset' => $_POST['offset'],
            'postCount' => 4,
            'formato' => 'lista',
            'typeClass' => $_POST['imgType']
        );
        $out = getPostsBlocks($args);
        die($out);
    }
    elseif ( $_POST['func'] == 'verMasPostAcciones' ) {
        $out = get_ActionTabs($_POST['orden'], $_POST['offset'], true);
        die($out);
    }
    elseif ( $_POST['func'] == 'verMasPostEspeciales' ) {
        $args = array(
            'post_type' => 'especiales',
            'offset' => $_POST['offset'],
            'postCount' => 4,
            'formato' => 'portada'
        );
        $out = getPostsBlocks($args);
        die($out);
    }
    elseif( $_POST['func'] == 'cargarPostTema'){
        
        $order = $_POST['order'];
        $category = $_POST['category'];
        $offset = $_POST['offset'];
        $postType = $_POST['postType'];
        $tab = $_POST['tab'];
        $author = $_POST['author'];

        if($tab == 'masActivos'){
            $argsEntradas = array(
                'post_type' => $postType,
                'order' => 'DESC',
                'posts_per_page' => 4,
                'meta_key' => '_total',
                'orderby' => 'meta_value_num',
                'category_name' => $category,
                'offset' => $offset,
            );


            $entradasQuery = new WP_Query($argsEntradas);

            if($postType == 'post'){
                if($entradasQuery->have_posts()) {

                         while($entradasQuery->have_posts()) { $entradasQuery->the_post();

                            $theMetaData = get_query_post_metadata($entradasQuery);

                            $out .= '<li class="clearfix">';
                            $out .= '<div class="article-holder">';
                            $out .= '<a href="'. get_permalink() .'" title="Ir a '. get_the_title() .'">'. get_the_title() .'</a>';
                            $out .= '<div class="item-metadata">';
                            $out .= '<div class="usr-avatar-holder">';
                            $out .= $theMetaData['avatar'];
                            $out .= '</div>';
                            $out .= $theMetaData['authorMeta'];
                            $out .= '</div>';
                            $out .= '</div>';
                            $out .= $theMetaData['echoes'];
                            $out .= '</li>';

                        }
                  }
            }elseif($postType == 'post_fotos'){
                  if($entradasQuery->have_posts()) {

                         while($entradasQuery->have_posts()) { $entradasQuery->the_post();

                            $theMetaData = get_query_post_metadata($fotosQuery);

                            $out .= '<li class="clearfix">';
                            $out .= get_the_post_thumbnail($entradasQuery->post->ID, 'listaChica');
                            $out .= '<a href="'. get_permalink() .'" class="identifyer" title="Ir a '. get_the_title() .'">Ver</a>';
                            $out .= '<a href="'. get_permalink() .'" title="Ir a '. get_the_title() .'" >'. get_the_title() .'</a>';
                            $out .= '<div class="item-metadata">';
                            $out .= $theMetaData['authorMeta'];
                            $out .= '</div>';
                            $out .= $theMetaData['echoes'];
                            $out .= '</li>';

                        }

                    }
            }elseif($postType == 'post_videos'){
                if($entradasQuery->have_posts()) {

                    while($entradasQuery->have_posts()) { $entradasQuery->the_post();

                        $theMetaData = get_query_post_metadata($entradasQuery);

                        $thumbnail = get_the_post_thumbnail($entradasQuery->post->ID, 'listaChica') ? get_the_post_thumbnail($entradasQuery->post->ID, 'listaChica') : '<img class="video-thumbnail lista" src="'. get_video_thumbnail($entradasQuery->post->ID) .'" alt="'. get_the_title() .'" />'; 

                        $out .= '<li class="clearfix">';
                        $out .= $thumbnail;
                        $out .= '<a href="'. get_permalink() .'" class="identifyer" title="Ir a '. get_the_title() .'">Ver</a>';
                        $out .= '<a href="'. get_permalink() .'" title="Ir a '. get_the_title() .'" >'. get_the_title() .'</a>';
                        $out .= '<div class="item-metadata">';
                        $out .= $theMetaData['authorMeta'];
                        $out .= '</div>';
                        $out .= $theMetaData['echoes'];
                        $out .= '</li>';
                    }


                }
            }elseif($postType == 'post_acciones'){
                if($entradasQuery->have_posts()) {

                    while($entradasQuery->have_posts()) { $entradasQuery->the_post();

                        $theMetaData = get_query_post_metadata($entradasQuery);
                        $tenemos = number_format( get_action_votes($entradasQuery->post->ID)*1 , 0 , ',' , '.' );
                        $necesitamos = number_format( get_field('requeridos', $entradasQuery->post->ID)*1 , 0 , ',' , '.' );

                        $out .= '<li class="clearfix">';
                        $out .= '<div class="article-holder">';
                        $out .= '<a href="'. get_permalink() .'">'. get_the_title() .'</a>';
                        $out .= '<div class="item-metadata">';
                        $out .= '<div class="usr-avatar-holder">';
                        $out .= $theMetaData['avatar'];
                        $out .= '</div>';
                        $out .= $theMetaData['authorMeta'];
                        $out .= '</div>';
                        $out .= '</div>';
                        $out .= '<ul class="actions-board resumed">';
                        $out .= '<li>Tenemos <span>'. $tenemos .'</span></li>';
                        $out .= '<li><a href="'. get_permalink() .'">Necesitamos <span>'. $necesitamos .'</span></a></li>';
                        $out .= '</ul>';
                        $out .= '</li>';
                    }


                }
            }
        }elseif($tab == 'masNuevos'){
            
            $lista = 'lista';
            if($postType == 'post' ){
                $lista = 'listaChica';
            }elseif($postType == 'post_acciones'){
                $lista = 'listaAccionesTab';              
            }
            
            $args = array(
            'post_type' => $postType,
            'offset' => $offset,
            'postCount' => 4,
            'formato' => $lista,
            'orden' => 'date',
            'categoria' => $category
        );
         
             if($lista == '')
                unset($argsPerfil['formato']);
            $out .= getPostsBlocks($args);
        }elseif($tab == 'noTab'){
           $lista = '';
           if($postType == 'post' ){$lista = 'listaChica';}
            
            $argsPerfil = array(
                'post_type' => $postType,
                'offset' => $offset,
                'postCount' => 4,
                'formato' => $lista,
                'orden' => 'date',
                'autor' => $author
            );
            
            if($lista == '')
                unset($argsPerfil['formato']);
            $out .= getPostsBlocks($argsPerfil); 
        }
        die($out);
        
    }
    elseif ( $_POST['func'] == 'anadirVisita' ) {
//        if( function_exists('w3tc_dbcache_flush') ) w3tc_dbcache_flush();
        if( function_exists('w3tc_objectcache_flush') ) w3tc_objectcache_flush();
        $views = get_post_meta($_POST['postid'], '_visitas', true) + 1;
        update_post_meta($_POST['postid'], '_visitas', $views);
        calculateActivity($_POST['postid']);
        $aryview = array("visitas" => $views);
        die( json_encode($aryview));
    }
    elseif ( $_POST['func'] == 'anadirShare' ) {
        $provider = $_POST['provider'];
        
        $prev_shares = get_post_meta($_POST['postid'], '_shares_'. $provider, true);
        
        if ( $prev_shares == '' ) { $prev_shares = 0; $prev_shares++; }
        else { $prev_shares = $prev_shares +1; }
        
        $new_shares = $prev_shares;
        update_post_meta($_POST['postid'], '_shares_'. $provider, $new_shares);
        
        calculateActivity($_POST['postid']);
        
        die("$new_shares");
        
    }
    elseif( $_POST['func'] == 'regularLogin' || $_POST['func'] == 'ajaxLogin' ) {
        $creds = array(
            'user_login' => $_POST['usrName'],
            'user_password' => $_POST['usrPass'],
            'remember' => true
        );
        
        $user = wp_signon( $creds, false );
        
        if ( is_wp_error($user) ) { die("error"); }
        else { die("logueado"); }
    }
    elseif( $_POST['func'] == 'logOut' ) {
        session_destroy();        
        wp_logout();
        die("deslogueado");
    }
    elseif( $_POST['func'] == 'actionSignature' ) {
        $out = "";
        
        $camposRequeridos = get_post_meta(intval( $_POST['postid'] ), 'campos_requeridos', true); 
        
        if( $_POST['user'] != 0 ){
            $user = get_userdata($_POST['user']);
            $out .= '<div id="usr-login-holder">';
            $out .= '<div id="usr-login-alts" class="clearfix">';
            $out .= '<p style="margin-bottom: 20px;">Estas logueado como <strong>'. nombre_y_apellido($user->ID) .'</strong></p>';
            if( !empty($camposRequeridos ) ){
                $out .= '<form id="voteActionLogued" class="clearfix" action="" method="post" >';
                foreach( $camposRequeridos as $theCampo ){
                    $out .= '<label for="log_action_requirements_'. $theCampo .'">'. ucfirst( $theCampo ) .'</label>';
                    $out .= '<input type="text" required id="log_action_requirements_'. $theCampo .'" name="action_requirements['. $theCampo .']" placeholder="Ingrese su '. $theCampo .'" data-inputtype="'. $theCampo .'" >';
                }
                $out .= '</form>';
            }
            $out .= '<a id="firmarLogueado" href="#" title="Firmar Acción" class="ganalytics evtjs goHome" data-ga-category="Participacion" data-ga_action="Firmas" data-ga_opt_label="BtnFirmas_Registrado" data-func="voteForAction" data-userid="'. $user->ID .'" data-username="'. nombre_y_apellido($user->ID) .'" data-useremail="'. $user->user_email .'" data-postid="'. $_POST['postid'] .'">Firmar Acción</a>';
            $out .= '</div>';
            $out .= '</div>';
        }
        else {
            $out .= '<div class="clearfix inLightBox">';
            
            $out .= '<p class="explicative">Para adherir a la acción puedes hacerlo con tu cuenta en la comunidad o simplemente usando tu nombre y tu correo</p>';
            $out .= '<div class="col">';
            $out .= '<form id="ajaxLoginForm" class="clearfix" action="" method="post" >';
            $out .= '<p class="main-Titles">¿Ya estás registrado? <br> Ingresa Aqui</p>';
            $out .= '<p>¿Tienes cuenta en la comunidad? Ingresa y firma aquí.</p>';
            $out .= '<label for="log_usrName">Email</label>';
            $out .= '<input type="email" name="usrName" id="log_usrName" placeholder="Email" required >';
            $out .= '<label for="log_usrPass">Clave</label>';
            $out .= '<input type="password" name="usrPass" id="log_usrPass" placeholder="Clave" required >';
            $out .= '<a href="#" title="¿Olvidaste tu clave?">¿Olvidaste tu clave?</a>';
            if( !empty($camposRequeridos ) ){
                foreach( $camposRequeridos as $theCampo ){
                    $out .= '<label for="log_action_requirements_'. $theCampo .'">'. ucfirst( $theCampo ) .'</label>';
                    $out .= '<input type="text" required id="log_action_requirements_'. $theCampo .'" name="action_requirements['. $theCampo .']" placeholder="Ingrese su '. $theCampo .'" data-inputtype="'. $theCampo .'" >';
                }
            }
            $out .= '<a href="#" title="Ingresar" class="action-ca evtjs ganalytics" data-ga-category="Participacion" data-ga_action="Firmas" data-ga_opt_label="BtnFirma_SinRegIngresar" data-func="ajaxLoginAndVote" >Ingresar y Firmar</a> ';
            $out .= '<ul class="allogin clearfix">';
            $out .= '<li class="twitterLogin"><a class="evtjs" data-func="openLightBox" data-width="560" data-height="800"  href="/?authenticate=1">Twitter</a></li>';
            $out .= '<li class="facebookLogin" ><a class="evtjs" data-func="openLightBox" data-width="430" data-height="330" href="/?loginfacebook=1">Facebook</a></li>';
            $out .= '<li class="googleLogin"><a class="evtjs" data-func="openLightBox" data-width="430" data-height="330" href="/?logingoogle=1">Google</a></li>';
            $out .= '</ul>';  
            $out .= '</form>';
            $out .= '</div>';
            
            $out .= '<div class="col ultima">';
            $out .= '<form id="ajaxVoteForm" class="clearfix" action="" method="post" >';
            $out .= '<p class="main-Titles">¿No deseas registrarte? <br>Firma tu adhesión</p>';
            $out .= '<p>¿No tienes cuenta en la comunidad? Adhiere poniendo tus datos aquí. No crearás una cuenta en elquintopoder.cl pero mostrarás tu apoyo.</p>';
            $out .= '<label for="no_log_usrName">Nombre Completo</label>';
            $out .= '<input type="text" name="usrName" id="no_log_usrName" placeholder="Nombre Completo" required >';
            $out .= '<label for="no_log_usrEmail">Email</label>';
            $out .= '<input type="email" name="usrEmail" id="no_log_usrEmail" value="" placeholder="Email" required >';
            if( !empty($camposRequeridos ) ){
                foreach( $camposRequeridos as $theCampo ){
                    $out .= '<label for="no_log_action_requirements_'. $theCampo .'">'. ucfirst( $theCampo ) .'</label>';
                    $out .= '<input type="text" required id="no_log_action_requirements_'. $theCampo .'" name="action_requirements['. $theCampo .']" placeholder="Ingrese su '. $theCampo .'" data-inputtype="'. $theCampo .'" >';
                }
            }
            $out .= '<a href="#" title="Registrar" class="action-ca evtjs ganalytics" data-ga-category="Participacion" data-ga_action="Firmas" data-ga_opt_label="BtnFirma_SinRegFirmar" data-func="voteForActionNoLogued" >Firmar</a> ';
            $out .= '</form>';
            $out .= '</div>';
            
            $out .= '</div>';
        }
        
        die($out);
    }
    elseif( $_POST['func'] == 'ajaxLoginAndVote' ) {
        
        $creds = array(
            'user_login' => $_POST['usrName'],
            'user_password' => $_POST['usrPass'],
            'remember' => true
        );
        
        $user = wp_signon( $creds, false );
        
        $thePost = get_post( $_POST['postid'] );
        
        $output = array(
            'status' => 'ok',
            'voteNum' => '',
            'socialUl' => '',
            'titulo' => $thePost->post_title
        );
        
        if ( is_wp_error($user) ) {
            $output['status'] = 'error';
        }
        else { 
            $nombre = nombre_y_apellido($user->ID);
            $voto = accionesInsert($_POST['postid'], $user->user_email, $nombre, $user->ID, $_POST['action_requirements']);
            
            if ($voto) {
                $votosFaltan = ( intval( get_field('requeridos', $_POST['postid']) ) - intval( get_action_votes($_POST['postid']) ) );
                $fbMessage = $nombre .' ha firmado la acción '. $output['titulo'] .' en elquintopoder.cl, aún nos faltan '. $votosFaltan .' firmas para cumplir la meta';
                
                $socialHtml = '<ul class="social-echoes">';
                $socialHtml .= get_socialEchoes(get_permalink( $_POST['postid'] ), $thePost->post_title , $_POST['postid'], true, false, $fbMessage);
                $socialHtml .= '</ul>';
                
                $output['socialUl'] = $socialHtml;
                $output['voteNum'] = get_action_votes($_POST['postid']);
                
            }
            else { $output['status'] = 'repetido'; }
        }
        die( json_encode($output) );
    }
    elseif( $_POST['func'] == 'voteForActionNoLogued' ) {
        $voto = accionesInsert($_POST['postid'], $_POST['usrEmail'], $_POST['usrName'], 0, $_POST['action_requirements'] );
        
        $thePost = get_post( $_POST['postid'] );
        $output = array(
            'status' => 'ok',
            'voteNum' => '',
            'socialUl' => '',
            'titulo' => $thePost->post_title
        );
        
        if ($voto) {
            $votosFaltan = ( intval( get_field('requeridos', $_POST['postid']) ) - intval( get_action_votes($_POST['postid']) ) );
            $fbMessage = $_POST['usrName'] .' ha firmado la acción '. $output['titulo'] .' en elquintopoder.cl, aún nos faltan '. $votosFaltan .' firmas para cumplir la meta';
            
            $socialHtml = '<ul class="social-echoes">';
            $socialHtml .= get_socialEchoes(get_permalink( $_POST['postid'] ), $thePost->post_title , $_POST['postid'], true, false, $fbMessage);
            $socialHtml .= '</ul>';
            
            $output['socialUl'] = $socialHtml;
            $output['voteNum'] = get_action_votes($_POST['postid']);
        }
        else { $output['status'] = 'repetido'; }
        die( json_encode($output) );
    }
    elseif( $_POST['func'] == 'voteForActionLogued' ) {
        $voto = accionesInsert($_POST['postid'], $_POST['usrMail'], $_POST['usrName'], $_POST['usrID'], $_POST['action_requirements']);
        
        $thePost = get_post( $_POST['postid'] );
        $output = array(
            'status' => 'ok',
            'voteNum' => '',
            'socialUl' => '',
            'titulo' => $thePost->post_title
        );
        
        if ($voto) {
            $votosFaltan = ( intval( get_field('requeridos', $_POST['postid']) ) - intval( get_action_votes($_POST['postid']) ) );
            $fbMessage = $_POST['usrName'] .' ha firmado la acción '. $output['titulo'] .' en elquintopoder.cl, aún nos faltan '. $votosFaltan .' firmas para cumplir la meta';
            
            $socialHtml = '<ul class="social-echoes">';
            $socialHtml .= get_socialEchoes(get_permalink( $_POST['postid'] ), $thePost->post_title , $_POST['postid'], true, false, $fbMessage);
            $socialHtml .= '</ul>';
            
            $output['socialUl'] = $socialHtml;
            $output['voteNum'] = get_action_votes($_POST['postid']);
            
            }
        else { $output['status'] = "repetido"; }
        
        die( json_encode( $output ) );
    }
    
    elseif( $_POST['func'] == 'showPublishForm' ) {
        $out = "";
        $author = $_POST['author'];
        $postType = $_POST['pType'];
        
        $user = get_userdata($_POST['author']);
        
        if( $postType == 'post' ) {
            $out .= '<div id="succsessResponse" class="clearfix inLightBox">';
            $out .= '<h2>Hola '. nombre_y_apellido($user->ID) .'</h2>';
            $out .= '<p>A través de este formulario podrás subir tu entrada a nuestra comunidad. Solo publicaremos entradas que sean de tu autoría.</p>';

            $out .= '<form id="publicationForm" action="" method="post" enctype="multipart/form-data" >';

            $out .= '<label for="postTitle">Título de tu Entrada (obligatorio)</label>';
            $out .= '<input id="postTitle" type="text" maxlength="70" name="postTitle" value="" placeholder="Título" required>';
            $out .= '<span class="formHelp">Máximo de 70 caracteres, quedan <span class="charCont">70</span> caracteres.</span>';
            
            $out .= '<label for="postContent">Texto (obligatorio)</label>';
            $out .= '<textarea id="postContentBox" name="postContent" placeholder="Descripción de la Entrada" required data-type="textarea" ></textarea>';
            $out .= '<span class="formHelp">Sugerimos un máximo de 3 mil carácteres</span>';
            $out .= '<label for="postCategory">¿En qué tema quieres publicar el contenido? (obligatorio)</label>';
            $out .= '<select name="postCategory" data-type="select" required>';
            $out .= '<option value="" >Selecciona</option>';
            $out .= categoryOptions();
            $out .= '</select>';

            $out .= '<label for="postBajada">Bajada (opcional)</label>';
            $out .= '<input type="text" name="postBajada" value="" placeholder="Bajada" >';
            $out .= '<span class="formHelp">Sugerimos un máximo de 300 carácteres</span>';

            $out .= '<label for="postDestacado">Destacado (opcional)</label>';
            $out .= '<textarea id="postDestacado" name="postDestacado" data-type="textarea" ></textarea>';
            $out .= '<span class="formHelp">Sugerimos un máximo de 300 carácteres</span>';

            $out .= '<label for="fotoUpload">Foto de tu Entrada (opcional)</label>';
            $out .= '<input type="file" name="fotoUpload" >';

            $out .= '<label for="videoUpload">Video de tu Entrada (opcional)</label>';
            $out .= '<p>Puedes subir video insertando la URL del video alojado en You Tube, Vimeo o Daily Motion.</p>';
            $out .= '<input type="text" name="videoUpload" value="" placeholder="http://www.youtube.com/watch?v=txqiwrbYGrs">';

            $out .= '<p class="checkboxCont">';
            $out .= '<span><input type="checkbox" name="termsConditions" value="acepto" required ></span>';
            $out .= '<label for="termsConditions">Acepto los <a href="http://www.elquintopoder.cl/terminos-de-uso/" title="Términos y Condiciones" >Términos y Condiciones</a></label>';
            $out .= '</p>';

            $out .= '<input type="hidden" name="postType" value="post">';
            $out .= '<input type="hidden" name="action" value="ajax_zone">';
            $out .= '<input type="hidden" name="func" value="publicarCosas">';
            $out .= '<input type="hidden" name="postStatus" value="draft">';
            $out .= '<input type="hidden" name="postAuthor" value="'. $author .'">';

            $out .= '<input id="SendStuff" type="submit" value="Enviar a Edición" class="goHome gac" data-goal="btn-publicar-entrada">';

            $out .= '</form>';
            $out .= '</div>';
        }
        elseif( $postType == 'post_fotos' ) {
            $out .= '<div id="succsessResponse" class="clearfix inLightBox">';
            $out .= '<h2>Hola '. nombre_y_apellido($user->ID) .'</h2>';
            $out .= '<p>A través de este formulario podrás subir una foto a nuestra comunidad. Si no es una foto original, te recomendamos cites al autor y la licencia que te permite compartirla.</p>';

            $out .= '<form id="publicationForm" action="" method="post" enctype="multipart/form-data" >';

            $out .= '<label for="postTitle">Título de la Foto (obligatorio)</label>';
            $out .= '<input id="postTitle" type="text" maxlength="70" name="postTitle" value="" placeholder="Título" required>';
            $out .= '<span class="formHelp">Máximo de 70 caracteres, quedan <span class="charCont">70</span> caracteres.</span>';
            
            $out .= '<label for="postCategory">¿En qué tema quieres publicar la foto? (obligatorio)</label>';
            $out .= '<select name="postCategory" required data-type="select" >';
            $out .= '<option value="" >Selecciona</option>';
            $out .= categoryOptions();
            $out .= '</select>';

//                $out .= '<p><strong>Origen del Contenido</strong></p>';
//                $out .= '<ul>';
//                $out .= '<li>';
//                $out .= '<span><input type="radio" id="fromPc" name="origenFoto" value="fromPc" ></span>';
//                $out .= '<label for="fromPc">Desde archivo en tu computador</label>';
//                $out .= '</li>';
//                $out .= '<li>';
//                $out .= '<span><input type="radio" id="fromUrl" name="origenFoto" value="fromUrl" ></span>';
//                $out .= '<label for="fromUrl">Desde URL externa</label>';
//                $out .= '</li>';
//                $out .= '</ul>';

            $out .= '<label for="fotoUpload">Selecciona tu Foto</label>';
            $out .= '<input type="file" name="fotoUpload" required >';

            $out .= '<label for="postContent">Descripción de tu foto (opcional)</label>';
            $out .= '<textarea id="postContentBox" name="postContent" placeholder="Descripción de la Foto" data-type="textarea" ></textarea>';

            $out .= '<p class="checkboxCont">';
            $out .= '<span><input type="checkbox" name="termsConditions" value="acepto" required ></span>';
            $out .= '<label for="termsConditions">Acepto los <a href="http://www.elquintopoder.cl/terminos-de-uso/" title="Términos y Condiciones" >Términos y Condiciones</a></label>';
            $out .= '</p>';

            $out .= '<input type="hidden" name="postType" value="post_fotos">';
            $out .= '<input type="hidden" name="action" value="ajax_zone">';
            $out .= '<input type="hidden" name="func" value="publicarCosas">';
            $out .= '<input type="hidden" name="postStatus" value="publish">';
            $out .= '<input type="hidden" name="postAuthor" value="'. $author .'">';

            $out .= '<input id="SendStuff" type="submit" value="Publicar" class="goHome gac" data-goal="btn-publicar-foto">';

            $out .= '</form>';
            $out .= '</div>';
        }
        elseif( $postType == 'post_videos' ) {
            $out .= '<div id="succsessResponse" class="clearfix inLightBox">';
            $out .= '<h2>Hola '. nombre_y_apellido($user->ID) .'</h2>';
            $out .= '<p>A través de este formulario podrás compartir un video en nuestra comunidad, subiendo la URL (dirección en Internet) donde está alojado el video.</p>';

            $out .= '<form id="publicationForm" action="" method="post" enctype="multipart/form-data" >';

            $out .= '<label for="postTitle">Título del Video (obligatorio)</label>';
            $out .= '<input id="postTitle" type="text" maxlength="70" name="postTitle" value="" placeholder="Título" required>';
            $out .= '<span class="formHelp">Máximo de 70 caracteres, quedan <span class="charCont">70</span> caracteres.</span>';
            
            $out .= '<label for="postCategory">¿En qué tema quieres publicar el video? (obligatorio)</label>';
            $out .= '<select name="postCategory" required data-type="select" >';
            $out .= '<option value="" >Selecciona</option>';
            $out .= categoryOptions();
            $out .= '</select>';

            $out .= '<p>Inserta la URL (dirección de Internet) del video</p>';
            $out .= '<input type="text" name="videoUpload" value="" required placeholder="http://www.youtube.com/watch?v=txqiwrbYGrs">';

            $out .= '<label for="postContent">Descripción de tu video (opcional)</label>';
            $out .= '<textarea id="postContent" name="postContent" placeholder="Descripción de la Video" data-type="textarea" ></textarea>';

            $out .= '<p class="checkboxCont">';
            $out .= '<span><input type="checkbox" name="termsConditions" value="acepto" required ></span>';
            $out .= '<label for="termsConditions">Acepto los <a href="http://www.elquintopoder.cl/terminos-de-uso/" title="Términos y Condiciones" >Términos y Condiciones</a></label>';
            $out .= '</p>';

            $out .= '<input type="hidden" name="postType" value="post_videos">';
            $out .= '<input type="hidden" name="action" value="ajax_zone">';
            $out .= '<input type="hidden" name="func" value="publicarCosas">';
            $out .= '<input type="hidden" name="postStatus" value="publish">';
            $out .= '<input type="hidden" name="postAuthor" value="'. $author .'">';

            $out .= '<input id="SendStuff" type="submit" value="Publicar" class="goHome gac" data-goal="btn-publicar-video">';

            $out .= '</form>';
            $out .= '</div>';
        }
        elseif ( $postType == 'post_acciones' ) {
            $out .= '<div id="succsessResponse" class="clearfix inLightBox">';
            $out .= '<h2>Hola '. nombre_y_apellido($user->ID) .'</h2>';
            $out .= '<p>Una acción es una carta pública, dirigida a una persona u organización, a través de la cual podrás solicitar firmas de apoyo para tu causa. Te recomendamos plantear con claridad el objetivo que buscas con la carta y la meta en número de firmas que quieres reunir.</p>';
            
            $out .= '<form id="publicationForm" action="" method="post" enctype="multipart/form-data" >';
            
            $out .= '<label for="postTitle">Título de tu acción (obligatorio)</label>';
            $out .= '<input id="postTitle" type="text" maxlength="70" name="postTitle" value="" placeholder="Título" required>';
            $out .= '<span class="formHelp">Máximo de 70 caracteres, quedan <span class="charCont">70</span> caracteres.</span>';
            
            $out .= '<label for="postContent">Texto de la carta de tu acción (obligatorio)</label>';
            $out .= '<textarea name="postContent" placeholder="Descripción de la Acción" data-type="textarea" ></textarea>';
            
            $out .= '<label for="postCategory">¿En qué tema quieres publicar la acción? (obligatorio)</label>';
            $out .= '<select name="postCategory" required data-type="select" >';
            $out .= '<option value="" >Selecciona</option>';
            $out .= categoryOptions();
            $out .= '</select>';
            
            $out .= '<label for="actionGoal">¿Cuántas firmas necesitas reunir? (obligatorio)</label>';
            $out .= '<input id="actionGoal" type="text" name="actionGoal" value="" placeholder="2000" required>';
            
            $out .= '<p class="checkboxCont">';
            $out .= '<span><input type="checkbox" name="termsConditions" value="acepto" required ></span>';
            $out .= '<label for="termsConditions">Acepto los <a href="http://www.elquintopoder.cl/terminos-de-uso/" title="Términos y Condiciones" >Términos y Condiciones</a></label>';
            $out .= '</p>';
            
            $out .= '<input type="hidden" name="postType" value="post_acciones">';
            $out .= '<input type="hidden" name="action" value="ajax_zone">';
            $out .= '<input type="hidden" name="func" value="publicarCosas">';
            $out .= '<input type="hidden" name="postStatus" value="publish">';
            $out .= '<input type="hidden" name="postAuthor" value="'. $author .'">';
            
            $out .= '<input id="SendStuff" type="submit" value="Publicar" class="goHome gac" data-goal="btn-publicar-accion">';
            
            $out .= '</form>';
            $out .= '</div>';
        }
        elseif ( $postType == 'propuestas' ) {
            $out .= '<div id="succsessResponse" class="clearfix inLightBox">';
            $out .= '<h2>Hola '. nombre_y_apellido($user->ID) .'</h2>';
            $out .= '<p>Una propuesta es una iniciativa dirigida al candidato a alcalde, la idea es que expeses una necesidad de tu comuna para que sea apoyada por otras personas con el objetivo que el candidato la responda. Te sugerimos que expongas el problema de forma clara y concisa.</p>';
            
            $out .= '<form id="publicationForm" action="" method="post" enctype="multipart/form-data" >';
            
            $out .= '<label for="postTitle">Título de tu Propuesta (obligatorio)</label>';
            $out .= '<input id="postTitle" type="text" maxlength="70" name="postTitle" value="" placeholder="Título" required>';
            $out .= '<span class="formHelp">Máximo de 70 caracteres, quedan <span class="charCont">70</span> caracteres.</span>';
            
            $out .= '<label for="postContent">Texto de la propuesta (obligatorio)</label>';
            $out .= '<textarea name="postContent" placeholder="Descripción de la Propuesta" data-type="textarea" ></textarea>';
            
            $out .= '<label for="postComuna">¿En qué comuna quieres publicar? (obligatorio)</label>';
            $out .= '<select name="postComuna" required data-type="select" >';
            $out .= '<option value="" >Selecciona</option>';
            $out .= comunasOptions();
            $out .= '</select>';
            
            $out .= '<label for="postTags">Escribe las etiquetas que describen tu propuesta</label>';
            $out .= '<input id="postTags" type="text" name="postTags" value="" placeholder="Urbanismo, Cultura, Ciclovías" required data-spotlight="'. getTemasForSpotlight() .'"/>';
            $out .= '<span class="formHelp">Para agregar más de una etiqueta debes separarlas por coma.</span>';
            $out .= '<div id="postTagsHolder"></div>';
            
            $out .= '<p class="checkboxCont">';
            $out .= '<span><input type="checkbox" name="termsConditions" value="acepto" required ></span>';
            $out .= '<label for="termsConditions">Acepto los <a href="http://www.elquintopoder.cl/terminos-de-uso/" title="Términos y Condiciones" >Términos y Condiciones</a></label>';
            $out .= '</p>';
            
            $out .= '<input type="hidden" name="postType" value="propuestas">';
            $out .= '<input type="hidden" name="action" value="ajax_zone">';
            $out .= '<input type="hidden" name="func" value="publicarCosas">';
            $out .= '<input type="hidden" name="postStatus" value="publish">';
            $out .= '<input type="hidden" name="postAuthor" value="'. $author .'">';
            
            $out .= '<input id="SendStuff" type="submit" value="Publicar" class="goHome gac" data-goal="btn-publicar-propuesta">';
            
            $out .= '</form>';
            $out .= '</div>';
        }
        die($out);
    }
    elseif( $_POST['func'] == 'publicarCosas' ) {
        
        include("purifier.php");
        $content = stripslashes($_POST['postContent']);
        
        $content = str_replace( "\n\r", '' , $content);
        $content = str_replace( "\n", '' , $content);
        $content = str_replace( "\r", '' , $content);
        
        
        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML' ,'Allowed', 'p,b,a[href],i,img');  
        $config->set('HTML','AllowedAttributes', 'a.href,a.title,a.rel,img.src');  
        $purifier = new HTMLPurifier($config);
        $content = $purifier->purify($content);
        
        $content = str_replace( "&nbsp;", '' , $content);
        $content = str_replace( "<span>", '<p>' , $content);
        $content = str_replace( "<\span>", '</p>' , $content);
      
        $args = array(
          'comment_status' => 'open',
          'ping_status' => 'open',
          'post_author' => $_POST['postAuthor'],
          'post_category' => array($_POST['postCategory']),
          'post_content' => $content,
          'post_status' => $_POST['postStatus'],
          'post_title' => $_POST['postTitle'],
          'post_type' => $_POST['postType']
        ); 

        $postid = wp_insert_post( $args );


        if ( !empty($_FILES['fotoUpload']) ) {        
            if ( ! function_exists( 'wp_handle_upload' ) ) { require_once( ABSPATH . 'wp-admin/includes/file.php' ); }
            $mimes = array(
                'jpg|jpeg|jpe' => 'image/jpeg',
                'gif' => 'image/gif',
                'png' => 'image/png',
                'bmp' => 'image/bmp',
                'tif|tiff' => 'image/tiff'
            );
            $fotoUpload = wp_handle_upload( $_FILES['fotoUpload'], array( 'mimes' => $mimes, 'test_form' => false ) );


            $filename = $fotoUpload['file'];

            $wp_filetype = wp_check_filetype(basename($filename), null );
            $wp_upload_dir = wp_upload_dir();

            $attachment = array(
                'guid' => $wp_upload_dir['baseurl'] . _wp_relative_upload_path( $filename ), 
                'post_mime_type' => $wp_filetype['type'],
                'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
                'post_content' => '',
                'post_status' => 'inherit'
            );
            $attach_id = wp_insert_attachment( $attachment, $filename, $postid );
            // you must first include the image.php file
            // for the function wp_generate_attachment_metadata() to work
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
            wp_update_attachment_metadata( $attach_id, $attach_data );
            update_post_meta($postid, '_thumbnail_id', $attach_id);
        }

        if ( $_POST['videoUpload'] ){
            update_post_meta($postid, 'video_link', $_POST['videoUpload']);
            
            update_field( 'video_link', $_POST['videoUpload'], $postid );
            acf_insert($_POST['videoUpload'], $postid, 'video_link');
        }
        
        if ( $_POST['actionGoal'] ){
            update_post_meta($postid, 'requeridos', $_POST['actionGoal']);
            
            update_field( 'requeridos', $_POST['actionGoal'], $postid );
            acf_insert(intval($_POST['actionGoal']), $postid, 'requeridos');
        }
        
        if ( $_POST['postBajada'] ){ 
            update_post_meta($postid, 'bajada', $_POST['postBajada']);
                        
            update_field( 'bajada', $_POST['postBajada'], $postid );
            acf_insert($_POST['postBajada'], $postid, 'bajada');
        }
        
        if ( $_POST['postDestacado'] ){ 
            update_post_meta($postid, 'texto_destacado', $_POST['postDestacado']);
            
            update_field( 'texto_destacado', $_POST['postDestacado'], $postid );
            acf_insert($_POST['postDestacado'], $postid, 'texto_destacado');
        }
        
        if( $_POST['postComuna'] ){
            wp_set_post_terms( $postid, array($_POST['postComuna']), 'comunas' );
        }
        if( $_POST['postTagsArray'] ){
            wp_set_post_terms( $postid, $_POST['postTagsArray'], 'temas' );
        }

        $thepost = get_post($postid);
        $autor = get_userdata( $thepost->post_author );
        $category = get_the_category($postid); 
        
        update_post_meta($postid, 'user_agent_check', $_SERVER['HTTP_USER_AGENT']);
        
        // Mail para el administrador
        
        if( $_POST['postType'] == 'post' ) { $tipoPost = 'la entrada'; }
        elseif( $_POST['postType'] == 'post_fotos' ) { $tipoPost = 'la foto'; }
        elseif( $_POST['postType'] == 'post_videos' ) { $tipoPost = 'el video'; }
        elseif( $_POST['postType'] == 'post_acciones' ) { $tipoPost = 'la acción'; }
        elseif( $_POST['postType'] == 'prouestas' ) { $tipoPost = 'la propuesta'; }
        
        if( $category[0]->term_id == 0 || empty($category) ){
            $comunaObj = get_term_by('id', $_POST['postComuna'], 'comunas');
            $pertenenciaString = ' en el tema <strong>'. $comunaObj->name .'</strong>';
        } else {
            $pertenenciaString = ' en el tema <strong>'. $category[0]->cat_name .'</strong>';
        }
        
        $titulo = 'Administración de Contenido';
        $subtitulo = '';
        $contenido = '<p><strong>'. nombre_y_apellido($autor->ID) .'</strong> ha creado '. $tipoPost .' <strong>"'. $thepost->post_title .'"</strong>'. $pertenenciaString .'</p>';
        $contenido .= '<p><a href="'. get_permalink($thepost->ID) .'" title="'. $thepost->post_title .'">'. get_permalink($thepost->ID) .'</a></p>';
        $contenido .= '<p>Saludos,</p>';
        $contenido .= '<p>Equipo El Quinto Poder</p>';
        $contenido .= '<p><a href="'. home_url() .'" title="El Quinto poder">'. home_url() .'</a></p>';
        $subject = 'Nuevo Contenido Publicado';
        $from = 'administracion@elquintopoder.cl';
        $destino = get_bloginfo('admin_email');
        
        mailMe($titulo, $subtitulo, $contenido, $subject, $from, $destino);
        mailMe($titulo, $subtitulo, $contenido, $subject, $from, 'c.rodriguez@fdd.cl');
        mailMe($titulo, $subtitulo, $contenido, $subject, $from, 'eabbagliati@fdd.cl');
        mailMe($titulo, $subtitulo, $contenido, $subject, $from, 'minfante@fdd.cl');
        mailMe($titulo, $subtitulo, $contenido, $subject, $from, 'xjara@fdd.cl');
//        mailMe($titulo, $subtitulo, $contenido, $subject, $from, 'fernando@ida.cl');
        
        
        // Mail par el usuario
        
        $titulo = 'Administración de Contenido';
        $subtitulo = '';
        if( $_POST['postType'] != 'post' ){
            $contenido = '<p>Felicitaciones<strong> '. nombre_y_apellido($autor->ID) .'</strong> has creado '. $tipoPost .' <strong>"'. $thepost->post_title .'"</strong>'. $pertenenciaString .'</p>';
            $contenido .= '<p><a href="'. get_permalink($thepost->ID) .'" title="'. $thepost->post_title .'">'. get_permalink($thepost->ID) .'</a></p>';
            $contenido .= '<p>Saludos,</p>';
            $contenido .= '<p>Equipo El Quinto Poder</p>';
            $contenido .= '<p><a href="'. home_url() .'" title="El Quinto poder">'. home_url() .'</a></p>';
        } else {
            $contenido = '<p>Felicitaciones<strong> '. nombre_y_apellido($autor->ID) .'</strong> has creado '. $tipoPost .' <strong>"'. $thepost->post_title .'"</strong>'. $pertenenciaString .'</p>';
            $contenido .= '<p>Esta entrada quedará sujeta a revisión y edición por parte de los Administradores.</p>';
            $contenido .= '<p>Se te enviará un e-mail una vez que esta entrada esté publicada.</p>';
            $contenido .= '<p>Saludos,</p>';
            $contenido .= '<p>Equipo El Quinto Poder</p>';
            $contenido .= '<p><a href="'. home_url() .'" title="El Quinto poder">'. home_url() .'</a></p>';
        }
        $subject = 'Nuevo Contenido Publicado';
        $from = 'administracion@elquintopoder.cl';
        $destino = $autor->user_email;
        
        mailMe($titulo, $subtitulo, $contenido, $subject, $from, $destino);
        
        
        ///////
        
        
        $entradaMessage = '<div class="clearfix inLightBox" >';
        $entradaMessage .= '<h2>¡Gracias, '. nombre_y_apellido($autor->ID) .'!</h2>';
        $entradaMessage .= '<p>Tu entrada será revisada y publicada a la brevedad. Te contactaremos en caso de algún problema.</p>';
        $entradaMessage .= '<a href="'. home_url() .'" title="Volver al Inicio" class="goHome" style="margin-top: 20px;" >Volver al Inicio</a>';
        $entradaMessage .= '</div>';
        
        $fotoMessage = '<div class="clearfix inLightBox" >';
        $fotoMessage .= '<h2>¡Gracias, '. nombre_y_apellido($autor->ID) .'!</h2>';
        $fotoMessage .= '<p>Tu foto ya está visible en nuestra comunidad.</p>';
        $fotoMessage .= get_the_post_thumbnail($thepost->ID, 'ajaxLoaded') ? get_the_post_thumbnail($thepost->ID, 'ajaxLoaded') : get_the_embed(get_field('video_link', $thepost->ID),'width="240" height="160"');
        $fotoMessage .= '<a href="'. get_permalink($thepost->ID) .'" title="Ver Foto" class="goHome" style="margin-top: 20px;" >Ver Foto</a>';
        $fotoMessage .= '</div>';
                
        $videoMessage = '<div class="clearfix inLightBox" >';
        $videoMessage .= '<h2>¡Gracias, '. nombre_y_apellido($autor->ID) .'!</h2>';
        $videoMessage .= '<p>Tu video ya está visible en nuestra comunidad.</p>';
        $videoMessage .= get_the_embed($_POST['videoUpload'],'width="240" height="160"');
        $videoMessage .= '<a href="'. get_permalink($thepost->ID) .'" title="Ver Video" class="goHome" style="margin-top: 20px;" >Ver Video</a>';
        $videoMessage .= '</div>';
        
        $actionMessage = '<div class="clearfix inLightBox" >';
        $actionMessage .= '<h2>¡Gracias, '. nombre_y_apellido($autor->ID) .'!</h2>';
        $actionMessage .= '<p>Tu acción ya está visible en nuestra comunidad.</p>';
        $actionMessage .= '<a href="'. get_permalink($thepost->ID) .'" title="Ver Acción" class="goHome" style="margin-top: 20px;" >Ver Acción</a>';
        $actionMessage .= '</div>';
        
        $propuestaMessage = '<div class="clearfix inLightBox" >';
        $propuestaMessage .= '<h2>¡Gracias, '. nombre_y_apellido($autor->ID) .'!</h2>';
        $propuestaMessage .= '<p>Tu propuesta ya está visible en nuestra comunidad.</p>';
        $propuestaMessage .= '<a href="'. get_permalink($thepost->ID) .'" title="Ver Propuesta" class="goHome" style="margin-top: 20px;" >Ver Propuesta</a>';
        $propuestaMessage .= '</div>';
        
        if( $_POST['postType'] == 'post' ) { $out = $entradaMessage; }
        elseif( $_POST['postType'] == 'post_fotos' ) { $out = $fotoMessage; }
        elseif( $_POST['postType'] == 'post_videos' ) { $out = $videoMessage; }
        elseif( $_POST['postType'] == 'post_acciones' ) { $out = $actionMessage; }
        elseif( $_POST['postType'] == 'propuestas' ) { $out = $propuestaMessage; }
        
        die($out);
    }
    elseif( $_POST['func'] == 'sendRegistroMail' ) {
        if( checkUserValidity($_POST['usrLogin'], $_POST['usrEmail']) ){
            $url = home_url() .'/url/?newUser=true&mail='. urlencode( $_POST['usrEmail'] ) .'&nombre='. urlencode( $_POST['usrName'] ) .'&apellido='. urlencode( $_POST['usrLastName'] ) .'&tipocuenta='. urlencode( $_POST['accountType'] ).'&usrPassword='. urlencode( $_POST['usrPassword'] ).'&usrLogin='. urlencode( $_POST['usrLogin'] ).'&suscribme='. urlencode( $_POST['suscribme'] );
            $titulo = 'Registro de usuarios';
            $subtitulo = 'Gracias por unirse a la Comunidad de El Quinto Poder';
            $contenido = '<p>¡Gracias por sumarte a la comunidad de El Quinto Poder! Para activar tu cuenta en nuestro sistema, pincha en el siguiente link:</p>';
            $contenido .= '<p><a href="'. $url .'" title="Active su cuenta" >Link de Activación</a></strong></p>';
            $contenido .= '<p>Una vez activada la cuenta, podrás publicar entradas, subir fotos, videos o proponer acciones de cambio.</p>';
            $contenido .= '<p>Saludos,</p>';
            $contenido .= '<p>Equipo El Quinto Poder</p>';
            $subject = 'Registro de Usuarios';
            $from = 'registro@elquintopoder.cl';
            $destino = $_POST['usrEmail'];

            mailMe($titulo, $subtitulo, $contenido, $subject, $from, $destino);

            $out = '<div id="responseBox" class="clearfix" >';
            $out .= '<h2>¡Gracias por registrarte en nuestra comunidad!</h2>';
            $out .= '<p>Hemos enviado un mensaje de confirmación a la dirección de correo que indicaste. Para activar tu cuenta, pincha el link que está en ese mensaje.</p>';
            $out .= '<p>Saludos, <br> Equipo El Quinto Poder</p>';
            $out .= '<p><a href="#" class="goHome evtjs" data-func="showLoginOptions" data-response="true">Cerrar pestaña</a></p>';
            $out .= '</div>';
            die($out);
        }else{
            $out = 'userExist';
            die($out);
        }
        
    }
    elseif( $_POST['func'] == 'fullsizeImage' ) {
        die(get_the_post_thumbnail($_POST['postid'], 'full'));        
    }
    elseif( $_POST['func'] == 'lightBoxPortada' ) {
        global $wp_embed;
        
        $p = get_post($_POST['pid']);
        $categories = get_the_category( $p->ID );
        foreach($categories as $cat){
            $catLink = get_term_link( $cat, 'category' );
            $catName = $cat->name;
        }
        
        $out = "";
        
         $out .= '<a href="#" id="cerrarLightbox" class="evtjs" data-func="closeLightBox" ></a>';
         $out .= '<h2>'. $p->post_title .'</h2>';
         $out .= '<div class="lightBoxMeta">';
         $out .= '<p>Por: <a href="/perfil-de-usuario/?user='. $p->post_author .'">'. nombre_y_apellido($p->post_author) .'</a></p>';
         $out .= '<p>En: <a href="'.$catLink.'">'. $catName .'</a></p>';
         
         $out .= '</div>';
         
        if($_POST['pType'] == 'post_fotos'){
            $out .= get_the_post_thumbnail($_POST['pid'], 'full');
        }elseif($_POST['pType'] == 'post_videos'){
            $out .= get_post_meta($_POST['pid'], 'ajaxEmbed', true);
        }
        $out .= '<p class="content-lightBox">'.cortar($p->post_content, 200).'</p>';
        $out .= '<a href="'.get_permalink($_POST['pid']).'" class="action-ca">Entrada completa</a>';
  
        die($out);
    }
    elseif( $_POST['func'] == 'getAccessToken' ) {
        $context = stream_context_create(array('https'=>array('ignore_errors'=>true))); 
        $at = file_get_contents("https://graph.facebook.com/oauth/access_token?client_id=264596836884988&client_secret=30001e9faa67e4bbc9445ebf8e3495fc&grant_type=client_credentials", false, $context); 
        $access_tocken = split("=", $at);
        
        die($access_tocken[1]);
    }
    elseif( $_POST['func'] == 'showUserEditForm' ){
        die( getUserEditForm( $_POST['usid'] ) );
    }
    elseif( $_POST['func'] == 'showMoreUserVotes' ) {
        $accionesAdheridas = my_actions($_POST['usid'], 5, $_POST['offset'], intval($_POST['logued']), 'evtjs');
        die($accionesAdheridas);
    }
    elseif( $_POST['func'] == 'unVoteForAction' ) {
        deleteActionVotes($_POST['pid'], $_POST['usid']);
        die();
    }
    elseif( $_POST['func'] == 'showEmailSendingForm' ) {
        $out .= '<div id="succsessResponse" class="clearfix inLightBox">';
        $out .= '<form id="sendEmailForm" action="" method="post" >';
        if( intval($_POST['logged']) == 0 ){
            $out .= '<label for="userName">Nombre</label>';
            $out .= '<input type="text" name="userName" id="userName" placeholder="Nombre" required>';
            $out .= '<label for="userEmail">Email</label>';
            $out .= '<input type="email" name="userEmail" id="userEmail" placeholder="Email" required>';
        }
        $out .= '<label for="userMessage">Mensaje</label>';
        $out .= '<textarea name="userMessage" id="userMessage" placeholder="Mensaje" required ></textarea>';
        
        $out .= '<input type="hidden" name="currentUsr" value="'. $_POST['currUsId'] .'">';
        $out .= '<input type="hidden" name="targetUsr" value="'. $_POST['usid'] .'">';
        $out .= '<input type="hidden" name="logged" value="'. intval($_POST['logged']) .'">';

        $out .= '<input id="SendStuff" type="submit" value="Enviar" class="goHome evtjs" data-func="sendEmailToUser" >';
        
        $out .= '</form>';
        $out .= '</div>';
        die($out);
    }
    elseif( $_POST['func'] == 'sendEmailToUser' ) {
        sendEmailToUser( intval($_POST['targetUsr']), intval($_POST['currentUsr']), $_POST );
        $out = '<h2>¡Gracias!</h2>';
        $out .= '<p>Tu mensaje ha sido enviado con éxito</p>';
        die( $out );
    }
    elseif( $_POST['func'] == 'apoyarPropuesta' ){
        $propuesta = get_post( $_POST['pid'] );
        $comunas = wp_get_post_terms( $_POST['pid'], 'comunas' );
        apoyarPropuesta( $_POST['pid'] );
        
        $fbMessage = 'Acabo de firmar la propuesta '. $propuesta->post_title .' para la comuna de '. $comunas[0]->name .', compromete a tus candidatos dando tu apoyo. elquintopoder.cl';
        
        $socialHtml = '<ul class="social-echoes">';
        $socialHtml .= get_socialEchoes(get_permalink( $_POST['pid'] ), $propuesta->post_title , $_POST['pid'], true, false, $fbMessage);
        $socialHtml .= '</ul>';
        
        $output = array(
            'apoyos' => getApoyos( $_POST['pid'] ),
            'tituloPropuesta' => $propuesta->post_title,
            'socialUl' => $socialHtml
        );
        
        die( json_encode($output) );
    }
    elseif( $_POST['func'] == 'responderPropuesta' ){
        $laPropuesta = get_post( $_POST['propuestapid'] );
        $out = '<div id="succsessResponse" class="clearfix inLightBox">';
        $out .= '<h2>Hola '. nombre_y_apellido( $_POST['candidatoID'] ) .'</h2>';
        $out .= '<p>Redacta tu respuesta a <strong>'. $laPropuesta->post_title .'</strong></p>';
        $out .= '<form id="respuestaCandidatoForm" action="" method="post" >';
        $out .= '<label for="postTitleBox" >Título de la Respuesta (obligatorio)</label>';
        $out .= '<input type="text" required id="postTitleBox" name="postTitleBox" placeholder="Ingrese el título de su respuesta">';
        
        $out .= '<label for="respuestaContenido" >Contenido de la Respuesta (obligatorio)</label>';
        $out .= '<textarea id="respuestaContenido" name="respuestaContenido" placeholder="Ingresa tu Respuesta" required></textarea>';
        $out .= '<input type="hidden" name="propuestaPid" value="'. $_POST['propuestapid'] .'" >';
        $out .= '<input type="hidden" name="candidatoId" value="'. $_POST['candidatoID'] .'" >';
        $out .= '<input type="hidden" name="comunaSlug" value="'. $_POST['comunaSlug'] .'" >';
        $out .= '<input id="SendStuff" type="submit" value="Enviar" class="goHome evtjs" data-func="publicarRespuesta" >';
        $out .= '</form>';
        $out .= '</div>';
        die($out);
    }
    elseif( $_POST['func'] == 'publicarRespuesta' ) {
        $laPropuesta = get_post( $_POST['propuestaPid'] );
        $comuna = get_term_by('slug', $_POST['comunaSlug'], 'comunas');
        $args = array(
          'comment_status' => 'open',
          'ping_status' => 'open',
          'post_author' => intval( $_POST['candidatoId'] ),
          'post_content' => nl2br( $_POST['respuestaContenido'] ),
          'post_status' => 'publish',
          'post_title' => $_POST['postTitleBox'],
          'post_type' => 'post_respuestas'
        ); 
        $postid = wp_insert_post( $args );
        wp_set_post_terms( $postid, array( $comuna->term_id ), 'comunas' );
        update_post_meta($postid, 'respuesta_a', $_POST['propuestaPid']);
        
        // Mail par el usuario
        
        $titulo = 'Un Candidato ha respondido tu propuesta';
        $subtitulo = '';
        $contenido = '<p><strong> '. nombre_y_apellido( $laPropuesta->post_author ) .'</strong> el candidato <strong> '. nombre_y_apellido( intval( $_POST['candidatoId'] ) ) .'</strong> ha respondido tu propuesta '. $laPropuesta->post_title .'</p>';
        $contenido .= '<p><a href="'. get_permalink($laPropuesta->ID) .'" title="'. $laPropuesta->post_title .'">'. get_permalink($laPropuesta->ID) .'</a></p>';
        $contenido .= '<p>Saludos,</p>';
        $contenido .= '<p>Equipo El Quinto Poder</p>';
        $contenido .= '<p><a href="'. home_url() .'" title="El Quinto poder">'. home_url() .'</a></p>';
        
        $propAuthor = get_userdata($laPropuesta->post_author);
        
        $subject = 'Un Candidato ha respondido tu propuesta';
        $from = 'administracion@elquintopoder.cl';
        $destino = $propAuthor->user_email;
        
        mailMe($titulo, $subtitulo, $contenido, $subject, $from, $destino);
        
        $out = '<h2>¡Gracias!</h2>';
        $out .= '<p>Tu respuesta se ha publicado con éxito</p>';
        
        die($out);
    }
    elseif( $_POST['func'] == 'apoyarRespuesta' ) {
        $currentVal = intval( get_post_meta( $_POST['pid'], $_POST['metaKey'], true ) );
        $currentVal++;
        update_post_meta($_POST['pid'], $_POST['metaKey'], $currentVal);
        die("$currentVal");
    }
    elseif( $_POST['func'] == 'verRespuestaCompleta' ) {
        $respuestaPost = get_post( $_POST['pid'] );
        $propuestaPost = get_post(get_post_meta($respuestaPost->ID, 'respuesta_a', true) );
        
        $out = '<div id="succsessResponse" class="clearfix respuestaCanidatos">';
        $out .= '<div class="lightBoxRespuesta">';
        $out .= '<div class="usr-avatar-holder">';
        $out .= get_simple_local_avatar( $respuestaPost->post_author, 40 );
        $out .= '</div>';
        $out .= '<div class="candidatoMeta clearfix">';
        $out .= '<h2>'. get_the_title($respuestaPost->ID) .'</h2>';
        $out .= '<p><em>En respuesta a <strong>'. get_the_title( $propuestaPost->ID ) .'</strong></em></p>';
        $out .= '</div>';
        $out .= '</div>';
        $out .= apply_filters('the_content', make_clickable($respuestaPost->post_content) );
        $out .= '<div class="lightBoxRespuesta bottom">';
        $out .= '<p >¿Te gusto esta respuesta?</p>';
        $out .= '<ul class="respuesta-support" >';
        $out .= '<li>';
        $out .= '<a class="thumbs down evtjs" href="#" data-func="apoyarRespuesta" data-pid="'. $respuestaPost->ID .'" data-action="iDontLike" title="Desaprovar" rel="nofollow"></a>';
        $out .= '<span class="thumbs-num">'. intval( get_post_meta($respuestaPost->ID, 'iDontLike', true) ) .'</span>';
        $out .= '</li>';
        $out .= '<li>';
        $out .= '<a class="thumbs up evtjs" href="#" data-func="apoyarRespuesta" data-pid="'. $respuestaPost->ID .'" data-action="iLike" title="Aprovar" rel="nofollow"></a>';
        $out .= '<span class="thumbs-num">'. intval( get_post_meta($respuestaPost->ID, 'iLike', true) ) .'</span>';
        $out .= '</li>';
        $out .= '</ul>';
        $out .= '</div>';
        $out .= '</div>';
        die($out);
    }
    elseif( $_POST['func'] == 'getPropuestasTab' ) {
        $out = get_PropuestasTabs($_POST['order'], 0, array( array( 'taxonomy' => 'comunas', 'field' => 'slug', 'terms' => $_POST['comuna'] ) ));
        die($out);
    }
    elseif( $_POST['func'] == 'completePostTags' ) {
        $out = '';
        $theTerm = get_term_by('name', $_POST['tagName'], 'temas');
        if( $theTerm ) {
            $out .= '<span class="postTagBox" data-id="'. $theTerm->term_id .'">'. $theTerm->name .'</span>';
            $out .= '<input type="hidden" name="postTagsArray[]" value="'. $theTerm->name .'" data-id="'. $theTerm->term_id .'">';
        } else {
            $newTerm = wp_insert_term(
                $_POST['tagName'], // the term 
                'temas', // the taxonomy
                array(
                    'description'=> '',
                    'slug' => sanitize_title( $_POST['tagName'] ),
                    'parent'=> 0
                )
            );
            $out .= '<span class="postTagBox" data-id="'. $newTerm['term_id'] .'" >'. ucfirst($_POST['tagName'])  .'</span>';
            $out .= '<input type="hidden" name="postTagsArray[]" value="'. ucfirst($_POST['tagName']) .'" data-id="'. $newTerm['term_id'] .'">';
        }
        die($out);
    }
    elseif( $_POST['func'] == 'updateSocialEmails' ) {
        if( intval( $_POST['usid'] ) < 0 ){ die(); }
        
        $email = $_POST['message_user_email'];
        
        $user = wp_update_user(array(
            'ID' => intval( $_POST['usid'] ),
            'user_email' => $email
        ));
        
        $exist = $wpdb->get_var("SELECT id FROM $wpdb->newsletter WHERE email = '$email'");
    
        if( !$exist ){
            if( email_exists($email) ) {
                $usuario = get_user_by('email', $email);
                $nombre = nombre_y_apellido( $usuario->ID );
                $wpdb->insert( $wpdb->newsletter, array(
                    'user_id' => $usuario->ID,
                    'email' => $usuario->user_email,
                    'nombre' => $nombre
                ));
            }
            else {
                $wpdb->insert( $wpdb->newsletter, array(
                    'user_id' => 0,
                    'email' => $email,
                    'nombre' => ''
                ));
            }
        }
        
        $out = '<h2 class="thankyou-title" >¡Muchas Gracias!</h2>';
        $out .= '<p class="thankyou-text" >';
        $out .= 'Tu correo electrónico ha sido actualizado satisfactoriamente a <strong>'. $email .'</strong>';
        $out .= '</p>';
        $out .= '<button class="thankyou-close-btn delegate-me" data-func="closeTargetBox" data-target=\'div[data-id="mensaje-de-error"]\' title="Cerrar este mensaje"></button>';
        
        $response = array(
            'type' => $user,
            'html' => $out
        );
        
        die( json_encode($response) );
    }
    elseif( $_POST['func'] == 'sendContactForm' ){
        sendContactForm();
        $out = '<div class="message success">';
        $out .= '<p><strong>¡Muchas Gracias! </strong>Tu mensaje ha sido enviado correctamente, nos pondremos en contacto contigo a la brevedad</p>';
        $out .= '</div>';

        die( $out );
    }
    else {
        die("status : Error D:");
    }
}


add_action('wp_ajax_ajax_zone', 'ajax_zone');
add_action('wp_ajax_nopriv_ajax_zone', 'ajax_zone');



?>