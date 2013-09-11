    <?php
        if (is_user_logged_in() ) { $current_user = wp_get_current_user(); }
        $parent = get_post($post->post_parent);
        if( $parent->post_name == 'municipales-2012' ){ $temasTitle = 'Temas en '. $post->post_title; }
        elseif( is_tax('comunas') || is_tax('temas') ){ $temasTitle = 'Temas en '. single_term_title('', false); }
    ?>
    <aside class="four">
        <div class="call-to-action municipales">
            <a href="#" class="propuestas evt gac" data-goal="btn-pub-single-propuestas" data-func="showPublishForm" data-posttype="propuestas" data-autor="<?php if($current_user) { echo $current_user->ID; } ?>" title="Publica una Propuesta" rel="nofollow" >Publica una Propuesta</a>
        </div>
        <ul id="corp-rules-holder">
            <li><a href="/que-es-el-quinto-poder" title="¿Qué es el Quinto Poder?" rel="section">¿Qué es el Quinto Poder?</a></li>
            <li><a href="/reglas-de-la-comunidad" title="Reglas de la Comunidad" rel="section">Reglas de la Comunidad</a></li>
        </ul>
        <h2 class="label"><?php echo $temasTitle; ?></h2>
        <ol id="temaRank" class="themes-ranking muniTemas">
            <?php
            if( $parent->post_name == 'municipales-2012' ){ temasCountMunicipales( $post->post_name ); }
            elseif( is_tax('comunas') ){
                $comunaObj = get_term_by('name', single_term_title('', false), 'comunas');
                temasCountMunicipales($comunaObj->slug);
            }
            elseif( is_tax('temas') ){
                if( isset( $_GET['comuna'] ) ) { $comunaSlug = $_GET['comuna']; }
                else { $comunaSlug = false;  }
                temasCountMunicipales($comunaSlug);
            }
            ?>
        </ol>
        <a class="evt see-more" data-func="showMoreTemas" href="#" title="Ver Más" rel="nofollow">Ver Más</a>
    </aside>
