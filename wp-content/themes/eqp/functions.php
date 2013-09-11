<?php

//////////////////////////////////////////////////////////////////////////////// Generales

/**
 * getPostsBlocks
 * 
 * Devuelve html con lista de posts, formateada acorde al parametro "formato"
 * 
 * 
 * <br />
 * Posibles claves y valores para $atts:
 * <ul>
 * <li> post_type     :   string o array, post|page|custom_type </li>
 * <li> categoria     :   string, Slug de categoria </li>
 * <li> taxonomy      :   string, Slug de taxonomia </li>
 * <li> termTax       :   string o array, Termino o terminos de la taxonomia a buscar </li>
 * <li> espTax        :   array, especificación de una taxonomia y términos adicionales a los anteriores </li>
 * <li> offset        :   int, se traduce al parametro offset del objecto WP_Query, numero de posts a saltarse antes de empezar a imprimir </li>
 * <li> postCount     :   int, se traduce al parametro posts_per_page del objecto WP_Query, numero total de posts a recuperar por pagina </li>
 * <li> orden         :   string, none|ID|author|title|date|modified|parent|rand|comment_count|menu_order|meta_value|meta_value_num </li>
 * <li> autor         :   int, user ID del autor </li>
 * <li> excluir       :   int, post ID de post a excluir </li>
 * <li> formato       :   string, formato html a imprimir lista|listaChica|articulos|portada|mediaPortada|actionsHome|listaAcciones </li>
 * <li> typeClass     :   string, nombre de clase a usar en thumbnails, se encarga de el ícono de visualización</li>
 * </ul>
 * 
 * @param array $atts Array con datos para realizar la query a la base de datos con la interfaz.
 * @return html string Estructura html formateada para bloques de posts, con usos varios
 * @name getPostsBlocks
 * @filesource function.php
 * @used-by ajaxCall.php (plugin)
 * @used-by acciones.php
 * @used-by category.php
 * @used-by especiales.php
 * @used-by functions.php
 * @used-by portadas.php
 * @used-by sidebar-single.php
 * @used-by usuario.php
 * 
 */
function getPostsBlocks($atts) {
    global $offsetPid, $offsetVideos, $offsetPost, $offsetFotos;

    $out = "";

    extract(shortcode_atts(array(
                'post_type' => 'post',
                'categoria' => false,
                'taxonomy' => false,
                'termTax' => false,
                'espTax' => false,
                'offset' => 0,
                'postCount' => 10,
                'orden' => 'date',
                'autor' => 0,
                'excluir' => false,
                'formato' => 'lista',
                'search' => false,
                'perfil' => false,
                'showName' => false,
                'typeClass' => '',
                'paginate' => false,
                'analytics' => false,
                    ), $atts));

    if ($formato == 'portada') {
        $offsetVideos = 0;
        $offsetPost = 0;
        $offsetFotos = 0;
    }

    $argumentos = array(
        'post_type' => $post_type,
        'offset' => $offset,
        'posts_per_page' => $postCount,
        'tax_query' => array(
            'relation' => 'AND',
            array(
                'taxonomy' => $taxonomy,
                'field' => 'slug',
                'terms' => $termTax
            ),
            $espTax
        ),
        'post_status' => 'publish',
        'orderby' => $orden,
        'order' => 'DESC',
        'category_name' => $categoria,
        'post__not_in' => $excluir,
        'author' => $autor,
        'paged' => get_query_var('page') ? get_query_var('page') : get_query_var('paged')
    );
    if (!$taxonomy) {
        unset($argumentos['tax_query']);
    }
    if (!$espTax) {
        unset($argumentos['tax_query']['relation']);
    }
    if (!$categoria) {
        unset($argumentos['category_name']);
    }
    if (!$excluir) {
        unset($argumentos['post__not_in']);
    }

    $count = 0;
    $genQuery = new WP_Query($argumentos);

    if ($genQuery->have_posts()) {
        while ($genQuery->have_posts()) {
            $genQuery->the_post();
            calculateActivity($genQuery->post->ID);
            $comunaSlug = false;
            if ($post_type == 'propuestas') {
                $comunaSlug = wp_get_post_terms($genQuery->post->ID, 'comunas', array("fields" => "slugs"));
                $comunaSlug = $comunaSlug[0];
            }

            $theMetaData = get_query_post_metadata($genQuery, $showName, $comunaSlug);

            if ($formato == 'lista') {
                if ($genQuery->post->post_type == 'post_videos') {
                    $thumbnail = get_the_post_thumbnail($genQuery->post->ID, 'listaChica') ? get_the_post_thumbnail($genQuery->post->ID, 'listaChica') : '<img class="video-thumbnail lista" src="' . get_video_thumbnail($genQuery->post->ID) . '" alt="' . get_the_title() . '" />';
                } else {
                    $thumbnail = get_the_post_thumbnail($genQuery->post->ID, 'listaChica');
                }
                $out .= '<li class="clearfix count">';
                $out .= $thumbnail;

                $analytics_string = !empty( $analytics ) ? implode(' ', $analytics) : '';

                $out .= '<a href="' . get_permalink() . '" class="identifyer" title="Ir a ' . get_the_title() . '" rel="nofollow">Ver</a>';
                $out .= '<a '. $analytics_string .' href="' . get_permalink() . '" title="Ir a ' . get_the_title() . '" rel="contents">' . get_the_title() . '</a>';
                $out .= '<div class="item-metadata">';
                $out .= $theMetaData['authorMeta'];
                $out .= '</div>';
                $out .= $theMetaData['echoes'];
                $out .= '</li>';
                $count++;
                continue;
            }
            elseif ($formato == 'listaChica') {
                $out .= '<li class="clearfix count">';
                $out .= '<div class="article-holder">';
                $out .= '<a href="' . get_permalink() . '" title="Ir a ' . get_the_title() . '">' . get_the_title() . '</a>';
                $out .= '<div class="item-metadata">';
                $out .= '<div class="usr-avatar-holder">';
                $out .= $theMetaData['avatar'];
                $out .= '</div>';
                $out .= $theMetaData['authorMeta'];
                $out .= '</div>';
                $out .= '</div>';
                $out .= $theMetaData['echoes'];
                $out .= '</li>';
                $count++;
                continue;
            }
            elseif ($formato == 'lista-blog') {
                $out .= '<li class="clearfix">';
                $out .= '<div class="clearfix">';
                $out .= '<div class="article-holder">';
                $out .= '<a href="' . get_permalink() . '" title="Ir a ' . get_the_title() . '">' . get_the_title() . '</a>';
                $out .= '<div class="item-metadata">';
                $out .= '<div class="usr-avatar-holder">';
                $out .= $theMetaData['avatar'];
                $out .= '</div>';
                $out .= $theMetaData['authorMeta'];
                $out .= '</div>';
                $out .= '</div>';
                $out .= $theMetaData['echoes'];
                $out .= '</div>';
                $out .= '<div class="clearfix">';
                $out .= cortar(get_the_content(), 560, '...(<a href="' . get_permalink() . '" title="Ir a ' . get_the_title() . '">ver más</a>)');
                $out .= '</div>';
                $out .= '</li>';
                $count++;
                continue;
            }
            elseif ($formato == 'articulos') {
                $tenemos = get_action_votes($genQuery->post->ID);
                $necesitamos = number_format(get_field('requeridos', $genQuery->post->ID) * 1, 0, ',', '.');
                if ($tenemos == 0 || $tenemos == false || $tenemos == '') {
                    $tenemos = '0';
                }

                $out .= '<article class="col four">';
                $out .= '<a href="' . get_permalink() . '" title="Ir a ' . get_the_title() . '" >' . get_the_title() . '</a>';
                $out .= '<div class="item-metadata">';
                $out .= '<div class="usr-avatar-holder">';
                $out .= $theMetaData['avatar'];
                $out .= '</div>';
                $out .= $theMetaData['authorMeta'];
                $out .= '</div>';
                $out .= '<p class="excerpt">' . cortar(get_the_content(), 115) . '</p>';
                $out .= '<ul class="actions-board">';
                $out .= '<li>Tenemos <span>' . $tenemos . '</span></li>';
                $out .= '<li>Necesitamos <span>' . $necesitamos . '</span></li>';
                $out .= '<li><a class="ganalytics" data-ga-category="Participacion" data-ga_action="Firmas" data-ga_opt_label="BtnFirma_AccionPortadilla" href="' . get_permalink() . '" title="Firma y Participa">Firma y Participa</a></li>';
                $out .= '</ul>';
                $out .= '</article>';
                $count++;
                continue;
            }
            elseif ($formato == 'portada') {

                if (is_category()) {
                    if ($genQuery->post->post_type == 'post') {
                        $typeClass = 'entry-type';
                    }
                    elseif ($genQuery->post->post_type == 'post_fotos') {
                        $typeClass = 'pict-type';
                    } 
                    elseif ($genQuery->post->post_type == 'post_videos') {
                        $typeClass = 'video-type';
                    } 
                    elseif ($genQuery->post->post_type == 'post-acciones') {
                        $typeClass = 'action-type';
                    }
                    $vermas = '<a href="' . get_permalink() . '" class="identifyer ' . $typeClass . '" title="Ir a ' . get_the_title() . '">Ver</a>';
                }

                $out .= '<li class="clearfix">';
                if ($genQuery->post->post_type == 'post') {
                    $out .= get_the_post_thumbnail($genQuery->post->ID, 'portadasDestacado') ? get_the_post_thumbnail($genQuery->post->ID, 'portadasDestacado') : '<img src="' . get_bloginfo('template_directory') . '/css/ui/default.jpg" title="' . get_the_title() . '" />';
                    $offsetPost++;
                } 
                elseif ($genQuery->post->post_type == 'post_videos') {
                    $out .= get_the_post_thumbnail($genQuery->post->ID, 'portadasDestacado') ? get_the_post_thumbnail($genQuery->post->ID, 'portadasDestacado') : '<img class="video-thumbnail portada" src="' . get_video_thumbnail($genQuery->post->ID) . '" alt="' . $genQuery->post->post_title . '" />';
                    $offsetVideos++;
                } 
                else {
                    $out .= get_the_post_thumbnail($genQuery->post->ID, 'portadasDestacado', array('alt' => get_the_title(), 'title' => get_the_title()));
                    $offsetFotos++;
                }
                $out .= '<a href="' . get_permalink() . '" title="Ir a ' . get_the_title() . '" >' . get_the_title() . '</a>';
                $out .= $vermas;
                $out .= '<div class="item-metadata">';
                $out .= $theMetaData['authorMeta'];
                $out .= '</div>';
                $out .= $theMetaData['echoes'];
                $out .= '</li>';
                $count++;
                continue;
            }
            elseif ($formato == 'mediaPortada') {
                if (get_field('destacado_portada', $genQuery->post->ID) == true && $count < 1) {
                    if ($genQuery->post->post_type == 'post_videos') {
                        $thumb = get_the_embed(get_field('video_link', $genQuery->post->ID), 'width="480"');
                    } else {
                        $thumb = get_the_post_thumbnail($genQuery->post->ID, 'carousel');
                    }

                    $offsetPid = $genQuery->post->ID;

                    $out .= '<div class="single-showcased-item">';
                    $out .= '<div id="thumbContainer">';
                    $out .= $thumb;
                    $out .= '</div>';

                    if ($genQuery->post->post_type == 'post_fotos') {
                        $out .= '<p>La foto del d&iacute;a</p>';
                    } else {
                        $out .= '<p>El Video destacado</p>';
                    }

                    $out .= '<a href="' . get_permalink() . '" class="showcased-item-title" title="ver ' . get_the_title() . '">' . get_the_title() . '</a>';
                    $out .= '<div class="item-metadata">';
                    $out .= '<div class="usr-avatar-holder">';
                    $out .= $theMetaData['avatar'];
                    $out .= '</div>';
                    $out .= $theMetaData['authorMeta'];
                    $out .= '</div>';
                    $out .= '<p class="excerpt">';
                    $out .= cortar(get_the_content(), 300);
                    $out .= '</p>';
                    $out .= $theMetaData['echoes'];
                    $out .= '</div>';
                    $count++;
                    continue;
                } else {
                    continue;
                }
            }
            elseif ($formato == 'actionsHome') {
                $tenemos = number_format(get_action_votes($genQuery->post->ID) * 1, 0, ',', '.');
                $necesitamos = number_format(get_field('requeridos', $genQuery->post->ID) * 1, 0, ',', '.');
                if ($tenemos == 0 || $tenemos == false || $tenemos == '') {
                    $tenemos = '0';
                }

                $out .= '<li class="clearfix">';
                $out .= '<div class="article-holder">';
                $out .= '<a href="' . get_permalink() . '" title="Ir a ' . get_the_title() . '" rel="contents">' . get_the_title() . '</a>';
                $out .= '<div class="item-metadata">';
                $out .= '<div class="usr-avatar-holder">';
                $out .= $theMetaData['avatar'];
                $out .= '</div>';
                $out .= $theMetaData['authorMeta'];
                $out .= '</div>';
                $out .= '</div>';
                $out .= '<ul class="actions-board resumed">';
                $out .= '<li>Tenemos <span>' . $tenemos . '</span></li>';
                $out .= '<li><a href="' . get_permalink() . '" title="Firma y participa en ' . get_the_title() . '" rel="contents">Necesitamos <span>' . $necesitamos . '</span></a></li>';
                $out .= '</ul>';
                $out .= '</li>';
                $count++;
                continue;
            }
            elseif ($formato == 'listaAcciones') {
                $tenemos = number_format(get_action_votes($genQuery->post->ID) * 1, 0, ',', '.');
                $necesitamos = number_format(get_field('requeridos', $genQuery->post->ID) * 1, 0, ',', '.');
                if ($tenemos == 0 || $tenemos == false || $tenemos == '') {
                    $tenemos = '0';
                }

                $out .= '<li class="clearfix">';
                $out .= '<div class="article-holder">';
                $out .= '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
                $out .= '<div class="item-metadata">';
                $out .= $theMetaData['authorMeta'];
                $out .= '</div>';
                $out .= '<ul class="actions-board resumed">';
                $out .= '<li>Tenemos <span>' . $tenemos . '</span></li>';
                $out .= '<li><a href="' . get_permalink() . '" title="Firma y participa en ' . get_the_title() . '" rel="contents">Necesitamos <span>' . $necesitamos . '</span></a></li>';
                $out .= '</ul>';
                $out .= '</div>';
                $out .= '</li>';
                $count++;
                continue;
            }
            elseif ($formato == 'listaAccionesTab') {
                $tenemos = number_format(get_action_votes($genQuery->post->ID) * 1, 0, ',', '.');
                $necesitamos = number_format(get_field('requeridos', $genQuery->post->ID) * 1, 0, ',', '.');
                if ($tenemos == 0 || $tenemos == false || $tenemos == '') {
                    $tenemos = '0';
                }

                $out .= '<li class="clearfix">';
                $out .= '<div class="article-holder">';
                $out .= '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
                $out .= '<div class="item-metadata">';
                $out .= '<div class="usr-avatar-holder">';
                $out .= $theMetaData['avatar'];
                $out .= '</div>';
                $out .= $theMetaData['authorMeta'];
                $out .= '</div>';
                $out .= '</div>';
                $out .= '<ul class="actions-board resumed">';
                $out .= '<li>Tenemos <span>' . $tenemos . '</span></li>';
                $out .= '<li><a href="' . get_permalink() . '" title="Firma y participa en ' . get_the_title() . '" rel="contents">Necesitamos <span>' . $necesitamos . '</span></a></li>';
                $out .= '</ul>';
                $out .= '</li>';
                $count++;
                continue;
            }
            elseif ($formato == 'listaMedia') {
                $tenemos = number_format(get_action_votes($genQuery->post->ID) * 1, 0, ',', '.');
                $necesitamos = number_format(get_field('requeridos', $genQuery->post->ID) * 1, 0, ',', '.');
                if ($tenemos == 0 || $tenemos == false || $tenemos == '') {
                    $tenemos = '0';
                }

                $out .= '<li class="clearfix">';
                $out .= '<div class="article-holder">';
                $out .= '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
                $out .= '<div class="item-metadata">';
                $out .= '<div class="usr-avatar-holder">';
                $out .= $theMetaData['avatar'];
                $out .= '</div>';
                $out .= $theMetaData['authorMeta'];
                $out .= '</div>';
                $out .= '</div>';
                $out .= '<ul class="actions-board resumed">';
                $out .= '<li>Tenemos <span>' . $tenemos . '</span></li>';
                $out .= '<li><a href="' . get_permalink() . '" title="Firma y participa en ' . get_the_title() . '" rel="contents">Necesitamos <span>' . $necesitamos . '</span></a></li>';
                $out .= '</ul>';
                $out .= '</li>';
                $count++;
                continue;
            }
            elseif ($formato == 'perfilUsuario') {
                $out .= '<li class="searchResult ' . $genQuery->post->post_type . '">';
                $out .= '<h2><a href="' . get_permalink() . '" title="' . get_the_title() . '">' . get_the_title() . '</a></h2>';
                $out .= '<p>' . get_the_excerpt() . '</p>';
                $out .= '<a class="permalink" href="' . get_permalink() . '" title="' . get_the_title() . '">' . get_permalink() . '</a>';
                $out .= '</li>';
            }
            elseif ($formato == 'perfilUsuario_respuestas') {
                $respuesta_a = get_post_meta($genQuery->post->ID, 'respuesta_a', true);
                $propuestaPost = get_post($respuesta_a);

                $out .= '<li class="searchResult ' . $genQuery->post->post_type . '">';
                $out .= '<h2><a href="' . get_permalink($propuestaPost->ID) . '" title="' . get_the_title($propuestaPost->ID) . '" rel="contents">' . get_the_title($propuestaPost->ID) . '</a></h2>';
                $out .= '<p>' . get_the_excerpt() . '</p>';
                $out .= '<a class="evt permalink" data-func="verRespuestaCompleta" data-pid="' . $genQuery->post->ID . '" href="#" title="Ver respuesta completa" rel="nofollow">Ver respuesta completa</a>';
                $out .= '</li>';
            }
            elseif ($formato == 'listaPropuestas') {
                $tenemos = getApoyos($genQuery->post->ID);
                $tenemos = $tenemos == 0 || $tenemos == false || $tenemos == '' ? '0' : $tenemos;

                $out .= '<li class="clearfix">';
                $out .= '<div class="article-holder">';
                $out .= '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
                $out .= '<div class="item-metadata">';
                $out .= $theMetaData['authorMeta'];
                $out .= '</div>';
                $out .= '<ul class="propuesta-board">';
                $out .= '<li><span>' . $tenemos . '</span> Ciudadanos apoyan</li>';
                ;
                $out .= '</ul>';
                $out .= '</div>';
                $out .= '</li>';
            }
            $count++;
        }
    }

    if ($paginate && $formato == 'lista-blog') {
        return '<ul class="article-list regular blog">' . $out . '</ul>' . pagination($genQuery, '/blog/', false);
    }
    if ($paginate && ($formato == 'perfilUsuario' || $formato == 'perfilUsuario_respuestas') && $out != "") {
        return '<ul id="searchResultsList" >' . $out . '</ul>' . pagination($genQuery, '/perfil-de-usuario/', false);
    }
    wp_reset_postdata();
    wp_reset_query();
    rewind_posts();
    return $out;
}

function get_contingenciaMessage() {
    $out = '';

    $args = array(
        'post_type' => 'contingencias',
        'post_status' => 'publish',
        'posts_per_page' => 1
    );
    $contQuery = new WP_Query($args);
    if ($contQuery->have_posts()) {
        while ($contQuery->have_posts()) {
            $contPost = $contQuery->post;
            break;
        }
    }

    if (contMessageDecider(get_field('opciones_contingencia', $contPost->ID), $contPost) && get_field('estado_contingencia', $contPost->ID)) {
        $out .= '<div class="message contingencia clearfix" >';
        $out .= '<span class="contingenciaTitle">' . get_field('texto_boton', $contPost->ID) . '</span>';
        if (get_field('link_contingencia', $contPost->ID)) {
            $out .= '<a class="contingenciaLink" href="' . get_field('link_contingencia', $contPost->ID) . '" title="' . get_field('texto_boton', $contPost->ID) . '" rel="section">';
            $out .= wp_get_attachment_image(get_field('imagen_contingencia', $contPost->ID), 'full', false, array('alt' => get_field('texto_boton', $contPost->ID), 'title' => get_field('texto_boton', $contPost->ID)));
            $out .= '</a>';
        } else {
            $out .= wp_get_attachment_image(get_field('imagen_contingencia', $contPost->ID), 'full', false, array('alt' => get_field('texto_boton', $contPost->ID), 'title' => get_field('texto_boton', $contPost->ID)));
        }
        $out .= '<a class="closer evt" href="#" data-func="closeHoldingDiv" title="Cerrar" rel="nofollow">cerrar</a>';
        $out .= '</div>';
    }
    return $out;
}

function inputEmailMessage(){
    if( get_field('require_login', 'options') && !is_user_logged_in() ){ return false; }
    
    $current_user = wp_get_current_user();
    $emailCheck = preg_match('/@twitter.com$|@facebook.com$/', $current_user->user_email);
    $emailCheckActive = get_field('email_check', 'options');
    $isActive = get_field('requerir_emails', 'options');

    if( !$isActive ){ return false; }
    if( $emailCheckActive && !$emailCheck ){ return false; }
    
    $tipoCuenta = get_user_meta( $current_user->ID, 'tipoCuenta', true );

    $showDefaultMess = get_field('mensaje_default', 'options');
    $showForm = get_field('mostrar_formulario', 'options');

    $customTitle = get_field('titulo_del_mensaje', 'options');
    $customMessage = get_field('contenido_contingencia', 'options');
    
    $out = '<div id="mensaje-contingencia-box" class="thankyou-message alert" data-id="mensaje-de-error">';

    $out .= '<h2 class="thankyou-title" >';
    if( $customTitle ){
        $out .= $customTitle;
    } 
    elseif( $showDefaultMess ) {
        $out .= 'Ingreso de email requerido';
    }
    $out .= '</h2>';

    if( $customMessage ){ 
        $out .= $customMessage; 
    }
    elseif( $showDefaultMess ) {
        $out .= '<p class="thankyou-text" >';
        $out .= '<strong>'. nombre_y_apellido($current_user->ID) .'</strong>, hemos detectado que te has registrado con '. $tipoCuenta .' por lo que tus ';
        $out .= '<a href="/perfil-de-usuario/" title="Ver tus datos de perfil" rel="nofollow">datos de perfil</a> están incompletos. ';
        $out .= 'Te invitamos a que completes la <a href="/perfil-de-usuario/" title="Ver tu información de tu perfil" rel="nofollow">información de tu perfil</a> ';
        $out .= 'o si prefieres actualiza tu información de contacto en el siguiente campo.';
        $out .= '</p>';
    }
    if( $showForm ){
        $out .= '<form id="email-form" class="new-nice-form inside-message clearfix" method="post" action="" >';
        $out .= '<div class="form-body">';
        $out .= '<label class="wrapper-label" for="message_user_email">Ingresa tu correo electrónico';
        $out .= '<input class="inner-input" type="email" placeholder="ejemplo@email.com" name="message_user_email" id="message_user_email" required >';
        $out .= '</label>';
        $out .= '<input class="pretty-checkbox" type="checkbox" name="message_subscribe_me" id="message_subscribe_me" value="subscribe_me" data-customValidation="checkCheckBox" checked >';
        $out .= '<label class="pretty-checkbox-label big-checkbox-label" for="message_subscribe_me" >Quiero suscribirme al newsletter</label>';
        $out .= '</div>';
        $out .= '<input type="submit" class="button-submit floated disabled" value="Enviar" disabled >';
        $out .= '<input type="hidden" name="usid" value="'. $current_user->ID .'" >';
        $out .= '</form>';
    }

    $out .= '<button class="evt thankyou-close-btn" data-func="closeHoldingDiv" >Cerrar</button>';
    $out .= '</div>';
    
    return $out; 
}

function contMessageDecider($options, $contPost) {
    global $post, $wp_query;
    $parent = get_post($post->post_parent);

    if (is_home() && in_array('home', (array) $options)) {
        return true;
    }
    if (is_singular()) {
        if (in_array($post->post_type, (array) $options)) {
            return true;
        }
        if (in_array('post', (array) $options) && is_page('entradas')) {
            return true;
        }
        if (in_array('post_fotos', (array) $options) && is_page('fotos')) {
            return true;
        }
        if (in_array('post_videos', (array) $options) && is_page('videos')) {
            return true;
        }
        if (in_array('post_acciones', (array) $options) && is_page('acciones-home')) {
            return true;
        }
        if (in_array('especiales', (array) $options) && is_page('especiales-home')) {
            return true;
        }
        if (in_array('propuestas', (array) $options) && (is_page('municipales-2012') || $parent->post_name == 'municipales-2012')) {
            return true;
        }
    } elseif (is_category()) {
        if (in_array('category', (array) $options)) {
            return true;
        }
        $contingenciaTerm_names = wp_get_post_terms($contPost->ID, 'category', array("fields" => "names"));
        if (in_array(single_cat_title('', false), $contingenciaTerm_names)) {
            return true;
        }
    } elseif (is_tax()) {
        if (in_array($wp_query->query_vars['taxonomy'], (array) $options)) {
            return true;
        }
        $contingenciaTerm_names = wp_get_post_terms($contPost->ID, $wp_query->query_vars['taxonomy'], array("fields" => "names"));
        if (in_array(single_term_title('', false), $contingenciaTerm_names)) {
            return true;
        }
    }
    return false;
}

