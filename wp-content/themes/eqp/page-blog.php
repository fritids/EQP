<?php
/*
* Template Name: Blog Home
*/
?>
<?php get_header(); ?>

     <section id="content" class="inside">
            
            <section id="inside-showcased-items">
                <div class="section-header">
                    <h1 class="pseudo-breadcrumb entries">
                        <?php echo breadcrumb(); ?>
                    </h1>
                    <?php waysToConnect(); ?>
                </div>
            </section>
            
            <section id="secondary-content">
                <section id="the-most" class="col eight inside">
                    <div id="tabs-holder">
                        <?php echo getPostsBlocks(array(
                            'post_type' => 'blog',
                            'postCount' => 6,
                            'formato' => 'lista-blog',
                            'paginate' => true
                        )); ?>
                    </div>
                </section>
                <?php get_sidebar('blog'); ?>                
            </section>
        </section>
        
    </div>

<?php get_footer();?>