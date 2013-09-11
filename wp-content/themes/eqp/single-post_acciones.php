<?php       

if($_GET["firmas"]=="true" && ($current_user->ID == $post->post_author || current_user_can('manage_options'))){

                header("Content-Type: application/csv") ; 
                header('Content-Disposition: attachment; filename="firmas.csv"');    
                $firmas = getFirmas($post->ID);
                $firmas =   iconv ( 'UTF-8', 'UTF-16LE//IGNORE', $firmas );
                
                echo $firmas;
                
                exit;
            }

get_header(); the_post();
            
            $categories = get_the_category( $post->ID );
            foreach( $categories as $cat ){
                $catList = $cat->cat_name .',';
            }
            $catList = rtrim($catList, ',');
            
            $autor = get_userdata($post->post_author);
            
            $current_user = wp_get_current_user();
            
            $thankyouMessage = thankyou_message( array(
                            'author_id' => $post->post_author,
                            'pid' => $post->ID,
                            'pName' => $postFirma
                        ), isset($_GET['newPost']) );
            
?>
  <section id="content" class="inside">
            <section id="inside-showcased-items">
                <div class="section-header">
                    <h1 class="pseudo-breadcrumb actions">
                        <?php echo breadcrumb(); ?>
                    </h1>
                    <?php waysToConnect(); ?>
                </div>
            </section>
            
            <section id="main-content" class="col eight inside" data-postid="<?php echo $post->ID ?>">
                <article>
                    <header>
                        <?php echo $thankyouMessage; ?>
                        <h1><?php the_title(); ?></h1>
                        <div class="item-metadata-holder">
                            <div class="item-metadata">
                                <div class="usr-avatar-holder">
                                    <?php echo get_avatar($autor->ID, 40); ?>
                                </div>
                                <p class="published-by">Por: <a href="/perfil-de-usuario/?user=<?php echo $autor->ID; ?>"><?php nombre_y_apellido($autor->ID, true);  ?></a></p>
                                <p class="published-where">En: <a href="#"><?php echo $catList ?></a></p>
                                <p class="published-when"><?php the_date(); ?></p>
                            </div>
                           <div class="social-echoes">
                               <?php $visitas =  get_post_meta($post->ID, '_visitas', true); ?>
                               <p class="visited">Número de visitas: </p>
                               <ul>
                                   <?php echo get_socialEchoes(get_permalink(), get_the_title(), $post->ID, 'single', true, false,  array(
                                        'socialaction' => 'Share'
                                    )); ?>
                                   <li class="plus"><g:plusone size="medium"></g:plusone></li>
                               </ul>
                           </div>
                        </div>
                    </header>
                    
                    <div class="article-body">
                        <div class="action-holder-article">
                            <h2 class="label">Descripci&oacute;n general de la acci&oacute;n</h2>
                            <div class="action-status-holder">
                                <ul class="action-status">
                                    <li id="numberOfVotes" >Tenemos <span><?php echo number_format( get_action_votes($post->ID)*1 , 0 , ',' , '.' ); ?></span></li>
                                    <li>Necesitamos <span><?php echo number_format( get_field('requeridos', $post->ID)*1 , 0 , ',' , '.' ); ?></span></li>                                             
                                </ul> 
                                <a href="#" class="action-ca evt ganalytics " data-ga-category="Participacion" data-ga_action="Firmas" data-ga_opt_label="BtnFirma_single" data-func="actionSignature">Firma y Participa</a>
                                <ul class="action-status">
                                    <?php echo getFirmas($post->ID, 'show'); ?> 
                                </ul>
                            </div>
                            <?php the_content(); ?>
                            
                        </div>
                        <?php getUserFirmBox( $post->post_author, true ); ?>
                        <div class="social-echoes">
                            <p>¿Te gustó esta Acción? ¡Compártela!</p>
                            
                               <ul class="fright">
                                <?php echo get_socialEchoes(get_permalink(), get_the_title(), $post->ID, 'single', true, false,  array(
                                        'socialaction' => 'Share'
                                    )); ?>
                                <li class="plus"><g:plusone size="medium"></g:plusone></li>
                                </ul>
                        </div>
                    </div>
                </article>
                <?php echo getTwitterBox(); ?>
                <!-- Start of HootSuite Embed -->

<!-- End of HootSuite Embed -->
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
                if(data != null){
                    $(".visited").html("Número de visitas: " +  data.visitas); 
                }
                
            }
            
        });
    </script>
<?php get_footer();?>