/**
 * get_postTabs
 * 
 * Devuelve html en formato Tabs Para el landing de Temas, usa getPostsBlocks
 * 
 * 
 * <br />
 * @param string $orden masNuevos|masActivos
 * @param string $category Slug de categoría a buscar
 * @param string $postType entradas|fotos|videos Define si se esta buscando un post_type específico
 * @return html string Estructura html formateada para bloques de posts envueltos en Tabs
 * @name get_postTabs
 * @filesource function.php
 * @used-by ajaxCall.php (plugin)
 * @used-by category.php
 * 
 */
function get_postTabs($orden, $category=false, $postType=false, $temas = false) {
    global $post, $wp_query, $offsetFotos, $offsetPost, $offsetVideos;
    $contador = 1;

    if ($postType) {
        $ppp = 20;
    } else {
        $ppp = 4;
    }

    if ($orden == 'masNuevos') {
        $args = array(
            'post_type' => 'post',
            'offset' => $offsetPost,
            'postCount' => $ppp,
            'formato' => 'listaChica',
            'orden' => 'date',
            'categoria' => $category
        );
        if (!$category) {
            unset($args['categoria']);
        }
        $entradas = getPostsBlocks($args);

        $args = array(
            'post_type' => 'post_fotos',
            'offset' => $offsetFotos,
            'postCount' => $ppp,
            'categoria' => false,
            'typeClass' => 'pict-type',
            'orden' => 'date',
            'categoria' => $category
        );
        if (!$category) {
            unset($args['categoria']);
        }
        $fotos = getPostsBlocks($args);

        $args = array(
            'post_type' => 'post_videos',
            'offset' => $offsetVideos,
            'postCount' => $ppp,
            'categoria' => false,
            'typeClass' => 'video-type',
            'orden' => 'date',
            'categoria' => $category
        );
        if (!$category) {
            unset($args['categoria']);
        }
        $videos = getPostsBlocks($args);

        $args = array(
            'post_type' => 'post_acciones',
            'offset' => 0,
            'postCount' => $ppp,
            'categoria' => false,
            'typeClass' => 'accion-type',
            'orden' => 'date',
            'formato' => 'listaAccionesTab',
            'categoria' => $category
        );
        if (!$category) {
            unset($args['categoria']);
        }
        $acciones = getPostsBlocks($args);


        $out = "";

        if ($entradas || $fotos || $videos || $acciones) {

            $out .= '<div id="currTab" class="tabs-content more-active">';
            if ($entradas && ($postType == false || $postType == 'entradas')) {
                $out .= '<section class="the-more entries">';
                $out .= '<h2 class="label channels-label entry-published-s"><a href="/entradas" title="Entrada">Entradas</a></h2>';
                $out .= '<ul class="article-list regular">';
                $out .= $entradas;
                $out .= '</ul>';
                if ($temas) {
                    $out .= '<a class="see-more evt" data-tab="masNuevos" data-offset="' . $offsetPost . '" data-cat="' . $category . '" data-type="post" title="Ver más Post" data-order="' . $orden . '" data-func="cargarPost" href="#">Ver más Entradas</a>';
                }
                $out .= '</section>';
            }
            if ($fotos && ($postType == false || $postType == 'fotos')) {
                $out .= '<section class="the-more fotos">';
                $out .= '<h2 class="label channels-label photo-published-s"><a href="/fotos" title="Fotos">Fotos</a></h2>';
                $out .= '<ul class="article-list vertical">';
                $out .= $fotos;
                $out .= '</ul>';
                if ($temas) {
                    $out .= '<a class="see-more evt" data-tab="masNuevos" data-offset="' . $offsetFotos . '" data-cat="' . $category . '" data-type="post_fotos" title="Ver más Post" data-order="' . $orden . '" data-func="cargarPost" href="#">Ver más Fotos</a>';
                }
                $out .= '</section>';
            }
            if ($videos && ($postType == false || $postType == 'videos')) {
                $out .= '<section class="the-more videos">';
                $out .= '<h2 class="label channels-label video-published-s"><a href="/videos" title="Videos">Videos</a></h2>';
                $out .= '<ul class="article-list vertical">';
                $out .= $videos;
                $out .= '</ul>';
                if ($temas) {
                    $out .= '<a class="see-more evt" data-tab="masNuevos" data-offset="' . $offsetVideos . '" data-cat="' . $category . '" data-type="post_videos" title="Ver más Post" data-order="' . $orden . '" data-func="cargarPost" href="#">Ver más Videos</a>';
                }
                $out .= '</section>';
            }
            if ($temas == true) {
                if ($acciones && ($postType == false || $postType == 'post_acciones')) {
                    $out .= '<section class="the-more videos">';
                    $out .= '<h2 class="label channels-label action-published-s"><a href="/acciones-home" title="Acciones">Acciones</a></h2>';
                    $out .= '<ul class="article-list actions regular">';
                    $out .= $acciones;
                    $out .= '</ul>';
                    if ($temas) {
                        $out .= '<a class="see-more evt" data-tab="masNuevos" data-cat="' . $category . '" data-type="post_acciones" title="Ver más Acciones" data-order="' . $orden . '" data-func="cargarPost" href="#">Ver más Acciones</a>';
                    }
                    $out .= '</section>';
                }
                $out .= '</div>';
            }
        }
    } elseif ($orden == 'masActivos') {

        $argsEntradas = array(
            'post_type' => 'post',
            'order' => 'DESC',
            'post_status' => 'publish',
            'posts_per_page' => $ppp,
            'meta_key' => '_total',
            'orderby' => 'meta_value_num',
            'category_name' => $category
        );
        $argsFotos = array(
            'post_type' => 'post_fotos',
            'post_status' => 'publish',
            'order' => 'DESC',
            'posts_per_page' => $ppp,
            'meta_key' => '_total',
            'orderby' => 'meta_value_num',
            'category_name' => $category,
        );
        $argsVideos = array(
            'post_type' => 'post_videos',
            'post_status' => 'publish',
            'order' => 'DESC',
            'posts_per_page' => $ppp,
            'meta_key' => '_total',
            'orderby' => 'meta_value_num',
            'category_name' => $category,
        );
        $argsAcciones = array(
            'post_type' => 'post_acciones',
            'post_status' => 'publish',
            'order' => 'DESC',
            'posts_per_page' => $ppp,
            'meta_key' => '_total',
            'orderby' => 'meta_value_num',
            'category_name' => $category,
        );

        if ( !$category ) {
            unset($argsEntradas['category_name']);
            unset($argsFotos['category_name']);
            unset($argsVideos['category_name']);
        }

        add_filter('posts_where', 'rangoTiempoEntradas');
        $entradasQuery = new WP_Query($argsEntradas);
        $fotosQuery = new WP_Query($argsFotos);
        $videosQuery = new WP_Query($argsVideos);
        $accionQuery = new WP_Query($argsAcciones);
        remove_filter('posts_where', 'rangoTiempoEntradas');
        
        if( $fotosQuery->post_count < 4 ){
            $fotosQuery = null;
            add_filter('posts_where', 'rangoTiempo');
            $fotosQuery = new WP_Query($argsFotos);
            remove_filter('posts_where', 'rangoTiempo');
        }
        
        if( $videosQuery->post_count < 4 ){
            $videosQuery = null;
            add_filter('posts_where', 'rangoTiempo');
            $videosQuery = new WP_Query($argsVideos);
            remove_filter('posts_where', 'rangoTiempo');
        }
        
        if( $accionQuery->post_count < 4 ){
            $accionQuery = null;
            add_filter('posts_where', 'rangoTiempo');
            $accionQuery = new WP_Query($argsAcciones);
            remove_filter('posts_where', 'rangoTiempo');
        }
        


        if ($entradasQuery->have_posts() || $fotosQuery->have_posts() || $fotosQuery->have_posts() || $accionQuery->have_posts()) {

            $out = "";
            $out .= '<div id="currTab" class="tabs-content more-active">';
            if ($entradasQuery->have_posts() && ($postType == false || $postType == 'entradas')) {
                $out .= '<section class="the-more entries">';
                $out .= '<h2 class="label channels-label entry-published-s"><a href="/entradas" title="Entrada">Entradas</a></h2>';
                $out .= '<ul class="article-list regular">';

                while ($entradasQuery->have_posts()) {
                    $entradasQuery->the_post();
                    calculateActivity($entradasQuery->post->ID);

                    $theMetaData = get_query_post_metadata($entradasQuery);

                    $out .= '<li class="clearfix">';
                    $out .= '<div class="article-holder">';
                    $out .= '<a href="' . get_permalink() . '" title="Ir a ' . get_the_title() . '">' . get_the_title() . '</a>';
                    $out .= '<div class="item-metadata">';
                    $out .= '<div class="usr-avatar-holder">';
                    $out .= $theMetaData['avatar'];
                    $out .= '</div>';
                    $out .= $theMetaData['authorMeta'];
                    $out .= '</div>';
                    $out .= '</div>';
                    $out .= $theMetaData['echoes'];
                    $out .= '</li>';
                    $cont++;
                }
                $out .= '</ul>';
                if ($temas) {
                    $out .= '<a class="see-more evt" data-tab="masActivos" data-cat="' . $category . '" data-type="post" title="Ver más Post" data-order="' . $orden . '" data-func="cargarPost" href="#">Ver más Entradas</a>';
                }
                $out .= '</section>';
            }

            if ($fotosQuery->have_posts() && ($postType == false || $postType == 'fotos')) {
                $out .= '<section class="the-more fotos">';
                $out .= '<h2 class="label channels-label photo-published-s"><a href="/fotos" title="Fotos">Fotos</a></h2>';
                $out .= '<ul class="article-list vertical">';

                while ($fotosQuery->have_posts()) {
                    $fotosQuery->the_post();
                    calculateActivity($fotosQuery->post->ID);
                    $theMetaData = get_query_post_metadata($fotosQuery);

                    $out .= '<li class="clearfix">';
                    $out .= get_the_post_thumbnail($fotosQuery->post->ID, 'listaChica');
                    $out .= '<a href="' . get_permalink() . '" class="identifyer evt"  data-func="lightBoxPortada" data-ptype="' . $fotosQuery->post->post_type . '" data-pid="' . $fotosQuery->post->ID . '" title="Ir a ' . get_the_title() . '">Ver</a>';
                    $out .= '<a href="' . get_permalink() . '" title="Ir a ' . get_the_title() . '" >' . get_the_title() . '</a>';
                    $out .= '<div class="item-metadata">';
                    $out .= $theMetaData['authorMeta'];
                    $out .= '</div>';
                    $out .= $theMetaData['echoes'];
                    $out .= '</li>';
                }
                $out .= '</ul>';
                if ($temas) {
                    $out .= '<a class="see-more evt" data-tab="masActivos" data-cat="' . $category . '" data-type="post_fotos" title="Ver más Fotos" data-order="' . $orden . '" data-func="cargarPost" href="#">Ver más fotos</a>';
                }
                $out .= '</section>';
            }

            if ($videosQuery->have_posts() && ($postType == false || $postType == 'videos')) {
                $out .= '<section class="the-more videos">';
                $out .= '<h2 class="label channels-label video-published-s"><a href="/videos" title="Videos">Videos</a></h2>';
                $out .= '<ul class="article-list vertical">';

                while ($videosQuery->have_posts()) {
                    $videosQuery->the_post();
                    calculateActivity($videosQuery->post->ID);
                    $theMetaData = get_query_post_metadata($videosQuery);

                    $thumbnail = get_the_post_thumbnail($videosQuery->post->ID, 'listaChica') ? get_the_post_thumbnail($videosQuery->post->ID, 'listaChica') : '<img class="video-thumbnail lista" src="' . get_video_thumbnail($videosQuery->post->ID) . '" alt="' . get_the_title() . '" />';

                    $out .= '<li class="clearfix">';
                    $out .= $thumbnail;
                    $out .= '<a href="' . get_permalink() . '" class="identifyer evt" data-func="lightBoxPortada" data-ptype="' . $videosQuery->post->post_type . '" data-pid="' . $videosQuery->post->ID . '"  title="Ir a ' . get_the_title() . '">Ver</a>';
                    $out .= '<a href="' . get_permalink() . '" title="Ir a ' . get_the_title() . '" >' . get_the_title() . '</a>';
                    $out .= '<div class="item-metadata">';
                    $out .= $theMetaData['authorMeta'];
                    $out .= '</div>';
                    $out .= $theMetaData['echoes'];
                    $out .= '</li>';
                }

                $out .= '</ul>';
                if ($temas) {
                    $out .= '<a class="see-more evt" data-tab="masActivos" data-cat="' . $category . '" data-type="post_videos" title="Ver más Videos" data-order="' . $orden . '" data-func="cargarPost" href="#">Ver más videos</a>';
                }
                $out .= '</section>';
            }
            if ($temas == true) {
                ;
                if ($accionQuery->have_posts() && ($postType == false || $postType == 'post_acciones')) {
                    $out .= '<section class="the-more entries">';
                    $out .= '<h2 class="label channels-label action-published-s"><a href="/acciones-home" title="Acciones">Acciones</a></h2>';
                    $out .= '<ul class="article-list actions regular">';

                    while ($accionQuery->have_posts()) {
                        $accionQuery->the_post();

                        $theMetaData = get_query_post_metadata($accionQuery);
                        $tenemos = number_format(get_action_votes($accionQuery->post->ID) * 1, 0, ',', '.');
                        $necesitamos = number_format(get_field('requeridos', $accionQuery->post->ID) * 1, 0, ',', '.');

                        $out .= '<li class="clearfix">';
                        $out .= '<div class="article-holder">';
                        $out .= '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
                        $out .= '<div class="item-metadata">';
                        $out .= '<div class="usr-avatar-holder">';
                        $out .= $theMetaData['avatar'];
                        $out .= '</div>';
                        $out .= $theMetaData['authorMeta'];
                        $out .= '</div>';
                        $out .= '</div>';
                        $out .= '<ul class="actions-board resumed">';
                        $out .= '<li>Tenemos <span>' . $tenemos . '</span></li>';
                        $out .= '<li><a href="' . get_permalink() . '">Necesitamos <span>' . $necesitamos . '</span></a></li>';
                        $out .= '</ul>';
                        $out .= '</li>';
                        $cont++;
                    }
                    $out .= '</ul>';
                    if ($temas) {
                        $out .= '<a class="see-more evt" data-tab="masActivos" data-cat="' . $category . '" data-type="post_acciones" title="Ver más Acciones" data-order="' . $orden . '" data-func="cargarPost" href="#">Ver más Acciones</a>';
                    }
                    $out .= '</section>';
                }
            }
            $out .= '</div>';
        }
    }
    return $out;
}

/**
 * get_ActionTabs
 * 
 * Devuelve html en formato Tabs Para el landing de Temas, usa getPostsBlocks
 * 
 * 
 * <br />
 * @param string $orden masActivas|masVotadas
 * @param string $offset se Convierte en offset en WP_Query
 * @param bool $noTab Define si el html de salida viene envuelto en Tabs
 * @return html string Estructura html formateada para bloques de posts
 * @name get_ActionTabs
 * @filesource function.php
 * @used-by ajaxCall.php (plugin)
 * @used-by acciones.php
 * 
 */
function get_ActionTabs($orden, $offset=0, $noTab=false, $category = false) {
    $out = '';

    $args = array(
        'post_type' => 'post_acciones',
        'order' => 'DESC',
        'post_per_page' => 10,
        'offset' => $offset,
        'orderby' => 'meta_value_num',
        'category_name' => $category,
        'post_status' => 'publish'
    );

    if ($category == false) {
        unset($args['category_name']);
    }

    if ($orden == 'masAdhesiones') {
        $args['meta_key'] = '_adhesiones';
    }

    
    $actionsQuery = new WP_Query($args);
    

    if (!$noTab) {
        $out .= '<div id="currTab" class="tabs-content more-active">';
        $out .= '<ul class="article-list actions regular">';
    }
//    $out .= '<section class="the-more entries">';
    if ($actionsQuery->have_posts()) {
        while ($actionsQuery->have_posts()) {
            $actionsQuery->the_post();
            calculateActivity($actionsQuery->post->ID);
            $theMetaData = get_query_post_metadata($actionsQuery);

            $tenemos = number_format(get_action_votes($actionsQuery->post->ID) * 1, 0, ',', '.');
            $necesitamos = number_format(get_field('requeridos', $actionsQuery->post->ID) * 1, 0, ',', '.');
            if ($tenemos == 0 || $tenemos == false || $tenemos == '') {
                $tenemos = '0';
            }

            $out .= '<li class="clearfix">';
            $out .= '<div class="article-holder">';
            $out .= '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
            $out .= '<div class="item-metadata">';
            $out .= '<div class="usr-avatar-holder">';
            $out .= $theMetaData['avatar'];
            $out .= '</div>';
            $out .= $theMetaData['authorMeta'];
            $out .= '</div>';
            $out .= '</div>';
            $out .= '<ul class="actions-board resumed">';
            $out .= '<li>Tenemos <span>' . $tenemos . '</span></li>';
            $out .= '<li><a href="' . get_permalink() . '">Necesitamos <span>' . $necesitamos . '</span></a></li>';
            $out .= '</ul>';
            $out .= '</li>';
        }
    }

    if (!$noTab) {
        $out .= '</ul>';
        $out .= '</div>';
    }

    return $out;
}

/**
 * get_entradas_postTabs
 * 
 * Devuelve html en formato Tabs Para el landing de Temas, usa getPostsBlocks
 * 
 * 
 * <br />
 * @param string $orden masActivos|masNuevos
 * @return html string Estructura html formateada para bloques de posts envueltos en Tabs
 * @name get_entradas_postTabs
 * @filesource function.php
 * @used-by ajaxCall.php (plugin)
 * @used-by portadas.php
 * 
 */
function get_entradas_postTabs($orden="masActivos") {
    global $post, $wp_query;
    $salida = "";

    if ($orden == 'masActivos') {

        $args = array(
            'post_type' => 'post',
            'order' => 'DESC',
            'post_per_page' => 8,
            'meta_key' => '_total',
            'orderby' => 'meta_value_num',
            'post_status' => 'publish'
        );

        add_filter('posts_where', 'rangoTiempoEntradas');
        $entradasQuery = new WP_Query($args);
        remove_filter('posts_where', 'rangoTiempoEntradas');
    } else {

        $args = array(
            'post_type' => 'post',
            'order' => 'DESC',
            'post_per_page' => 8,
            'orderby' => 'date',
            'post_status' => 'publish',
            'offset' => 4
        );

        $entradasQuery = new WP_Query($args);
    }

    if ($entradasQuery->have_posts()) {
        while ($entradasQuery->have_posts()) {
            $entradasQuery->the_post();
            calculateActivity($entradasQuery->post->ID);
            $theMetaData = get_query_post_metadata($entradasQuery);
            $salida .= '<li class="clearfix">';
            $salida .= '<div class="article-holder">';
            $salida .= '<a href="' . get_permalink() . '" title="Ir a ' . get_the_title() . '">' . get_the_title() . '</a>';
            $salida .= '<div class="item-metadata">';
            $salida .= '<div class="usr-avatar-holder">';
            $salida .= $theMetaData['avatar'];
            $salida .= '</div>';
            $salida .= $theMetaData['authorMeta'];
            $salida .= '</div>';
            $salida .= '</div>';
            $salida .= $theMetaData['echoes'];
            $salida .= '</li>';
        }
    }


    $out = "";
    $out .= '<div id="currTab" class="tabs-content more-active">';
    $out .= '<section class="the-more entries">';
//    $out .= '<h2 class="label channels-label entry-published-s"><a href="/entradas" title="Entrada">Entradas</a></h2>';
    $out .= '<ul class="article-list regular">';
    $out .= $salida;
    $out .= '</ul>';
    $out .= '</section>';
    $out .= '</div>';

    return $out;
}

/**
 * get_entradas_postTabs
 * 
 * Devuelve html Lista de post, post_type = post y paginador, a modo de archivo de posts
 * 
 * 
 * <br />
 * @return html string Estructura html de post, post_type = post
 * @name get_entradas_postTabs
 * @filesource function.php
 * @used-by entradas-todas.php
 * 
 */
function get_allEntries() {
    $args = array(
        'post_type' => 'post',
        'order' => 'DESC',
        'posts_per_page' => 8,
        'orderby' => 'date',
        'post_status' => 'publish',
        'paged' => get_query_var('page') ? get_query_var('page') : get_query_var('paged')
    );

    $entradasQuery = new WP_Query($args);

    if ($entradasQuery->have_posts()) {
        while ($entradasQuery->have_posts()) {
            $entradasQuery->the_post();
            calculateActivity($entradasQuery->post->ID);
            $theMetaData = get_query_post_metadata($entradasQuery);

            $salida .= '<li class="clearfix">';
            $salida .= '<div class="article-holder">';
            $salida .= '<a href="' . get_permalink() . '" title="Ir a ' . get_the_title() . '">' . get_the_title() . '</a>';
            $salida .= '<div class="item-metadata">';
            $salida .= '<div class="usr-avatar-holder">';
            $salida .= $theMetaData['avatar'];
            $salida .= '</div>';
            $salida .= $theMetaData['authorMeta'];
            $salida .= '</div>';
            $salida .= '</div>';
            $salida .= $theMetaData['echoes'];
            $salida .= '</li>';
        }
    }

    echo '<div id="currTab" class="tabs-content more-active">';
    echo '<section class="the-more entries">';
    echo '<ul class="article-list regular">';
    echo $salida;
    echo '</ul>';
    pagination($entradasQuery, "/entradas/todas-las-entradas/");
    echo '</section>';
    echo '</div>';
}

/**
 * get_allMedia
 * 
 * Devuelve html Lista de post, post_type = post_fotos, post_videos y paginador, a modo de archivo de posts
 * 
 * 
 * <br />
 * @param string $postType post_fotos|post_videos
 * @param string $typeClass define la clase css para el icono del thumbnail
 * @param string $url define url para el paginador, debe ser la url en donde se encuentra, incluido el / final
 * @param int $offsetPid define el post id destacado a excluir de la query.
 * @return html string Estructura html de post, post_type = post_fotos, post_videos
 * @name get_allMedia
 * @filesource function.php
 * @used-by portadas.php
 * 
 */
function get_allMedia($postType, $typeClass, $url) {
    global $offsetPid;

    $i = 0;

    $args = array(
        'post_type' => $postType,
        'order' => 'DESC',
        'posts_per_page' => 12,
        'orderby' => 'date',
        'post__not_in' => array($offsetPid),
        'post_status' => 'publish',
        'paged' => get_query_var('page') ? get_query_var('page') : get_query_var('paged')
    );
    $mediaQuery = new WP_Query($args);

    if ($mediaQuery->have_posts()) {
        while ($mediaQuery->have_posts()) {
            $mediaQuery->the_post();
            calculateActivity($mediaQuery->post->ID);
            $theMetaData = get_query_post_metadata($mediaQuery);
            if ($mediaQuery->post->post_type == 'post_videos') {
                $thumbnail = get_the_post_thumbnail($mediaQuery->post->ID, 'listaChica') ? get_the_post_thumbnail($mediaQuery->post->ID, 'listaChica') : '<img class="video-thumbnail lista" src="' . get_video_thumbnail($mediaQuery->post->ID) . '" alt="' . get_the_title() . '" />';
                update_post_meta($mediaQuery->post->ID, 'ajaxEmbed', get_the_embed(get_field('video_link', $mediaQuery->post->ID), 'width="600"'));
            } else {
                $thumbnail = get_the_post_thumbnail($mediaQuery->post->ID, 'listaChica');
                $dataFunc = 'data-func="lightBoxPortada"';
            }
            $salida .= '<li>';
            $salida .= $thumbnail;
            $salida .= '<a href="' . get_permalink() . '" data-gallery = "' . $i . '" data-pid="' . $mediaQuery->post->ID . '" data-ptype="' . $mediaQuery->post->post_type . '" data-func="lightBoxPortada" class="identifyer ' . $typeClass . ' evt" title="Ir a ' . get_the_title() . '">Ver</a>';
            $salida .= '<a class="" href="' . get_permalink() . '" title="Ir a ' . get_the_title() . '" >' . get_the_title() . '</a>';
            $salida .= '<div class="item-metadata">';
            $salida .= $theMetaData['authorMeta'];
            $salida .= '</div>';
            $salida .= $theMetaData['echoes'];
            $salida .= '</li>';
            $i++;
        }
    }


    echo '<ul id="postsHolderList" class="article-list vertical clearfix">';
    echo $salida;
    echo '</ul>';
    pagination($mediaQuery, $url);
}

