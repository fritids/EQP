<?php

//MENUS SUPPORT
add_theme_support('menus');
register_nav_menus(array('principal' => 'Menu Header'));

//Funcion para estructuras en comun
function bloques($params, $estructura) {
    $out = "";

    extract(shortcode_atts(array(
        'post_type' => 'post',
        'order' => 'DESC',
        'taxonomy' => false,
        'terms' => false,
        'cat' => false,
        'offset' => 0,
        'tax_query' => false,
        'items' => 1
                    ), $params));
    extract(shortcode_atts(array(
        'bajada' => false, //llamado a Excerpt
        'imagen' => false, //con o sin imagen
        'imagen_size' => 'full', //tamaño de la imagen 
        'acciones' => false,
        'firma' => false,
        'class' => false,
        'return_type' => 'html'
                    ), $estructura)
    );


    $args = array(
        'post_type' => $post_type,
        'order' => $order,
        'orderby' => $orderby,
        'category_name' => $cat,
        'offset' => $offset,
        'paged' => $paged,
        'posts_per_page' => $items,
    );

    $loop = new WP_Query($args);

    while ($loop->have_posts()) : $loop->the_post();
        $imagen = "";
        $image_size = "";

        if (has_post_thumbnail()) {
            $imagen = get_the_post_thumbnail($loop->post->ID, $image_size);
        }
        if ($bajada != 0) {
            $extracto = get_field('texto_destacado', $loop->post->ID) ? cortar(get_field('texto_destacado', $loop->post->ID), $bajada) : cortar($loop->post->post_content, $bajada);
            $excerpt = '<p class="excerpt">' . $extracto . '</p>';
        } else {
            $excerpt = "";
        }

        $tenemos = number_format(get_action_votes($loop->post->ID) * 1, 0, ',', '.');
        $necesitamos = number_format(get_field('requeridos', $loop->post->ID) * 1, 0, ',', '.');
        if ($tenemos == 0 || $tenemos == false || $tenemos == '') {
            $tenemos = '0';
        }

        if ($acciones != false) {
            $accion = '<div class="accionStatus">
                       <div class="accionCount">
                       <span class="Izq"><img src="' . get_bloginfo("stylesheet_directory") . '/img/trianAccL.svg" data-svgFallback="' . get_bloginfo("stylesheet_directory") . '/img/trianAccL.png"/></span>
                       <div class="accWrap">
                       <span class="weHave">Tenemos <strong>' . $tenemos . '</strong></span>
                       <span class="weNeed">Necesitamos <strong>' . $necesitamos . '</strong></span>
                       </div>
                       <span class="Der"><img src="' . get_bloginfo("stylesheet_directory") . '/img/trianAcc.svg" data-svgFallback="' . get_bloginfo("stylesheet_directory") . '/img/trianAcc.png"/></span>
                       </div>
                       <a class="firm" href="'. get_permalink() .'" title="Firma y Participa!" rel="contents">Firma y Participa</a>
                       </div>';
        } else {
            $accion = "";
        }

        $categories = get_the_category($loop->post->ID);
        foreach ($categories as $cat) {
            $catList[0] = $cat->cat_name;
            $catList[1] = $cat->term_id;
        }

        $avatar = get_simple_local_avatar($loop->post->post_author, 40);

        $out .= '<article class="' . $class . '" data-equializeHeights="' . ( $acciones ? 'sliderAcciones' : 'sliderEntradas' ) . '">';
        $out .= '<h3><a href="' . get_permalink() . '" title="ir a ' . get_the_title() . '" rel="contents">' . get_the_title() . '</a></h3>';
        $out .= mobile_get_author_html($loop->post);
        $out .= $excerpt;
        $out .= $accion ? '<div>' . $accion . '</div>' : '';
        $out .= '</article>';

    endwhile;

    if ($return_type == 'html') {
        return $out;
    } else {
        return array(
            'html' => $out,
            'offset' => $offset,
            'post_count' => $loop->post_count,
            'found_posts' => $loop->found_posts,
            'posts_left' => $loop->found_posts - $offset - $loop->post_count
        );
    }
}

function mobile_get_entradas_activas($params) {
    $queryArgs = array(
        'post_type' => 'post',
        'post_status' => 'publish',
        'posts_per_page' => $params['posts_per_page'],
        'order' => 'DESC',
        'meta_key' => '_total',
        'orderby' => 'meta_value_num',
        'offset' => $params['offset']
    );
        add_filter('posts_where', 'rangoTiempoEntradas');
    $q = new WP_Query($queryArgs);
        remove_filter('posts_where', 'rangoTiempoEntradas');

    if ($q->have_posts()) {
        while ($q->have_posts()) {
            $q->the_post();

            $extracto = get_field('texto_destacado', $q->post->ID) ? cortar(get_field('texto_destacado', $q->post->ID), 300) : cortar($q->post->post_content, 300);
            $excerpt = '<p class="excerpt">' . $extracto . '</p>';

            $categories = get_the_category($q->post->ID);
            foreach ($categories as $cat) {
                $catList[0] = $cat->cat_name;
                $catList[1] = $cat->term_id;
            }

            $out .= '<article class="column4 recentNew unidad-discreta" data-equializeHeights="sliderEntradas">';
            $out .= '<h3><a href="' . get_permalink() . '" title="ir a ' . get_the_title() . '" rel="contents">' . get_the_title() . '</a></h3>';
            $out .= mobile_get_author_html($q->post);
            $out .= $excerpt;
            $out .= '</article>';
        }
    }
    if ($params['return_type'] === 'array') {
        return array(
            'html' => $out,
            'offset' => $params['offset'],
            'post_count' => $q->post_count,
            'found_posts' => $q->found_posts,
            'posts_left' => $q->found_posts - $params['offset'] - $q->post_count
        );
    } else {
        return $out;
    }
}

function SlideHomeMobile() {
    $out = "";

    foreach ((array) get_field('carrusel_home_destacado', 'options') as $thePost) {
        if (!is_object($thePost)) {
            $thePost = get_post($thePost['post_id_destacado']);
        } // por si viene un post_id

        if (has_post_thumbnail($thePost->ID)) {
            mobile_regenerate_image_data(get_post_thumbnail_id($thePost->ID), 'carousel');
        }

        if ($thePost->post_type == 'post_videos') {
            $thumbnail = get_the_embed(get_field('video_link', $thePost->ID)) ? get_the_embed(get_field('video_link', $thePost->ID)) : get_the_post_thumbnail($thePost->ID, 'carousel');
        } else {
            $thumbnail = get_the_post_thumbnail($thePost->ID, 'carousel');
        }

        $currentItem = $count == 0 ? 'current' : '';

        $extracto = get_field('texto_destacado', $thePost->ID) ? cortar(get_field('texto_destacado', $thePost->ID), 250) : cortar($thePost->post_content, 250);

        $out .= '<li data-item="' . ($count + 1) . '">';
        $out .= '   <div class="column6 slideImage slider-thumbnail">' . $thumbnail . '</div>';
        $out .= '   <div class="slideContent">';
        $out .= '       <h3 class="tituloSlide"><a href="' . get_permalink($thePost->ID) . '" rel="content" title="' . $thePost->post_title . '">' . get_the_title($thePost->ID) . '</a></h3>';
        $out .= mobile_get_author_html($thePost, 'mobile-dark-bg', false);
        $out .= '       <p class="excerptDesta hide-on-phones"> ' . $extracto . ' </p>';
        $out .= '   </div>';
        $out .= '</li>';

        $count++;
    }
    return $out;
}

function mobile_get_mediadeldia($pType) {
    $out = "";
    $q = new WP_Query(array(
        'posts_per_page' => 1,
        'post_type' => $pType,
        'order' => 'DESC',
        'orderby' => 'date',
        'meta_key' => 'destacado_portada'
    ));
    if ($q->have_posts()) {
        while ($q->have_posts()) {
            $q->the_post();
            if ($pType == 'post_videos') {
                $thumbnail = mobile_get_the_embed(get_field('video_link', $q->post->ID)) ? mobile_get_the_embed(get_field('video_link', $q->post->ID)) : get_the_post_thumbnail($q->post->ID, 'fotoSingle', array('alt' => $q->post->post_title, 'title' => $q->post->post_title));
                update_post_meta($q->post->ID, 'ajaxEmbed', $thumbnail);
            } else {
                $thumbnail = get_the_post_thumbnail($q->post->ID, 'fotoSingle', array('alt' => $q->post->post_title, 'title' => $q->post->post_title, 'class' => 'evt', 'data-attid' => $q->post->ID, 'data-posttype' => $q->post->post_type, 'data-func' => 'media_verMasGrande'));
            }

            $extracto = get_field('texto_destacado', $q->post->ID) ? cortar(get_field('texto_destacado', $q->post->ID), 400) : cortar($q->post->post_content, 400);

            $out .= '<div class="column8 slideImage">';
            $out .= $thumbnail;
            $out .= '</div>';
            $out .= '<h2 class="tituloFotoDesta"><a  href="' . get_permalink() . '" title="Ver ' . get_the_title() . '" rel="content">';
            $out .= get_the_title();
            $out .= '</a></h2>';
            $out .= mobile_get_author_html($q->post);
            $out .= '<p class="excerpt hide-on-phones">';
            $out .= $extracto;
            $out .= '</p>';
        }
    }
    wp_reset_query();
    return $out;
}

