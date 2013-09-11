<?php

    $categories = get_the_category( $post->ID );
    foreach( $categories as $cat ){
        $catList = $cat->cat_name .',';
    }
    $catList = rtrim($catList, ',');
    $category_id = $cat->term_id;

    get_header();
    the_post();
    
    if( $post->post_type == 'post_videos') { $thumb = get_the_embed(get_field('video_link', $post->ID), 'width="620"'); }
    else { $thumb = '<a href="#" title="Ver en tamaño completo" rel="nofollow" class="evt" data-func="fullsizeImage" data-pid="'. $post->ID .'" >'. get_the_post_thumbnail($post->ID, 'fotoSingle', array('alt' => get_the_title($post->ID), 'title' => 'Ver en tamaño completo')) .'</a>'; } 

    $autor = get_userdata($post->post_author);

    if($post->post_type == 'post'){ 
        $typeIcon = 'entries';
        $postFirma = 'Entrada';
        $GAactionLabel = 'Entradas';
    }
    elseif($post->post_type == 'post_fotos'){
        $typeIcon = 'photos';
        $postFirma = 'Foto';
        $GAactionLabel = 'Fotos';
    }
    elseif($post->post_type == 'post_videos'){ 
        $typeIcon = 'video';
        $postFirma = 'Video';
        $GAactionLabel = 'Videos';
    }
    
    $thankyouMessage = thankyou_message( array( 'author_id' => $post->post_author, 'pid' => $post->ID, 'pName' => $postFirma ), isset($_GET['newPost']) );

?>
        <section id="content" class="inside">
            <section id="inside-showcased-items">
                <div class="section-header">
                    <h1 class="pseudo-breadcrumb <?php echo $typeIcon; ?>">
                        <?php echo breadcrumb(); ?>
                    </h1>
                    <?php waysToConnect(); ?>
                </div>
            </section>
            
            <section id="main-content" class="col eight inside">
                <article>
                    <header>
                        <?php echo $thankyouMessage; ?>
                        <h1><?php the_title(); ?></h1>
                        <div class="item-metadata-holder">
                            <div class="item-metadata">
                                <div class="usr-avatar-holder">
                                    <?php echo get_avatar(get_the_author_meta('ID'), 40); ?>
                                </div>
                                <p class="published-by">Por: <a href="/perfil-de-usuario/?user=<?php echo $autor->ID; ?>"><?php nombre_y_apellido($autor->ID, true);  ?></a></p>
                                <p class="published-where">En: <a href="<?php echo get_category_link($category_id); ?>"><?php echo $catList ?></a></p>
                                <p class="published-when"><?php the_date(); ?></p>
                            </div>
                           <div class="social-echoes">
                               <?php $visitas =  get_post_meta($post->ID, '_visitas', true); ?>
                               <p class="visited">Número de visitas: </p>
                               <ul>
                                   <?php echo get_socialEchoes(get_permalink(), get_the_title(), $post->ID, 'single', true, false, array(
                                        'socialaction' => 'Share'
                                    )); ?>
                                   <li class="plus"><g:plusone size="medium"></g:plusone></li>
                               </ul>
                               
                           </div>
                        </div>
                    </header>
                    
                    <div class="article-body">
                        <?php if(get_field('bajada', $post->ID)){ ?>
                        <p class="excerpt">
                            <?php echo get_field('bajada', $post->ID) ?>
                        </p>
                        <?php } ?>
                        <?php
                        
                            if($thumb){
                                echo '<figure class="single-thumnail" >';
                                echo $thumb;
                                echo '</figure>';
                            }
                            
                            $textoDestacado = get_field('texto_destacado', $post->ID); 
                            if ( $textoDestacado ) { echo '<blockquote id="textoDestacado">'. $textoDestacado .'</blockquote>'; }
                            
                            the_content();
                            
                            if($post->post_type != 'post_videos' && get_field('video_link', $post->ID) ) {
                                echo '<div class="clearfix">';
                                echo get_the_embed(get_field('video_link', $post->ID), 'width="620"');
                                update_post_meta($post->ID, 'ajaxEmbed', get_the_embed(get_field('video_link', $post->ID), 'width="600"'));
                                echo '</div>';
                            }
                     
                            getUserFirmBox( $post->post_author, true );
                        ?>
                            
                        <div class="social-echoes clearfix">
                            <p>¿Te gustó esta <?php echo $postFirma ?>? ¡Compártela!</p>
                            
                            <ul class="fright">
                                <?php echo get_socialEchoes(get_permalink(), get_the_title(), $post->ID, 'single', true, false, array(
                                        'socialaction' => 'Share'
                                    )); ?>
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

            <?php get_sidebar('single'); ?>
            
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

    <?php if( $thankyouMessage && function_exists('w3tc_pgcache_flush') ){ w3tc_pgcache_flush(); } ?>
<?php get_footer();?>