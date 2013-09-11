    <aside class="four">
            <h2 class="label">Categorías</h2>
                <ol id="temaRank" class="themes-ranking">
                    <?php temasCount(false, true); ?>
                </ol>
            <?php
                if( is_page() || is_tax() ) {
                    echo getLastCommentsByPostType('blog');
                }
                else {
                    $terms = wp_get_post_terms( $post->ID, 'categorias_blog', array("fields" => "slugs"));
                    $args = array(
                        'post_type' => $post->post_type,
                        'offset' => 0,
                        'formato' => 'listaChica',
                        'postCount' => 6,
                        'orden' => 'date',
                        'excluir' => array($post->ID),
                        'taxonomy' => 'categorias_blog',
                        'termTax' => $terms
                    );
                    $related = getPostsBlocks($args);
                    if( $related ){
                        echo '<h2 class="label sidebarTitle">Entradas Relacionadas</h2>';
                        echo '<ul class="article-list">';
                        echo $related;
                        echo '</ul>';
                    }
                }
            ?>
    </aside>


