<?php
/*
*    Template Name: Municipales Portadilla
*/
get_header(); the_post(); 
$fecha = get_the_date();
?>

<section id="content" class="inside special">
    <section id="inside-showcased-items" class="municipalesHeader">
        <div id="special-edition-holder">
           <?php the_post_thumbnail('singleMunicipals'); ?>
           <div><h1><?php the_title(); ?></h1></div>
       </div>
    </section>
    <section id="secondary-content">
        
        <div class="municipales comuna col three">
            <?php getComunaInfoyCandidatos( 'la-reina', $post->ID, true ); ?>
            <?php getComunaInfoyCandidatos( 'providencia', $post->ID, true ); ?>
            <?php getComunaInfoyCandidatos( 'estacion-central', $post->ID, true ); ?>            
        </div>
        <div id="municipalesContentHolder" class="col six">
            <div class="municipales">
                <h2 class="label">De qué trata Municipales 2012</h2>
                <article class="article-body">
                    <?php the_content(); ?>
                    <time pubdate datetime="<?php echo $fecha; ?>" title="publicado el <?php echo $fecha; ?>"><?php echo $fecha; ?></time>
                </article>
            </div>
        </div>
        <div class="municipales comuna col three ultima">
            <?php getComunaInfoyCandidatos( 'recoleta', $post->ID, true ); ?>
            <?php getComunaInfoyCandidatos( 'maipu', $post->ID, true ); ?>
            <?php getComunaInfoyCandidatos( 'cartagena', $post->ID, true ); ?>
        </div>
    </section>
</section>
</div>
<?php get_footer();?>