function mobile_get_lastmedia($settings) {
    $out = "";
    $counter = 0;
    $q = new WP_Query(array(
        'post_type' => $settings['post_type'],
        'posts_per_page' => $settings['posts_per_page'],
        'order' => 'DESC',
        'orderby' => 'date',
        'offset' => $settings['offset']
    ));
    if ($q->have_posts()) {
        while ($q->have_posts()) {
            $q->the_post();

            if ($counter == 0) {
                $figClass = 'bigFoto';
                $thumbSize = 'fotoSingle';
            } else {
                $figClass = 'smallFoto';
                $thumbSize = 'news_destacado';
            }

            $out .= '<figure class="' . $figClass . ' unidad-discreta aLeft ">';

            // debería hacer que se auto genere el tamaño del thumbnail, pero aparentemente es demasiado pesado para hacerlo en un loop
//        mobile_regenerate_image_data( get_post_thumbnail_id( $q->post->ID ), $thumbSize );
            $out .= get_the_post_thumbnail($q->post->ID, $thumbSize, array('class' => 'evt', 'data-attid' => $q->post->ID, 'data-posttype' => $q->post->post_type, 'data-func' => 'media_verMasGrande'));
            $out .= '<figcaption>';
            $out .= '<a href="' . get_permalink() . '" title="Ver ' . get_the_title() . '" rel="contents">' . get_the_title() . '</a>';
            $out .= mobile_get_author_html($q->post, 'only-on-phones');
            $out .= '</figcaption>';
            $out .= '</figure>';

            $counter++;
        }
    }
    wp_reset_query();
    return $out;
}

function mobile_get_activemedia($settings) {
    $out = "";
    $counter = 0;
    $queryArgs = array(
        'post_type' => $settings['post_type'],
        'post_status' => 'publish',
        'posts_per_page' => $settings['posts_per_page'],
        'order' => 'DESC',
        'meta_key' => '_total',
        'orderby' => 'meta_value_num',
        'offset' => $settings['offset']
    );

    add_filter('posts_where', 'rangoTiempo');
    $q = new WP_Query($queryArgs);
    remove_filter('posts_where', 'rangoTiempo');

    if ($q->have_posts()) {
        while ($q->have_posts()) {
            $q->the_post();
            if ($counter == 0) {
                $figClass = 'bigFoto';
                $thumbSize = 'fotoSingle';
            } else {
                $figClass = 'smallFoto';
                $thumbSize = 'news_destacado';
            }

            $out .= '<figure class="' . $figClass . ' unidad-discreta aLeft ">';

            // debería hacer que se auto genere el tamaño del thumbnail, pero aparentemente es demasiado pesado para hacerlo en un loop
//        mobile_regenerate_image_data( get_post_thumbnail_id( $q->post->ID ), $thumbSize );
            $out .= get_the_post_thumbnail($q->post->ID, $thumbSize, array('class' => 'evt', 'data-attid' => $q->post->ID, 'data-posttype' => $q->post->post_type, 'data-func' => 'media_verMasGrande'));
            $out .= '<figcaption>';
            $out .= '<a href="' . get_permalink() . '" title="Ver ' . get_the_title() . '" rel="contents">' . get_the_title() . '</a>';
            $out .= mobile_get_author_html($q->post, 'only-on-phones');
            $out .= '</figcaption>';
            $out .= '</figure>';

            $counter++;
        }
    }
    wp_reset_query();
    return $out;
}

function mobile_get_actions($settings) {
    $out = "";
    $isHome = is_home();
    $isPortadilla = is_page( 'acciones-home' );

    if ($settings['type'] == 'featured') {
        $counter = 1;
        $featuredActions = get_field('acciones_destacadas', 'options');

        foreach ((array) $featuredActions as $action) {
            if ($counter == count($featuredActions)) { $lastClass = 'last'; } 
            else { $lastClass = ''; }

            if( $isHome ){
                $button_atts = 'class="firm track-action" ';
                $button_atts .= 'data-ga-category="Participacion" ';
                $button_atts .= 'data-ga_action="Firmas" ';
                $button_atts .= 'data-ga_opt_label="BtnMobFirma_Home" ';
            } 
            elseif( $isPortadilla ){
                $button_atts = 'class="firm track-action" ';
                $button_atts .= 'data-ga-category="Participacion" ';
                $button_atts .= 'data-ga_action="Firmas" ';
                $button_atts .= 'data-ga_opt_label="BtnMobFirma_Accionportadilla" ';
            }
            else {
                $button_atts = 'class="firm"';
            }

            $theAction = get_post($action['accion_id']);

            $tenemos = number_format(mobile_get_action_votes($theAction->ID) * 1, 0, ',', '.');
            $necesitamos = number_format(get_field('requeridos', $theAction->ID) * 1, 0, ',', '.');
            if (!$tenemos) { $tenemos = '0'; }

            $out .= '<article class="column4 unidad-discreta ' . $lastClass . '">';
            $out .= '<h3 data-equalize="featuredTitles" ><a href="' . get_permalink($theAction->ID) . '" title="Ver ' . get_the_title($theAction->ID) . '" rel="contents">' . get_the_title($theAction->ID) . '</a></h3>';
            $out .= mobile_get_author_html($theAction);
            $out .= '<div class="accionStatus">';
            $out .= '<div class="accionCount">';
            $out .= '<span class="Izq"><img src="' . get_bloginfo('stylesheet_directory') . '/img/trianAccL.svg" data-svgfallback="' . get_bloginfo('stylesheet_directory') . '/img/trianAccL.png"/></span>';
            $out .= '<div class="accWrap">';
            $out .= '<span class="weHave">Tenemos <strong>' . $tenemos . '</strong></span>';
            $out .= '<span class="weNeed">Tenemos <strong>' . $necesitamos . '</strong></span>';
            $out .= '</div>';
            $out .= '<span class="Der"><img src="' . get_bloginfo('stylesheet_directory') . '/img/trianAcc.svg" data-svgfallback="' . get_bloginfo('stylesheet_directory') . '/img/trianAcc.png" /></span>';
            $out .= '</div>';
            $out .= '<a '. $button_atts .' href="' . get_permalink($theAction->ID) . '" title="Firma y Participa" rel="content">Firma y Participa</a>';
            $out .= '</div>';
            $out .= '</article>';

            $counter++;
        }
    } elseif ($settings['type'] == 'masAdherencia' || $settings['type'] == 'masRecientes') {
        $args = array(
            'post_type' => 'post_acciones',
            'posts_per_page' => 3,
            'order' => 'DESC',
            'offset' => 0
        );

        if ($settings['type'] == 'masAdherencia') {
            $args['orderby'] = 'meta_value_num';
            $args['meta_key'] = '_adhesiones';
        } else {
            $args['orderby'] = 'date';
        }


        $query = new WP_Query($args);
        if ($query->have_posts()) {
            $counter = 1;
            $out .= '<li>';
            while ($query->have_posts()) {
                $query->the_post();
                if ($counter == $query->post_count) {
                    $lastClass = 'last';
                } else {
                    $lastClass = '';
                }

                $theAction = $query->post;

                $tenemos = number_format(mobile_get_action_votes($theAction->ID) * 1, 0, ',', '.');
                $necesitamos = number_format(get_field('requeridos', $theAction->ID) * 1, 0, ',', '.');
                if (!$tenemos) {
                    $tenemos = '0';
                }

                $out .= '<article class="column4 unidad-discreta ' . $lastClass . '">';
                $out .= '<h3 data-equalize="featuredTitles" ><a href="' . get_permalink($theAction->ID) . '" title="Ver ' . get_the_title($theAction->ID) . '" rel="contents">' . get_the_title($theAction->ID) . '</a></h3>';
                $out .= mobile_get_author_html($theAction);
                $out .= '<div class="accionStatus">';
                $out .= '<div class="accionCount">';
                $out .= '<span class="Izq"><img src="' . get_bloginfo('stylesheet_directory') . '/img/trianAccL.svg" data-svgfallback="' . get_bloginfo('stylesheet_directory') . '/img/trianAccL.png" /></span>';
                $out .= '<div class="accWrap">';
                $out .= '<span class="weHave">Tenemos <strong>' . $tenemos . '</strong></span>';
                $out .= '<span class="weNeed">Tenemos <strong>' . $necesitamos . '</strong></span>';
                $out .= '</div>';
                $out .= '<span class="Der"><img src="' . get_bloginfo('stylesheet_directory') . '/img/trianAcc.svg" data-svgfallback="' . get_bloginfo('stylesheet_directory') . '/img/trianAcc.png" /></span>';
                $out .= '</div>';
                $out .= '<a class="firm" href="' . get_permalink($theAction->ID) . '" title="Firma y Participa" rel="content">Firma y Participa</a>';
                $out .= '</div>';
                $out .= '</article>';

                $counter++;
            }
            $out .= '</li>';
            $query = null; // se anula para ahorrar memoria
        }
    }

    return $out;
}