/**
 * get_allActions
 * 
 * Devuelve html Lista de post, post_type = post_acciones a modo de archivo de posts
 * 
 * 
 * <br />
 * @return html string Estructura html de post, post_type = post_acciones
 * @name get_allActions
 * @filesource function.php
 * @used-by acciones-todas.php
 * 
 */
function get_allActions() {
    $args = array(
        'post_type' => 'post_acciones',
        'order' => 'DESC',
        'posts_per_page' => 10,
        'post_status' => 'publish',
        'paged' => get_query_var('page') ? get_query_var('page') : get_query_var('paged')
    );
    $actionsQuery = new WP_Query($args);

    if ($actionsQuery->have_posts()) {
        while ($actionsQuery->have_posts()) {
            $actionsQuery->the_post();
            calculateActivity($actionsQuery->post->ID);
            $theMetaData = get_query_post_metadata($actionsQuery);

            $tenemos = number_format(get_action_votes($actionsQuery->post->ID) * 1, 0, ',', '.');
            $necesitamos = number_format(get_field('requeridos', $actionsQuery->post->ID) * 1, 0, ',', '.');
            if ($tenemos == 0 || $tenemos == false || $tenemos == '') {
                $tenemos = '0';
            }

            $salida .= '<li class="clearfix">';
            $salida .= '<div class="article-holder">';
            $salida .= '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
            $salida .= '<div class="item-metadata">';
            $salida .= '<div class="usr-avatar-holder">';
            $salida .= $theMetaData['avatar'];
            $salida .= '</div>';
            $salida .= $theMetaData['authorMeta'];
            $salida .= '</div>';
            $salida .= '</div>';
            $salida .= '<ul class="actions-board resumed">';
            $salida .= '<li>Tenemos <span>' . $tenemos . '</span></li>';
            $salida .= '<li><a href="' . get_permalink() . '">Necesitamos <span>' . $necesitamos . '</span></a></li>';
            $salida .= '</ul>';
            $salida .= '</li>';
        }
    }

    echo '<div id="currTab" class="tabs-content more-active">';
    echo '<ul class="article-list actions regular">';
    echo $salida;
    echo '</ul>';
    pagination($actionsQuery, '/acciones-home/todas-las-acciones/');
    echo '</div>';
}

function get_the_carousel() {

    $count = 0;
    $out = '';


    foreach ((array) get_field('carrusel_home_destacado', 'options') as $thePost) {
        if (!is_object($thePost)) {
            $thePost = get_post($thePost['post_id_destacado']);
        }

        $categories = get_the_category($thePost->ID);
        foreach ($categories as $cat) {
            $catList[0] = $cat->cat_name;
            $catList[1] = $cat->term_id;
        }
        $avatar = get_simple_local_avatar($thePost->post_author, 40);
//        $autor = get_userdata($thePost->post_author);

        $echoes = '<div class="social-echoes">';
        $echoes .= '<ul>';
        $echoes .= get_socialEchoes(get_permalink($thePost->ID), $thePost->post_title, $thePost->ID);
        $echoes .= '</ul>';
        $echoes .= '</div>';

        $authorMeta = '<p class="published-by">Por: <a href="/perfil-de-usuario/?user=' . $thePost->post_author . '" title="Ver perfil de ' . nombre_y_apellido($thePost->post_author) . '" rel="author">' . nombre_y_apellido($thePost->post_author) . '</a></p>';
        $authorMeta .= '<p class="published-where">En: <a href="' . get_category_link($catList[1]) . '" title="Ver mas en ' . $catList[0] . '" rel="tag">' . $catList[0] . '</a></p>';
        $authorMeta .= '<p class="published-when">' . mysql2date(get_option('date_format'), $thePost->post_date) . '</p>';

        if ($thePost->post_type == 'post_videos') {
            $thumbnail = get_the_embed(get_field('video_link', $thePost->ID)) ? get_the_embed(get_field('video_link', $thePost->ID)) : get_the_post_thumbnail($thePost->ID, 'carousel', array('alt' => $thePost->post_title, 'title' => $thePost->post_title));
        } else {
            $thumbnail = get_the_post_thumbnail($thePost->ID, 'carousel', array('alt' => $thePost->post_title, 'title' => $thePost->post_title));
        }

        $currentItem = $count == 0 ? 'current' : '';
        $out .= '<div class="carousel-item ' . $currentItem . '" data-item="' . ($count + 1) . '">';
        $out .= '<div class="carousel-pict-holder">';
        $out .= $thumbnail;
        $out .= '</div>';
        $out .= '<div class="carousel-info-holder">';
        $out .= '<a href="' . get_permalink($thePost->ID) . '" class="carousel-title" title="Ver ' . $thePost->post_title . '" rel="contents">' . $thePost->post_title . '</a>';
        $out .= '<div class="item-metadata">';
        $out .= '<div class="usr-avatar-holder">';
        $out .= $avatar;
        $out .= '</div>';
        $out .= $authorMeta;
        $out .= '</div>';
        $out .= '<p class="excerpt">';
        $out .= cortar($thePost->post_content, 250) . ' <a href="' . get_permalink($thePost->ID) . '" class="see-more" title="Continuar leyendo ' . $thePost->post_title . '" rel="contents">Ver más</a>';
        $out .= '</p>';
        $out .= $echoes;
        $out .= '</div>';
        $out .= '</div>';

        $count++;
    }

    return $out;
}

function get_the_carousel_controls() {

    $count = 0;
    $out = '';

    foreach ((array) get_field('carrusel_home_destacado', 'options') as $thePost) {
        if (!is_object($thePost)) {
            $thePost = get_post($thePost['post_id_destacado']);
        } // por si viene un post_id

        $current = $count == 0 ? "current" : '';
        $out .= '<li><a href="#" class="evt ' . $current . '" rel="nofollow" title="Ver ' . $thePost->post_title . '" data-func="carouselControl" data-item="' . ($count + 1) . '">' . get_simple_local_avatar($thePost->post_author, 40) . '</a></li>';
        $count++;
    }
    return $out;
}

function get_featured_actions() {

    $count = 0;
    $out = '';

    foreach ((array) get_field('acciones_destacadas', 'options') as $thePostId) {
        if(is_numeric( $thePostId['accion_id'] ) ){
            $thePost = get_post( $thePostId['accion_id'] );
            $categories = get_the_category($thePost->ID);
            foreach ($categories as $cat) {
                $catList[0] = $cat->cat_name;
                $catList[1] = $cat->term_id;
            }
            $avatar = get_simple_local_avatar($thePost->post_author, 40);
            $autor = get_userdata($thePost->post_author);

            $echoes = '<div class="social-echoes">';
            $echoes .= '<ul>';
            $echoes .= get_socialEchoes(get_permalink($thePost->ID), $thePost->post_title, $thePost->ID);
            $echoes .= '</ul>';
            $echoes .= '</div>';

            $authorMeta = '<p class="published-by">Por: <a href="/perfil-de-usuario/?user=' . $thePost->post_author . '">' . nombre_y_apellido($thePost->post_author) . '</a></p>';
            $authorMeta .= '<p class="published-where">En: <a href="' . get_category_link($catList[1]) . '">' . $catList[0] . '</a></p>';
            $authorMeta .= '<p class="published-when">' . mysql2date(get_option('date_format'), $thePost->post_date) . '</p>';

            $tenemos = number_format(get_action_votes($thePost->ID) * 1, 0, ',', '.');
            $necesitamos = number_format(get_field('requeridos', $thePost->ID) * 1, 0, ',', '.');
            if ($tenemos == 0 || $tenemos == false || $tenemos == '') {
                $tenemos = '0';
            }

            $out .= '<article class="col four">';
            $out .= '<a href="' . get_permalink($thePost->ID) . '" title="Ir a ' . $thePost->post_title . '" >' . $thePost->post_title . '</a>';
            $out .= '<div class="item-metadata">';
            $out .= '<div class="usr-avatar-holder">';
            $out .= $avatar;
            $out .= '</div>';
            $out .= $authorMeta;
            $out .= '</div>';
            $out .= '<p class="excerpt">' . cortar($thePost->post_content, 115) . '</p>';
            $out .= '<ul class="actions-board">';
            $out .= '<li>Tenemos <span>' . $tenemos . '</span></li>';
            $out .= '<li>Necesitamos <span>' . $necesitamos . '</span></li>';
            $out .= '<li><a href="' . get_permalink($thePost->ID) . '" title="Firma y Participa" class="ganalytics" data-ga-category="Participacion" data-ga_action="Firmas" data-ga_opt_label="BtnFirma_Home">Firma y Participa</a></li>';
            $out .= '</ul>';
            $out .= '</article>';
            $count++;
        }
    }
    return $out;
}

function getEspecialContent($type) {
    global $post;
    
    $out = "";
    $count = 0;
    
    
    foreach ((array) get_field($type, $post->ID) as $postObjectAr) {
        switch( $type ){
            case '_entradas' :
                if( is_object( $postObjectAr['_entrada'] ) ){ $thePost = $postObjectAr['_entrada']; }
                else { $thePost = get_post( intval( $postObjectAr['_entrada'] ) ); }
                break;
            case '_fotos' :
                if( is_object( $postObjectAr['_foto'] ) ){ $thePost = $postObjectAr['_foto']; }
                else { $thePost = get_post( intval( $postObjectAr['_foto'] ) ); }
                break;
            case '_videos' : 
                if( is_object( $postObjectAr['_video'] ) ){ $thePost = $postObjectAr['_video']; }
                else { $thePost = get_post( intval( $postObjectAr['_video'] ) ); }
                break;
            case '_acciones' : 
                if( is_object( $postObjectAr['_accion'] ) ){ $thePost = $postObjectAr['_accion']; }
                else { $thePost = get_post( intval( $postObjectAr['_accion'] ) ); }
                break;
        }
        
        if( $count == 3 ) { $lastItem = 'ultima'; }
        else { $lastItem = ''; }

        $categories = get_the_category($thePost->ID);
        foreach ($categories as $cat) {
            $catList[0] = $cat->cat_name;
            $catList[1] = $cat->term_id;
        }
        $avatar = get_simple_local_avatar($thePost->post_author, 40);
        $autor = get_userdata($thePost->post_author);

        $echoes = '<div class="social-echoes">';
        $echoes .= '<ul>';
        $echoes .= get_socialEchoes(get_permalink($thePost->ID), $thePost->post_title, $thePost->ID);
        $echoes .= '</ul>';
        $echoes .= '</div>';

        $authorMeta = '<p class="published-by">Por: <a href="/perfil-de-usuario/?user=' . $thePost->post_author . '">' . nombre_y_apellido($thePost->post_author) . '</a></p>';
        $authorMeta .= '<p class="published-where">En: <a href="' . get_category_link($catList[1]) . '">' . $catList[0] . '</a></p>';
        $authorMeta .= '<p class="published-when">' . mysql2date(get_option('date_format'), $thePost->post_date) . '</p>';

        if ($type == '_entradas') {
            $out .= '<li class="col three '. $lastItem  .' ">';
            $out .= '<div>';
            $out .= '<a class="related-content-title equalize-me especial-title" href="' . get_permalink($thePost->ID) . '" title="Ir a ' . $thePost->post_title . '" rel="contents">' . $thePost->post_title . '</a>';
            $out .= '<div class="item-metadata">';
            $out .= '<div class="usr-avatar-holder">';
            $out .= $avatar;
            $out .= '</div>';
            $out .= $authorMeta;
            $out .= '</div>';
            $out .= '</div>';
            $out .= $echoes;
            $out .= '</li>';
            $count++;
        } 
        elseif ($type == '_fotos') {
            $out .= '<li class="col three '. $lastItem  .' ">';
            $out .= '<a data-func="lightBoxPortada" data-pid="'. $thePost->ID .'" data-ptype="post_fotos" class="hidden-content-link evt" href="' . get_permalink($thePost->ID) . '" title="Ir a ' . $thePost->post_title . '" rel="contents">';
            $out .= get_the_post_thumbnail($thePost->ID, 'ajaxLoaded', array('style' => 'width: 100%;', 'title' => 'Ver foto mas grande'));
            $out .= '</a>';
            $out .= '<a class="related-content-title equalize-me especial-title" href="' . get_permalink($thePost->ID) . '" title="Ir a ' . $thePost->post_title . '" >' . $thePost->post_title . '</a>';
            $out .= '<div class="item-metadata">';
            $out .= '<div class="usr-avatar-holder">';
            $out .= $avatar;
            $out .= '</div>';
            $out .= $authorMeta;
            $out .= '</div>';
            $out .= $echoes;
            $out .= '</li>';
            $count++;
        } 
        elseif ($type == '_videos') {
            $out .= '<li class="col three '. $lastItem  .' ">';
            $out .= '<a data-func="lightBoxPortada" data-pid="'. $thePost->ID .'" data-ptype="post_videos" class="hidden-content-link evt" href="' . get_permalink($thePost->ID) . '" title="Ir a ' . $thePost->post_title . '">';
            $out .= get_the_post_thumbnail($thePost->ID, 'ajaxLoaded') ? get_the_post_thumbnail($thePost->ID, 'ajaxLoaded', array('style' => 'width: 100%;', 'title' => 'Ver Video')) : '<img class="video-thumbnail lista" src="' . get_video_thumbnail($thePost->ID) . '" alt="' . $thePost->post_title . '" style="width: 100%;" />';
            $out .= '</a>';
            $out .= '<a class="related-content-title equalize-me especial-title" href="' . get_permalink($thePost->ID) . '" title="Ir a ' . $thePost->post_title . '" >' . $thePost->post_title . '</a>';
            $out .= '<div class="item-metadata">';
            $out .= '<div class="usr-avatar-holder">';
            $out .= $avatar;
            $out .= '</div>';
            $out .= $authorMeta;
            $out .= '</div>';
            $out .= $echoes;
            $out .= '</li>';
            $count++;
        }
        elseif ($type == '_acciones' && $thePost->post_title) {
            $tenemos = number_format(get_action_votes($thePost->ID) * 1, 0, ',', '.');
            $necesitamos = number_format(get_field('requeridos', $thePost->ID) * 1, 0, ',', '.');
            if ($tenemos == 0 || $tenemos == false || $tenemos == '') {
                $tenemos = '0';
            }

            $out .= '<li class="col three '. $lastItem  .' ">';
            $out .= '<div>';
            $out .= '<a class="related-content-title equalize-me especial-title" href="' . get_permalink($thePost->ID) . '" title="Firma y participa en '. $thePost->post_title .'" rel="contents">' . $thePost->post_title . '</a>';
            $out .= '<div class="item-metadata">';
            $out .= '<div class="usr-avatar-holder">';
            $out .= $avatar;
            $out .= '</div>';
            $out .= $authorMeta;
            $out .= '</div>';
            $out .= '<ul class="actions-votes resumed clearfix">';
            $out .= '<li class="we-have">Tenemos <span class="actions-votes-number" >' . $tenemos . '</span></li>';
            $out .= '<li class="we-need">Necesitamos <span class="actions-votes-number">' . $necesitamos . '</span></li>';
            $out .= '</ul>';
            $out .= '</div>';
            $out .= '</li>';
            $count++;
        }
    }
    
    return $out ? '<ul class="clearfix especial-content-list '. substr($type, 1, strlen($type)) .'" >'. $out .'</ul>' : false;
}

function searchAuthors($info) {
    global $wpdb;
    $busqueda = $info['s'];
    
    $rQuery = "SELECT user_id, MATCH ( meta_value, meta_key ) AGAINST ( %s ) AS score FROM $wpdb->usermeta WHERE MATCH ( meta_value, meta_key  ) AGAINST ( %s ) GROUP BY user_id ORDER BY score DESC, user_id ASC LIMIT 50";
    $prepared = $wpdb->prepare( $rQuery, $busqueda, $busqueda );
    $resultado = $wpdb->get_results($prepared);
    
    return $resultado;
}

function getTheBanners() {
    $out = "";
    $current = "";
    $count = 0;
    $banners = get_field('homeBanners', 'options');
    if (!empty($banners)) {
        $out .= '<div id="bannersCont">';

        foreach ($banners as $banner) {
            $current = $count === 0 ? 'current' : '';

            $out .= '<a class="ganalytics '. $current .'" data-ga-category="CampanasInternas" data-ga_action="SidebarHome" data-ga_opt_label="' . $banner['nombre_banner'] . '" href="' . $banner['url_banner'] . '" title="' . $banner['nombre_banner'] . '" >';
            $out .= wp_get_attachment_image($banner['imagen_banner'], 'banners');
            $out .= '</a>'; 

            $count++;
        }

        $out .= '</div>';
    }
    return $out;
}

function get_mainNav() {
    global $post, $wp_query;
    $tax = get_the_terms($post->ID, 'tipologia');
    $categories = get_categories(array('hide_empty' => 0, 'exclude' => 1, 'orderby' => 'name'));

    if ($tax && !is_wp_error($tax)) { foreach ($tax as $taxed) { $taxAr[] = $taxed->slug; } }
    else { $taxAr = array(); }

    $inicio = is_front_page() ? 'current' : '';
    $entradas = is_page('entradas') || is_singular('post') ? 'current' : '';
    $fotos = is_page('fotos') || is_singular('post_fotos') ? 'current' : '';
    $videos = is_page('videos') || is_singular('post_videos') ? 'current' : '';
    $acciones = is_page('acciones-home') || is_singular('post_acciones') ? 'current' : '';
    $temas = is_category() ? 'current' : '';
    $especiales = is_page('especiales-home') || is_singular('especiales') ? 'current' : '';

    $out = '<li><a href="' . home_url() . '" class="' . $inicio . '" title="Inicio">Inicio</a></li>';
    $out .= '<li><a href="/entradas" class="' . $entradas . '" title="Entradas">Entradas</a></li>';
    $out .= '<li><a href="/fotos" class="' . $fotos . '" title="Fotos">Fotos</a></li>';
    $out .= '<li><a href="/videos" class="' . $videos . '" title="Videos">Videos</a></li>';
    $out .= '<li><a href="/acciones-home" class="' . $acciones . '" title="Acciones">Acciones</a></li>';
    $out .= '<li class="father">';
    $out .= '<a id="showTemas" href="#" class="menu-father ' . $temas . '" title="Ver Temas">Temas</a>';
    $out .= '<ul>';
    foreach ($categories as $cat) {
        $out .= '<li><a href="' . get_category_link($cat->term_id) . '" title="' . $cat->cat_name . '">' . $cat->cat_name . '</a></li>';
    }
    $out .= '</ul>';
    $out .= '</li>';
    $out .= '<li><a href="/especiales-home" class="' . $especiales . '" title="Especiales">Especiales</a></li>';

    echo $out;
}

function get_temasEnLaRed() {
    $out = "";
    foreach ((array) get_field('temas_red', 'options') as $tema) {
        $out .= '<li>';
        $out .= '<div>';
        $out .= '<img src="' . get_sub_field('foto_red', $tema) . '" alt="' . get_sub_field('titular', $tema) . '"/>';
        $out .= '</div>';
        $out .= '<a href="' . get_sub_field('url', $tema) . '" title="' . get_sub_field('titular', $tema) . '" >' . get_sub_field('titular', $tema) . '</a>';
        $out .= '</li>';
    }
    echo $out;
}

function temasCount($tipo = false, $taxonomy = false) {
    $out = "";
    $i = 1;

    $filtro = '';
    if ($tipo) {
        $filtro = '?tipo=' . $tipo;
    }

    $args = array(
        'orderby' => 'count',
        'order' => 'DESC',
        'hide_empty' => 1,
        'exclude' => 1,
        'taxonomy' => 'category'
    );

    if ($taxonomy) {
        $args['taxonomy'] = 'categorias_blog';
    }

    if( is_singular('post_acciones') ){ $gac_cat = 'LinksSingleAccion'; }
    else{ $gac_cat = 'LinksPortadillas'; }

    $categories = get_categories($args);
    foreach ((array) $categories as $cat) {
        $link = get_category_link($cat->term_id);
        if ($taxonomy) {
            $link = get_term_link($cat, 'categorias_blog');
        }
        $out .= '<li data-count="' . $cat->count . '"><span>' . $i . '</span><a class="ganalytics" data-ga-category="SidebarLink" data-ga_action="'. $gac_cat .'" data-ga_opt_label="LinksTema" href="' . $link . $filtro . '" title="' . $cat->name . '" rel="tag">' . $cat->name . ' (' . $cat->count . ')</a></li>';
        $i++;
    }
    echo $out;
}

function orderTemasByCount($a, $b) {
    if ($a->count == $b->count) {
        return 0;
    }
    return ($a->count > $b->count) ? -1 : 1;
}

/**
 * @todo Mezclar con temasCount()
 */
function temasCountMunicipales($comunaSlug = false) {
    $out = "";
    $i = 1;
    $filtro = "";
    if ($comunaSlug) {
        $filtro = '?comuna=' . $comunaSlug;
    }
    // seteo variables para query
    $comunaTemasTerms = array();
    $queryArgs = array(
        'post_type' => 'propuestas',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'comunas',
                'field' => 'slug',
                'terms' => $comunaSlug
            )
        )
    );
    if ($comunaSlug == false) {
        unset($queryArgs['tax_query']);
    }
    $propQuery = new WP_Query($queryArgs);
    if ($propQuery->have_posts()) {
        while ($propQuery->have_posts()) {
            $propQuery->the_post();
            // lleno un array con los ids de todos los terms de "temas" que encuentre
            $comunaTemasTerms = array_merge($comunaTemasTerms, wp_get_post_terms($propQuery->post->ID, 'temas', array("fields" => "ids")));
        }
    } wp_reset_query();
    $propQuery->rewind_posts();

    // genero array solo con los count por comuna ( cuenta los values duplicados, en este caso los IDS)
    $comunaTemasTerms_dupCount = array_count_values($comunaTemasTerms);
    // borro los duplicados
    $comunaTemasTerms = array_unique($comunaTemasTerms);
    $temasTerms = array();
    // lleno otro array con los objetos Terms
    foreach ((array) $comunaTemasTerms as $temaID) {
        $objBuffer = get_term($temaID, 'temas');
        // reseteo los count totales por un count por comuna
        $objBuffer->count = $comunaTemasTerms_dupCount[$temaID];
        $temasTerms[] = $objBuffer;
    }

    // ordeno los objetos por count
    usort($temasTerms, 'orderTemasByCount');

    // lleno el string html
    foreach ((array) $temasTerms as $tema) {
        $link = get_term_link($tema);
        $display = $i >= 16 ? 'style="display: none;"' : '';
        $out .= '<li ' . $display . ' data-count="' . $tema->count . '"><span>' . $i . '</span><a href="' . $link . $filtro . '" title="Ver ' . $tema->name . '" rel="tag">' . $tema->name . ' (' . $tema->count . ')</a></li>';
        $i++;
    }
    echo $out;
}

function waysToConnect($mess=false) {
    global $post;
    
    $out = "";
    $optlabelString = "";

    $size = 's';
    if ($mess) {
        $message = '<p>Estamos conversando también en nuestras redes sociales</p>';
        $size = 'xl';
    }
    
    if( is_front_page() ){ $optlabelString = "BtnEQP_Home"; }
    elseif( is_page() ){
        switch ($post->post_name){
            case 'fotos' :
                $optlabelString = "FotosHome";
                break;
            case 'videos' :
                $optlabelString = "VideosHome";
                break;
            case 'acciones-home' :
                $optlabelString = "AccionesHome";
                break;
            case 'entradas' :
                $optlabelString = "EntradasHome";
                break;
            case 'especiales-home' :
                $optlabelString = "EspecialesHome";
                break;
            default : 
                $optlabelString = $post->post_name . "-Page";
                break;
        }
    }
    elseif( is_single() ){
        switch ($post->post_type){
            case 'post_fotos' :
                $optlabelString = "FotosSingle";
                break;
            case 'post_videos' :
                $optlabelString = "VideosSingle";
                break;
            case 'post_acciones' :
                $optlabelString = "AccionesSingle";
                break;
            case 'post' :
                $optlabelString = "EntradasSingle";
                break;
            case 'especiales' :
                $optlabelString = "EspecialesSingle";
                break;
        }
    }
    

    $out .= '<div id="ways-to-connect">';
    $out .= '<h2 class="label">Síguenos en</h2>';
    $out .= $message;
    $out .= '<ul>';
    $out .= '<li><a class="ganalytics" data-ga-category="Outbound_Trafico" data-ga_action="EQPTwitter" data-ga_opt_label="'. $optlabelString .'" href="http://twitter.com/#!/elquintopoder" title="Siguenos en Twitter"><img src="' . get_bloginfo("template_directory") . '/css/ui/ico-twitter-' . $size . '.png"/></a></li>';
    $out .= '<li><a class="ganalytics" data-ga-category="Outbound_Trafico" data-ga_action="EQPFacebook" data-ga_opt_label="'. $optlabelString .'" href="http://www.facebook.com/elquintopoder" title="Siguenos en Facebook"><img src="' . get_bloginfo("template_directory") . '/css/ui/ico-facebook-' . $size . '.png"/></a></li>';
    $out .= '<li><a class="ganalytics" data-ga-category="Outbound_Trafico" data-ga_action="EQPRSS" data-ga_opt_label="'. $optlabelString .'" href="' . home_url() . '/feed" title="Conectate con nuestro feed RSS"><img src="' . get_bloginfo("template_directory") . '/css/ui/ico-rss-' . $size . '.png"/></a></li>';
    $out .= '<li><a class="ganalytics" data-ga-category="Outbound_Trafico" data-ga_action="EQPTumblr" data-ga_opt_label="'. $optlabelString .'" href="http://elquintopoder.tumblr.com/" title="Siguenos en Tumblr"><img src="' . get_bloginfo("template_directory") . '/css/ui/ico-tumblr-' . $size . '.png"/></a></li>';
    $out .= '</ul>';
    $out .= '</div>';
    echo $out;
}

