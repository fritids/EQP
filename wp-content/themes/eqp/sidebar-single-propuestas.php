<?php if (is_user_logged_in() ) { $current_user = wp_get_current_user(); } ?>
    <aside class="four">
        <div class="call-to-action municipales">
            <a href="#" class="propuestas evt gac" data-goal="btn-pub-single-propuestas" data-func="showPublishForm" data-posttype="propuestas" data-autor="<?php if($current_user) { echo $current_user->ID; } ?>" title="Publica una Propuesta" rel="nofollow" >Publica una Propuesta</a>
        </div>
        <ul id="corp-rules-holder">
            <li><a href="/que-es-el-quinto-poder" title="¿Qué es el Quinto Poder?" rel="section">¿Qué es el Quinto Poder?</a></li>
            <li><a href="/reglas-de-la-comunidad" title="Reglas de la Comunidad" rel="section">Reglas de la Comunidad</a></li>
        </ul>
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
            echo '<h2 class="label">Propuestas por Temas</h2>';
            echo '<ol id="temaRank" class="themes-ranking muniTemas">';
            $comunaSlug = wp_get_post_terms($post->ID, 'comunas', array("fields" => "slugs"));
            temasCountMunicipales($comunaSlug[0]);
            echo '</ol>';
            echo '<a class="evt see-more" data-func="showMoreTemas" href="#" title="Ver Más" rel="nofollow">Ver Más</a>';
            
            $args = array(
                'post_type' => 'propuestas',
                'typeClass' => 'propuesta-type',
                'postCount' => 8,
                'orden' => 'rand',
                'excluir' => array( $post->ID ),
                'taxonomy' => 'temas',
                'termTax' => wp_get_post_terms($post->ID, 'temas', array("fields" => "slugs")),
                'espTax' => array(
                    'taxonomy' => 'comunas',
                    'field' => 'slug',
                    'terms' => wp_get_post_terms($post->ID, 'comunas', array("fields" => "slugs"))
                ),
                'formato' => 'listaPropuestas'
            );
            $related = getPostsBlocks($args);
        
            if($related) {
                echo '<h2 class="label inside">Propuestas relacionadas</h2>';
                echo '<ul class="article-list">';
                echo $related;
                echo '</ul>';
            }
        ?>
    </aside>