function mobile_get_especial_contents($typeName, $pid) {
    $counter = 0;
    $outArray = array();
    $finalOut = '';

    foreach ((array) get_field($typeName, intval($pid)) as $postObjectAr) {
        if ($typeName === '_entradas') {
            $daPost = get_post(intval($postObjectAr['_entrada']));

            if ($counter === 1) { // segunda vuelta
                $edgeClass = 'last';
                $counter = -1;
            } else {
                $edgeClass = '';
            }

            $extracto = get_field('texto_destacado', $daPost->ID) ? cortar(get_field('texto_destacado', $daPost->ID), 300) : cortar($daPost->post_content, 300);

            $out = '<article class="recentNew unidad-discreta column6 editorial ' . $edgeClass . '">';
            $out .= '<h3><a href="' . get_permalink($daPost->ID) . '" title="Ir a ' . get_the_title($daPost->ID) . '" rel="contents">' . get_the_title($daPost->ID) . '</a></h3>';
            $out .= mobile_get_author_html($daPost);
            $out .= '<p class="excerpt">';
            $out .= $extracto;
            $out .= '</p>';
            $out .= '</article>';

            $outArray[] = $out;

            $counter++;
        } elseif ($typeName === '_fotos' || $typeName === '_videos') {
            $daPost = get_post(intval($postObjectAr[substr($typeName, 0, -1)]));

            if ($typeName === '_videos') {
                $embedIframe = mobile_get_the_embed(get_field('video_link', $daPost->ID), 'style="width: 100%;"');
                update_post_meta($daPost->ID, 'ajaxEmbed', $embedIframe);
            }

            $out = '<figure class="fotoEntryEdit unidad-discreta">';
            $out .= get_the_post_thumbnail($daPost->ID, 'carousel', array('class' => 'evt', 'data-attid' => $daPost->ID, 'data-posttype' => $daPost->post_type, 'data-func' => 'media_verMasGrande'));
            $out .= '<figcaption>';
            $out .= '<h3><a href="' . get_permalink($daPost->ID) . '" title="Ver ' . get_the_title($daPost->ID) . '" rel="contents">' . get_the_title($daPost->ID) . '</a></h3>';
            $out .= mobile_get_author_html($daPost, 'only-on-phones');
            $out .= '</figcaption>';
            $out .= '</figure>';

            $outArray[] = $out;
        } elseif ($typeName === '_acciones') {
            $daPost = get_post(intval($postObjectAr['_accion']));

            if ($counter === 3) { // 4° vuelta
                $edgeClass = 'last';
                $counter = -1;
            } else {
                $edgeClass = '';
            }

            $tenemos = number_format(mobile_get_action_votes($daPost->ID) * 1, 0, ',', '.');
            $necesitamos = number_format(get_field('requeridos', $daPost->ID) * 1, 0, ',', '.');
            if (!$tenemos) {
                $tenemos = '0';
            }

            $out = '<article class="unidad-discreta ' . $edgeClass . '">';
            $out .= '<h3><a href="' . get_permalink($daPost->ID) . '" title="Ver ' . get_the_title($daPost->ID) . '" rel="contents">' . get_the_title($daPost->ID) . '</a></h3>';
            $out .= mobile_get_author_html($daPost);
            $out .= '<div class="accionStatus">';
            $out .= '<div class="accionCount">';
            $out .= '<span class="Izq"><img src="' . get_bloginfo('stylesheet_directory') . '/img/trianAccL.svg"/></span>';
            $out .= '<div class="accWrap">';
            $out .= '<span class="weHave">Tenemos <strong>' . $tenemos . '</strong></span>';
            $out .= '<span class="weNeed">Tenemos <strong>' . $necesitamos . '</strong></span>';
            $out .= '</div>';
            $out .= '<span class="Der"><img src="' . get_bloginfo('stylesheet_directory') . '/img/trianAcc.svg"/></span>';
            $out .= '</div>';
            $out .= '<a class="firm" href="' . get_permalink($daPost->ID) . '" title="Firma y Participa" rel="content">Firma y Participa</a>';
            $out .= '</div>';
            $out .= '</article>';

            $outArray[] = $out;

            $counter++;
        }
    }

    if ($typeName !== '_acciones' && !empty($outArray)) {
        $primerBloque = array_slice($outArray, 0, 2);
        $segundoBloque = array_slice($outArray, 2, 2);
        if (!empty($primerBloque)) {
            $finalOut .= '<li class="clearfix first-group">';
            $finalOut .= implode('', $primerBloque);
            $finalOut .= '</li>';
        }
        if (!empty($segundoBloque)) {
            $finalOut .= '<li class="clearfix last-group">';
            $finalOut .= implode('', $segundoBloque);
            $finalOut .= '</li>';
        }
    } elseif (!empty($outArray)) {
        $finalOut .= implode('', $outArray);
    }

    return $finalOut;
}

function mobile_get_author_html($postObj, $aditionalClass = false, $fullInfo = true) {
    $categories = get_the_category($postObj->ID);
    foreach ((array) $categories as $cat) {
        $catList[0] = $cat->cat_name;
        $catList[1] = $cat->term_id;
    }
    $out = '<div class="item-meta ' . $aditionalClass . '">';
    $out .= '<div class="smallAvatar">';
    $out .= get_simple_local_avatar($postObj->post_author, 40);
    $out .= '</div>';
    $out .= '<p class="publicadoPor">Por: <a href="/perfil-de-usuario/?user=' . $postObj->post_author . '" title="Ver el perfil de ' . nombre_y_apellido($postObj->post_author) . '" rel="nofollow">' . nombre_y_apellido($postObj->post_author) . '</a></p>';
    if ($fullInfo) {
        $out .= $catList[0] ? '<p class="publicadoEn">En: ' . $catList[0] . '</p>' : '';
        $out .= '<p class="publicadoCuando">' . mysql2date(get_option('date_format'), $postObj->post_date) . '</p>';
    }
    $out .= '</div>';

    return $out;
}

function mobile_regenerate_image_data($attId, $sizeToCheck) {
    $attMeta = wp_get_attachment_metadata(intval($attId));
    if (isset($attMeta['sizes'][$sizeToCheck])) {
        return false;
    } else {
        $attPath = get_attached_file(intval($attId));
        $attach_data = wp_generate_attachment_metadata(intval($attId), $attPath);
        wp_update_attachment_metadata(intval($attId), $attach_data);
    }
}

function mobile_get_the_embed($url, $sizeStr = false) {
    global $wp_embed;

    $url = explode('//', $url);
    $url = 'http://' . $url[1];

    return $wp_embed->run_shortcode('[embed' . ($sizeStr ? ' ' . $sizeStr : '') . ']' . $url . '[/embed]');
}

function mobile_getSocialShares($overrides) {
    extract(shortcode_atts(array(
        'pid' => 0,
        'wrapper' => false,
        'customMessage' => false
                    ), $overrides));

    $thePost = get_post(intval($pid));

    $urlencoded = urldecode(get_permalink($thePost->ID));
    $title = urlencode($thePost->post_title);
    $shortUrl = $_SERVER['HTTP_HOST'] . '/?p=' . $pid;
    $imageThumb = has_post_thumbnail($pid) ? wp_get_attachment_image_src(get_post_thumbnail_id($thePost->ID)) : 'http://www.elquintopoder.cl/logo/logofb.jpg';

    $mensaje .= 'Lee, opina y comparte: ' . get_the_title($thePost->ID) . ' en elquintopoder.cl ';

    if ($customMessage) {
        $mensaje = $customMessage;
    }

    $fblink = 'http://www.facebook.com/sharer.php?s=100&p[title]=' . $title . '&p[summary]=' . urldecode($mensaje) . '%21&p[url]=' . $urlencoded . '&&p[images][0]=' . (is_string($imageThumb) ? $imageThumb : $imageThumb[0]);
    $twlink = 'https://twitter.com/intent/tweet?text=' . $title . '+' . $shortUrl . '&hashtags=5poder&via=elquintopoder';

    $fbAnchor = '<a class="faceBot track-action track-social evt" data-ga_network="Facebook" da-ga_socialaction="Share" data-provider="facebook" data-pid="' . $pid . '" data-func="anadirShares" href="' . $fblink . '" title="Compartir en Facebook" rel="nofollow" >Comparte</a>';
    $twtAnchor = '<a class="tweetBot track-action track-social evt" data-ga_network="Twitter" da-ga_socialaction="Share" data-provider="twitter" data-pid="' . $pid . '" data-func="anadirShares" href="' . $twlink . '" title="Compartir en Twitter" rel="nofollow" >Difunde</a>';

    if ($wrapper) {
        return '<' . $wrapper . '>' . $fbAnchor . '</' . $wrapper . '><' . $wrapper . '>' . $twtAnchor . '</' . $wrapper . '>';
    }

    return $fbAnchor . $twtAnchor;
}

