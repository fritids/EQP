
    <aside class="four">
        
        <?php if(get_field('banner_sidebar_desarrollo-regional','options') && is_category('desarrollo-regional')){?>
            <div class="banner_top">
                <a href="<?php the_field('url_banner_sidebar','options'); ?>" rel="external" title="link externo">
                   <?php echo wp_get_attachment_image(get_field('banner_sidebar_desarrollo-regional', 'options'), 'full');?>
                </a>
            </div>
        <?php }?>
        <?php if(get_field('banner_embed','options') && is_category('desarrollo-regional')){?>
        <div class="embedIframe">
            <?php the_field('banner_embed','options')?>
        </div>
        <?php }?>
        <h2 class="label">Temas</h2>
        <ol id="temaRank" class="themes-ranking">
            <?php temasCount(); ?>
        </ol> 
    </aside>