function get_the_embed($url, $sizeStr='') {
    global $wp_embed;
    
    $url = explode('//', $url);
    $url = 'http://' . $url[1];
    
    return $wp_embed->run_shortcode('[embed ' . $sizeStr . ']' . $url . '[/embed]');
}

function socialFollows() {
    if (is_user_logged_in()) {

        $current_user = wp_get_current_user();
        $userProvider = get_user_meta($current_user->ID, 'oa_social_login_identity_provider', true);

        $urlCheckTwit = get_user_meta($current_user->ID, 'twitter', true);
        $urlCheckFb = get_user_meta($current_user->ID, 'facebook', true);

        if ($userProvider == 'Twitter' && !$urlCheckTwit) {
            update_user_meta($current_user->ID, 'twitter', $current_user->user_url);
        } elseif ($userProvider == 'Facebook' && !$urlCheckFb) {
            update_user_meta($current_user->ID, 'facebook', $current_user->user_url);
        }
    }
}

function comentariosLoop($comment, $args, $depth) {
    $GLOBALS['comment'] = $comment;
    global $post;

    $isCandidato = false;

    if (!empty($comment->user_id)) {
        $author = nombre_y_apellido($comment->user_id);
        $authorObject = get_userdata($comment->user_id);
        if (in_array('candidato', (array) $authorObject->roles)) {
            $isCandidato = 'class="candidatoComment"';
        }
    } else {
        $author = get_comment_author();
    }

    $commentOut .= '<li ' . $isCandidato . '><div>';
    $commentOut .= '<div class="usr-avatar-holder">';
    $commentOut .= get_simple_local_avatar($comment->user_id, 40);
    $commentOut .= '</div>';
    $commentOut .= '<div id="comment-' . $comment->comment_ID . '">';
    $commentOut .= '<div class="item-metadata">';
    if ($comment->user_id != 0) {
        $commentOut .= '<p class="published-by">Por: <a href="/perfil-de-usuario/?user=' . $comment->user_id . '">' . $author . '</a></p>';
    } else {
        $commentOut .= '<p class="published-by">Por: ' . $author . '</p>';
    }
    $commentOut .= '<p class="published-when">' . get_comment_date() . '</p>';
    $commentOut .= '</div>';
    $content = get_comment_text();

    $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";

    $commentOut .= apply_filters('comment_text', preg_replace($reg_exUrl, '<a href="$0">$0</a> ', $content));

//  $commentOut .= apply_filters('comment_text',get_comment_text());
    $commentOut .= '</div>';
    $commentOut .= '<div class="further-actions clearfix">';
    $commentOut .= '<ul>';
 // $commentOut .= '<li><a href="" class="respond-to-comment" title="Respóndele al autor de este comentario">Responder a este comentario</a></li>';
    $commentOut .= '<li>';
    $commentOut .= $comment->comment_parent == 0 ? get_comment_reply_link(array('depth' => 1, 'max_depth' => 2, 'login_text' => 'Registrarse para responder', 'reply_text' => 'Responder al autor de este comentario', 'respond_id' => 'commentform')) : '';
    $commentOut .= '</li>';
    $commentOut .= commentsvotePanel();
    $commentOut .= '</ul>';
    $commentOut .= '</div>';
    $commentOut .= '<div></li>';

    echo $commentOut;
}

/**
 * getFirmas
 * 
 * Devuelve html con lista de firmantes y descarga de cvs, en single de acciones
 * 
 * <br />
 * Posibles claves y valores para $atts:
 * <ul>
 * <li> pid           :   int, post ID de un post de acciones </li>
 * <li> show          :   string, 'show' para limitar la base de datos a 10</li>
 * </ul>
 * 
 * @param int $pid, string $show.
 * @return html string Estructura html formateada para lista de últimos firmantes, con usos en post_acciones
 * @return descarga de cvs
 * @name getFirmas
 * @filesource function.php
 * @used-by single-post_acciones.php
 * 
 */
