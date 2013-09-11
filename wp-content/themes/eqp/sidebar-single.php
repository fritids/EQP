    <?php
    
        $cat = false;
        $tax = false;
        $taxTerms = false;
        $tQuery = false;
        
        $categorias = get_the_category( $post->ID );
        foreach((array)$categorias as $c) {
            $cat = $c->slug;
            break;
        }
        
        if($post->post_type == 'post'){ 
            $actionTit = 'Publica una Entrada'; 
            $titulo = 'Entradas relacionadas';
            $formato = 'listaChica';
            $typeClass = 'entry-type';
            $widgets = false;
            $typeIcon = 'entry';
            $catTitle = 'entradas';
            $optName = 'Entradas';
            $gacSufix = 'Entrada';
        }
        elseif($post->post_type == 'post_fotos'){ 
            $actionTit = 'Publica una Foto'; 
            $titulo = 'Fotos relacionadas';
            $formato = 'lista';
            $typeClass = 'pict-type';
            $vertical = 'vertical';
            $widgets = true;
            $typeIcon = 'photos';
            $catTitle = 'fotos';
            $optName = 'Fotos';
            $gacSufix = 'Foto';
        }
        elseif($post->post_type == 'post_videos'){ 
            $actionTit = 'Publica un Video'; 
            $titulo = 'Videos relacionados';
            $formato = 'lista';
            $typeClass = 'video-type';
            $vertical = 'vertical';
            $widgets = true;
            $typeIcon = 'videos';
            $catTitle = 'videos';
            $optName = 'Videos';
            $gacSufix = 'Video';
        }
        elseif($post->post_type == 'post_acciones'){ 
            $actionTit = 'Publica una Acción'; 
            $titulo = 'Acciones relacionadas';
            $formato = 'listaAcciones';
            $typeClass = 'video-type';
            $widgets = true;
            $typeIcon = 'actions';
            $catTitle = false;
            $optName = 'Acciones';
            $gacSufix = 'Accion';
        }
        elseif($post->post_type == 'propuestas'){
            $actionTit = 'Publica una Propuesta'; 
            $titulo = 'Propuestas relacionadas';
            $formato = 'listaPropuestas';
            $typeClass = 'propuesta-type';
            $widgets = true;
            $typeIcon = 'propuestas';
            $catTitle = 'propuestas';
            $cat = false;
            $tax = 'temas';
            $taxTerms = wp_get_post_terms($post->ID, 'temas', array("fields" => "slugs"));
            $tQuery = array(
                'taxonomy' => 'comunas',
                'field' => 'slug',
                'terms' => wp_get_post_terms($post->ID, 'comunas', array("fields" => "slugs"))
            );
        }
        
        if (is_user_logged_in() ) { $current_user = wp_get_current_user(); }
        
        $args = array(
            'post_type' => $post->post_type,
            'offset' => 0,
            'formato' => $formato,
            'typeClass' => $typeClass,
            'postCount' => 8,
            'categoria' => $cat,
            'orden' => 'rand',
            'excluir' => array( $post->ID ),
            'taxonomy' => $tax,
            'termTax' => $taxTerms,
            'espTax' => $tQuery,
            'analytics' => array(
                'class="ganalytics"',
                'data-ga-category="SidebarLink"',
                'data-ga_action="LinksSingle'. $gacSufix .'"',
                'data-ga_opt_label="LinksTemaRelated"'
            )
        );

        if( $cat == false ) { unset($args['categoria']); }
        if( $tax == false ) { unset($args['taxonomy']); }
        if( $taxTerms == false ) { unset($args['termTax']); }
        
        $related = getPostsBlocks($args);
    ?>

    <aside class="four">
        <div class="call-to-action">
            <a href="#" class="<?php echo $typeIcon; ?> evt" data-func="showPublishForm" data-ga_opt_label="BtnPubli_Entradas_<?php echo $optName; ?>" data-posttype="<?php echo $post->post_type; ?>" data-autor="<?php if($current_user) { echo $current_user->ID; } ?>" title="<?php echo $actionTit; ?>" rel="nofollow" ><?php echo $actionTit; ?></a>
        </div>
        <ul id="corp-rules-holder">
            <li><a href="/que-es-el-quinto-poder" title="¿Qué es el Quinto Poder?" rel="section">¿Qué es el Quinto Poder?</a></li>
            <li><a href="/reglas-de-la-comunidad" title="Reglas de la Comunidad" rel="section">Reglas de la Comunidad</a></li>
        </ul>
        
        <!------------------------------------------------------ Banner adminsitrable -->
        
        <?php if(get_field('banner_desarrollo-regional', 'options')&& in_category('desarrollo-regional')){
            echo 
            '<a class="banner-a ganalytics" data-ga-category="CampanasInternas" data-ga_action="EntradaSingle" data-ga_opt_label="BannerSidebar_dr" rel="external" title="ir a link externo" href="'. get_field("url_banner_articulo","options") .'">'. wp_get_attachment_image(get_field('banner_desarrollo-regional', 'options'), 'full').'</a>';
         } ?>
        
        <!------------------------------------------------------ Cajas para respuestas de Candidatos -->
                        
        <?php
            if( $post->post_type == 'propuestas' ) {
                $comunasTerms = wp_get_post_terms( $post->ID, 'comunas', array('fields' => 'slugs') );
                $respuestas = getRespuestas( $post->ID, $comunasTerms[0] );
                if( $respuestas != '' ) {
                    echo '<h2 class="label inside">Respuestas de los Candidatos</h2>';
                    echo '<div class="respuestas-holder" >';
                    echo $respuestas;
                    echo '</div>';
                }
            }
        ?>

        <!------------------------------------------------------ END Cajas para respuestas de Candidatos -->
            
        <?php if($related) : ?>
        <h2 class="label inside"><?php echo $titulo; ?></h2>
        <ul class="article-list <?php echo $vertical; ?>">
            <?php echo $related; ?>
        </ul>
        <?php endif; ?>
        
        <?php if($widgets) : ?>
            <h2 class="label"><?php if($catTitle) { echo $catTitle.' por '; } ?>Temas</h2>
                <ol id="temaRank" class="themes-ranking">
                    <?php
                        if( $post->post_type == 'propuestas' ) { 
                            $comunaSlug = wp_get_post_terms($post->ID, 'comunas', array("fields" => "slugs"));
                            temasCountMunicipales($comunaSlug[0]);
                        }
                        else { temasCount($catTitle); }
                    ?>
                </ol>
        <?php endif; ?>
    </aside>