function mobile_get_action_votes($postid) {
    global $wpdb;
    $number = $wpdb->get_var($wpdb->prepare(" SELECT COUNT(post_id) FROM $wpdb->acciones WHERE $wpdb->acciones.post_id = %d ", $postid));

    return $number;
}

function mobile_get_action_voters_list($postid) {
    global $wpdb;

    $out = "";
    $firmantes = $wpdb->get_results($wpdb->prepare("
        SELECT nombre
        FROM $wpdb->acciones 
        WHERE $wpdb->acciones.post_id = $postid
        ORDER BY id DESC
        LIMIT 10
    "));
    if (empty($firmantes)) {
        return false;
    } else {
        $out .= '<ul class="adherentesList">';
        foreach ((array) $firmantes as $user) {
            $out .= '<li>' . $user->nombre . '</li>';
        }
        $out .= '</ul>';
    }
    return $out;
}

function mobile_getComments($comment, $args, $depth) {
    global $post;
    $GLOBALS['comment'] = $comment;

    if (!empty($comment->user_id)) {
        $authorName = nombre_y_apellido($comment->user_id);
    } else {
        $authorName = get_comment_author();
    }

    $isReply = $comment->comment_parent != 0 ? 'commenAnswer' : 'comentParent';

    $commentOut .= '<div id="comment-' . $comment->comment_ID . '" class="' . $isReply . ' clearfix" data-comment-parent="' . $comment->comment_parent . '" >';
    $commentOut .= '<header class="comment-header clearfix">';
    $commentOut .= '<div class="commentMeta clearfix">';
    $commentOut .= '<div class="smallAvatar">';
    $commentOut .= get_simple_local_avatar($comment->user_id, 40);
    $commentOut .= '</div>';
    $commentOut .= '<div class="commentAutor">';
    $commentOut .= '<p>Por: <strong>' . $authorName . '</strong></p>';
    $commentOut .= '<p>' . get_comment_date() . '</p>';
    $commentOut .= '</div>';
    $commentOut .= '<div class="commentFeed">';
    $commentOut .= '<div class="likeCount">';
    $commentOut .= '<div class="likesWrap">';

    $commentOut .= '<a href="javascript:void(0);" onclick="votecomment( ' . $comment->comment_ID . ', 1 );" class="like" title="Aprovar comentario"></a>';
    $commentOut .= '</div>';
    $commentOut .= '</div>';
    $commentOut .= '<div class="unlikeCount">';
    $commentOut .= '<div class="likesWrap">';
    $commentOut .= '<a href="javascript:void(0);" onclick="votecomment( ' . $comment->comment_ID . ', -1 );" class="unlike" title="Desaprobar comentario"></a>';
    if (($votos = CVGetCommentVote($comment->comment_ID)) < 0) {
        $spanClass = 'negative';
    } else {
        $spanClass = 'positive';
    }
    $commentOut .= '<span class="commentsvote_' . $spanClass . '" id="commentsvote_span_' . $comment->comment_ID . '">' . $votos . '</span>';
    $commentOut .= '</div>';
    $commentOut .= '</div>';
    $commentOut .= '</div>';
    $commentOut .= $comment->comment_parent != 0 ? '' : get_comment_reply_link(array('depth' => 1, 'max_depth' => 2, 'login_text' => 'Responder', 'reply_text' => 'Responder', 'respond_id' => 'commentform'));
    $commentOut .= '</div>';
    $commentOut .= '</header>';
    $commentOut .= '<div class="comment">';

    $content = get_comment_text();
    $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
    $commentOut .= apply_filters('comment_text', preg_replace($reg_exUrl, '<a href="$0" rel="nofollow">$0</a> ', $content));

    $commentOut .= '</div>';

    echo $commentOut;
}

function mobile_list_contents($overrides) {
    extract(shortcode_atts(array(
        'autor' => null,
        'items' => 5,
        'offset' => 0,
        'order' => 'DESC',
        'orderby' => 'date',
        'searchString' => null,
        'echo' => false
                    ), $overrides));

    $query = new WP_Query(array(
        'post_type' => array('post', 'post_fotos', 'post_videos', 'post_acciones', 'especiales'),
        'posts_per_page' => $items,
        'offset' => $offset,
        'order' => $order,
        'orderby' => $orderby,
        'author' => $autor,
        's' => $searchString
    ));

    $out = '';
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $currentPost = $query->post;

            switch ($currentPost->post_type) {
                case 'post':
                    $boxClass = 'publiEntry';
                    break;
                case 'post_fotos':
                    $boxClass = 'publiFoto';
                    break;
                case 'post_videos':
                    $boxClass = 'publiVideo';
                    break;
                case 'post_acciones':
                    $boxClass = 'publiAccion';
                    break;
                default :
                    $boxClass = 'publiEqp';
                    break;
            }
            $extracto = get_field('texto_destacado', $currentPost->ID) ? cortar(get_field('texto_destacado', $currentPost->ID), 300) : cortar($currentPost->post_content, 300);

            $out .= '<li class="' . $boxClass . '">';
            $out .= '<h3><a href="' . get_permalink() . '" title="Ir a ' . get_the_title() . '" rel="contents">' . get_the_title() . '</a></h3>';
            $out .= '<p>' . $extracto . '</p>';
            $out .= '</li>';
        }
    }
    wp_reset_query();

    if ($echo) {
        echo $out;
    } else {
        return $out;
    }
}

function mobile_get_related($overrides) {
    extract(shortcode_atts(array(
        'pType' => 'post',
        'items' => 6,
        'currentPost' => 0,
        'taxQuery' => false,
        'categorySlug' => false
                    ), $overrides));

    $out = "";

    $theQuery = new WP_Query(array(
        'post_type' => $pType,
        'posts_per_page' => $items,
        'post__not_in' => $currentPost,
        'category_name' => $categorySlug
    ));

    if ($theQuery->have_posts()) {
        while ($theQuery->have_posts()) {
            $theQuery->the_post();
            $out .= '<li>';
            $out .= '<h3><a href="' . get_permalink() . '" title="Ver ' . get_the_title() . '" rel="content">' . get_the_title() . '</a></h3>';
            $out .= mobile_get_author_html($theQuery->post);
            $out .= '</li>';
        }
    }
    wp_reset_query();

    return $out;
}

function mobile_get_page_content($slug) {
    $args = array(
        'name' => $slug,
        'post_type' => 'page',
        'post_status' => 'publish',
        'numberposts' => 1
    );
    $daPost = get_posts($args);

    echo '<h2 class="long-list-title" >';
    echo $daPost[0]->post_title;
    echo '</h2>';
    echo '<div class="colapsable-content">';
    echo apply_filters('the_content', $daPost[0]->post_content);
    echo '</div>';
}

function mobile_breadcrumbs($lastTitle = false) {
    global $post;
    $out = "";
    if ($post->post_type !== 'page') {
        switch (get_post_type()) {
            case 'post':
                $parentPid = 24; //pagina Entradas
                break;
            case 'post_fotos' :
                $parentPid = 31; //pagina Fotos
                break;
            case 'post_videos' :
                $parentPid = 33; //pagina Videos
                break;
            case 'post_acciones' :
                $parentPid = 35; //pagina Acciones
                break;
            case 'especiales' :
                $parentPid = 37; //pagina Especiales
                break;
        }
        $postParent = get_post($parentPid);
    } elseif (is_search()) {
        $postParent = false;
    } else {
        $postParent = $post;
    }
    $out .= '<ul class="breadcrums">';

    $out .= '<li><a href="' . home_url() . '" title="Ir al Inicio" rel="index">Inicio</a></li>';
    $out .= '<li>/</li>';
    $out .= '<li><a href="' . ( $postParent ? get_permalink($postParent->ID) : home_url()) . '" title="Ver ' . ( $lastTitle ? $lastTitle : get_the_title($postParent->ID) ) . '" rel="section">' . ( $lastTitle ? $lastTitle : get_the_title($postParent->ID) ) . '</a></li>';

    $out .= '</ul>';

    return $out;
}