function getFirmas($pid, $show=false) {
    global $wpdb, $post, $current_user;

    if ($pid != 11508 && $pid != 11528) {
        $out = "";
        $cvs = "";
        $i = 0;
        $limit = "";

        if ($show) { $limit = 'LIMIT 10'; }

        $firmantes = $wpdb->get_results(
                $wpdb->prepare("
                SELECT nombre, email, id 
                FROM $wpdb->acciones 
                WHERE $wpdb->acciones.post_id = $pid
                ORDER BY id DESC $limit
        "));

        if ($firmantes) {
            $out .= '<li class="firmantes">Últimos adherentes<span>';
            $out .= '<table class="firmantes">';
            foreach ($firmantes as $firmante) {
                $i++;
                $out .= '<tr>';
                if ($i < 10) {
                    $i = '0' . $i;
                }
                $out .= '<td>' . $firmante->nombre . '</td>';
                $out .= '</tr>';
                $firmaName = utf8_encode($firmante->nombre);
                $cvs .= $firmaName . ", " . $firmante->email . "\n";
            }
            $out .= '</table>';
            $out .= '</span>';
            if ($post->post_author == $current_user->ID || current_user_can('manage_options')) {
                $out .= '<a class="action-ca" href="' . get_permalink($pid) . '/?firmas=true" title="descargar archiovo CSV, con la lista de todos los firmantes">Descargar lista</a>';
            }
            $out .= '</li>';
        }

        $cvs = iconv("UTF-8", "ISO-8859-1", $cvs);
        if ($_GET["firmas"] == "true") {

            return $cvs;
        } else {
            return $out;
        }
    }
}

/**
 * getTwitterBox
 * 
 * Devuelve html con caja de twitter según palabras claves ingresadas por custom field en post acciones
 * 
 * <br />
 * 
 * @return html string Estructura html formateada para caja de twitter
 * @return descarga de cvs
 * @name getFirmas
 * @filesource function.php
 * @used-by single-post_acciones.php
 * 
 */
function getTwitterBox() {
    global $post;
    $out = "";
    if (get_field('_twitterBox', $post->ID) == true) {

        $filter = get_field('_twitterField', $post->ID);
        if (!$filter)
            $filter = 'elquintopoder';

        $out .= '<div id="feeds-box" class="twitterAction" data-filter="' . $filter . '" >';
        if ($filter == 'elquintopoder' || !$filter) {
            $out .= '<h2 class="label">En las redes</h2>';
        } else {
            $out .= '<h2 class="label">En las redes sobre: "' . $filter . '"</h2>';
        }
        $out .= '<ul>';
        $out .= '<li id="ajaxLoader"><img src="/wp-content/themes/eqp/css/ui/ajax-loader.gif" ></li>';
        $out .= '</ul>';
        $out .= '</div>';
    }
    return $out;
}

function get_thumb_src($pid) {
    $out = "";

    $out = wp_get_attachment_image_src(get_post_meta($pid, '_thumbnail_id', true), 'full');

    return $out[0];
}

/* * ***************************************************************************** formularios de publicación */

function get_publish_form() {
    $out = "";

    if (empty($_GET['author']) || empty($_GET['pType'])) {
        return false;
    }

    $author = $_GET['author'];
    $postType = $_GET['pType'];

    $user = get_userdata($_GET['author']);

    if ($postType == 'post') {
        echo '<form id="publicationForm_single" class="publishform" action="" method="post" enctype="multipart/form-data" >';

        echo '<h2>Hola ' . nombre_y_apellido($user->ID) . '</h2>';
        echo '<p>A través de este formulario podrás subir tu entrada a nuestra comunidad. Solo publicaremos entradas que sean de tu autoría.</p>';

        echo '<label for="postTitle">Título de tu Entrada (obligatorio)</label>';
        echo '<input id="postTitle" type="text" maxlength="70" name="postTitle" value="" placeholder="Título" required>';
        echo '<span class="formHelp">Máximo de 70 caracteres, quedan <span class="charCont">70</span> caracteres.</span>';

        echo '<label for="postContent">Texto (obligatorio)</label>';

        wp_editor('', 'postcontentbox', array(
            'textarea_name' => 'postContent',
            'media_buttons' => false,
            'quicktags' => array(
                'buttons' => 'strong,em,link,block,del,ins,img,ul,ol,li,code,more,spell,close'
            )
        ));
//        $out .= '<textarea id="postContentBox" name="postContent" placeholder="Descripción de la Entrada" required data-type="textarea" ></textarea>';
        echo '<span class="formHelp">Sugerimos un máximo de 3 mil carácteres</span>';
        echo '<label for="postCategory">¿En qué tema quieres publicar el contenido? (obligatorio)</label>';
        echo '<select name="postCategory" data-type="select" required>';
        echo '<option value="" >Selecciona</option>';
        echo categoryOptions();
        echo '</select>';

        echo '<label for="postBajada">Bajada (opcional)</label>';
        echo '<input type="text" name="postBajada" value="" placeholder="Bajada" >';
        echo '<span class="formHelp">Sugerimos un máximo de 300 carácteres</span>';

        echo '<label for="postDestacado">Destacado (opcional)</label>';
        echo '<textarea id="postDestacado" name="postDestacado" data-type="textarea" ></textarea>';
        echo '<span class="formHelp">Sugerimos un máximo de 300 carácteres</span>';

        echo '<label for="fotoUpload">Foto de tu Entrada (opcional)</label>';
        echo '<input type="file" name="fotoUpload" >';

        echo '<label for="videoUpload">Video de tu Entrada (opcional)</label>';
        echo '<p>Puedes subir video insertando la URL del video alojado en You Tube, Vimeo o Daily Motion.</p>';
        echo '<input type="text" name="videoUpload" value="" placeholder="http://www.youtube.com/watch?v=txqiwrbYGrs">';

        echo '<p class="checkboxCont">';
        echo '<span><input type="checkbox" name="termsConditions" value="acepto" required ></span>';
        echo '<label for="termsConditions">Acepto los <a href="/condiciones-de-uso" title="Términos y Condiciones" >Términos y Condiciones</a></label>';
        echo '</p>';

        echo '<input type="hidden" name="postType" value="post">';
        echo '<input type="hidden" name="action" value="ajax_zone">';
        echo '<input type="hidden" name="func" value="publicarCosas">';
        echo '<input type="hidden" name="postStatus" value="draft">';
        echo '<input type="hidden" name="postAuthor" value="' . $author . '">';

        echo '<input id="SendStuff" type="submit" value="Enviar a Edición" class="goHome gac" data-goal="btn-publicar-entrada">';

        echo '</form>';
    }
    elseif ($postType == 'post_fotos') {

        echo '<form id="publicationForm_single" action="" method="post" enctype="multipart/form-data" >';

        echo '<h2>Hola ' . nombre_y_apellido($user->ID) . '</h2>';
        echo '<p>A través de este formulario podrás subir una foto a nuestra comunidad. Si no es una foto original, te recomendamos cites al autor y la licencia que te permite compartirla.</p>';

        echo '<label for="postTitle">Título de la Foto (obligatorio)</label>';
        echo '<input id="postTitle" type="text" maxlength="70" name="postTitle" value="" placeholder="Título" required>';
        echo '<span class="formHelp">Máximo de 70 caracteres, quedan <span class="charCont">70</span> caracteres.</span>';

        echo '<label for="postCategory">¿En qué tema quieres publicar la foto? (obligatorio)</label>';
        echo '<select name="postCategory" required data-type="select" >';
        echo '<option value="" >Selecciona</option>';
        echo categoryOptions();
        echo '</select>';
        echo '<label for="fotoUpload">Selecciona tu Foto</label>';
        echo '<input type="file" name="fotoUpload" required >';

        echo '<label for="postContent">Descripción de tu foto (opcional)</label>';
        echo '<textarea id="postContentBox" name="postContent" placeholder="Descripción de la Foto" data-type="textarea" ></textarea>';

        echo '<p class="checkboxCont">';
        echo '<span><input type="checkbox" name="termsConditions" value="acepto" required ></span>';
        echo '<label for="termsConditions">Acepto los <a href="/condiciones-de-uso" title="Términos y Condiciones" >Términos y Condiciones</a></label>';
        echo '</p>';

        echo '<input type="hidden" name="postType" value="post_fotos">';
        echo '<input type="hidden" name="action" value="ajax_zone">';
        echo '<input type="hidden" name="func" value="publicarCosas">';
        echo '<input type="hidden" name="postStatus" value="publish">';
        echo '<input type="hidden" name="postAuthor" value="' . $author . '">';

        echo '<input id="SendStuff" type="submit" value="Publicar" class="goHome gac" data-goal="btn-publicar-foto">';

        echo '</form>';
    }
    elseif ($postType == 'post_videos') {
        echo '<form id="publicationForm_single" action="" method="post" enctype="multipart/form-data" >';

        echo '<h2>Hola ' . nombre_y_apellido($user->ID) . '</h2>';
        echo '<p>A través de este formulario podrás compartir un video en nuestra comunidad, subiendo la URL (dirección en Internet) donde está alojado el video.</p>';

        echo '<label for="postTitle">Título del Video (obligatorio)</label>';
        echo '<input id="postTitle" type="text" maxlength="70" name="postTitle" value="" placeholder="Título" required>';
        echo '<span class="formHelp">Máximo de 70 caracteres, quedan <span class="charCont">70</span> caracteres.</span>';

        echo '<label for="postCategory">¿En qué tema quieres publicar el video? (obligatorio)</label>';
        echo '<select name="postCategory" required data-type="select" >';
        echo '<option value="" >Selecciona</option>';
        echo categoryOptions();
        echo '</select>';

        echo '<p>Inserta la URL (dirección de Internet) del video</p>';
        echo '<input type="text" name="videoUpload" value="" required placeholder="http://www.youtube.com/watch?v=txqiwrbYGrs">';

        echo '<label for="postContent">Descripción de tu video (opcional)</label>';
        echo '<textarea id="postContent" name="postContent" placeholder="Descripción de la Video" data-type="textarea" ></textarea>';

        echo '<p class="checkboxCont">';
        echo '<span><input type="checkbox" name="termsConditions" value="acepto" required ></span>';
        echo '<label for="termsConditions">Acepto los <a href="/condiciones-de-uso" title="Términos y Condiciones" >Términos y Condiciones</a></label>';
        echo '</p>';

        echo '<input type="hidden" name="postType" value="post_videos">';
        echo '<input type="hidden" name="action" value="ajax_zone">';
        echo '<input type="hidden" name="func" value="publicarCosas">';
        echo '<input type="hidden" name="postStatus" value="publish">';
        echo '<input type="hidden" name="postAuthor" value="' . $author . '">';

        echo '<input id="SendStuff" type="submit" value="Publicar" class="goHome gac" data-goal="btn-publicar-video">';

        echo '</form>';
    }
    elseif ($postType == 'post_acciones') {
        echo '<form id="publicationForm_single" action="" method="post" enctype="multipart/form-data" >';

        echo '<h2>Hola ' . nombre_y_apellido($user->ID) . '</h2>';
        echo '<p>Una acción es una carta pública, dirigida a una persona u organización, a través de la cual podrás solicitar firmas de apoyo para tu causa. Te recomendamos plantear con claridad el objetivo que buscas con la carta y la meta en número de firmas que quieres reunir.</p>';

        echo '<label for="postTitle">Título de tu acción (obligatorio)</label>';
        echo '<input id="postTitle" type="text" maxlength="70" name="postTitle" value="" placeholder="Título" required>';
        echo '<span class="formHelp">Máximo de 70 caracteres, quedan <span class="charCont">70</span> caracteres.</span>';

        echo '<label for="postContent">Texto de la carta de tu acción (obligatorio)</label>';
        echo '<textarea name="postContent" placeholder="Descripción de la Acción" data-type="textarea" ></textarea>';

        echo '<label for="postCategory">¿En qué tema quieres publicar la acción? (obligatorio)</label>';
        echo '<select name="postCategory" required data-type="select" >';
        echo '<option value="" >Selecciona</option>';
        echo categoryOptions();
        echo '</select>';

        echo '<label for="actionGoal">¿Cuántas firmas necesitas reunir? (obligatorio)</label>';
        echo '<input id="actionGoal" type="text" name="actionGoal" value="" placeholder="2000" required>';

        echo '<p class="checkboxCont">';
        echo '<span><input type="checkbox" name="termsConditions" value="acepto" required ></span>';
        echo '<label for="termsConditions">Acepto los <a href="/condiciones-de-uso" title="Términos y Condiciones" >Términos y Condiciones</a></label>';
        echo '</p>';

        echo '<input type="hidden" name="postType" value="post_acciones">';
        echo '<input type="hidden" name="action" value="ajax_zone">';
        echo '<input type="hidden" name="func" value="publicarCosas">';
        echo '<input type="hidden" name="postStatus" value="publish">';
        echo '<input type="hidden" name="postAuthor" value="' . $author . '">';

        echo '<input id="SendStuff" type="submit" value="Publicar" class="goHome gac" data-goal="btn-publicar-accion">';

        echo '</form>';
    }
    elseif ($postType == 'propuestas') {
        echo '<form id="publicationForm_single" action="" method="post" enctype="multipart/form-data" >';

        echo '<h2>Hola ' . nombre_y_apellido($user->ID) . '</h2>';
        echo '<p>Una propuesta es una iniciativa dirigida al candidato a alcalde, la idea es que expeses una necesidad de tu comuna para que sea apoyada por otras personas con el objetivo que el candidato la responda. Te sugerimos que expongas el problema de forma clara y concisa.</p>';

        echo '<label for="postTitle">Título de tu Propuesta (obligatorio)</label>';
        echo '<input id="postTitle" type="text" maxlength="70" name="postTitle" value="" placeholder="Título" required>';
        echo '<span class="formHelp">Máximo de 70 caracteres, quedan <span class="charCont">70</span> caracteres.</span>';

        echo '<label for="postContent">Texto de la propuesta (obligatorio)</label>';
        echo '<textarea name="postContent" placeholder="Descripción de la Propuesta" data-type="textarea" ></textarea>';

        echo '<label for="postComuna">¿En qué comuna quieres publicar? (obligatorio)</label>';
        echo '<select name="postComuna" required data-type="select" >';
        echo '<option value="" >Selecciona</option>';
        echo comunasOptions();
        echo '</select>';

        echo '<label for="postTags">Escribe las etiquetas que describen tu propuesta</label>';
        echo '<input id="postTags" type="text" name="postTags" value="" placeholder="Urbanismo, Cultura, Ciclovías" required data-spotlight="' . getTemasForSpotlight() . '"/>';
        echo '<span class="formHelp">Para agregar más de una etiqueta debes separarlas por coma.</span>';
        echo '<div id="postTagsHolder"></div>';

        echo '<p class="checkboxCont">';
        echo '<span><input type="checkbox" name="termsConditions" value="acepto" required ></span>';
        echo '<label for="termsConditions">Acepto los <a href="/condiciones-de-uso" title="Términos y Condiciones" >Términos y Condiciones</a></label>';
        echo '</p>';

        echo '<input type="hidden" name="postType" value="propuestas">';
        echo '<input type="hidden" name="action" value="ajax_zone">';
        echo '<input type="hidden" name="func" value="publicarCosas">';
        echo '<input type="hidden" name="postStatus" value="publish">';
        echo '<input type="hidden" name="postAuthor" value="' . $author . '">';

        echo '<input id="SendStuff" type="submit" value="Publicar" class="goHome gac" data-goal="btn-publicar-propuesta">';

        echo '</form>';
    }
}

//////////////////////////////////////////////////////////////////////////////// Auxiliares
// social count
function get_tweets($url) {

    $json_string = file_get_contents('http://urls.api.twitter.com/1/urls/count.json?url=' . $url);
    $json = json_decode($json_string, true);

    return intval($json['count']);
}

function get_likes($url) {

    $json_string = file_get_contents('http://graph.facebook.com/?ids=' . $url);
    $json = json_decode($json_string, true);

    $total_count = 0;

    if (isset($json[$url]['shares']))
        $total_count += intval($json[$url]['shares']);
    if (isset($json[$url]['likes']))
        $total_count += intval($json[$url]['likes']);

    return $total_count;
}

function get_plusones($url) {

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, "https://clients6.google.com/rpc");
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"' . $url . '","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
    $curl_results = curl_exec($curl);
    curl_close($curl);

    $json = json_decode($curl_results, true);

    return intval($json[0]['result']['metadata']['globalCounts']['count']);
}

function get_shares($provider, $pid) {
    $shares = get_post_meta($pid, '_shares_' . $provider, true);
    return intval($shares);
}

function get_socialEchoes($url, $title, $pid, $single = false, $showComments = true, $customMessage = false, $ga_stuff = false) {
    $thePost = get_post($pid);
    $pType = $thePost->post_type;
    $analyticsClass = "";
    $analyticsDataStringFb = "";
    $analyticsDataStringTwt = "";

    $urlencoded = urldecode($url);
    $title = urlencode($title);
    
    $shortUrl = $_SERVER['HTTP_HOST'] . '/?p=' . $pid;

    $imageThumb = '';

    $realtitle = get_the_title( $thePost->ID );
    $realtitle = preg_replace('/\#/', '', $realtitle);
    if ($pType == 'post') {
        $mensaje .= 'lee, opina y comparte la entrada '.  $realtitle .' en elquintopoder.cl ';
    }
    if ($pType == 'post_fotos') {
        $mensaje .= 'mira, opina y comparte la foto '.  $realtitle .' en elquintopoder.cl ';
    }
    if ($pType == 'post_videos') {
        $mensaje .= 'lee, opina y comparte el video '.  $realtitle .' en elquintopoder.cl ';
    }
    if ($pType == 'post_acciones') {
        $mensaje .= 'lee, opina y comparte la acción '.  $realtitle .' en elquintopoder.cl ';
    }
    if ($pType == 'especiales') {
        $mensaje .= 'lee, opina y comparte la expecial '.  $realtitle .' en elquintopoder.cl ';
    }
    if ($pType == 'propuestas') {
        $mensaje .= 'lee, opina y comparte la propuesta '.  $realtitle .' en elquintopoder.cl ';
    }

    if (has_post_thumbnail($pid)) {
        $imageID = get_post_thumbnail_id($pid);
        $imageThumb = wp_get_attachment_url($imageID);
    } else {
        $imageThumb = 'http://www.elquintopoder.cl/logo/logofb.jpg';
    }

    if ($customMessage) {
        $mensaje = $customMessage;
    }

    $singleClass = "";
    if ($single != false) {
        $singleClass = "singleSocial";
    }
    
    if( !empty($ga_stuff) ){ 
        $analyticsClass = 'socialganalytics';
        $analyticsDataUnified = array();
        if( isset( $ga_stuff['socialaction'] ) ){ $analyticsDataUnified[] = 'data-ga_socialaction="'. $ga_stuff['socialaction'] .'"'; }
        if( isset( $ga_stuff['opt_target'] ) ){ $analyticsDataUnified[] = 'data-ga_opt_target="'. $ga_stuff['opt_target'] .'"'; }
        if( isset( $ga_stuff['opt_pagePath'] ) ){ $analyticsDataUnified[] = 'data-ga_opt_pagePath="'. $ga_stuff['opt_pagePath'] .'"'; }
        
        $analyticsDataStringFb = implode(' ', $analyticsDataUnified) . ' data-ga_network="Facebook"';
        $analyticsDataStringTwt = implode(' ', $analyticsDataUnified) . ' data-ga_network="Twitter"';
    }
    
    $fblink = 'http://www.facebook.com/sharer.php?s=100&p[title]=' . $title . '&p[summary]=' . urldecode($mensaje) . '%21&p[url]=' . $urlencoded . '&&p[images][0]=' . $imageThumb;
    $twlink = 'https://twitter.com/intent/tweet?text=' . $title . '+' . $shortUrl . '&hashtags=5poder&via=elquintopoder';
    $echoes = "";

    $echoes .= '<li><a class="sEchoes ' . $singleClass . ' fb evt '. $analyticsClass .'" '. $analyticsDataStringFb .'  data-func="anadirShares" data-pid="' . $pid . '" data-provider="facebook" data-noprevent="true" href="' . $fblink . '" target="_blank" title="Comparte en Facebook">' . get_shares('facebook', $pid) . '</a></li>';
    $echoes .= '<li><a class="sEchoes ' . $singleClass . ' twt evt '. $analyticsClass .'" '. $analyticsDataStringTwt .' data-func="anadirShares" data-pid="' . $pid . '" data-provider="twitter" data-noprevent="true" href="' . $twlink . '" target="_blank" title="Comparte en Twitter">' . get_shares('twitter', $pid) . '</a></li>';
    $echoes .= $showComments ? '<li><a class="sEchoes ' . $singleClass . ' wp" href="' . $url . '#posted-comments" title=" Ver Comentarios">' . return_comments_number($pid) . '</a></li>' : '';

    return $echoes;
}

// actions!
function accionesInsert($postid, $email, $nombre, $userid = 0, $customFields = array()) {
    global $wpdb;

    if (($postid && $email && $nombre) && checkVoteValidity($postid, $email, $customFields)) {
        
        $inputArray = array(
            'user_id' => $userid,
            'post_id' => $postid,
            'email' => $email,
            'nombre' => $nombre
        );
        
        foreach( (array)$customFields as $key => $val ){
            $inputArray[ $key ] = $val;
        }
        
        $wpdb->insert($wpdb->acciones, $inputArray);

        update_post_meta($postid, '_adhesiones', get_action_votes($postid));
        sendActionEmail_byHundred($postid, $email);
        
        return true;
    }
    
    else {
        return false;
    }
}

function get_action_votes($postid) {
    global $wpdb;
    $number = $wpdb->get_var($wpdb->prepare("
        SELECT COUNT(post_id) 
        FROM $wpdb->acciones 
        WHERE $wpdb->acciones.post_id = %d
    ", $postid ));
    
    return $number;    //se cambió * por post_id
}

function my_actions($userid, $ppp = 3, $offset = 0, $editable = false, $jsClass = 'evt') {
    global $wpdb;
    $out = "";
    
    $sqlQuery = "
        SELECT post_id
        FROM $wpdb->acciones
        WHERE user_id = %d 
        LIMIT %d , %d 
    ";
    $actionsQuery = $wpdb->get_results( $wpdb->prepare( $sqlQuery, $userid, $offset, $ppp ) );

    if (!empty($actionsQuery)) {
        foreach ($actionsQuery as $action) {
            $thePost = get_post($action->post_id);

            $tenemos = number_format(get_action_votes($thePost->ID) * 1, 0, ',', '.');
            $necesitamos = number_format(get_field('requeridos', $thePost->ID) * 1, 0, ',', '.');
            if (empty($tenemos)) {
                $tenemos = '0';
            }

            $out .= '<li class="userVotedForThis">';
            $out .= '<div class="actionData">';
            $out .= '<h3><a href="' . get_permalink($thePost->ID) . '" title="' . get_the_title($thePost->ID) . '">' . get_the_title($thePost->ID) . '</a></h3>';
            $out .= '<p>' . date_i18n("d \d\e F, Y", strtotime($thePost->post_date)) . '</p>';
            $out .= '</div>';

            if ($editable) {
                $out .= '<a class="' . $jsClass . ' action-ca" data-func="unVoteForAction" data-pid="' . $thePost->ID . '" data-usid="' . $userid . '" href="#" title="Deshaderir de ' . get_the_title($thePost->ID) . '" rel="nofollow">Desadherir</a>';
            } else {
                $out .= '<ul class="actions-board resumed">';
                $out .= '<li>Tenemos <span>' . $tenemos . '</span></li>';
                $out .= '<li><a href="' . get_permalink($thePost->ID) . '">Necesitamos <span>' . $necesitamos . '</span></a></li>';
                $out .= '</ul>';
            }

            $out .= '</li>';
        }
    }
    return $out;
}

function sendActionEmail_byHundred($postid, $email) {
    $thepost = get_post($postid);
    $postAuthor = get_userdata($thepost->post_author);

    $voteCount = (get_action_votes($postid) * 1) / 100;

    if (get_action_votes($postid) == get_field('requeridos', $postid)) {
        $titulo = 'Servicio Firma de Acciones';
        $subtitulo = '¡Felicitaciones!';
        $contenido = '<p>Tu Acción <strong>"' . $thepost->post_title . '"</strong> ha llegado a la meta de ' . get_field('requeridos', $postid) . '</strong></p>';
        $contenido .= '<p><a href="' . get_permalink($thepost->ID) . '" title="' . $thepost->post_title . '">' . get_permalink($thepost->ID) . '</a></p>';
        $contenido .= '<p>Saludos,</p>';
        $contenido .= '<p>Equipo El Quinto Poder</p>';
        $contenido .= '<p><a href="' . home_url() . '" title="El Quinto poder">' . home_url() . '</a></p>';
        $subject = 'Tu Acción ha llegado a su meta';
        $from = 'acciones@elquintopoder.cl';
        $destino = $postAuthor->user_email;
        mailMe($titulo, $subtitulo, $contenido, $subject, $from, $destino);
    } elseif (is_int($voteCount)) {
        $titulo = 'Servicio Firma de Acciones';
        $subtitulo = '¡Felicitaciones!';
        $contenido = '<p>Tu Acción <strong>"' . $thepost->post_title . '"</strong> ha llegado a <strong>' . get_action_votes($postid) . ' Firmas!.</strong> de un total de ' . get_field('requeridos', $postid) . '</p>';
        $contenido .= '<p><a href="' . get_permalink($thepost->ID) . '" title="' . $thepost->post_title . '">' . get_permalink($thepost->ID) . '</a></p>';
        $contenido .= '<p>Saludos,</p>';
        $contenido .= '<p>Equipo El Quinto Poder</p>';
        $contenido .= '<p><a href="' . home_url() . '" title="El Quinto poder">' . home_url() . '</a></p>';
        $subject = get_action_votes($postid) . ' personas han firmado tu acción';
        $from = 'acciones@elquintopoder.cl';
        $destino = $postAuthor->user_email;
        mailMe($titulo, $subtitulo, $contenido, $subject, $from, $destino);
    }
}

function checkVoteValidity($postid, $email, $custom = array()) {
    global $wpdb;
    
    $sqlQuery = "
        SELECT email
        FROM $wpdb->acciones
        WHERE email= %s
        AND post_id= %d
    ";
    $exist = $wpdb->get_results( $wpdb->prepare( $sqlQuery, $email, $postid ) ); //se cambio * por email

    if (!$exist) {
        return true;
    } else {
        return false;
    }
}

function deleteActionVotes($pid, $usid) {
    global $wpdb;
    $sqlQuery = "
        DELETE FROM $wpdb->acciones 
        WHERE post_id = %d 
        AND user_id = %d 
    ";
    $wpdb->query( $wpdb->prepare( $sqlQuery, $pid, $usid ) );
    update_post_meta($pid, '_adhesiones', get_action_votes($pid));
}


// municipales 2012
function apoyarPropuesta($pid) {
    $currentCount = intval(getApoyos($pid));
    $currentCount++;
    return update_post_meta($pid, 'apoyos_propuesta', $currentCount);
}

function getApoyos($pid, $echo = false) {
    if ($echo) {
        echo intval(get_post_meta($pid, 'apoyos_propuesta', true));
    } else {
        return get_post_meta($pid, 'apoyos_propuesta', true);
    }
}

function getRespuestas($pid, $comuna) {
    $apoyarBox = "";
    $boxes = "";

    $candidatos = get_users(array('role' => 'candidato'));

    foreach ((array) $candidatos as $candidato) {
        if (get_field('candidato_por', 'user_' . $candidato->ID) == $comuna) {
            $tituloRespuesta = '';
            $excerptRespuesta = "Aún no responde esta propuesta...";
            $theLink = "";
            $apoyarBox = "";
            $queryArgs = array(
                'post_type' => 'post_respuestas',
                'post_status' => 'publish',
                'posts_per_page' => 1,
                'author' => $candidato->ID,
                'tax_query' => array(array('taxonomy' => 'comunas', 'field' => 'slug', 'terms' => $comuna)),
                'meta_key' => 'respuesta_a',
                'meta_value' => $pid
            );

            $respuestaQuery = new WP_Query($queryArgs);
            if ($respuestaQuery->have_posts()) {
                while ($respuestaQuery->have_posts()) {
                    $respuestaQuery->the_post();
                    $apoyarBox = '<ul class="respuesta-support" >';
                    $apoyarBox .= '<li>';
                    $apoyarBox .= '<a class="thumbs down evt" href="#" data-func="apoyarRespuesta" data-pid="' . $respuestaQuery->post->ID . '" data-action="iDontLike" title="Desaprovar" rel="nofollow"></a>';
                    $apoyarBox .= '<span class="thumbs-num">' . intval(get_post_meta($respuestaQuery->post->ID, 'iDontLike', true)) . '</span>';
                    $apoyarBox .= '</li>';
                    $apoyarBox .= '<li>';
                    $apoyarBox .= '<a class="thumbs up evt" href="#" data-func="apoyarRespuesta" data-pid="' . $respuestaQuery->post->ID . '" data-action="iLike" title="Aprovar" rel="nofollow"></a>';
                    $apoyarBox .= '<span class="thumbs-num">' . intval(get_post_meta($respuestaQuery->post->ID, 'iLike', true)) . '</span>';
                    $apoyarBox .= '</li>';
                    $apoyarBox .= '</ul>';

                    $tituloRespuesta = '<p><strong>' . get_the_title() . '</strong></p>';
                    $excerptRespuesta = cortar(get_the_excerpt(), 100);
                    $theLink = '<a class="keepReading evt action-ca municipales" data-func="verRespuestaCompleta" data-pid="' . $respuestaQuery->post->ID . '" href="/perfil-de-usuario/?user=' . $candidato->ID . '" title="Seguir Leyendo" rel="contents">Seguir leyendo</a>';
                }
            }
            wp_reset_query();
            wp_reset_postdata();
            $respuestaQuery->rewind_posts();

            if (isThisTheCandidato($candidato->ID) && $theLink == '') {
                $theLink = '<button class="evt action-ca" data-func="responderPropuesta" data-propuestaPid="' . $pid . '" data-comunaSlug="' . $comuna . '" data-candidatoID="' . $candidato->ID . '" >Responder</button>';
            }

            $responded = $tituloRespuesta != '' ? 'responded' : '';

            $boxes .= '<div class="respuesta-box ' . $responded . '" >';
            $boxes .= '<div class="respuesta-meta" >';
            $boxes .= '<div class="usr-avatar-holder">';
            $boxes .= get_simple_local_avatar($candidato->ID, 40);
            $boxes .= '</div>';
            $boxes .= $apoyarBox;
            $boxes .= '</div>';
            $boxes .= '<div class="respuesta-body">';
            $boxes .= '<h4 class="candidato-name"><a href="/perfil-de-usuario/?user=' . $candidato->ID . '" title="Ir al perfil de ' . nombre_y_apellido($candidato->ID) . '" rel="contents">' . nombre_y_apellido($candidato->ID) . '</a></h4>';
            $boxes .= $tituloRespuesta;
            $boxes .= '<p>' . $excerptRespuesta . '</p>';
            $boxes .= $theLink;
            $boxes .= '</div>';
            $boxes .= '</div>';
        }
    }
    return $boxes;
}

function getRespuestasNumber($usid) {
    global $wpdb;
    $sqpQuery = "
        SELECT COUNT(ID) 
        FROM $wpdb->posts 
        WHERE $wpdb->posts.post_status = 'publish' 
        AND $wpdb->posts.post_type = 'post_respuestas' 
        AND $wpdb->posts.post_author = %d
    ";
    $number = $wpdb->get_var( $wpdb->prepare( $sqpQuery, $usid ) );
    return $number;
}

function isThisTheCandidato($usid) {
    global $current_user;
    if (is_user_logged_in() && $current_user->ID == $usid && in_array('candidato', $current_user->roles)) {
        return true;
    }
    return false;
}

function getComunaInfoyCandidatos($comunaSlug, $parentId, $echo = false) {
    $out = "";
    $candidatos = getCandidatosPorComuna($comunaSlug);
    $comunas = get_pages(array('child_of' => $parentId));
    $comuna = false;
    foreach ($comunas as $com) {
        if ($com->post_name == $comunaSlug) {
            $comuna = $com;
            break;
        }
    }
    if ($comuna != false) {
        $out .= '<div class="comunaBoxHolder">';
        $out .= '<h2 class="label"><a href="' . get_permalink($comuna->ID) . '" title="Ver propuestas para ' . $comuna->post_title . '" rel="subsection">' . $comuna->post_title . '</a></h2>';
        $out .= '<ul class="candidatos-list">';

        foreach ((array) $candidatos as $cand) {
            $out .= '<li>';
            $out .= '<div class="usr-avatar-holder chico">';
            $out .= get_simple_local_avatar($cand->ID, 25);
            $out .= '</div>';
            $out .= '<div class="candidato-info" >';
            $out .= '<h4><a href="/perfil-de-usuario/?user=' . $cand->ID . '" title="Ir al perfil de ' . nombre_y_apellido($cand->ID) . '" rel="contents">' . nombre_y_apellido($cand->ID) . '</a> <span class="candidato-partido">(' . get_field('partido_candidato', 'user_' . $cand->ID) . ')</span></h4>';
            $out .= '<p><strong>Respuestas: </strong>' . countPostOverTime('post_respuestas', $cand->ID) . '</p>';
            $out .= '</div>';
            $out .= '</li>';
        }
        $out .= '</ul>';
        $out .= '<div class="comuna-metadata">';
        $out .= '<p><strong>Propuestas: </strong>' . countPropuestasByComuna($comunaSlug) . '</p>';
        $out .= '<p><strong>Población: </strong>' . get_field('poblacion', $comuna->ID) . '</p>';
        $out .= '<p><strong>Universo Electoral: </strong>' . get_field('universo_electoral', $comuna->ID) . '</p>';
        $out .= '</div>';
        $out .= '</div>';
    }
    if ($echo) {
        echo $out;
    } else {
        return $out;
    }
}

function cmp($a, $b) {
    if ($a->meta_order_num == $b->meta_order_num) {
        return 0;
    }
    return ($a->meta_order_num < $b->meta_order_num) ? -1 : 1;
}

function getCandidatosPorComuna($comunaSlug) {
    $filteredCandidatos = array();
    $candidatos = get_users(array(
        'role' => 'candidato'
            ));

    foreach ($candidatos as $cand) {
        if (get_field('candidato_por', 'user_' . $cand->ID) == $comunaSlug) {
            $cand->meta_order_num = get_user_meta($cand->ID, 'candidato_order_num', true);
            $filteredCandidatos[] = $cand;
        }
    }
    usort($filteredCandidatos, 'cmp');
    return $filteredCandidatos;
}

function get_PropuestasTabs($orden, $offset, $tQuery) {
    $out = "";

    $args = array(
        'post_type' => 'propuestas',
        'post_status' => 'publish',
        'posts_per_page' => 5,
        'meta_key' => 'apoyos_propuesta',
        'orderby' => 'meta_value_num',
        'offset' => $offset,
        'tax_query' => $tQuery
    );
    if ($orden == 'masNuevas') {
        unset($args['meta_key']);
        $args['orderby'] = 'date';
    }
    $propuestaQuery = new WP_Query($args);
    $out .= '<div id="currTab" class="tabs-content more-active">';
    $out .= '<ul class="article-list actions regular">';
    if ($propuestaQuery->have_posts()) {
        while ($propuestaQuery->have_posts()) {
            $propuestaQuery->the_post();
            $comunaSlug = wp_get_post_terms($propuestaQuery->post->ID, 'comunas', array("fields" => "slugs"));
            $theMetaData = get_query_post_metadata($propuestaQuery, false, $comunaSlug[0]);

            $out .= '<li class="clearfix">';
            $out .= '<div class="article-holder">';
            $out .= '<a href="' . get_permalink() . '" title="Ir a ' . get_the_title() . '" rel="contents">' . get_the_title() . '</a>';
            $out .= '<div class="item-metadata">';
            $out .= '<div class="usr-avatar-holder">';
            $out .= $theMetaData['avatar'];
            $out .= '</div>';
            $out .= $theMetaData['authorMeta'];
            $out .= '</div>';
            $out .= '</div>';

            $tenemos = getApoyos($propuestaQuery->post->ID);
            $tenemos = $tenemos == 0 || $tenemos == false || $tenemos == '' ? '0' : $tenemos;
            $out .= '<a class="action-ca inBigList" href="' . get_permalink() . '" title="Ir a ' . get_the_title() . '" rel="contents"><span>' . $tenemos . '</span> Ciudadanos apoyan</a>';

            $out .= '</li>';
        }
    }
    $out .= '</ul>';
    $out .= '</div>';

    wp_reset_postdata();
    wp_reset_query();
    return $out;
}

function getCandidatosToPortada($comunaSlug) {
    $out = "";
    $candidatosAr = getCandidatosPorComuna($comunaSlug);
    if (!empty($candidatosAr)) {
        foreach ($candidatosAr as $candidato) {
            $out .= '<li class="clearfix">';
            $out .= get_simple_local_avatar($candidato->ID, 210);
            $out .= '<h2><a href="/perfil-de-usuario/?user=' . $candidato->ID . '" title="Ver Perfil de ' . nombre_y_apellido($candidato->ID) . '" rel="contents" >' . nombre_y_apellido($candidato->ID) . '</a></h2>';
//        $out .= '<p>'. cortar( get_user_meta( $candidato->ID, 'description', true ), 60 ) .'</p>';
            $out .= '<p><strong>Partido:</strong> ' . get_field('partido_candidato', 'user_' . $candidato->ID) . '</p>';
            $out .= '<p><strong>Respuestas Publicadas:</strong> ' . getRespuestasNumber($candidato->ID) . '</p>';
            $out .= '</li>';
        }
    }
    return $out;
}

function get_allPropuestas($taxQuery) {
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    $salida = "";
    $args = array(
        'post_type' => 'propuestas',
        'order' => 'DESC',
        'posts_per_page' => 10,
        'post_status' => 'publish',
        'tax_query' => $taxQuery,
        'paged' => $paged,
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $comunaSlug = wp_get_post_terms($query->post->ID, 'comunas', array("fields" => "slugs"));
            $theMetaData = get_query_post_metadata($query, false, $comunaSlug[0]);

            $salida .= '<li class="clearfix">';
            $salida .= '<div class="article-holder">';
            $salida .= '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
            $salida .= '<div class="item-metadata">';
            $salida .= '<div class="usr-avatar-holder">';
            $salida .= $theMetaData['avatar'];
            $salida .= '</div>';
            $salida .= $theMetaData['authorMeta'];
            $salida .= '</div>';
            $salida .= '</div>';

            $tenemos = getApoyos($query->post->ID);
            $tenemos = $tenemos == 0 || $tenemos == false || $tenemos == '' ? '0' : $tenemos;
            $salida .= '<a class="action-ca inBigList" href="' . get_permalink() . '" title="Ir a ' . get_the_title() . '" rel="contents"><span>' . $tenemos . '</span> Ciudadanos apoyan</a>';

            $salida .= '</li>';
        }
    }
    $url = $_SERVER['REQUEST_URI'];
    $url = explode('page/', $url);
    $url = $url[0];

    echo '<div id="currTab" class="tabs-content more-active">';
    echo '<ul class="article-list actions regular">';
    echo $salida;
    echo '</ul>';
    pagination($query, $url);
    echo '</div>';

    wp_reset_postdata();
}

function comunasOptions() {
    $out = "";
    $args = array(
        'orderby' => 'name',
        'hide_empty' => 0
    );
    $comunas = get_terms('comunas', $args);
    foreach ($comunas as $com) {
        $out .= '<option value="' . $com->term_id . '" >' . $com->name . '</option>';
    }
    return $out;
}

function getTemasForSpotlight() {
    $out = "";
    $args = array(
        'orderby' => 'name',
        'hide_empty' => 0
    );
    $comunas = get_terms('temas', $args);
    foreach ($comunas as $com) {
        $out .= $com->name . ',';
    }
    $out = rtrim($out, ',');
    return $out;
}

function countPropuestasByComuna($comunaSlug) {
    $args = array(
        'post_type' => 'propuestas',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'tax_query' => array(array('taxonomy' => 'comunas', 'field' => 'slug', 'terms' => $comunaSlug))
    );
    $theQuery = new WP_Query($args);
    return $theQuery->post_count;
}

// userdatas
function crearUsuario($info) {
    global $wpdb;

    $newUser = wp_create_user(urldecode($info['usrLogin']), urldecode($info['usrPassword']), urldecode($info['mail']));
    
    if( function_exists('w3tc_pgcache_flush') ) { w3tc_pgcache_flush(); }
    
    if ( !is_wp_error($newUser) ) {

        $theuser = get_userdata($newUser);

        if ( $info['apellido'] ) {
            update_user_meta($theuser->ID, 'last_name', urldecode($info['apellido']));
        }
        if ( $info['suscribme'] ) {
            update_user_meta($theuser->ID, 'newsletter_suscriber', urldecode($info['suscribme']));
        }
        update_user_meta($theuser->ID, 'first_name', urldecode($info['nombre']));
        update_user_meta($theuser->ID, 'tipoCuenta', urldecode($info['tipocuenta']));

        $creds = array(
            'user_login' => $theuser->user_login,
            'user_password' => urldecode($info['usrPassword']),
            'remember' => true
        );

        $wpdb->query(
                "
            DELETE FROM $wpdb->usermeta 
            WHERE meta_key = 'first_name'
            AND meta_value = ''
            "
        );
        $wpdb->query(
                "
            DELETE FROM $wpdb->usermeta 
            WHERE meta_key = 'last_name'
            AND meta_value = ''
            "
        );

        $usercheck = wp_signon($creds, false);

        $titulo = 'Registro de usuarios';
        $subtitulo = 'Gracias por unirse a la Comunidad de El Quinto Poder';
        $contenido = '<p>Tu cuenta en la comunidad de El Quinto Poder ha sido activada de manera satisfactoria. Los datos de la cuenta son:</p>';
        $contenido .= '<p><strong>Nombre de Usuario:</strong> ' . urldecode($info['usrLogin']) . '</p>';
//        $contenido .= '<p><strong>Contraseña:</strong> '. urldecode( $info['usrPassword'] ) .'</p>';
        $contenido .= '<p>Puedes agregar y modificar datos de tu perfil de usuarios a través de la opción <a href="' . home_url() . '/perfil-de-usuario/?user=' . $newUser . '" title="Editar Datos" >Editar Datos</a></p>';
        $contenido .= '<p>Saludos,</p>';
        $contenido .= '<p>Equipo El Quinto Poder</p>';
        $subject = 'Registro de Usuarios';
        $from = 'registro@elquintopoder.cl';
        $destino = $info['mail'];

        mailMe($titulo, $subtitulo, $contenido, $subject, $from, $destino);

        return $theuser->ID;
    }
}

function saveUserData() {
    global $wpdb, $simple_local_avatars;

    $usid = intval( $_POST['usid'] );

    if ($usid) {

        //userdata
        $email = $_POST['usrEmail'];
        $pass = $_POST['usrPass'];

        $args = array(
            'ID' => $usid,
            'user_email' => $email,
            'user_pass' => $pass
        );

        if (!$email) {
            unset($args['user_email']);
        }

        if (!$pass) {
            unset($args['user_pass']);
        } else {

            $user = get_userdata($usid);

            $titulo = 'Perfil de Usuario';
            $subtitulo = 'Ha cambiado su información de ingreso';
            $contenido = '<p>Su informaci+ón de ingreso ha sido cambiada satisfactoriamente</p>';
            $contenido .= '<p><strong>Nombre de Usuario:</strong> ' . $user->user_email . '</p>';
            $contenido .= '<p><strong>Contraseña:</strong> ' . $pass . '</p>';
            $subject = 'Perfil de Usuario';
            $from = 'usuarios@elquintopoder.cl';
            $destino = $info['mail'];

            mailMe($titulo, $subtitulo, $contenido, $subject, $from, $destino);
        }

        if ($email || $pass) {
            wp_update_user($args);
        }

        $status = get_user_meta($usid, 'statusCuenta', true);
        if ($status == 'incompleta') {
            update_user_meta($usid, 'statusCuenta', 'incompleta');
        }

        // usermeta
        $description = $_POST['usrDescription'];
        if ($description) {
            update_user_meta($usid, 'description', $description);
        }

        $firstName = $_POST['usrFirstName'];
        if ($firstName) {
            update_user_meta($usid, 'first_name', $firstName);
        }

        $lastName = $_POST['usrLastName'];
        if ($lastName) {
            update_user_meta($usid, 'last_name', $lastName);
        }

        $facebook = $_POST['usrFacebook'];
        if ($facebook) {
            update_user_meta($usid, 'facebook', $facebook);
        }

        $twitter = $_POST['usrTwitter'];
        if ($twitter) {
            update_user_meta($usid, 'twitter', $twitter);
        }

        $license = $_POST['usrLicence'];
        if ($license) {
            update_user_meta($usid, 'licencia', $license);
        }

        // avatar
        $simple_local_avatars->edit_user_profile_update($usid);

        return true;
    } else {
        return false;
    }
}

function checkUserValidity($login, $email) {
    global $wpdb;
    
    $emailQuery = "SELECT user_email FROM $wpdb->users WHERE user_email = s%";
    $validEmail = $wpdb->get_var( $wpdb->prepare( $emailQuery, $email ) );
    
    $loginQuery = "SELECT user_login FROM $wpdb->users WHERE user_login = %s";
    $validLogin = $wpdb->get_var( $wpdb->prepare( $loginQuery, $login )  );

    if ($validEmail || $validLogin) { return false; }
    else { return true; }
}

function nombre_y_apellido($uid, $echo=false) {
    $theUser = get_userdata($uid);

    $provider = get_user_meta($uid, 'oa_social_login_identity_provider', true);

    if ($provider == 'Twitter' && (!$theUser->first_name || !$theUser->last_name)) {
        $theName = $theUser->display_name;
    } else {
        $theName = $theUser->first_name . ' ' . $theUser->last_name;
    }

    if ($theName == '' || $theName == ' ') {
        $theName = $theUser->nickname ? $theUser->nickname : $theUser->user_login;
    }

    if ($echo) {
        echo $theName;
    } else {
        return $theName;
    }
}

function twitterExampleEmail($uid) {
    global $wpdb;
    
    $pruebaQuery = "SELECT display_name FROM $wpdb->users WHERE ID = %d AND user_email LIKE '%@example.com%'";
    $prueba = $wpdb->get_results( $wpdb->prepare( $pruebaQuery, $uid  ) );
    
    if ($prueba) { return true; }
    else { return false; }
}

function get_perfil_social() {
    // @TODO limpiar funcion
    $out = "";
    $mail = "";

    if (is_user_logged_in()) {
        $current_user = wp_get_current_user();
//            $avatar = get_simple_local_avatar( $current_user->ID, 40 );
//            $authorName = nombre_y_apellido($current_user->ID);
        $userProvider = get_user_meta($current_user->ID, 'oa_social_login_identity_provider', true);
//            $actualURL = $_SERVER["REQUEST_URI"];

        if ($userProvider && (!$current_user->first_name || !$current_user->last_name || twitterExampleEmail($current_user->ID))) {
            if (!$_POST['okPerfil']) {
                $out .= '<div id="lightBox-wrapper" class="data-socialPerfil">';
                $out .= '<div id="loadPerfil" data-user="' . $current_user->ID . '">';
                $out .= '<form id="usrEditForm" method="post" action="' . get_bloginfo('home') . '">';
                $out .= '<div class="usr-avatar-holder">';
                $out .= get_simple_local_avatar($current_user->ID, 50);
                $out .= '<a id="changeAvatarAction" href="#" title="Cambiar Foto" class="evt" data-func="ChangeAvatar">Cambiar Foto</a>';
                $out .= '<input id="uploadAvatar" type="file" name="simple-local-avatar" >';
                $out .= '</div>';

                if (!$current_user->first_name) {
                    $valueName = $current_user->display_name;
                } else {
                    $valueName = $current_user->first_name;
                }

                $out .= '<div class="usr-profile-info">';
                $out .= '<p>Por favor, completa tu registro</p>';
                $out .= '<div id="usrGeneralInfo">';
                $out .= '<label for="usrFirstName" >Nombre*</label>';
                $out .= '<input type="text" name="usrFirstName" autocomplete="off" value="' . $valueName . '" placeholder="Ingrese su nombre" required >';
                $out .= '<label for="usrLastName" >Apellido*</label>';
                $out .= '<input type="text" name="usrLastName" autocomplete="off" value="' . $current_user->last_name . '" placeholder="Ingrese su apellido" required >';
                $out .= '<label for="usrEmail" >E-mail*</label>';
                if (!twitterExampleEmail($current_user->ID)) {
                    $mail = $current_user->user_email;
                }
                $out .= '<input type="email" name="usrEmail" autocomplete="off" value="' . $mail . '" placeholder="Ingrese su e-mail" required>';
                $out .= '<input type="submit" class="submitPerfil" value="Guardar Datos">';
                $out .= '<a id="cancelUser" class="submitPerfil" href="' . wp_logout_url(home_url()) . '" data-func="cancelPerfil">Cancelar</a>';
                $out .= '<input type="hidden" name="okPerfil" value="okPerfil" >';
                $out .= '<input type="hidden" name="usid" value="' . $current_user->ID . '" >';
                $out .= '</div>';
                $out .= '</div>';
                $out .= '</form>';
                $out .= ' </div>';
                $out .= '</div>';
            } else {
                update_user_meta($_POST['usid'], 'first_name', $_POST['usrFirstName']);
                update_user_meta($_POST['usid'], 'last_name', $_POST['usrLastName']);

                if (preg_match('/twitter/i', $current_user->user_url)) {
                    update_user_meta($_POST['usid'], 'twitter', $current_user->user_url);
                } elseif (preg_match('/facebook/i', $current_user->user_url)) {
                    update_user_meta($_POST['usid'], 'facebook', $current_user->user_url);
                }

                $args = array(
                    'ID' => $_POST['usid'],
                    'user_email' => $_POST['usrEmail']
                );
                wp_update_user($args);
            }
        }
    }
    return $out;
}

function getUserEditForm($userid) {
    $usuario = get_userdata($userid);
    $facebook = get_user_meta($usuario->ID, 'facebook', true);
    $twitter = get_user_meta($usuario->ID, 'twt_usr_url', true);
    $licencia = get_user_meta($usuario->ID, 'licencia', true);
    $liceneseOoptions = array(
        'CC BY' => 'CC BY : Reconocimiento',
        'CC BY-SA' => 'CC BY-SA : Reconocimiento Compartir Igual',
        'CC BY-ND' => 'CC BY-ND : Reconocimiento Sin Obra Derivada',
        'CC BY-NC' => 'CC BY-NC : Reconocimiento No Comercial',
        'CC BY-NC-SA' => 'CC BY-NC-SA : Reconocimiento No Comercial Compartir Igual',
        'CC BY-NC-ND' => 'CC BY-NC-ND : Reconocimiento No Comercial Sin Obra Derivada'
    );

    $out = '';
    $out .= '<div class="userInfoBox clearfix">';
    $out .= '<form id="usrEditForm" method="post" action="" enctype="multipart/form-data">';
    $out .= '<input type="hidden" name="usid" value="' . $userid . '" >';
    $out .= '<div class="usr-avatar-holder grande">';
    $out .= get_avatar($usuario->ID, 100);
    $out .= '<a id="changeAvatarAction" href="#" title="Cambiar Foto" class="evt" data-func="ChangeAvatar">Cambiar Foto</a>';
    $out .= '<input id="uploadAvatar" type="file" name="simple-local-avatar" >';
    $out .= '</div>';
    $out .= '<div class="fLeft noMargin userMetaBox">';
    $out .= '<fieldset class="inputSeparation">';
    $out .= '<label for="usrFirstName" >Nombre</label>';
    $out .= '<input type="text" name="usrFirstName" autocomplete="off" value="" placeholder="' . $usuario->first_name . '" >';
    $out .= '<label for="usrLastName" >Apellido</label>';
    $out .= '<input type="text" name="usrLastName" autocomplete="off" value="" placeholder="' . $usuario->last_name . '" >';
    $out .= '<label for="usrDescription" >Descríbete</label>';
    $out .= '<textarea name="usrDescription" autocomplete="off" placeholder="' . $usuario->user_description . '">'. $usuario->user_description  .'</textarea>';
    $out .= '<h2>Licenciamento de Contenidos</h2>';
    $out .= '<p>Selecciona que tipo de licenciamiento quieres aplicar a tus contenidos. Si tienes dudas, lee nuestras <a href="/condiciones-de-uso" title="ir a Condiciones de Uso" rel="section">Condiciones de Uso</a>.</p>';
    $out .= '<select name="usrLicence" required>';
    foreach ($liceneseOoptions as $key => $val) {
        $selected = $licencia == $key ? 'selected="selected"' : '';
        $out .= '<option value="' . $key . '" ' . $selected . '>' . $val . '</option>';
    }
    $out .= '</select>';
    $out .= '</fieldset>';
    $out .= '<fieldset class="inputSeparation">';
    $out .= '<h2>Datos de Acceso</h2>';
    $out .= '<div>';
    $out .= '<label for="usrEmail" >E-mail</label>';

    $email = !twitterExampleEmail($usuario->ID) ? $usuario->user_email : "";

    $out .= '<input type="email" name="usrEmail" autocomplete="off" value="" placeholder="' . $email . '" >';
    $out .= '</div>';
    $out .= '<div><a id="changePassBtn" class="action-ca nofloat" data-func="showPaswordsInput" href="#" title="Cambiar Contraseña" rel="nofollow">Cambiar Contraseña</a></div>';
    $out .= '<div class="changePass">';
    $out .= '<label for="usrPass" >Contraseña</label>';
    $out .= '<input type="password" autocomplete="off" name="usrPass" value="" placeholder="Escribe tu nueva contraseña" >';
    $out .= '<label for="usrPassRepeat" >Repite tu contraseña</label>';
    $out .= '<input type="password" autocomplete="off" name="usrPassRepeat" value="" placeholder="Repite tu nueva contraseña" >';
    $out .= '</div>';
    $out .= '<h2>Contacto</h2>';
    $out .= '<p>Agrega tu Perfil en Facebook y tu Timeline en Twitter de modo que los otros usuarios de El Quinto Poder puedan contactarte por otras vías.</p>';
    $out .= '<ul class="usrSocial">';
    $out .= '<li class="clearfix">';
    $out .= '<label for="usrFacebook" ><img src="' . get_bloginfo('template_directory') . '/css/ui/ico-facebook-xl.png" alt="Facebook"/></label>';
    $out .= '<input id="usrFacebook" type="text" name="usrFacebook" value="" placeholder="' . $facebook . '" >';
    $out .= '<span class="edit-form-help-text">Debes ingresar la dirección completa de tu usuario de facebook <strong>Ej: https://www.facebook.com/miusuario/</strong></span>';
    $out .= '</li>';
    $out .= '<li class="clearfix">';
    $out .= '<label for="usrTwitter" ><img src="' . get_bloginfo('template_directory') . '/css/ui/ico-twitter-xl.png" alt="Twitter"/></label>';
    $out .= '<input id="usrTwitter" type="text" name="usrTwitter" value="" placeholder="' . $twitter . '" >';
    $out .= '<span class="edit-form-help-text">Debes ingresar la dirección completa de tu usuario de twitter <strong>Ej: https://twitter.com/miusuario/</strong></span>';
    $out .= '</li>';
    $out .= '</ul>';
    $out .= '</fieldset>';
    $out .= '</div>';
    $out .= '</form>';
    $out .= '</div>';

    return $out;
}

function mailUserBtn($usid, $logged) {

    if ($logged) {
        $currentUser = wp_get_current_user();
        $currenUserId = $currentUser->ID;
    } else {
        $currenUserId = 0;
    }

    $out .= '<li><a class="evt" data-func="showEmailSendingForm" data-usid="' . $usid . '" data-logged="' . intval($logged) . '" data-currentUser="' . intval($currenUserId) . '" href="#" title="Enviar email a ' . nombre_y_apellido($usid) . '">';
    $out .= '<img src="' . get_bloginfo('template_directory') . '/css/ui/ico-mail-xl.png" alt="Enviar email a ' . nombre_y_apellido($usid) . '"/>';
    $out .= '</a></li>';
    echo $out;
}

function sendEmailToUser($targetUsr, $currUsr, $data) {
    $targetUser = get_userdata($targetUsr);
    if ($currUsr) {
        $currentUser = get_userdata($currUsr);
        $currUserName = nombre_y_apellido($currUsr);
        $currUserEmail = $currentUser->user_email;
    } else {
        $currUserName = $data['userName'];
        $currUserEmail = $data['userEmail'];
    }
    mailMe('Mensaje de contacto', 'El usuario ' . $currUserName . ' (' . $currUserEmail . ') le ha enviado un mensaje', '<p>' . $data['userMessage'] . '</p>', 'Mensaje de Contacto | El Quinto Poder', $currUserEmail, $targetUser->user_email);
    return true;
}

function categoryOptions() {
    $out = "";
    $args = array(
        'hide_empty' => 0,
        'exclude' => 1
    );
    $categories = get_categories($args);
    foreach ($categories as $cat) {
        $out .= '<option value="' . $cat->term_id . '" >' . $cat->name . '</option>';
    }
    return $out;
}

function sendContactForm() {
    $titulo = 'Contacto';
    $subtitulo = 'Ha llegado un mensaje de Contacto por parte de ' . $_POST['nombre'];
    $contenido = apply_filters( 'the_content', $_POST['mensaje'] );
    $subject = $_POST['asunto'];
    $from = $_POST['email'];
    $destino = get_bloginfo('admin_email');
    mailMe($titulo, $subtitulo, $contenido, $subject, $from, $destino);
}

function sendSugerenciaEspecial() {
    $titulo = 'Sugerencia de Especial';
    $subtitulo = 'Ha llegado una sugerencia de especial por parte de ' . $_POST['nombre'] . ' acerca del tema <strong>' . $_POST['tema'] . '</strong>';
    $contenido = '<p>' . stripslashes($_POST['mensaje']) . '</p>';
    $subject = 'Sugerencia de Especial';
    $from = $_POST['email'];
    $destino = get_bloginfo('admin_email');

    mailMe($titulo, $subtitulo, $contenido, $subject, $from, $destino);
}

function calculateActivity($pid) {

    $thePost = get_post($pid);

    $visitas = get_post_meta($pid, '_visitas', true);
    $sharesFacebook = get_post_meta($pid, '_shares_facebook', true);
    $sharesTwitter = get_post_meta($pid, '_shares_twitter', true);
    $comments = $thePost->comment_count;

    $total = $visitas + ( ($sharesFacebook + $sharesTwitter) * 3 ) + ($comments * 5);

    // si el post es menor a 30 dias
    if (strtotime($thePost->post_date) > strtotime("-30 days")) {
        $total = $total;
    }

    // si el post es mayor a 30 dias pero menor a 60 dias
    elseif (strtotime($thePost->post_date) <= strtotime("-30 days") && strtotime($thePost->post_date) >= strtotime("-60 days")) {
        $total = ($total / 2);
    }

    // si el post es mayor a 60 dias
    if (strtotime($thePost->post_date) < strtotime("-60 days")) {
        $total = ($total / 3);
    }

    update_post_meta($pid, '_total', $total);
}

function rangoTiempo($where = '') {
    $where .= " AND post_date > '" . date('Y-m-d', strtotime('-20 days')) . "'";
    return $where;
}

function rangoTiempoEntradas($where = '') {
    $where .= " AND post_date > '" . date('Y-m-d', strtotime('-7 days')) . "'";
    return $where;
}

function get_id_acf($campo) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'acf_fields';
    return $wpdb->get_var("SELECT id FROM $table_name WHERE name = '$campo'");
}

function get_order_no($fieldID, $postID) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'acf_values';
    $arraykey = $wpdb->get_results("SELECT COUNT(post_id) as counter FROM $table_name WHERE field_id = $fieldID AND post_id = $postID", OBJECT); //se cambio * por post_id
    return $arraykey->counter;
}

