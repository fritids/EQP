<?php
/*
Template Name: Portadas
*/
get_header(); ?>

    <?php 
        if($post->post_name == 'entradas'){ $typeIcon = 'entries'; }
        elseif($post->post_name == 'fotos'){ $typeIcon = 'photos'; }
        elseif($post->post_name == 'videos'){ $typeIcon = 'video'; }
    ?>
     <section id="content" class="inside">
            
            <section id="inside-showcased-items">
                <div class="section-header">
                    <h1 class="pseudo-breadcrumb <?php echo $typeIcon; ?>">
                        <?php echo breadcrumb(); ?>
                    </h1>
                    <?php waysToConnect(); ?>
                </div>
                <?php if($post->post_name == 'entradas') { ?>
                
                <ul class="article-list vertical">
                    <?php
                        
                        $args = array(
                            'post_type' => 'post',
                            'offset' => 0,
                            'postCount' => 4,
                            'formato' => 'portada'
                        );
                        echo getPostsBlocks($args);
                    
                    ?>
                </ul>
                
                <?php } else { ?>
                    
                    <?php
                        
                        $args = array(
                            'post_type' => 'post_'.$post->post_name,
                            'offset' => 0,
                            'postCount' => -1,
                            'formato' => 'mediaPortada'
                        );
                        echo getPostsBlocks($args);
                    ?>
                
                <?php } ?>
            </section>
            
            <section id="secondary-content">
                <?php if($post->post_name == 'entradas') { ?>
                <section id="the-most" class="col eight inside">
                    <div id="tabs-holder">  
                        <ul class="menu">  
                            <li id="more-active"><a href="#" title="Lo más activo" class="evt" data-func="getEntradasTabs" data-orden="masActivos" >Lo m&aacute;s activo</a></li>  
                            <li id="more-recent" class="current"><a href="#" title="Lo más nuevo" class="evt" data-func="getEntradasTabs" data-orden="masNuevos" >Lo m&aacute;s nuevo</a></li>  
                            <li id="all-the-posts"><a href="/entradas/todas-las-entradas/" title="Todas las entradas">Todas las entradas</a></li>  
                        </ul>  
                        <span id="preTabs" class="clear"></span> 
                        
                        <?php echo get_entradas_postTabs("masNuevos"); ?>
                        
                    </div>
                </section>
                <?php } else { ?>
                        <section class="col eight inside channel-entry">
                            <h2 class="label">La Galería de el Quinto Poder</h2>
                                <?php
                                    if ( $post->post_name == 'fotos' ) { $type = 'lens'; }
                                    else { $type = 'play'; }
                                    get_allMedia('post_'. $post->post_name, $type, '/'.$post->post_name.'/');
                                ?>
<!--                            <a href="#" class="see-more evt" data-func="verMasPostPortadas" data-imgtype="<?php echo $type; ?>" data-postType="<?php echo 'post_'. $post->post_name; ?>" title="Ver más <?php echo $post->post_name; ?>">Ver más <?php echo $post->post_name; ?></a>-->
                        </section>
                    <?php } ?>
                <?php get_sidebar('portadas'); ?>                
            </section>
        </section>
        
    </div>

<?php get_footer();?>