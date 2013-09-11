<?php get_header(); the_post();
$autor = get_userdata($post->post_author);
$categories = get_the_category( $post->ID );
foreach( $categories as $cat ){ $catList = $cat->cat_name .','; }
$catList = rtrim($catList, ',');
?>
        <article class="single-post">
            <header class="topEntry">
                <div class="row topEntryWrap">
                    <?php echo mobile_breadcrumbs(); ?>
                    <h1 class="entryTitle column10"><?php the_title(); ?></h1>
                    <?php if( is_singular('post') ) { echo mobile_readLater_button( 'phones' ); } ?>
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
                <div class="clearfix action-content-wrapper" >
                    <div class="entryContent actions column9">
                        <?php the_content(); ?>
                    </div>
                    <aside class="column3 asideEntry last">
                        <div class="asideActions">
                            <?php
                                $tenemos = number_format( mobile_get_action_votes( $post->ID ) * 1, 0, ',', '.' );
                                $necesitamos = number_format( get_field( 'requeridos', $post->ID ) * 1, 0, ',', '.' );
                                if ( !$tenemos ) { $tenemos = '0'; }

                                echo '<span class="Need">Necesitamos <strong>'. $necesitamos .'</strong></span>';
                                echo '<span id="numero_firmas" class="Have">Tenemos <strong>'. $tenemos .'</strong></span>';
                            ?>
                        </div>
                        <a class="firm asideFirm evt track-action" data-ga-category="Participacion" data-ga_action="Firmas" data-func="firma_y_participa" data-ga_opt_label="BtnMobFirma_single" data-pid="<?php echo $post->ID; ?>" href="#">Firma y Participa</a>
                        <?php if( $firmantes = mobile_get_action_voters_list( $post->ID ) ) : ?>
                        <div class="lastadh">
                            <h3>Últimos Adherentes</h3>
                            <?php echo $firmantes; ?>
                        </div>
                        <?php endif; ?>
                    </aside>
                </div>
                <div class="greyBorder accion">
                    <p class="firmName"><?php nombre_y_apellido($autor->ID, true);  ?></p>
                    <p class="firmText">
                        <?php echo $autor->user_description ? make_clickable( $autor->user_description ) : 'Usuario de El Quinto Poder'; ?>
                    </p>
                </div>
                <div class="socialShare accion">
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