function acf_insert($value, $post_id, $fieldName) {
    global $wpdb;

    $fieldId = get_id_acf($fieldName);
    $orderno = get_order_no($fieldId, $post_id);

    $table_name = $wpdb->prefix . 'acf_values';

    $wpdb->query(" DELETE FROM $table_name WHERE post_id = '$post_id' AND field_ID = '$fieldId' ");

    $data['value'] = $value;
    $data['post_id'] = $post_id;
    $data['field_id'] = $fieldId;
    $data['order_no'] = $orderNo;

    $wpdb->insert($table_name, $data);
}

function getOpenGraphMetas() {
    global $post;
    $out = '';

    $out .= '<meta property="og:site_name" content="' . get_bloginfo('name') . '" />';

    if (is_front_page()) {
        $titleTag = get_bloginfo('name');
    } else {
        $titleTag = get_the_title($post->ID);
    }
    $out .= '<meta property="og:title" content="' . $titleTag . '" />';

    if (is_front_page()) {
        $urlTag = home_url();
    } elseif (is_singular()) {
        $urlTag = get_permalink($post->ID);
    } else {
        $urlTag = 'http://' . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    }
    $out .= '<meta property="og:url" content="' . $urlTag . '" />';

    if (is_front_page()) {
        $descTag = get_bloginfo('description');
    } else {
        $descTag = $post->post_content == true ? cortar($post->post_content, 140) : get_bloginfo('description');
    }
    $out .= '<meta property="og:description" content="' . $descTag . '" />';

    if (is_front_page()) {
        $imgTag = 'http://www.elquintopoder.cl/logo/logofb.jpg';
    } else {
        $imgTag = get_thumb_src($post->ID) ? get_thumb_src($post->ID) : get_bloginfo('template_directory') . '/css/ui/logo.png';
    }
    $out .= '<meta property="og:image" content="' . $imgTag . '" />';
    $out .= '<link rel="image_src" href="' . $imgTag . '" />';

    if ($post->post_type == 'post_videos') {
        $out .= '<meta property="og:type" content="video" />';
        $out .= '<meta property="og:video" content="' . get_field('video_link', $post->ID) . '?version=3&amp;autohide=1"/>';
        $out .= '<meta property="og:video:type" content="application/x-shockwave-flash"/>';
        $out .= '<meta property="og:video:width" content="640">';
        $out .= '<meta property="og:video:height" content="360">';
        $out .= '<meta property="og:site_name" content="YouTube">';
        $out .= '<meta property="twitter:player:width" content="640">';
        $out .= '<meta property="twitter:player:height" content="360">';
        $out .= '<meta property="og:video:width" content="640">';
        $out .= '<meta name="og:video:height" content="200" /> ';
        $out .= '<meta name="og:video:width" content="300" />';
    }
    echo $out;
}

// function para afectar a TODOS los posts
function affect_ALL_the_posts() {

    $args = array(
        'post_type' => 'any',
        'posts_per_page' => -1
    );

    $gigaQuery = new WP_Query($args);

    if ($gigaQuery->have_posts()) {
        while ($gigaQuery->have_posts()) {
            $gigaQuery->the_post();

            calculateActivity($gigaQuery->post->ID);
        }
    }
}

function getLastCommentsByPostType($post_type) {
    global $wpdb;
    $out = "";
    $sql = "
        SELECT comment_ID,comment_post_ID,comment_author,comment_content,user_id,post_title,post_name
        FROM $wpdb->comments 
        LEFT OUTER JOIN $wpdb->posts 
        ON ($wpdb->comments.comment_post_ID = $wpdb->posts.ID) 
        WHERE comment_approved = '1' 
        AND comment_type = '' 
        AND post_password = '' 
        AND post_type = %s 
        ORDER BY comment_date_gmt DESC LIMIT 6";
    
$comments = $wpdb->get_results( $wpdb->prepare( $sql, $post_type ) );
    if (!empty($comments)) {
        $out .= '<h2 class="label sidebarTitle">Últimos comentarios</h2>';
        $out .= '<ul class="article-list wall blog">';
        foreach ((array) $comments as $item) {
            $out .= '<li>';
            $out .= '<div class="usr-avatar-holder">';
            $out .= get_simple_local_avatar($item->user_id, 40);
            $out .= '</div>';
            $out .= '<p>' . $item->comment_author . '</p>';
            $out .= '<p>' . cortar($item->comment_content, 140) . '</p>';
            $out .= '<p class="comment-metadata">En: <a href="' . get_permalink($item->comment_post_ID) . '" title="' . $item->post_title . '" >' . $item->post_title . '</a></p>';
            $out .= '</li>';
        }
        $out .= '</ul>';
    }
    return $out;
}

function get_query_post_metadata($query, $userPerfil=false, $comunaSlug = false) {
    if ($query->post->post_type == 'blog') {
        $categories = wp_get_post_terms($query->post->ID, 'categorias_blog');
        foreach ((array) $categories as $cat) {
            $catList[0] = $cat->name;
            $catList[1] = $cat->term_id;
        }
        if( ! empty($categories) && ( !is_wp_error($catList[0]) && $catList[0] ) ){ $catLink = '<p class="published-where">En: <a href="' . get_term_link($cat, 'categorias_blog') . '">' . $catList[0] . '</a></p>'; }
    } elseif ($query->post->post_type == 'propuestas') {
        $categories = wp_get_post_terms($query->post->ID, 'temas');
        $catLink = '<p class="published-where">En:';

        if ($comunaSlug) {
            $filtro = '?comuna=' . $comunaSlug;
        } else {
            $filtro = '';
        }

        foreach ((array) $categories as $cat) {
            $catLink .= '<a href="' . get_term_link($cat) . $filtro . '">' . $cat->name . '</a>, ';
        }
        $catLink .= '</p>';
    } else {
        $categories = get_the_category($query->post->ID);
        foreach ((array) $categories as $cat) {
            $catList[0] = $cat->cat_name;
            $catList[1] = $cat->term_id;
        }
        $catLink = '<p class="published-where">En: <a href="' . get_category_link($catList[1]) . '">' . $catList[0] . '</a></p>';
    }
    $avatar = get_simple_local_avatar($query->post->post_author, 40);

    $echoes = '<div class="social-echoes">';
    $echoes .= '<ul>';
    $echoes .= get_socialEchoes(get_permalink(), get_the_title(), $query->post->ID);
    $echoes .= '</ul>';
    $echoes .= '</div>';
    if ($userPerfil == false) {
        $authorMeta = '<p class="published-by">Por: <a href="/perfil-de-usuario/?user=' . $query->post->post_author . '" title="Ir al perfil de ' . nombre_y_apellido($query->post->post_author) . '">' . nombre_y_apellido($query->post->post_author) . '</a></p>';
    }
    $authorMeta .= $catLink;
    $authorMeta .= '<p class="published-when">' . get_the_date() . '</p>';

    $output = array(
        'avatar' => $avatar,
        'echoes' => $echoes,
        'authorMeta' => $authorMeta
    );
    return $output;
}

function mailMe($titulo, $subtitulo, $contenido, $subject, $from, $destino) {
    add_filter('wp_mail_content_type', create_function('', 'return "text/html";'));
    $cont = '<html>';
    $cont .= '<head>';
    $cont .= '</head>';
    $cont .= '<body>';
    $cont .= '<div style="width:100%; background:#ffffff; font-family: helvetica, arial, sans-serif; font-size: 13px; color: #333333;">';
    $cont .= '<table style="width: 600px; margin: 40px auto;">';
    $cont .= '<tr>';
    $cont .= '<td style="padding-bottom: 50px; border-bottom: 1px dotted #cccccc; margin-bottom: 40px; line-height: 140%;">';
    $cont .= '<h1 style="display: block; margin: 40px 0 20px 0; font-size: 28px; font-weight: bold; color: #1A3347;" >' . $titulo . '</h1>';
    $cont .= '<p><strong>' . $subtitulo . '</strong></p>';
    $cont .= $contenido;
    $cont .= '</td>';
    $cont .= '</tr>';
    $cont .= '</table>';
    $cont .= '</div>';
    $cont .= '</body>';
    $cont .= '</html>';

    $headers = 'From: El Quinto Poder <' . $from . '>' . "\r\n";
    wp_mail($destino, $subject, $cont, $headers);
}

function countPostOverTime($post_type, $usid = false) {
    global $wpdb;

    $authorQuery = "";
    $dateQuery = "AND $wpdb->posts.post_date >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY)";
    if ($usid != false) {
        $authorQuery = "AND $wpdb->posts.post_author = %d";
        $dateQuery = "";
    }
    
    
    $fullQuery = "
        SELECT COUNT(ID) 
        FROM $wpdb->posts 
        WHERE $wpdb->posts.post_status = 'publish' 
        AND $wpdb->posts.post_type = %s
        $dateQuery
        $authorQuery
        LIMIT 0, 30
    ";
    
    $number = $wpdb->get_var( $wpdb->prepare( $fullQuery, $post_type, $usid ) );
    return $number;
}

function getTwitterStatus($userid) {
    $i = 1;
    $url = "http://twitter.com/statuses/user_timeline/$userid.xml?count=$i";

    $xml = simplexml_load_file($url);

    if ($xml) {
        foreach ($xml->status as $status) {
            $text = $status->text;
        }
        return $text;
    } else {
        return "Twittter Privado";
    }
}

function transientTwitter($cuenta, $transient) {
    $html = get_transient($transient);
    if ($html === false || $html === '') {
        $html = base64_encode(getTwitterStatus($cuenta));
        set_transient($transient, $html, 60 * 60 * 12);
    }
    echo base64_decode($html);
}

