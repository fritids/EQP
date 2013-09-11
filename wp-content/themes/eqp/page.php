<?php get_header(); the_post();?>


<section id="content" class="inside gen-singles">

    <section id="inside-showcased-items">
        <div class="section-header">
            <h1 class="pseudo-breadcrumb eqp">
                <?php echo breadcrumb(); ?>
            </h1>
            <?php waysToConnect(); ?>
        </div>   
    </section>
    <div class="article-body">
        <?php the_content(); ?>
    </div>

</section>
</div>


<?php get_footer()?>