<?php
get_header(); the_post() ;

    $autor = get_userdata($post->post_author);
    $categories = get_the_category( $post->ID );
    foreach( $categories as $cat ){ $catList = $cat->cat_name .','; }
    $catList = rtrim($catList, ',');
    
?>
        <article>
            <header class="topEntry">
                <div class="row topEntryWrap">
                    <?php echo mobile_breadcrumbs(); ?>
                    <h1 class="entryTitle column10">
                        <?php the_title();?>
                    </h1>
                    <?php if( is_singular('post') ) { echo mobile_readLater_button( 'normal' ); } ?>
                    <div class="column12">
                        <div class="item-meta entryMeta aLeft">
                            <div class="smallAvatar">
                               <?php echo get_avatar(get_the_author_meta('ID'), 47); ?>
                            </div>
                            <p class="publicadoPor">Por: <a href="/perfil-de-usuario/?user=<?php echo $autor->ID; ?>" title="Ver perfil de <?php nombre_y_apellido($autor->ID, true); ?>" rel="nofollow" ><?php nombre_y_apellido($autor->ID, true); ?></a></p>
                            <p class="publicadoEn">En: <?php echo $catList ?></p>
                            <p class="publicadoCuando"><?php the_date(); ?></p>
                        </div>
                        <ul class="socialCounter hide-on-phones">
                            <li class="faceCount"><?php echo mobile_get_shares( 'facebook', $post->ID ) ?></li>
                            <li class="tweetCount"><?php echo mobile_get_shares( 'twitter', $post->ID ) ?></li>
                            <li class="comentCount"><?php echo mobile_comments_number( $post->ID ) ?></li>
                        </ul>
                    </div>
                    <?php if( is_singular('post') ) { echo mobile_readLater_button( 'phones' ); } ?>
                </div>
            </header>
            <div class="row entryWrap">
                <div class="entryContent column9">
                    <div class="entryContent-body" >
                        <?php
                            echo '<div class="thumbnail-holder">';
                            if( is_singular('post') || is_singular('post_fotos') ){
                                mobile_regenerate_image_data( get_post_thumbnail_id( $post->ID ) , 'mobile_single' );
                                the_post_thumbnail('mobile_single', array( 'title' => get_the_title(), 'alt' => get_the_title() ));
                                if( is_singular( 'post_fotos' ) ){
                                    echo '<button class="btnZoom hide-on-phones evt" data-func="verFotoCompleta" data-imgID="'. get_post_thumbnail_id( $post->ID ) .'" data-imageTitle="'. get_the_title() .'" title="ver imagen en Tamaño Completo">ver imagen Tamaño Completo</button>';
                                }
                            }
                            elseif( is_singular('post_videos') ) {
                                $embedIframe = mobile_get_the_embed( get_field('video_link', $post->ID), 'style="width: 100%;"' );
                                update_post_meta($post->ID, 'ajaxEmbed', $embedIframe);
                                echo $embedIframe;
                            }
                            echo '</div>';

                            if( get_field('bajada') ){
                                echo '<p class="bajada">';
                                the_field('bajada');
                                echo '</p>';
                            }

                            $content = apply_filters('the_content', get_the_content());
                            $content = str_replace( '<br />', '</p><p>', $content );
                            echo $content;
                        ?>
                    </div>
                    <div class="greyBorder">
                        <p class="firmName"><?php nombre_y_apellido($autor->ID, true); ?></p>
                        <p class="firmText">
                            <?php echo $autor->user_description ? make_clickable( $autor->user_description ) : 'Usuario de El Quinto Poder'; ?>
                        </p>
                    </div>
                    <div class="socialShare">
                        <p class="aLeft">¿Te gustó esta entrada?</p>
                        <ul class="socialBottom">
                            <?php echo mobile_getSocialShares(array(
                                'pid' => $post->ID,
                                'wrapper' => 'li'
                            )); ?>
                        </ul>
                    </div>
                     
                    <!-- Comentarios -->
                    <?php comments_template( '', true ); ?>
                </div>
                <!-- SideBar -->
                <?php get_sidebar('single'); ?>
            </div>
        </article>
        <script>
            $.ajax({
                type: "POST",
                url: '/wp-admin/admin-ajax.php',
                data: 'action=ajax_count&funcion=cva&postid=<?php echo $post->ID; ?>',
                dataType: "json",
                success : function(data){}
            });
        </script>
     <?php get_footer();?>