function return_comments_number($pid=false) {
    $number = get_comments_number($pid);
    if ($number > 1) {
        $output = str_replace('%', number_format_i18n($number), '%');
    } elseif ($number == 0) {
        $output = '0';
    } else {
        $output = '1';
    }
    return $output;
}

function breadcrumb() {
    global $post;
    $postParent = get_post($post->post_parent);
    $out = "";
    $out .= '<div id="breadcrumb">';
    if (!is_front_page()) {
        $out .= '<span><a href="' . home_url() . '" title="Inicio" rel="index">Inicio</a>';
        if (is_category()) {
            $out .= ' / Temas</span>';
            if ($_GET['tipo']) {
                $out .= ucfirst($_GET['tipo']) . ' en ';
            }
            $out .= single_cat_title("", false);
//            return $out;
        } elseif (is_tax('categorias_blog')) {
            $taxObj = get_term_by('slug', get_query_var('term'), 'categorias_blog');
            $out .= ' / Blog / Categorías</span>';
            $out .= $taxObj->name;
            return $out;
        } elseif (is_tax('comunas')) {
            $out .= ' / <a href="/especiales-home" title="Ir a Especiales" rel="section" >Especiales</a> / <a href="/especiales-home/municipales-2012" title="Ir a Municipales 2012" rel="section" >Municipales 2012</a></span>';
            $out .= single_term_title("", false);
            return $out;
        } elseif (is_tax('temas')) {
            $out .= ' / <a href="/especiales-home" title="Ir a Especiales" rel="section" >Especiales</a> / <a href="/especiales-home/municipales-2012" title="Ir a Municipales 2012" rel="section" >Municipales 2012</a>';
            if ($_GET['comuna']) {
                $comunaObj = get_term_by('slug', $_GET['comuna'], 'comunas');
                $out .= ' / ' . $comunaObj->name . '</span>';
            } else {
                $out .= '</span>';
            }
            $out .= single_term_title("", false);
            return $out;
        } elseif (is_post_type_archive()) {
            $out .= '</span>';
            $out .= post_type_archive_title('', false);
            return $out;
        } elseif (is_search()) {
            $out .= '</span>';
            $out .= 'Resultados de Búsqueda';
        }

        if (is_singular('page')) {
            if ($post->post_parent > 0 && count($post->ancestors) <= 1) {
                $out .= ' / <a href="' . get_permalink($post->post_parent) . '" title="' . get_the_title($post->post_parent) . '">' . get_the_title($post->post_parent) . '</a>';
            } elseif ($postParent->post_name == 'municipales-2012') {
                $out .= ' / <a href="/especiales-home" title="Ir a Especiales" rel="section" >Especiales</a> / <a href="/especiales-home/municipales-2012" title="Ir a Municipales 2012" rel="section" >Municipales 2012</a></span>';
            }
        } elseif (is_singular('post')) {
            $out .= '</span>';
            $out .= 'Entradas';
        } elseif (is_singular('post_fotos')) {
            $out .= '</span>';
            $out .= 'Fotos';
        } elseif (is_singular('post_videos')) {
            $out .= '</span>';
            $out .= 'Videos';
        } elseif (is_singular('post_acciones')) {
            $out .= '</span>';
            $out .= 'Acciones';
        } elseif (is_singular('blog')) {
            $out .= ' / Blog</span>';
            $out .= 'Entradas';
        } elseif (is_singular('propuestas')) {
            $comunaObj = wp_get_post_terms($post->ID, 'comunas');
            $out .= ' / <a href="/especiales-home" title="Ir a Especiales" rel="section" >Especiales</a> / <a href="/especiales-home/municipales-2012" title="Ir a Municipales 2012" rel="section" >Municipales 2012</a> / <a href="' . get_term_link($comunaObj[0]) . '" title="Ir a ' . $comunaObj[0]->name . '" rel="tag" ><strong>' . $comunaObj[0]->name . '</strong></a></span>';
            $out .= 'Propuestas ciudadanas en <strong>' . $comunaObj[0]->name . '</strong>';
        }

        if (!is_single() && !is_category() && !is_search()) {
            $out .= '</span>';
            $out .= get_the_title();
        }
    }
    $out .= '</div>';
    return $out;
}

function volversubir() {
    echo '<ul id="volvSubi">
            <li id="volver"><a href="' . $_SERVER['HTTP_REFERER'] . '" title="Ver pÃ¡gina visitada antes">Volver</a></li>
            <li><a href="javascript:window.scrollTo(0,0);" title="Ver parte superior de esta pÃ¡gina">Subir</a></li>
        </ul>';
}

function getUserFirmBox($authorID, $echo = false) {
    $theAuthor = get_userdata($authorID);
    $out = '<div class="userFirmBox">';
    $out .= '<p class="firmName">' . nombre_y_apellido($authorID) . '</p>';
    $out .= apply_filters('the_content', make_clickable( $theAuthor->user_description ));
    $out .= '</div>';
    if ($echo) {
        echo $out;
    } else {
        return $out;
    }
}

/**
 *
 * getPagination
 * 
 * Devuelve un string html formateado con los lnks de paginación
 * 
 * @name getPagination
 * @param object $query, WP_Query, puede ser el objeto $wp_query o cualquier instancia personalizada de WP_Query
 * @param string $baseURL, debe ser el url de la pagina en donde se ejecuta la función, ej: si la pagina es http://www.misitio.com/entradas/ entonces $baseURL = "/entradas/"
 * @param bool $echo, echo or return?
 * @return string 
 */
function pagination($query, $baseURL = '', $echo = true) {
    $out = "";

    if (!$baseURL) {
        $baseURL = get_bloginfo('url');
    }
    $page = $query->query_vars["paged"];
    if (!$page)
        $page = 1;
    $qs = $_SERVER["QUERY_STRING"] ? "?" . $_SERVER["QUERY_STRING"] : "";

    if ($query->found_posts > $query->query_vars["posts_per_page"]) {
        $out .= '<div id="paginacion">';
        if ($page > 1) {
            $out .= '<a class="previous page" href="' . $baseURL . 'page/' . ($page - 1) . '/' . $qs . '">Anterior</a>';
        }
        $out .= '<ul>';
        for ($i = 1; $i <= $query->max_num_pages; $i++) {
            if ($i == $page) {
                $out .= '<li><a class="current-page" href="' . $baseURL . 'page/' . ($i) . '/' . $qs . '" >' . $i . '</a></li>';
                $onetime = false;
            } elseif ($query->max_num_pages >= 9 && $i > 3 && $query->max_num_pages - 2 > $i) {
                if ($onetime == false)
                    $out .= '<li class="separador">...</li>';
                $onetime = true;
            }
            else {
                $out .= '<li><a href="' . $baseURL . 'page/' . $i . '/' . $qs . '">' . $i . '</a></li>';
            }
        }
        $out .= '</ul>';
        if ($page < $query->max_num_pages) {
            $out .= '<a class="next page"  href="' . $baseURL . 'page/' . ($page + 1) . '/' . $qs . '">Siguiente</a>';
        }
        $out .= '</div>';
    }
    if ($echo) {
        echo $out;
    } else {
        return $out;
    }
}

function find_parent($id, $stop = 0) {
    $post = get_post($id);
    if ($post->post_parent == $stop || empty($post->post_parent)) {
        return $post->ID;
    } else {
        return find_parent($post->post_parent);
    }
}

function cortar($str, $n, $cutter = false) {
    $str = trim($str);
    $str = strip_tags($str);
    if (strlen($str) > $n) {
        $out = substr($str, 0, $n);
        $out = explode(" ", $out);
        array_pop($out);
        if ($cutter) {
            $out = implode(" ", $out) . $cutter;
        } else {
            $out = implode(" ", $out) . "...";
        }
    } else {
        $out = $str;
    }
    return $out;
}

function xml2array($contents, $get_attributes=1) {
    if (!$contents)
        return array();

    if (!function_exists('xml_parser_create')) {
        return array();
    }
    $parser = xml_parser_create();
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
    xml_parse_into_struct($parser, $contents, $xml_values);
    xml_parser_free($parser);

    if (!$xml_values)
        return;
    $xml_array = array();
    $parents = array();
    $opened_tags = array();
    $arr = array();
    $current = &$xml_array;

    foreach ($xml_values as $data) {
        unset($attributes, $value);
        extract($data);
        $result = '';
        if ($get_attributes) {
            $result = array();
            if (isset($value))
                $result['value'] = $value;

            if (isset($attributes)) {
                foreach ($attributes as $attr => $val) {
                    if ($get_attributes == 1)
                        $result['attr'][$attr] = $val;
                }
            }
        } elseif (isset($value)) {
            $result = $value;
        }
        if ($type == "open") {
            $parent[$level - 1] = &$current;
            if (!is_array($current) or (!in_array($tag, array_keys($current)))) {
                $current[$tag] = $result;
                $current = &$current[$tag];
            } else {
                if (isset($current[$tag][0])) {
                    array_push($current[$tag], $result);
                } else {
                    $current[$tag] = array($current[$tag], $result);
                }
                $last = count($current[$tag]) - 1;
                $current = &$current[$tag][$last];
            }
        } elseif ($type == "complete") {
            if (!isset($current[$tag])) {
                $current[$tag] = $result;
            } else {
                if ((is_array($current[$tag]) and $get_attributes == 0)
                        or (isset($current[$tag][0]) and is_array($current[$tag][0]) and $get_attributes == 1)) {
                    array_push($current[$tag], $result);
                } else {
                    $current[$tag] = array($current[$tag], $result);
                }
            }
        } elseif ($type == 'close') {
            $current = &$parent[$level - 1];
        }
    }
    return($xml_array);
}

function printMe($wea) {
    echo '<pre>';
    print_r($wea);
    echo '</pre>';
}

function nl2br_revert($string) {
    $br = preg_match('`<br>[\\n\\r]`', $string) ? '<br>' : '<br />';
    return preg_replace('`' . $br . '([\\n\\r])`', '$1', $string);
}

function in_array_of_objects($needle, $haystack, $property) {
    foreach ($haystack as $obj) {
        foreach ($obj as $key => $val) {
            if ($key == $property && $needle == $val) {
                return true;
            }
        }
    }
    return false;
}

function new_publish_content_form_fields( $settings ){
    $wysiwyg_settings_array = array(
        'media_buttons' => false,
        'teeny' => true,
        'quicktags' => false,
        'textarea_rows' => '10'
    );
    
    echo '<label for="post_name">Título de tu '. $settings['post_type_name'] .' <span class="required-message" >(obligatorio)</span></label>';
    echo '<input type="text" name="post_name" id="post_name" placeholder="Escribe el título de tu '. $settings['post_type_name'] .'" required data-customValidation="checkMaxLength" data-max="70">';
    echo '<span class="form-helper-message" >Máximo de 70 caracteres, quedan 70 caracteres</span>';
    
    echo '<label for="post_category">¿En qué tema quieres publicar el contenido? <span class="required-message" >(obligatorio)</span></label>';
    echo '<select name="post_category" id="post_category" required >';
    echo '<option value="" >Selecciona un tema</option>';
    echo categoryOptions();
    echo '</select>';
    
    if( $settings['post_type'] == 'post_fotos' ){
        echo '<label for="post_image">Selecciona tu foto <span class="required-message" >(obligatorio)</span></label>';
        echo '<input class="ugly-input" data-placeholder="Seleccione una imagen" data-type="image" type="file" name="post_image" id="post_image" accept="image/*" required>';
    }
    elseif( $settings['post_type'] == 'post_videos' ){
        echo '<label for="post_video">Dirección del video <span class="required-message" >(obligatorio)</span></label>';
        echo '<input type="text" name="post_video" id="post_video" required placeholder="Ingresa la dirección o URL del video" >';
        echo '<span class="form-helper-message" >Puedes vincular un video insertando la dirección o URL del mismo. <br><strong>Ej.: http://www.youtube.com/watch?v=qNKqZDRXWzk</strong></span>';
    }
    elseif( $settings['post_type'] == 'post_acciones' ){
        echo '<label for="post_firmas_required">¿Cuántas firmas necesitas reunir? <span class="required-message" >(obligatorio)</span></label>';
        echo '<input type="text" name="post_firmas_required" id="post_firmas_required" placeholder="20000" required >';
        echo '<span class="form-helper-message" >Debes ingresar un número en cifras sin puntos, espacios o comas. <strong>Ej.: 1000000</strong></span>';
        
        echo '<label >Información requerida para firmar</label>';
        echo '<ul class="checkbox-group" >';
        
        echo '<li class="fake-checkbox-holder" >';
        echo '<input class="pretty-checkbox" type="checkbox" name="post_requirements[]" data-name="post_requirements" id="post_requirements_nombre" value="nombre" required checked disabled >';
        echo '<label class="pretty-checkbox-label" for="post_requirements_nombre" >Nombre</label>';
        echo '</li>';
        
        echo '<li class="fake-checkbox-holder" >';
        echo '<input class="pretty-checkbox" type="checkbox" name="post_requirements[]" data-name="post_requirements" id="post_requirements_email" value="email" required checked disabled >';
        echo '<label class="pretty-checkbox-label" for="post_requirements_email" >Email</label>';
        echo '</li>';
        
        echo '<li class="fake-checkbox-holder" >';
        echo '<input class="pretty-checkbox" type="checkbox" name="post_requirements[]" data-name="post_requirements" id="post_requirements_rut" value="rut" >';
        echo '<label class="pretty-checkbox-label" for="post_requirements_rut" >RUT</label>';
        echo '</li>';
        
        echo '<li class="fake-checkbox-holder" >';
        echo '<input class="pretty-checkbox" type="checkbox" name="post_requirements[]" data-name="post_requirements" id="post_requirements_profesion" value="profesion" >';
        echo '<label class="pretty-checkbox-label" for="post_requirements_profesion" >Profesión</label>';
        echo '</li>';
        
        echo '<li class="fake-checkbox-holder" >';
        echo '<input class="pretty-checkbox" type="checkbox" name="post_requirements[]" data-name="post_requirements" id="post_requirements_institucion" value="institucion" >';
        echo '<label class="pretty-checkbox-label" for="post_requirements_institucion" >Institucion</label>';
        echo '</li>';
        
        echo '</ul>';
    }
    
    if( $settings['post_type'] == 'post_fotos' ){ $contentLabelText = '<label for="post_content">Descripción de tu foto <span class="optional-message" >(opcional)</span></label>'; }
    elseif( $settings['post_type'] == 'post_videos' ){ $contentLabelText = '<label for="post_content">Descripción de tu video <span class="optional-message" >(opcional)</span></label>'; }
    elseif( $settings['post_type'] == 'post_acciones' ){ $contentLabelText = '<label for="post_content">Descripción de tu acción <span class="required-message" >(obligatorio)</span></label>'; }
    else { $contentLabelText = '<label for="post_content">Contenido de tu entrada <span class="required-message" >(obligatorio)</span></label>'; }
    
    echo $contentLabelText;
    
    if( $settings['post_type'] == 'post' || $settings['post_type'] == 'post_acciones' ){
        $wysiwyg_settings_array['editor_class'] = 'required invalid-input';
    }
    wp_editor( '', 'post_content', $wysiwyg_settings_array );
    unset( $wysiwyg_settings_array['editor_class'] );
    
    echo '<span class="form-helper-message" >Sugerimos un máximo de 3 mil caracteres</span>';
    
    if( $settings['post_type'] == 'post' ){
        echo '<label for="post_bajada">Bajada <span class="optional-message" >(opcional)</span></label>';
        wp_editor( '', 'post_bajada', $wysiwyg_settings_array );
    
        echo '<label for="post_destacado">Texto destacado <span class="optional-message" >(opcional)</span></label>';
        wp_editor( '', 'post_destacado', $wysiwyg_settings_array );
    
        echo '<label for="post_image">Foto de tu entrada <span class="optional-message" >(opcional)</span></label>';
        echo '<input class="ugly-input" data-placeholder="Seleccione una imagen" data-type="image" type="file" name="post_image" id="post_image" accept="image/*">';
        
        echo '<label for="post_video">Video de tu entrada <span class="optional-message" >(opcional)</span></label>';
        echo '<input type="text" name="post_video" id="post_video" placeholder="Ingresa la dirección o URL del video" >';
        echo '<span class="form-helper-message" >Puedes vincular un video insertando la dirección o URL del mismo. <br><strong>Ej.: http://www.youtube.com/watch?v=qNKqZDRXWzk</strong></span>';
    }
    return false;
}
function new_publish_contents( $autor, $data, $ptname ){
    $args = array(
          'comment_status' => 'open',
          'ping_status' => 'open',
          'post_author' => $data['content_author_id'],
          'post_category' => array($data['post_category']),
          'post_content' => strip_tags($data['post_content'], '<a>'),
          'post_status' => $data['content_post_status'],
          'post_title' => $data['post_name'],
          'post_type' => $data['content_post_type']
        );
    $postid = wp_insert_post( $args );
    
    if ( $data['post_bajada'] ){ 
        update_post_meta($postid, 'bajada', strip_tags($data['post_bajada'], '<a>'));
        update_field( 'bajada', strip_tags($data['post_bajada'], '<a>'), $postid );
    }
    if ( $data['post_destacado'] ){ 
        update_post_meta($postid, 'texto_destacado', strip_tags($data['post_destacado'], '<a>'));
        update_field( 'texto_destacado', strip_tags($data['post_destacado'], '<a>'), $postid );
    }
    if ( $data['post_video'] ){
        update_post_meta($postid, 'video_link', $data['post_video']);
        update_field( 'video_link', $data['post_video'], $postid );
        update_post_meta($postid, 'ajaxEmbed', get_the_embed($data['post_video']));
    }
    if ( $data['post_firmas_required'] ){
        update_post_meta($postid, 'requeridos', $data['post_firmas_required']);
        update_field( 'requeridos', $data['post_firmas_required'], $postid );
    }
    if ( ! empty( $data['post_requirements'] ) ){
        update_post_meta($postid, 'campos_requeridos', $data['post_requirements']);
    }
    
    if ( ! empty( $_FILES['post_image'] ) ) {
        if ( ! function_exists( 'wp_handle_upload' ) ) { require_once( ABSPATH . 'wp-admin/includes/file.php' ); }
        $mimes = array(
            'jpg|jpeg|jpe' => 'image/jpeg',
            'png' => 'image/png',
        );
        $fotoUpload = wp_handle_upload( $_FILES['post_image'], array( 'mimes' => $mimes, 'test_form' => false ) );
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
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
        wp_update_attachment_metadata( $attach_id, $attach_data );
        update_post_meta($postid, '_thumbnail_id', $attach_id);
    }
    update_post_meta($postid, 'user_agent_check', $_SERVER['HTTP_USER_AGENT']);
    
    // adicion de post completa
    // comienza respuesta
    
    $theNewPost = get_post( $postid );
    
    $category = get_the_category($postid);
    $pertenenciaString = ' en el tema <strong>'. $category[0]->cat_name .'</strong>';
    
    if( $data['content_post_type'] === 'post_foto' ){ $tipoPost = 'la foto'; }
    elseif( $data['content_post_type'] === 'post_videos' ){ $tipoPost = 'el video'; }
    elseif( $data['content_post_type'] === 'post_acciones' ){ $tipoPost = 'la acción'; }
    else{ $tipoPost = 'la entrada'; }
    
    
    ////////////////////// mail para admins
    $titulo = 'Administración de Contenido';
    $subtitulo = '';
    $contenido = '<p><strong>'. nombre_y_apellido( $autor->ID ) .'</strong> ha creado '. $tipoPost .' <strong>"'. $theNewPost->post_title .'"</strong>'. $pertenenciaString .'</p>';
    $contenido .= '<p><a href="'. get_permalink( $postid ) .'" title="'. $theNewPost->post_title .'">'. get_permalink( $postid ) .'</a></p>';
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
    mailMe($titulo, $subtitulo, $contenido, $subject, $from, 'fernando@ida.cl');
    
    
    ////////////////////// mail para autor
    $titulo = 'Administración de Contenido';
    $subtitulo = '';
    if( $data['content_post_type'] != 'post' ){
        $contenido = '<p>Felicitaciones<strong> '. nombre_y_apellido($autor->ID) .'</strong> has creado '. $tipoPost .' <strong>"'. $theNewPost->post_title .'"</strong>'. $pertenenciaString .'</p>';
        $contenido .= '<p><a href="'. get_permalink( $theNewPost->ID ) .'" title="'. $theNewPost->post_title .'">'. get_permalink( $theNewPost->ID ) .'</a></p>';
        $contenido .= '<p>Saludos,</p>';
        $contenido .= '<p>Equipo El Quinto Poder</p>';
        $contenido .= '<p><a href="'. home_url() .'" title="El Quinto poder">'. home_url() .'</a></p>';
    } else {
        $contenido = '<p>Felicitaciones<strong> '. nombre_y_apellido($autor->ID) .'</strong> has creado '. $tipoPost .' <strong>"'. $theNewPost->post_title .'"</strong>'. $pertenenciaString .'</p>';
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
    
    if( get_post_type( $postid ) != 'post' ){ 
       wp_redirect( get_permalink( $postid ) . '?newPost=1029384756+' . getRandNum() );
       exit;
    }
    
    // mensaje de respuesta html
    $out = '<div class="thankyou-message no-bg" >';
    $out .= '<h2 class="thankyou-title" >¡Gracias, '. nombre_y_apellido($autor->ID) .'!</h2>';
    $out .= '<p class="thankyou-text" >Tu entrada <strong>"'. $theNewPost->post_title .'"</strong> será revisada y publicada a la brevedad.<br>Te contactaremos en caso de algún problema.</p>';
    $out .= '<a class="thankyou-go-back" href="'. home_url() .'" title="Volver al Inicio" rel="index" >Seguir Navegando</a>';
    $out .= '</div>';
    
    return $out;
}
function thankyou_message( $postData, $checking ){
    if( is_user_logged_in() && $checking ){
        $current_user = wp_get_current_user();
        if ( $current_user->ID != $postData['author_id'] ){ return false; }
        
        if (has_post_thumbnail($postData['pid'])) {
            $imageID = get_post_thumbnail_id($postData['pid']);
            $imageThumb = wp_get_attachment_url($imageID);
        }
        else { $imageThumb = 'http://www.elquintopoder.cl/logo/logofb.jpg'; }
        
        $mensaje .= 'Opina y comparte '.  get_the_title( $postData['pid'] ) .' en elquintopoder.cl ';
                
        $shortUrl = home_url() . '/?p=' . $postData['pid'];
        
        $fblink = 'http://www.facebook.com/sharer.php?s=100&p[title]=' . get_the_title( $postData['pid'] ) . '&p[summary]=' . urldecode($mensaje) . '%21&p[url]=' . urldecode( get_permalink( $postData['pid'] ) ) . '&&p[images][0]=' . $imageThumb;
        $twlink = 'https://twitter.com/intent/tweet?text=' . get_the_title( $postData['pid'] ) . '+' . $shortUrl . '&hashtags=5poder&via=elquintopoder';
        
        $out = '<div class="thankyou-message" >';
        $out .= '<h2 class="thankyou-title" >¡Gracias, '. nombre_y_apellido($postData['author_id']) .'!</h2>';
        $out .= '<p class="thankyou-text" >Tu <strong>'. $postData['pName'] .'</strong> se encuentra visible en nuestra comunidad.</p>';
        $out .= '<p class="thankyou-helper" >La dirección de tu <strong>'. $postData['pName'] .'</strong> es ';
        $out .= '<a class="thankyou-link" href="'. get_permalink( $postData['pid'] ) .'" title="Comparte este link" rel="nofollow" >'. get_permalink( $postData['pid'] ) .'</a>';
        $out .= '</p>';
        $out .= '<p class="thankyou-share">';
        $out .= '<span class="thankyou-share-text">¡Compártela en tus redes!</span>';
        $out .= '<a class="thankyou-share-icon facebook evt" data-func="anadirShares" data-pid="' . $postData['pid'] . '" data-provider="facebook" data-noprevent="true" href="'. $fblink .'" title="Compartir en Facebook" rel="nofollow">Compartir en Facebook</a></li>';
        $out .= '<a class="thankyou-share-icon twitter evt" data-func="anadirShares" data-pid="' . $postData['pid'] . '" data-provider="twitter" data-noprevent="true" href="'. $twlink .'" title="Compartir en Twitter" rel="nofollow">Compartir en Twitter</a></li>';
        $out .= '</p>';
        $out .= '</div>';
        
        return $out;
    }
    else { return false; }
}

function get_destacados_newsletter( $itemsArray, $newsType, $echo = false, $utms=false ){
    $out = '';
    $counter = 0;
    $posDestacada=1;
    $arrayCounter = 1;
    if( $newsType == 'boletin_semanal' ){ $destKey = 'id_segundo_destacado_boletin_semanal'; }
    else { $destKey = 'id_destacado_secundario_especial_newsletter'; }
    foreach( $itemsArray as $item ){
        $thePost = get_post( $item[ $destKey ] );
        $bajada = get_field('texto_destacado', $thePost->ID) ? get_field('texto_destacado', $thePost->ID) : $thePost->post_content; 
        $categories = get_the_category($thePost->ID);
        foreach ($categories as $cat) {
            $catList[0] = $cat->cat_name;
            $catList[1] = $cat->term_id;
        }
        
        if( $counter === 0 ){ $out .= '<tr>'; }
        
        $out .= '<td style=" padding-left: 20px; padding-right: 10px; padding-bottom: 30px; padding-top: 0px; vertical-align: top; width: 50%;">';
        
        $out .= '<a style=" font-size: 16px; text-decoration: none; color: #336D88; font-weight: bold; display: block; margin-bottom: 10px; "  href="'. get_permalink( $thePost->ID ) .$utms.'destacado_secundario_'.$posDestacada.'" title="Ver '. $thePost->post_title .'">';
        $out .= $thePost->post_title;
        $out .= '</a>';
        $out .= '<table style="border-collapse: collapse;" >';
        $out .= '<tr>';
        $out .= '<td style=" width: 60px; " >';
        $out .= '<a style="border-radius: 5px; padding-top: 5px; padding-left: 5px; outline: none; border: 0px; display: block; width: 50px; height: 50px; background: url(http://mailings.ida.cl/eqp/fondo-perfil.png) 50% 50% no-repeat; " href="'. home_url() .'/perfil-de-usuario/'.$utms.'destacado_secundario_autor_foto_'.$posDestacada.'&amp;user='. $thePost->post_author .'" title="Ver perfil de '. nombre_y_apellido( $thePost->post_author ) .'">';
        $out .= get_simple_local_avatar($thePost->post_author, 40);
        $out .= '</a>';
        $out .= '</td>';
        $out .= '<td style="padding-left: 10px; font-size: 11px;" >';
        $out .= '<p style="color: #666666; margin: 0px; padding: 0px;" >Por: <a style="color: #CA4141; text-decoration: none;" href="'. home_url() .'/perfil-de-usuario/'.$utms.'destacado_secundario_autor_nombre_'.$posDestacada.'&amp;user='. $thePost->post_author .'" title="Ver Perfil de '. nombre_y_apellido( $thePost->post_author ) .'" >'. nombre_y_apellido( $thePost->post_author ) .'</a></p>';
        $out .= '<p style="color: #666666; margin: 0px; padding: 0px;" >En: <a style="color: #CA4141; text-decoration: none;" href="'. get_category_link($catList[1]) .$utms.'destacado_secundario_tema_'.$posDestacada.'" title="Ver ' . $catList[0] . '" >' . $catList[0] . '</a></p>';
        $out .= '<p style="color: #666666; margin: 0px; padding: 0px;" >' . mysql2date(get_option('date_format'), $thePost->post_date) . '</p>';
        $out .= '</td>';
        $out .= '</tr>';
        $out .= '</table>';
        $out .= '<p style="line-height: 150%;" >';
        $out .= cortar( $bajada , 300);
        $out .= '</p>';
        
        $out .= '</td>';
        
        $posDestacada++;
        if( $counter === 1 ){
            $out .= '</tr>';
            $counter = 0;
            continue;
        }
        
        $counter++;
    }
    
    if( $echo ){ echo $out; }
    else { return $out; }
}
function get_dest_tercer_nivel( $itemsArray, $echo = false, $utms=false ){
    $out = '';
    $counter = 0;
    $posDestacada=1;
//    $arrayCounter = 1;
//    $destKey = 'id_tercer_destacado_boletin_semanal';
    
    foreach( $itemsArray as $item ){
        if( $counter === 0 ){ $out .= '<tr>'; }
        
        $out .= '<td style=" padding-left: 20px; padding-right: 10px; padding-bottom: 30px; padding-top: 0px; vertical-align: top; width: 50%;">';
        
        regenerate_image_data( $item['imagen_tercer_cont'] , 'news_destacado' );
        $out .= '<a style="display: block;"  href="'. $item['link_tercer_cont'] . $utms .'destacado_terciario_img_'.$posDestacada.'" title="Ver '. $item['titulo_tercer_cont'] .'">';        
        $out .= wp_get_attachment_image( $item['imagen_tercer_cont'] , false, 'news_destacado', array('style' => 'width: 320px; height: 200px;'));
        $out .= '</a>';
        
        $out .= '<a style=" font-size: 16px; text-decoration: none; color: #336D88; font-weight: bold; display: block; margin-bottom: 10px; margin-top: 10px;"  href="'. $item['link_tercer_cont'] . $utms .'destacado_terciario_'.$posDestacada.'" title="Ver '. $item['titulo_tercer_cont'] .'">';
        $out .= $item['titulo_tercer_cont'];
        $out .= '</a>';
        
        $out .= '<div style="line-height: 150%;" >';
        $out .= $item['texto_tercer_cont'];
        $out .= '</div>';
        
        /*
        $out .= '<table style="border-collapse: collapse;" >';
        $out .= '<tr>';
        $out .= '<td style=" width: 60px; " >';
        $out .= '<a style="border-radius: 5px; padding-top: 5px; padding-left: 5px; outline: none; border: 0px; display: block; width: 50px; height: 50px; background: url(http://mailings.ida.cl/eqp/fondo-perfil.png) 50% 50% no-repeat; " href="'. home_url() .'/perfil-de-usuario/?user='. $thePost->post_author .'" title="Ver perfil de '. nombre_y_apellido( $thePost->post_author ) .'">';
        $out .= get_simple_local_avatar($thePost->post_author, 40);
        $out .= '</a>';
        $out .= '</td>';
        $out .= '<td style="padding-left: 10px; font-size: 11px;" >';
        $out .= '<p style="color: #666666; margin: 0px; padding: 0px;" >Por: <a style="color: #CA4141; text-decoration: none;" href="'. home_url() .'/perfil-de-usuario/?user='. $thePost->post_author .'" title="Ver Perfil de '. nombre_y_apellido( $thePost->post_author ) .'" >'. nombre_y_apellido( $thePost->post_author ) .'</a></p>';
        $out .= '<p style="color: #666666; margin: 0px; padding: 0px;" >En: <a style="color: #CA4141; text-decoration: none;" href="'. get_category_link($catList[1]) .'" title="Ver ' . $catList[0] . '" >' . $catList[0] . '</a></p>';
        $out .= '<p style="color: #666666; margin: 0px; padding: 0px;" >' . mysql2date(get_option('date_format'), $thePost->post_date) . '</p>';
        $out .= '</td>';
        $out .= '</tr>';
        $out .= '</table>';
        */
        
        $out .= '</td>';
        
        if( $counter === 1 ){
            $out .= '</tr>';
            $counter = 0;
            continue;
        }
        $posDestacada++;
        $counter++;
    }
    
    if( $echo ){ echo $out; }
    else { return $out; }
}

function get_superDest_news( $theID ){
    $thePost = get_post( $theID );
    $categories = get_the_category($thePost->ID);
    foreach ($categories as $cat) {
        $catList[0] = $cat->cat_name;
        $catList[1] = $cat->term_id;
    }
    $bajada = get_field('texto_destacado', $thePost->ID) ? get_field('texto_destacado', $thePost->ID) : $thePost->post_content; 
    
    $out = '';
    
    $out .= '<table style="border-collapse: collapse; margin-bottom: 30px;"  >';
    $out .= '<tr>';
    $out .= '<td style="vertical-align: middle;" >';
    
    regenerate_image_data( get_post_thumbnail_id( $theID ), 'news_gigante');
    $out .= get_the_post_thumbnail( $theID, 'news_gigante', array( 'style' => 'width: 700px; height: 340px;' ));
    
    $out .= '</td>';
    $out .= '</tr>';
    
    $out .= '<tr>';
    $out .= '<td>';
    $out .= '<a style="font-size: 20px; font-weight: bold; font-weight: bold; color: #336D88; text-decoration: none; display: block; margin-bottom: 10px; margin-top: 10px;" href="'. get_permalink( $theID ) .'" title="Ver '. get_the_title( $theID ) .'">';
    $out .= get_the_title( $theID );
    $out .= '</a>';
    $out .= '</td>';
    $out .= '</tr>';
    $out .= '<tr>';
    $out .= '<td>';
    $out .= '<table style="border-collapse: collapse;" >';
    $out .= '<tr>';
    $out .= '<td style=" width: 60px; " >';
    $out .= '<a style="border-radius: 5px; padding-top: 5px; padding-left: 5px; outline: none; border: 0px; display: block; width: 50px; height: 50px; background: url(http://mailings.ida.cl/eqp/fondo-perfil.png) 50% 50% no-repeat; " href="'. home_url() .'/perfil-de-usuario/?user='. $thePost->post_author .'" title="Ver perfil de '. nombre_y_apellido( $thePost->post_author ) .'">';
    $out .= get_simple_local_avatar($thePost->post_author, 40);
    $out .= '</a>';
    $out .= '</td>';
    $out .= '<td style="padding-left: 10px; font-size: 11px;" >';
    $out .= '<p style="color: #666666; margin: 0px; padding: 0px;" >Por: <a style="color: #CA4141; text-decoration: none;" href="'. home_url() .'/perfil-de-usuario/?user='. $thePost->post_author .'" title="Ver Perfil de '. nombre_y_apellido( $thePost->post_author ) .'" >'. nombre_y_apellido( $thePost->post_author ) .'</a></p>';
    $out .= '<p style="color: #666666; margin: 0px; padding: 0px;" >En: <a style="color: #CA4141; text-decoration: none;" href="'. get_category_link($catList[1]) .'" title="Ver ' . $catList[0] . '" >' . $catList[0] . '</a></p>';
    $out .= '<p style="color: #666666; margin: 0px; padding: 0px;" >' . mysql2date(get_option('date_format'), $thePost->post_date) . '</p>';
    $out .= '</td>';
    $out .= '</tr>';
    $out .= '</table>';
    $out .= '</td>';
    $out .= '</tr>';
    $out .= '<tr>';
    $out .= '<td style=" color: #333333; line-height: 160%; font-size: 14px; margin-bottom: 20px;">';
    $out .= cortar( $bajada, 600 );
    $out .= '</td>';
    $out .= '</tr>';
    $out .= '</table>';
    
    if( $echo ){ echo $out; }
    else { return $out; }
}

function get_html_content_box( $permalink ){
    if( function_exists('w3tc_pgcache_flush') ) { w3tc_pgcache_flush(); }
    $contents = getcurl($permalink);
    $contents = explode('</html>', $contents);
    
    $out = '<div style="margin: 40px auto; width: 700px;">';
    $out .= '<textarea style="displat: block; width: 100%; height: 300px;" >';
    $out .= $contents[0] . '</html>';
    $out .= '</textarea>';
    $out .= '</div>';
    
    return $out;
}

function regenerate_image_data( $attId, $sizeToCheck ){
    $attMeta = wp_get_attachment_metadata( intval($attId) );
    if( isset($attMeta['sizes'][$sizeToCheck]) ){ return false; }
    else {
        $attPath = get_attached_file( intval($attId) );
        $attach_data = wp_generate_attachment_metadata( intval($attId), $attPath );
        wp_update_attachment_metadata( intval($attId), $attach_data );
    }
}
function regenerar_fotos( $pid ){
    if( get_post_type( $pid ) == 'newsletters' ){
        $newsFields = get_field('newsletter_type_info', $pid);
        $theLayout = $newsFields[0];
        
        if( $theLayout['acf_fc_layout'] == 'boletin_semanal' ){
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            if( $theLayout['imagen_destacada_boletin_semanal'] ){
                regenerate_image_data( $theLayout['imagen_destacada_boletin_semanal'], 'news_gigante' );
            }

            foreach( (array)$theLayout['destacados_primer_nivel_boletin_semanal'] as $item ){
                if( has_post_thumbnail( $item['id_primer_destacado_boletin_semanal'] ) ){
                    regenerate_image_data(get_post_thumbnail_id($item['id_primer_destacado_boletin_semanal']), 'news_destacado' );
                }
            }

            foreach( (array)$theLayout['destacados_segundo_nivel_boletin_semanal'] as $item ){
                if( has_post_thumbnail( $item['id_segundo_destacado_boletin_semanal'] ) ){
                    regenerate_image_data(get_post_thumbnail_id($item['id_segundo_destacado_boletin_semanal']), 'news_destacado' );
                }
            }
            
            foreach( (array)$theLayout['destacados_tercer_nivel_boletin_semanal'] as $item ){
                if( has_post_thumbnail( $item['id_segundo_destacado_boletin_semanal'] ) ){
                    regenerate_image_data(get_post_thumbnail_id($item['id_tercer_destacado_boletin_semanal']), 'news_destacado' );
                }
            }
        }

        if( $theLayout['acf_fc_layout'] == 'especial_newsletter' ){
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            foreach( (array)$theLayout['destacados_secundarios_especial_newsletter'] as $item ){
                if( has_post_thumbnail( $item['id_destacado_secundario_especial_newsletter'] ) ){
                    regenerate_image_data(get_post_thumbnail_id($item['id_destacado_secundario_especial_newsletter']), 'news_destacado' );
                }
            }
        }
    }
}
add_action('acf_save_post', 'regenerar_fotos', 20);

function getcurl($getURL, $formvars=false) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $getURL);
    curl_setopt($ch, CURLOPT_USERPWD, "5podertest:5access");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'ida');
