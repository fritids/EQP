<?php get_header(); the_post();
    $temasTags = wp_get_post_terms( $post->ID, 'temas' );
    $comunasTerms = wp_get_post_terms( $post->ID, 'comunas', array('fields' => 'slugs') );
    $catList = "";
    $filtro = '?comuna='. $comunasTerms[0];
    foreach( $temasTags as $tema ){
        $catList .= '<a href="'. get_term_link( $tema ) . $filtro .'" title="Ver '. $tema->name .'" rel="tag">'. $tema->name .'</a>, ';
    }
    $catList = rtrim($catList, ', ');
    
    $autor = get_userdata($post->post_author);
    $current_user = wp_get_current_user();
?>
  <section id="content" class="inside">
            <section id="inside-showcased-items">
                <div class="section-header">
                    <h1 class="pseudo-breadcrumb propuestas">
                        <?php echo breadcrumb(); ?>
                    </h1>
                    <?php waysToConnect(); ?>
                </div>
            </section>
            
            <section id="main-content" class="col eight inside" data-postid="<?php echo $post->ID ?>">
                <article>
                    <header>
                        <h1><?php the_title(); ?></h1>
                        <div class="item-metadata-holder">
                            <div class="item-metadata">
                                <div class="usr-avatar-holder">
                                    <?php echo get_avatar($autor->ID, 40); ?>
                                </div>
                                <p class="published-by">Por: <a href="/perfil-de-usuario/?user=<?php echo $autor->ID; ?>" title="Ir al Perfil de <?php nombre_y_apellido($autor->ID, true);  ?>" rel="author"><?php nombre_y_apellido($autor->ID, true);  ?></a></p>
                                <p class="published-where">En: <?php echo $catList ?></p>
                                <p class="published-when"><?php the_date(); ?></p>
                            </div>
                           <div class="social-echoes">
                                <?php $visitas =  get_post_meta($post->ID, '_visitas', true); 
                                     echo '<p class="visited">Número de visitas: '. $visitas .'</p>';
                                ?>
                               <ul>
                                   <?php echo get_socialEchoes(get_permalink(), get_the_title(), $post->ID, 'single'); ?>
                                   <li class="plus"><g:plusone size="medium"></g:plusone></li>
                               </ul>
                           </div>
                        </div>
                    </header>
                    
                    <div class="article-body">
                        <div class="action-holder-article">
                            <h2 class="label">Descripci&oacute;n general de la propuesta</h2>
                            <div class="action-status-holder">
                                <ul class="action-status">
                                    <li id="numberOfVotes" ><span><?php getApoyos( $post->ID, true ) ?></span><br>Ciudadanos apoyan</li>                                         
                                </ul> 
                                <a href="#" class="action-ca municipales gac evt" data-goal="apoyar-propuesta-single" data-func="apoyarPropuesta" data-pid="<?php echo $post->ID; ?>">Apoyar</a>
                            </div>
                            <?php the_content(); ?>
                            
                        </div>
                        <?php getUserFirmBox( $post->post_author, true ); ?>
                        
                        <div class="social-echoes">
                            <p>¿Te gustó esta Propuesta? ¡Compártela!</p>

                            <ul class="fright">
                                <?php echo get_socialEchoes(get_permalink(), get_the_title(), $post->ID, 'single'); ?>
                                <li class="plus"><g:plusone size="medium"></g:plusone></li>
                            </ul>
                        </div>
                    </div>
                </article>
                <?php echo getTwitterBox(); ?>
                
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

        <?php get_sidebar('single-propuestas'); ?>
            
    </section>
</div>
    <script>
        $.ajax({
            type: "POST",
            url: '/wp-admin/admin-ajax.php',
            data: 'action=ajax_count&funcion=cva&postid=<?php echo $post->ID; ?>',
            dataType: "json",
            success : function(data){
                if(data != null){
                    $(".visited").html("Número de visitas: " +  data.visitas); 
                }
                
            }
            
        });
    </script>
<?php get_footer();?>