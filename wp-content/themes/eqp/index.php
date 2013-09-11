<?php get_header();?>


        <section id="content">
            <section id="carousel">
                <?php echo get_the_carousel(); ?>
                <div id="carousel-nav">
                    <p>Destacamos:</p>
                    <ul>
                        <?php echo get_the_carousel_controls(); ?>
                    </ul>
                </div>  
            </section>
            
            <section id="thisWeek">
                <p class="tab-label">Esta semana</p>
                <ul>
                    <li><a href="/entradas" class="entry-publish-s"><span><?php echo countPostOverTime('post'); ?></span> Entradas</a></li>
                    <li><a href="/fotos" class="photo-publish-s"><span><?php echo countPostOverTime('post_fotos'); ?></span> Fotos</a></li>
                    <li><a href="/videos" class="video-publish-s"><span><?php echo countPostOverTime('post_videos'); ?></span> Videos</a></li>
                    <li><a href="/acciones-home" class="action-publish-s"><span><?php echo countPostOverTime('post_acciones'); ?></span> Acciones</a></li>
                </ul>
            </section>
            
            <section id="showcased-actions">
                <h2 class="label channels-label action-published-s">Acciones destacadas</h2>
                <div class="articles-holder">
                    <?php echo get_featured_actions(); ?>
                </div>
            </section>
            
            <section id="secondary-content">
                <section id="the-most" class="col eight">
                    
                    <div id="tabs-holder">  
                        <ul class="menu">  
                            <li id="more-active" class="current"><a href="#" title="Lo más activo" class="evt" data-func="getTab" data-orden="masActivos">Lo m&aacute;s activo</a></li>  
                            <li id="more-recent"><a href="#" title="Lo más nuevo" class="evt" data-func="getTab" data-orden="masNuevos">Lo m&aacute;s nuevo</a></li>  
                        </ul>  
                        <span id="preTabs" class="clear"></span> 
                        <?php echo get_postTabs('masActivos'); ?>
                    </div>
                </section>
                <?php get_sidebar(); ?>
            </section>
            
            <section id="terciary-content">
                <section id="showcased-comments" class="col eight">
                    <h2 class="label">&Uacute;ltimos comentarios</h2>
                    <ul class="article-list wall">
                        <?php get_recent_comments(); ?>
                    </ul>
                </section>
                
                <section id="newsletter" class="col four">
                    <h2 class="label">Mant&eacute;nte al tanto</h2>
                    <p>Suscríbete a nuestro Newsletter y te mantendremos al tanto de las últimas acciones y publicaciones de la comunidad.</p>
                    <?php echo getNewsletterForm(); ?>
                </section>
            </section>
            
        </section>
        
    </div>


<?php get_footer();?>