//    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    if ($formvars != false)
        curl_setopt($ch, CURLOPT_POSTFIELDS, $formvars);
    $page = curl_exec($ch);
    if (!curl_errno($ch)) {
        $info = curl_getinfo($ch);
    }
    curl_close($ch);
    return $page;
}

function getRandNum(){
    return str_pad( base_convert( mt_rand(0, pow(255, 6) ), 10, 16 ), 12, '0', STR_PAD_LEFT);
}
//////////////////////////////////////////////////////////////////////////////// Actions y Filters
if ($_REQUEST["flush"] == "cache") {
    $plugin_totalcacheadmin = & w3_instance('W3_Plugin_TotalCacheAdmin');
    $plugin_totalcacheadmin->flush_all();

    function autoCloseAndRefreshParent() {

        echo "
                <script>
                    window.opener._gaq.push(['_setCustomVar', 1, 'Usuarios Logueados', 'Si', 2]);
                    window.opener._gaq.push(['_trackPageview']);
                    
                    setTimeout(function(){ window.opener.location.href = window.opener.location.href; window.close(); }, 500);
                    
                </script>";
        exit;
    }

    add_action('wp_head', 'autoCloseAndRefreshParent');
}

function includeModernizr() {
    if (!is_admin()) {
        wp_deregister_script('modernizr');
        wp_register_script('modernizr', get_bloginfo("template_directory") . '/js/modernizr.js');
        wp_enqueue_script('modernizr');
    }
}

add_action('wp_enqueue_scripts', 'includeModernizr');

function theme_queue_js() {
    if (!is_admin()) {
        if (is_singular() && comments_open() && (get_option('thread_comments') == 1))
            wp_enqueue_script('comment-reply');
    }
}
add_action('get_header', 'theme_queue_js');

function my_function_admin_bar() {
    return false;
}

add_filter("show_admin_bar", "my_function_admin_bar");

function SearchFilter($query) {
    if ( $query->is_search ) {
        $query->set('posts_per_page', 10);
        $query->set('orderby', 'meta_value_num');
        $query->set('meta_key', '_total');
        $query->set('post_type', array('post', 'post_fotos', 'post_videos', 'post_acciones', 'especiales'));
    }
    return $query;
}

add_filter('pre_get_posts', 'SearchFilter');

function notifyAuthor($pid) {
    $thePost = get_post($pid);
    $theAuthor = get_userdata($thePost->post_author);

    $titulo = 'Administración de Contenido';
    $subtitulo = '';
    $contenido = '<p>¡Felicitaciones! <strong>' . nombre_y_apellido($theAuthor->ID) . '</strong> tu entrada ' . $thePost->post_title . ' se ha publicado o editado, puedes verla en:</p>';
    $contenido .= '<p><a href="' . get_permalink($thePost->ID) . '" title="' . $thePost->post_title . '">' . get_permalink($thePost->ID) . '</a></p>';
    $contenido .= '<p>Saludos,</p>';
    $contenido .= '<p>Equipo El Quinto Poder</p>';
    $contenido .= '<p><a href="' . home_url() . '" title="El Quinto poder">' . home_url() . '</a></p>';
    $subject = 'Nuevo Contenido Publicado';
    $from = 'administracion@elquintopoder.cl';
    $destino = $theAuthor->user_email;

    mailMe($titulo, $subtitulo, $contenido, $subject, $from, $destino);
}

add_action('publish_post', 'notifyAuthor');

function setupActivity($pid) {
    return calculateActivity($pid);
}

add_action('publish_post', 'setupActivity');

function setupApoyos($pid) {
    $thePost = get_post($pid);
    if ($thePost->post_type == 'propuestas') {
        $apoyoActual = get_post_meta($pid, 'apoyos_propuesta', true);
        update_post_meta($pid, 'apoyos_propuesta', intval($apoyoActual));
    }
}

add_action('publish_post', 'setupApoyos');

function extraCandidatosFields($userObj) {
    $out = '';
    if (in_array('candidato', $userObj->roles)) {
        $out .= '<h3>Información adicional para Candidatos</h3>';
        $out .= '<table class="form-table">';
        $out .= '<tbody>';
        $out .= '<tr>';
        $out .= '<th><label for="candidato_order_num" >Posición del candidato</label></th>';
        $out .= '<td>';
        $out .= '<input type="text" name="candidato_order_num" id="candidato_order_num" value="' . get_user_meta($userObj->ID, 'candidato_order_num', true) . '" class="regular-text">';
        $out .= '<span class="description">Numero de orden en el cual debe aparecer el candidato, debe ser un numero desde 1 hacia arriba.</span>';
        $out .= '</td>';
        $out .= '</tr>';
        $out .= '</tbody>';
        $out .= '</table>';
    }
    echo $out;
}

add_action('edit_user_profile', 'extraCandidatosFields');

function save_extra_profile_fields($user_id) {
    $userObj = get_userdata($user_id);
    if (in_array('candidato', $userObj->roles) && $_POST['candidato_order_num'] != '') {
        update_user_meta($user_id, 'candidato_order_num', $_POST['candidato_order_num']);
    }
}

add_action('edit_user_profile_update', 'save_extra_profile_fields');

function publish_editor_syle( $mce_css ) {
        if( !is_admin() ){
            if ( ! empty( $mce_css ) ) { $mce_css .= ','; }
            $mce_css .= get_bloginfo('template_directory') . '/css/editor-style.css';
            return $mce_css;
        }
}
add_filter( 'mce_css', 'publish_editor_syle' );

function analytics_custom_stuff(){
    global $post;

    if( is_single() ){ 
        $daCategories = get_the_category( $post->ID );
        foreach( $daCategories as $cat ){ $currentcat = $cat->cat_name; break; }

        switch ($post->post_type) {
            case 'post_fotos' : $currentPType = '_Foto'; break;
            case 'post_videos' : $currentPType = '_Video'; break;
            case 'post_acciones' : $currentPType = '_Accion'; break;
            case 'especiales' : $currentPType = '_Especial'; break;
            case 'post' : $currentPType = '_Entrada'; break;
        }

        echo "_gaq.push(['_setCustomVar', 5, 'tema', '" . $currentcat . $currentPType . "', 3]);\n";
    }
    if( is_user_logged_in() ){
        echo "_gaq.push(['_setCustomVar', 1, 'Usuarios Logueados', 'Si', 2]);";
    } else {
        echo "_gaq.push(['_setCustomVar', 1, 'Usuarios Logueados', 'No', 2]);";
    }
}
add_action('google_analyticator_extra_js_before', 'analytics_custom_stuff');

add_theme_support('menus');
add_theme_support('post-thumbnails');
add_image_size('listaChica', 130, 85, true);
add_image_size('carousel', 480, 320, true);
add_image_size('portadasDestacado', 210, 210, true);
add_image_size('fotoSingle', 620, 415, true);
add_image_size('singleEspecials', 900, 440, true);
add_image_size('singleEspecials_chico', 440, 440, true);
add_image_size('singleMunicipals', 900, 220, true);
add_image_size('ajaxLoaded', 240, 160, true);
add_image_size('showFullSize', 800, 600, true);
add_image_size('banners', 300, 150, true);
add_image_size('news_destacado', 320, 200, true);
add_image_size('news_gigante', 700, 340, true);
add_image_size('mobile_single', 700, 470, true);
add_image_size('feed', 238, 122, true);

// estas dos funciones son para ocasiones especiales
function pichicatear_acciones( $action_id, $count ){
    $i = $count;
    while( $i-- ){
        $email = 'dummy_'. $i . '@email.com';
        $nombre = 'Usuario_' . $i; 
        accionesInsert($action_id, $email, $nombre);
    }
}
function getEspecialInfo(){
    if( $_REQUEST['listaespeciales'] == 'mostrar' ){
        $out = "";
        $espQuery = new WP_Query(array(
            'post_type' => 'especiales',
            'posts_per_page' => -1
        ));
        if( $espQuery->have_posts() ) { while( $espQuery->have_posts() ) { $espQuery->the_post();
            $out .= '<div style="display: block; margin-bottom: 40px;" >';
            $out .= '<p><strong>Especial: </strong>' . get_the_title() . '</p>';
            $out .= '<p><strong>Especial ID: </strong>' . get_the_ID() . '</p>';
            
            $out .= '<p><strong>Entradas Ajuntas: </strong></p>';
            $out .= '<ul>';
            foreach( (array)get_field('_entradas', get_the_ID()) as $item ){
                $out .= '<li>'. $item['_entrada']->ID .'</li>';
            }
            $out .= '</ul>';
            
            $out .= '<p><strong>Fotos Ajuntas: </strong></p>';
            $out .= '<ul>';
            foreach( (array)get_field('_fotos', get_the_ID()) as $item ){
                $out .= '<li>'. $item['_foto']->ID .'</li>';
            }
            $out .= '</ul>';
            
            $out .= '<p><strong>Videos Ajuntas: </strong></p>';
            $out .= '<ul>';
            foreach( (array)get_field('_videos', get_the_ID()) as $item ){
                $out .= '<li>'. $item['_video']->ID .'</li>';
            }
            $out .= '</ul>';
            
            $out .= '<p><strong>Acciones Ajuntas: </strong></p>';
            $out .= '<ul>';
            foreach( (array)get_field('_acciones', get_the_ID()) as $item ){
                $out .= '<li>'. $item['_accion']->ID .'</li>';
            }
            $out .= '</ul>';
            
            $out .= '</div>';
        }}
        echo $out;
        exit;
    }
}
//add_action('init', 'getEspecialInfo');
if(function_exists("register_options_page")){
    register_options_page('Opciones Generales');
    register_options_page('Banners');
    register_options_page('Mensaje Contingencia');
    register_options_page('Bloquear Publicaciones');
}

function get_the_most(){
    global $wpdb;

    $out = '';

    $primeras_Query = new WP_Query(array(
        'post_type' => 'post',
        'post_status' => 'publish',
        'posts_per_page' => 25,
        'orderby' => 'date',
        'order' => 'ASC'
    ));

    if( $primeras_Query->have_posts() ){
        $out .= '<p><strong>Primeras 25 entradas creadas</strong></p>';
        $out .= '<ul>';

        while( $primeras_Query->have_posts() ){ $primeras_Query->the_post();
            $out .= '<li><a href="'. get_permalink() .'" >'. get_the_title() .'</a></li>';
        }

        $out .= '</ul>';
    }

    $mas_comentadas = new WP_Query(array(
        'post_type' => 'post',
        'post_status' => 'publish',
        'posts_per_page' => 25,
        'orderby' => 'comment_count',
        'order' => 'DESC'
    ));

    if( $mas_comentadas->have_posts() ){
        $out .= '<p><strong>Primeras 25 más comentadas</strong></p>';
        $out .= '<ul>';

        while( $mas_comentadas->have_posts() ){ $mas_comentadas->the_post();
            $out .= '<li><a href="'. get_permalink() .'" >'. get_the_title() .'</a></li>';
        }

        $out .= '</ul>';
    }

    $user_query = $wpdb->get_results("
        SELECT post_author as user_id, COUNT(ID) as contador
        FROM $wpdb->posts
        WHERE post_type = 'post'
        AND post_status = 'publish'
        AND post_author != 0
        GROUP BY post_author
        ORDER BY contador DESC
        LIMIT 26
    ");

    $out .= '<p><strong>Usuarios que más han publicado</strong></p>';
    $out .= '<ul>';

    foreach( $user_query as $user ){
        $out .= '<li>'. nombre_y_apellido( $user->user_id ) .' ('. $user->contador .')</li>';
    }

    $out .= '</ul>';

    echo $out;
    exit;
}
if( $_GET['estadisticas'] === 'mostrar' ){ get_the_most(); }

?>