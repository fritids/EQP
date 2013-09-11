<?php get_header(); the_post();?>
        
        <?php
        
            $categories = wp_get_post_terms( $post->ID, 'categorias_blog');
            foreach( $categories as $cat ){
                $catList = $cat->name .',';
            }
            $catList = rtrim($catList, ',');
            $category_id = $cat->term_id;
            if( $post->post_type == 'post_videos') { $thumb = get_the_embed(get_field('video_link', $post->ID), 'width="620"'); }
            else { $thumb = '<a href="#" title="Ver en tamaño completo" class="evt" data-func="fullsizeImage" data-pid="'. $post->ID .'" >'. get_the_post_thumbnail($post->ID, 'fotoSingle') .'</a>'; } 
            
            $autor = get_userdata($post->post_author);
            
        ?>
        <section id="content" class="inside">
            <section id="inside-showcased-items">
                <div class="section-header">
                    <h1 class="pseudo-breadcrumb entries">
                        <?php echo breadcrumb(); ?>
                    </h1>
                    <?php waysToConnect(); ?>
                </div>
            </section>
            
            <section id="main-content" class="col eight inside">
                <article>
                    <header>
                        <h1><?php the_title(); ?></h1>
                        <div class="item-metadata-holder">
                            <div class="item-metadata">
                                <div class="usr-avatar-holder">
                                    <?php echo get_avatar(get_the_author_meta('ID'), 40); ?>
                                </div>
                                <p class="published-by">Por: <a href="/perfil-de-usuario/?user=<?php echo $autor->ID; ?>"><?php nombre_y_apellido($autor->ID, true);  ?></a></p>
                                <?php 
                                    if( ! empty($categories) && $catList ){ 
                                        echo '<p class="published-where">En: <a href="'. get_term_link( $cat, 'categorias_blog' ) .'" title="'. $catList .'">'. $catList .'</a></p>';
                                    }
                                ?>
                                <p class="published-when"><?php the_date(); ?></p>
                            </div>
                           <div class="social-echoes">
                               <?php $visitas =  get_post_meta($post->ID, '_visitas', true); ?>
                               <p class="visited">Número de visitas: </p>
                               <ul>
                                   <?php echo get_socialEchoes(get_permalink(), get_the_title(), $post->ID, 'single'); ?>
                                   <li class="plus"><g:plusone size="medium"></g:plusone></li>
                               </ul>
                               
                           </div>
                        </div>
                    </header>
                    
                    <div class="article-body">
                        <p class="excerpt">
                            <?php echo get_field('bajada', $post->ID) ?>
                        </p>
                        <figure>
                          <?php echo $thumb; ?>
                        </figure>
                        <?php 
                            $textoDestacado = get_field('texto_destacado', $post->ID); 
                            if ( $textoDestacado ) { echo '<blockquote id="textoDestacado">'. $textoDestacado .'</blockquote>'; }
                        ?>
                        <?php echo apply_filters('the_content',get_the_content()); ?>
                        <div class="clearfix">
                            <?php 
                            if($post->post_type != 'post_videos' && get_field('video_link', $post->ID) ) {
                                echo get_the_embed(get_field('video_link', $post->ID), 'width="620"');
                            }
                            ?>
                        </div>
                            
                        <div class="social-echoes clearfix">
                            <p>¿Te gustó esta <?php echo $postFirma ?>? ¡Compártela!</p>
                            
                            <ul class="fright">
                                <?php echo get_socialEchoes(get_permalink(), get_the_title(), $post->ID, 'single'); ?>
                                <li class="plus"><g:plusone size="medium"></g:plusone></li>
                            </ul>
                        </div>
                    </div>
                </article>
            
<!-------------------------------------------------------------------------------------------------------------------                Comentarios-->
            <div id="comments">
                <h3 class="label">Comentarios</h3>
                <div id="comments-header" class="clearfix">
                    <p class="comments-counter"><?php echo return_comments_number(); ?> comentarios</p>
<!--                    <a href="#" class="comment-ca evt" data-func="showCommentForm">Agrega tu comentario</a>-->
                </div>
                <?php comments_template( '', true ); ?>
            </div>             

            </section>

            <?php get_sidebar('blog'); ?>
            
        </section>
    </div>
    
    <script>
        $.ajax({
            type: "POST",
            url: '/wp-admin/admin-ajax.php',
            data: 'action=ajax_count&funcion=cva&postid=<?php echo $post->ID; ?>',
            dataType: "json",
            success : function(data){
                $(".visited").html("Número de visitas: " +  data.visitas); 
            }
            
        });
    </script>


<?php get_footer();?>