function mobile_readLater_button($type) {
    if (!is_user_logged_in()) {
        return false;
    }

    global $post, $current_user;
    // carga informacion en la variable global $current_user
    get_currentuserinfo();

    $noLeidos = (array) get_user_meta($current_user->ID, 'lectura_no_leidos', true);
    $status = 'no_leido';
    $buttonText = 'Leer después';
    $buttonTitle = 'Agregar a la lista de lectura';

    if (in_array($post->ID, $noLeidos)) {
        $status = 'leido';
        $buttonText = 'Marcar leído';
        $buttonTitle = 'Marcar esto como leído';
    }

    $btnClass = $type === 'phones' ? 'only-on-phones' : 'hide-on-phones';

    echo '<a class="readLater ' . $btnClass . ' evt" data-func="marcar_' . $status . '" data-pid="' . $post->ID . '" data-user="' . $current_user->ID . '" href="#" title="' . $buttonTitle . '" rel="nofollow">';
    echo $buttonText;
    echo '</a>';
}

function mobile_get_shares($provider, $pid) {
    $shares = get_post_meta($pid, '_shares_' . $provider, true);
    return intval($shares);
}

function mobile_comments_number($pid = false) {
    if (!$pid) {
        global $post;
        $pid = $post->ID;
    }
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

function mobile_get_user_activity($uid, $pType) {
    global $wpdb;
    $number = $wpdb->get_var("
        SELECT COUNT(ID) 
        FROM $wpdb->posts 
        WHERE $wpdb->posts.post_type = '$pType'
        AND $wpdb->posts.post_author = $uid
    ");
    return $number;
}

function mobile_getOpenGraphMetas() {
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

function get_firmaAccionForm($user, $pid) {
    $customFields = get_post_meta($pid, 'campos_requeridos', true);

    $out = '<form id="action-vote-form" class="ajax-form contacto" action="" method="post">';
    $out .= '<h2 class="lightbox-title">Firmar Acción</h2>';
    if ($user) {
        $nombreUsuario = nombre_y_apellido($user->ID);
        $out .= '<p class="nice-paragraph">Firma esta acción con tu cuenta de El Quinto Poder</p>';
        $out .= '<div class="clearfix user-resume" >';
        $out .= get_simple_local_avatar($user->ID, 40);
        $out .= '<p class="nice-paragraph"><small>Has ingresado como </small><br><strong>' . $nombreUsuario . '</strong></p>';
        $out .= '</div>';
        $out .= '<input type="hidden" name="usrName" value="' . $nombreUsuario . '" >';
        $out .= '<input type="hidden" name="usrEmail" value="' . $user->user_email . '" >';
        $out .= '<input type="hidden" name="usrID" value="' . $user->ID . '" >';
        $out .= '<input type="hidden" id="userLogued" name="userLogued" value="' . $user->ID . '" >';
    } else {
        $out .= '<div class="user-login-box">';
        $out .= '<input type="checkbox" name="userTest" required value="" style="display:none;" >';
        $out .= '<p class="nice-paragraph">Para adherir a la acción <strong>' . get_the_title($pid) . '</strong>, puedes hacerlo a través de:</p>';
        $out .= '<button class="sendForm action-call evt track-action" data-ga-category="Participacion" data-ga_action="Firmas" data-ga_opt_label="BtnMobFirma_RegIngresar" data-func="accion_loginChoice" data-choice="login" title="Ingresa con tu usuario de El Quinto Poder">Tu cuenta en El Quinto Poder</button>';
        $out .= '<p class="nice-paragraph helper">o si no posees una cuenta puedes:</p>';
        $out .= '<button class="sendForm action-call evt track-action" data-ga-category="Participacion" data-ga_action="Firmas" data-ga_opt_label="BtnMobFirma_SinRegIngresar" data-func="accion_loginChoice" data-choice="noLogin" title="Ingresa tu nombre y correo electrónico">Ingresar tu nombre y correo electrónico</button>';
        $out .= '</div>';
    }
    if (!empty($customFields)) {
        foreach ((array) $customFields as $field) {
            if ($field == 'rut') {
                $customValidation = 'data-custom-validation="validarRut"';
            } else {
                $customValidation = '';
            }

            $out .= '<label for="accion_fields_' . $field . '" >' . ucfirst($field) . '</label>';
            $out .= '<input type="text" name="accion_fields[' . $field . ']" id="accion_fields_' . $field . '" placeholder="Ingresa tu ' . $field . '" required ' . $customValidation . ' >';
        }
    }

    $out .= '<input type="hidden" name="accionPid" value="' . $pid . '" >';
    $out .= '<input type="hidden" name="logued_user" value="' . ( $user ? 'true' : 'false' ) . '" >';

    $out .= '<input id="sendBtn" class="sendForm aRight" type="submit" value="Firmar Acción">';
    $out .= '</form>';

    return $out;
}

function mobile_accionesInsert($overrides) {
    global $wpdb;

    extract(shortcode_atts(array(
        'postid' => false,
        'email' => false,
        'nombre' => false,
        'userid' => 0,
        'aditionalFields' => array()
                    ), $overrides));


    if (($postid && $email && $nombre) && mobile_checkVoteValidity($postid, $email)) {
        if (!empty($aditionalFields)) {
            $wpdb->insert($wpdb->acciones, array(
                'user_id' => $userid,
                'post_id' => $postid,
                'email' => $email,
                'nombre' => $nombre,
                'rut' => $aditionalFields['rut'],
                'profesion' => $aditionalFields['profesion'],
                'institucion' => $aditionalFields['institucion']
            ));
        } else {
            $wpdb->insert($wpdb->acciones, array(
                'user_id' => $userid,
                'post_id' => $postid,
                'email' => $email,
                'nombre' => $nombre
            ));
        }
        update_post_meta($postid, '_adhesiones', mobile_get_action_votes($postid));
        mobile_sendActionEmail_byHundred($postid, $email);
        return true;
    } else {
        return false;
    }
}

function mobile_checkVoteValidity($postid, $email) {
    global $wpdb;

    $sqlQuery = "SELECT email FROM $wpdb->acciones WHERE email= %s AND post_id= %d ";
    $exist = $wpdb->get_results($wpdb->prepare($sqlQuery, $email, $postid));

    if (!$exist) {
        return true;
    } else {
        return false;
    }
}

function mobile_sendActionEmail_byHundred($postid, $email) {
    $thepost = get_post($postid);
    $postAuthor = get_userdata($thepost->post_author);

    $voteCount = intval(get_action_votes($postid)) / 100;

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

        mobile_mailMe(array(
            'encabezado' => $titulo,
            'subtitulo' => $subtitulo,
            'contenido' => $contenido,
            'destino' => $destino,
            'subject' => $subject,
            'headers' => array(
                'From: El Quinto Poder <' . $from . '>',
                'Subject: ' . $subject,
                'X-Mailer: PHP/' . phpversion()
            )
        ));
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

        mobile_mailMe(array(
            'encabezado' => $titulo,
            'subtitulo' => $subtitulo,
            'contenido' => $contenido,
            'destino' => $destino,
            'subject' => $subject,
            'headers' => array(
                'From: El Quinto Poder <' . $from . '>',
                'Subject: ' . $subject,
                'X-Mailer: PHP/' . phpversion()
            )
        ));
    }
}

function mobile_inputEmailMessage(){
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

function mobile_mailMe($data) {
    $htmlString = '<!DOCTYPE html>';
    $htmlString .= '<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head>';
    $htmlString .= '<body style="margin: 0px; padding: 0px; font-family: sans-serif; color: #333333; font-size: 12px;" >';
    $htmlString .= '<table style=" border-collapse: collapse; width: 100%; font-family: sans-serif; " >';
    $htmlString .= '<tr>';
    $htmlString .= '<td style="padding-top: 40px; padding-bottom: 20px; background: #e6e6e6; text-align: center; font-size: 12px; color: #7f7f7f; border-bottom: 5px solid #183248; "></td></tr></table>';
    $htmlString .= '<table align="center"  cellpadding="0" cellpadding="0"  style=" width: 700px; margin: 0 auto; margin-top: 30px; margin-bottom: 30px; background: #ffffff; border-collapse: collapse; font-family: sans-serif; " >';
    $htmlString .= '<tr><td>';
    $htmlString .= '<table style=" border-collapse: collapse;  width: 100%; font-family: sans-serif;" >';
    $htmlString .= '<tr>';
    $htmlString .= '<td style=" width: 50%; padding-bottom: 20px; border-bottom: 1px dotted #999999; font-family: sans-serif;">';
    $htmlString .= '<a href="http://elquintopoder.cl" title="El Quinto Poder" style=" text-decoration: none; border: 0px;" >';
    $htmlString .= '<img style="outline: none; border: 0px;" src="http://mailings.ida.cl/eqp/logo-header.png" alt="El Quinto Poder" >';
    $htmlString .= '</a></td>';
    $htmlString .= '<td style="text-align: right; width: 50%; padding-bottom: 20px; border-bottom: 1px dotted #999999;" >';
    $htmlString .= '<span style="font-family: sans-serif; display: inline-block; width: 168px; font-weight: bold; text-align: right; padding-bottom: 10px; border-bottom: 1px dotted #999999;" >S&iacute;guenos en</span>';
    $htmlString .= '<div style="padding-top: 10px;" >';
    $htmlString .= '<a style="text-decoration: none;" href="https://twitter.com/elquintopoder" title="Twitter" ><img style="outline: none; border: 0px;"  src="http://mailings.ida.cl/eqp/ico-twitter.png" alt="El Quinto Poder" ></a>';
    $htmlString .= '<a style="text-decoration: none;" href="https://www.facebook.com/elquintopoder" title="Facebook" ><img style="outline: none; border: 0px;"  src="http://mailings.ida.cl/eqp/ico-facebook.png" alt="El Quinto Poder" ></a>';
    $htmlString .= '<a style="text-decoration: none;" href="http://elquintopoder.tumblr.com/" title="Tumblr" ><img style="outline: none; border: 0px;"  src="http://mailings.ida.cl/eqp/ico-tumblr.png" alt="El Quinto Poder" ></a>';
    $htmlString .= '</div></td></tr>';
    $htmlString .= '<tr>';
    $htmlString .= '<td style=" width: 99%; padding-top: 20px;">';
    $htmlString .= '<h1 style="color: #183248; font-size: 28px; font-weight: bold; text-transform: uppercase; font-family: sans-serif; " >';
    $htmlString .= $data['encabezado']; // titulo
    $htmlString .= '</h1>';
    $htmlString .= '</td>';
    $htmlString .= '<td style=" width: 1%; text-align: right;  padding-top: 20px;"></td>';
    $htmlString .= '</tr></table>';
    $htmlString .= '<table style=" border-collapse: collapse; margin-bottom: 20px;" >';
    $htmlString .= '<tr>';
    $htmlString .= '<td style="font-size: 14px; line-height: 150%; color: #333333; font-family: sans-serif;" >';
    $htmlString .= '<h2 style="font-size: 18px; font-weight: bold; color: #333333; margin-bottom: 20px; font-family: sans-serif;" >';
    $htmlString .= $data['subtitulo']; // subtitulo
    $htmlString .= '</h2>';
    $htmlString .= $data['contenido']; //contenido
    $htmlString .= '</td></tr></table></td></tr></table>';
    $htmlString .= '<table style="border-collapse: collapse; width: 100%;" ><tr>';
    $htmlString .= '<td style="padding-top: 20px; padding-bottom: 40px; background: #e6e6e6; border-top: 5px solid #183248; ">';
    $htmlString .= '<table style=" border-collapse: collapse; width: 700px; margin: 0 auto;" ><tr><td>';
    $htmlString .= '<a href="http://elquintopoder.cl" title="El Quinto Poder" style="text-decoration: none; border: 0px; " >';
    $htmlString .= '<img style="outline: none; border: 0px;"  src="http://mailings.ida.cl/eqp/logo-footer.png" alt="El Quinto Poder" >';
    $htmlString .= '</a></td>';
    $htmlString .= '<td style="font-size: 12px; color: #7f7f7f; padding-left: 20px; font-family: sans-serif; " >';
    $htmlString .= '<strong>Fundación Democracia y Desarrollo</strong><br>';
    $htmlString .= '<strong>Licencia</strong> CC:BY<br>';
    $htmlString .= '<strong>Dirección postal:</strong> Roberto del Río,Providencia, Santiago 7510359 | Chile';
    $htmlString .= '</td>';
    $htmlString .= '<td style="text-align: right; padding-left: 20px; font-family: sans-serif;" >';
    $htmlString .= '<span style="display: inline-block; width: 168px; font-weight: bold; text-align: right; padding-bottom: 10px; border-bottom: 1px dotted #999999;" >S&iacute;guenos en</span>';
    $htmlString .= '<div style="padding-top: 10px;" >';
    $htmlString .= '<a style="text-decoration: none;" href="https://twitter.com/elquintopoder" title="Twitter" ><img style="outline: none; border: 0px;"  src="http://mailings.ida.cl/eqp/ico-twitter.png" alt="El Quinto Poder" ></a>';
    $htmlString .= '<a style="text-decoration: none;" href="https://www.facebook.com/elquintopoder" title="Facebook" ><img style="outline: none; border: 0px;"  src="http://mailings.ida.cl/eqp/ico-facebook.png" alt="El Quinto Poder" ></a>';
    $htmlString .= '<a style="text-decoration: none;" href="http://elquintopoder.tumblr.com/" title="Tumblr" ><img style="outline: none; border: 0px;"  src="http://mailings.ida.cl/eqp/ico-tumblr.png" alt="El Quinto Poder" ></a>';
    $htmlString .= '</div></td></tr></table></td></tr></table></body></html>';

    $destino = $data['destino'];
    $subject = $data['subject'];
    $headers = $data['headers'];

    add_filter('wp_mail_content_type', 'set_html_content_type');
    wp_mail($destino, $subject, $htmlString, $headers);
    remove_filter('wp_mail_content_type', 'set_html_content_type');
}

function set_html_content_type() {
    return 'text/html';
}
// ACTIONS & FILTERS 
// manejador de AJAX para tema mobile
function mobile_ajax() {
    global $post, $wpdb, $current_user;

    if ($_POST['func'] === 'load_more_slider') {
        $offset_start = intval($_POST['offset_start']);
        $offset_end = intval($_POST['offset_end']);
        $edge = $_POST['edgeType'];
        $orderType = $_POST['orderType'];
        $pType = $_POST['pType'];

        if (($edge == 'first' && $offset_start == 0) || intval($_POST['noMorePost'])) {
            $response = array('status' => 'noBack');
            die(json_encode($response));
        }

        $realOffset = $edge == 'last' ? $offset_end : ($offset_start - 12);
        if ($realOffset < 0) {
            $realOffset = 0;
        }

        if ($orderType === 'recientes') {
            $estructura = array('class' => 'column4 recentNew', 'bajada' => 300, 'return_type' => 'json');
            $params = array('items' => 6, 'offset' => $realOffset);

            $primerBloque = bloques($params, $estructura);

            $params['offset'] = $realOffset + 6;
            $segundoBloque = bloques($params, $estructura);
        } else {
            $primerBloque = mobile_get_entradas_activas(array('posts_per_page' => 6, 'offset' => $realOffset, 'return_type' => 'array'));
            $segundoBloque = mobile_get_entradas_activas(array('posts_per_page' => 6, 'offset' => $realOffset + 6, 'return_type' => 'array'));
        }

        $out = "";

        if (!empty($primerBloque) && $primerBloque['post_count'] > 0) {
            $out .= '<li>';
            $out .= $primerBloque['html'];
            $out .= '</li>';
        }
        if (!empty($segundoBloque) && $segundoBloque['post_count'] > 0) {
            $out .= '<li>';
            $out .= $segundoBloque['html'];
            $out .= '</li>';
        }

        $status = 'ok';
        if (empty($primerBloque) || empty($segundoBloque) || $primerBloque['posts_left'] < 1 || $segundoBloque['posts_left'] < 1) {
            $status = 'noPostLeft';
        }
        $response = array(
            'edge' => $edge,
            'order_type' => $orderType,
            'html' => $out,
            'new_offset_start' => $realOffset,
            'new_offset_end' => ($realOffset + 12),
            'status' => $status
        );

        die(json_encode($response));
    }
    elseif ($_POST['func'] === 'marcar_no_leido') {
        if (!is_user_logged_in()) {
            die(json_encode(array('status' => 'nologued')));
        }

        $post_to_save = intval($_POST['post_id']);
        $user_id = intval($_POST['user_id']);
        $leidos = (array) get_user_meta($user_id, 'lectura_leidos', true);
        $noLeidos = (array) get_user_meta($user_id, 'lectura_no_leidos', true);

        $status = 'repeated';

        if (in_array($post_to_save, $leidos)) {
            if (empty($leidos[0])) {
                unset($leidos[0]);
            }
            unset($leidos[array_search($post_to_save, $leidos)]);
            array_filter($leidos);
            sort($leidos);
            update_user_meta($user_id, 'lectura_leidos', $leidos);
        }

        if (!in_array($post_to_save, $noLeidos)) {
            if (empty($noLeidos[0])) {
                unset($noLeidos[0]);
            }
            $status = 'ok';
            $noLeidos[] = $post_to_save;
            array_filter($noLeidos);
            sort($noLeidos);
            update_user_meta($user_id, 'lectura_no_leidos', $noLeidos);
        }

        die(json_encode(array(
            'status' => $status,
            'post_name' => get_the_title($post_to_save),
            'no_leidos' => get_user_meta($user_id, 'lectura_no_leidos', true),
            'leídos' => get_user_meta($user_id, 'lectura_leidos', true)
        )));
    }
    elseif ($_POST['func'] === 'marcar_leido') {
        if (!is_user_logged_in()) {
            die(json_encode(array('status' => 'nologued')));
        }

        $post_to_save = intval($_POST['post_id']);
        $user_id = intval($_POST['user_id']);
        $leidos = (array) get_user_meta($user_id, 'lectura_leidos', true);
        $noLeidos = (array) get_user_meta($user_id, 'lectura_no_leidos', true);
        $status = 'repeated';

        if (in_array($post_to_save, $noLeidos)) {
            if (empty($noLeidos[0])) {
                unset($noLeidos[0]);
            }
            unset($noLeidos[array_search($post_to_save, $noLeidos)]);
            array_filter($noLeidos);
            sort($noLeidos);
            update_user_meta($user_id, 'lectura_no_leidos', $noLeidos);
        }

        if (!in_array($post_to_save, $leidos)) {
            if (empty($leidos[0])) {
                unset($leidos[0]);
            }
            $status = 'ok';
            $leidos[] = $post_to_save;
            array_filter($leidos);
            sort($leidos);
            update_user_meta($user_id, 'lectura_leidos', $leidos);
        }

        die(json_encode(array(
            'status' => $status,
            'post_name' => get_the_title($post_to_save),
            'no_leidos' => get_user_meta($user_id, 'lectura_no_leidos', true),
            'leídos' => get_user_meta($user_id, 'lectura_leidos', true)
        )));
    }
    elseif ($_POST['func'] === 'verFotoCompleta') {
        $imgID = intval($_POST['imageID']);

        $response = '<h2 class="lightbox-title" >' . $_POST['imageTitle'] . '</h2>';
        $response .= wp_get_attachment_image($imgID, 'full', false, array('class' => 'imagen-completa', 'alt' => $_POST['imageTitle']));

        die(json_encode(array(
            'status' => 'ok',
            'html' => $response
        )));
    } 
    elseif ($_POST['func'] === 'sendContactForm') {
        mobile_mailMe(array(
            'encabezado' => 'Contacto El Quinto Poder',
            'subtitulo' => $_POST['contacto_asunto'],
            'contenido' => apply_filters('the_content', $_POST['contacto_mensaje']),
            'destino' => get_bloginfo('admin_email'),
            'subject' => 'Contacto El Quinto Poder',
            'headers' => array(
                'From: ' . $_POST['contacto_nombre'] . ' <' . $_POST['contacto_email'] . '>',
                'Subject: Contacto El Quinto Poder',
                'X-Mailer: PHP/' . phpversion()
            )
        ));

        $htmlResponse = '<div class="mensajes success">';
        $htmlResponse .= '<h3 class="mensajes-title" >¡Muchas Gracias!</h3>';
        $htmlResponse .= '<p class="mensajes-text" >Tu mensaje se ha enviado con éxito, te contactaremos a la brevedad</p>';
        $htmlResponse .= '</div>';

        die($htmlResponse);
    }
    elseif ($_POST['func'] === 'contactarUsuario') {
        $usid = intval($_POST['user_id']);
        $targetUser = get_userdata($usid);

        $out .= '<form class="ajax-form contacto" id="ajax-contact-form" action="" method="post" >';
        $out .= '<h2 class="lightbox-title" >Contactar a ' . nombre_y_apellido($usid) . '</h2>';

        $out .= '<label for="ajax_contact_name" >Nombre</label>';
        $out .= '<input type="text" name="ajax_contact_name" id="ajax_contact_name" placeholder="Escribe tu nombre" required>';

        $out .= '<label for="ajax_contact_email" >Email</label>';
        $out .= '<input type="email" autocapitalize="off" autocorrect="off" name="ajax_contact_email" id="ajax_contact_email" placeholder="Escribe tu email" required>';

        $out .= '<label for="ajax_contact_message" >Mensaje</label>';
        $out .= '<textarea name="ajax_contact_message" id="ajax_contact_message" placeholder="Escribe tu mensaje..." required></textarea>';

        $out .= '<input type="hidden" name="target_user_email" value="' . $targetUser->user_email . '">';
        $out .= '<input class="sendForm aRight disabled" disabled type="submit" value="Enviar">';

        $out .= '</form>';

        die($out);
    }
    elseif ($_POST['func'] === 'enviarContactoaUsuario') {
        mobile_mailMe(array(
            'encabezado' => 'Contacto El Quinto Poder',
            'subtitulo' => $_POST['ajax_contact_name'] . ' te ha enviado un mensaje',
            'contenido' => apply_filters('the_content', $_POST['ajax_contact_message']),
            'destino' => $_POST['target_user_email'],
            'subject' => 'Contacto El Quinto Poder',
            'headers' => array(
                'From: ' . $_POST['ajax_contact_name'] . ' <' . $_POST['ajax_contact_email'] . '>',
                'Subject: Contacto El Quinto Poder',
                'X-Mailer: PHP/' . phpversion()
            )
        ));

        $htmlResponse = '<div class="mensajes success in-lightbox">';
        $htmlResponse .= '<h3 class="mensajes-title" >¡Muchas Gracias!</h3>';
        $htmlResponse .= '<p class="mensajes-text" >Tu mensaje se ha enviado con éxito al email del usuario. El email enviado contiene tus datos para que el usuario te conteste directamente.</p>';
        $htmlResponse .= '</div>';

        die($htmlResponse);
    }
    elseif ($_POST['func'] === 'loadUserActivityType') {
        $user_id = intval($_POST['user_id']);
        $contentType = $_POST['content_type'];
        $status = 'ok';
        $out = "";

        if ($contentType === 'publicado') {
            $out = mobile_list_contents(array(
                'autor' => $user_id,
                'items' => 10
            ));
            $typeTitle = 'Contenidos Publicados';
        } else {
            if ($contentType === 'porLeer') {
                $typeTitle = 'Contenidos por leer';
                $metaName = 'lectura_no_leidos';
            } else {
                $typeTitle = 'Contenidos leídos';
                $metaName = 'lectura_leidos';
            }

            $metaArray = (array) get_user_meta($user_id, $metaName, true);
            if (!empty($metaArray)) {
                foreach ($metaArray as $item) {
                    $currentPost = get_post($item);

                    switch ($currentPost->post_type) {
                        case 'post':
                            $boxClass = 'publiEntry';
                            break;
                        case 'post_fotos':
                            $boxClass = 'publiFoto';
                            break;
                        case 'post_videos':
                            $boxClass = 'publiVideo';
                            break;
                        case 'post_acciones':
                            $boxClass = 'publiAccion';
                            break;
                        default :
                            $boxClass = 'publiEntry';
                            break;
                    }

                    $extracto = get_field('texto_destacado', $currentPost->ID) ? cortar(get_field('texto_destacado', $currentPost->ID), 300) : cortar($currentPost->post_content, 300);

                    $out .= '<li class="' . $boxClass . '">';
                    $out .= '<h3><a href="' . get_permalink($currentPost->ID) . '" title="Ir a ' . $currentPost->post_title . '" rel="contents">' . $currentPost->post_title . '</a></h3>';
                    $out .= '<p>' . $extracto . '</p>';
                    $out .= '</li>';
                }
            }

            if (!trim($out)) {
                $out = '<li>No hay post ' . $contentType . '</li>';
                $status = 'noPosts';
            }
        }

        die(json_encode(array(
            'status' => $status,
            'typeTitle' => $typeTitle,
            'html' => $out
        )));
    }
    elseif ($_POST['func'] === 'loadUserPublished') {
        die(json_encode(array(
            'html' => mobile_list_contents(array(
                'autor' => intval($_POST['user_id']),
                'items' => 10,
                'offset' => intval($_POST['offset'])
            )),
            'newOffset' => intval($_POST['offset']) + 10
        )));
    }
    elseif ($_POST['func'] === 'loadMoreEntradas') {
        $offset = intval($_POST['offset']);


        $out = "";

        if ($_POST['orderType'] === 'recientes') {
            $estructura = array('class' => 'column4 recentNew unidad-discreta', 'bajada' => 300);
            $params = array('items' => 6, 'offset' => $offset);
            $primerBloque = bloques($params, $estructura);

            $offset += 6;
            $params['offset'] = $offset;
            $segundoBloque = bloques($params, $estructura);

            $offset += 6;
            $params['offset'] = $offset;
            $tercerBloque = bloques($params, $estructura);
        } else {
            $primerBloque = mobile_get_entradas_activas(array('posts_per_page' => 6, 'offset' => $offset));

            $offset += 6;
            $segundoBloque = mobile_get_entradas_activas(array('posts_per_page' => 6, 'offset' => $offset));

            $offset += 6;
            $tercerBloque = mobile_get_entradas_activas(array('posts_per_page' => 6, 'offset' => $offset));
        }

        if ($primerBloque) {
            $out .= '<li>' . $primerBloque . '</li>';
        }
        if ($segundoBloque) {
            $out .= '<li>' . $segundoBloque . '</li>';
        }
        if ($tercerBloque) {
            $out .= '<li>' . $tercerBloque . '</li>';
        }

        die(json_encode(array(
            'status' => 'ok',
            'html' => $out,
            'newOffset' => $offset
        )));
    }
    elseif ($_POST['func'] === 'loadMoreSearchResults') {
        $offset = intval($_POST['offset']);
        $searchTerm = $_POST['searchTerm'];

        die(mobile_list_contents(array(
            'items' => 10,
            'searchString' => $searchTerm,
            'offset' => $offset,
        )));
    } 
    elseif ($_POST['func'] === 'regularLoginAction') {
        $out = '<form id="regular-login-form" class="ajax-form contacto" action="" method="">';
        $out .= '<h2 class="lightbox-title">Ingresar</h2>';

        $out .= '<label for="usrName">Nombre de usuario</label>';
        $out .= '<input type="text" autocapitalize="off" autocorrect="off" name="usrName" id="usrName" placeholder="Ingresa tu nombre de usuario" required>';

        $out .= '<label for="usrPass">Contraseña</label>';
        $out .= '<input type="password" autocapitalize="off" autocorrect="off" name="usrPass" id="usrPass" placeholder="*******" required>';

        $out .= '<input class="sendForm aRight disabled" disabled type="submit" value="Ingresar">';
        $out .= '</form>';

        die($out);
    } 
    elseif ($_POST['func'] === 'loguearUser') {
        $creds = array(
            'user_login' => $_POST['usrName'],
            'user_password' => $_POST['usrPass'],
            'remember' => true
        );
        $user = wp_signon($creds, false);
        if (is_wp_error($user)) {
            die(json_encode(array(
                'status' => 'error',
                'html' => '<span class="form-helper error-message">Nombre de usuario o contraseña incorrectos</span>'
            )));
        }

        die(json_encode(array('status' => 'ok')));
    } 
    elseif ($_POST['func'] === 'firma_y_participa') {
        if (is_user_logged_in()) {
            get_currentuserinfo(); // setea el currenUserData en la variable global $current_user
            $htmlResponse = get_firmaAccionForm($current_user, intval($_POST['pid']));
        } else {
            $htmlResponse = get_firmaAccionForm(false, intval($_POST['pid']));
        }
        die($htmlResponse);
    } 
    elseif ($_POST['func'] === 'firmarAccion') {
        $status = 'ok';

        $pid = intval($_POST['accionPid']);
        $email = ( isset($_POST['usrEmail']) ? $_POST['usrEmail'] : $_POST['voter_email'] );
        $nombre = ( isset($_POST['usrName']) ? $_POST['usrName'] : $_POST['voter_name'] );
        $userId = ( isset($_POST['usrID']) ? intval($_POST['usrID']) : 0 );

        if ( (!$email || !$nombre) && !$_POST['usrLogin'] ) {
            die(json_encode(array(
                'status' => 'error_nouser'
            )));
        }

        $voteType = 'no_login_vote';

        if (isset($_POST['usrPass'])) {
            $voteType = 'login_vote';

            $creds = array(
                'user_login' => $_POST['usrLogin'],
                'user_password' => $_POST['usrPass'],
                'remember' => true
            );
            $user = wp_signon($creds, false);

            if (is_wp_error($user)) {
                die(json_encode(array(
                    'status' => 'error_contrasena'
                )));
            }

            $email = $user->user_email;
            $nombre = nombre_y_apellido($user->ID);
            $userId = $user->ID;
        }

        $data = array(
            'postid' => $pid,
            'email' => $email,
            'nombre' => $nombre,
            'userid' => $userId,
            'aditionalFields' => ( isset($_POST['accion_fields']) ? $_POST['accion_fields'] : array() )
        );
        $check = mobile_accionesInsert($data);


        if ($check) {
            $out = '<h2 class="lightbox-title">¡Gracias por Firmar!</h2>';
            $out .= '<p class="nice-paragraph">Ayuda a difundir esta Acción compartiéndola en tus redes sociales</p>';
            $out .= '<ul class="socialBottom">';
            $out .= mobile_getSocialShares(array('pid' => intval($_POST['accionPid']), 'wrapper' => 'li'));
            $out .= '</ul>';
        } else {
            $status = 'error_repetido';
            $out = '<h2 class="lightbox-title">¡Oops!</h2>';
            $out .= '<p class="nice-paragraph">No puedes firmar más de una vez</p>';
        }

        die(json_encode(array(
            'status' => $status,
            'html' => $out,
            'votos' => mobile_get_action_votes($pid),
            'check' => $check,
            'vote_type' => $voteType
        )));
    } 
    elseif ($_POST['func'] === 'media_verMasGrande') {
        // si el post no cumple con los requisitos muere antes
        if (($_POST['pType'] === 'post_videos' && !get_field('video_link', intval($_POST['pid']))) || ($_POST['pType'] === 'post_fotos' && !has_post_thumbnail(intval($_POST['pid'])))) {
            die(array('status' => 'notOk'));
        }

        $daPost = get_post(intval($_POST['pid']));

        if ($_POST['pType'] === 'post_videos') {
            $mediaItem = get_post_meta(intval($_POST['pid']), 'ajaxEmbed', true) ? get_post_meta(intval($_POST['pid']), 'ajaxEmbed', true) : mobile_get_the_embed(get_field('video_link', intval($_POST['pid'])));
        } else {
            $mediaItem = get_the_post_thumbnail(intval($_POST['pid']), 'full', array('class' => 'lightBox-preview'));
        }


        $extracto = get_field('texto_destacado', $daPost->ID) ? cortar(get_field('texto_destacado', $daPost->ID), 300) : cortar($daPost->post_content, 300);

        $out = '<h2 class="lightbox-title" >' . $daPost->post_title . '</h2>';
        $out .= $mediaItem;
        $out .= '<p class="media-preview-excerpt">' . $extracto . '</p>';

        die(json_encode(array(
            'status' => 'ok',
            'html' => $out
        )));
    }
}

add_action('wp_ajax_mobile_ajax', 'mobile_ajax');
add_action('wp_ajax_nopriv_mobile_ajax', 'mobile_ajax');

add_filter('body_class', 'custom_body_classes');

function custom_body_classes($classes) {
    global $post;

    if ($post->post_name === 'entradas') {
        $classes[] = 'portada-entradas';
    } elseif ($post->post_name == 'fotos') {
        $classes[] = 'portada-fotos';
    } elseif ($post->post_name == 'videos') {
        $classes[] = 'portada-videos';
    } elseif ($post->post_name == 'acciones-home') {
        $classes[] = 'portada-acciones';
    } elseif (is_singular('especiales')) {
        $classes[] = 'single-especiales';
    }

    return $classes;
}

add_filter('nav_menu_css_class', 'custom_menu_clases', 10, 2);

function custom_menu_clases($classes, $item) {
    global $post;
    if (is_front_page()) {
        return $classes;
    }
    if ($item->title === 'Entradas' && $post->post_type === 'post') {
        $classes[] = 'current-menu-item';
    } 
    elseif ($item->title === 'Fotos' && $post->post_type === 'post_fotos') {
        $classes[] = 'current-menu-item';
    } 
    elseif ($item->title === 'Videos' && $post->post_type === 'post_videos') {
        $classes[] = 'current-menu-item';
    } 
    elseif ($item->title === 'Acciones' && $post->post_type === 'post_acciones') {
        $classes[] = 'current-menu-item';
    }

    return $classes;
}

function include_eqp_scripts() {
    if (!is_admin()) {
        wp_deregister_script('modernizr');
        wp_register_script('modernizr', get_bloginfo("stylesheet_directory") . '/js/modernizr.custom.js');
        wp_deregister_script('jquery');
        wp_register_script('jquery', 'http://ajax.aspnetcdn.com/ajax/jquery/jquery-1.9.1.min.js');
        wp_deregister_script('jQueryRaF');
        wp_register_script('jQueryRaF', get_bloginfo("stylesheet_directory") . '/js/jQueryRaF.js');
        wp_deregister_script('Swipejs');
        wp_register_script('Swipejs', get_bloginfo("stylesheet_directory") . '/js/swipe.js');
        wp_deregister_script('mainScript');
        wp_register_script('mainScript', get_bloginfo("stylesheet_directory") . '/js/script.js');

        if (is_singular() && comments_open() && ( get_option('thread_comments') == 1 )) {
            wp_enqueue_script('comment-reply');
        }

        wp_enqueue_script('modernizr');
        wp_enqueue_script('jquery');
        wp_enqueue_script('jQueryRaF');
        wp_enqueue_script('Swipejs');
        wp_enqueue_script('mainScript');
    }
}

add_action('wp_enqueue_scripts', 'include_eqp_scripts');

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
?>