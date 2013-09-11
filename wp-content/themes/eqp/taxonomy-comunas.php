<?php get_header();
$comuna = get_term_by('name', single_term_title('', false), 'comunas');
?>
     <section id="content" class="inside">
            <section id="inside-showcased-items">
                <div class="section-header">
                    <h1 class="pseudo-breadcrumb propuestas">
                        <?php echo breadcrumb(); ?>
                    </h1>
                    <?php waysToConnect(); ?>
                </div>
                <?php if( $comuna->description ){ echo '<p class="comunaDescription">'. $comuna->description .'</p>'; } ?>
            </section>
            <section id="secondary-content">
                <section class="col eight inside channel-entry">
                    <h2 class="label">Propuestas en <?php single_term_title(); ?></h2>
                    <?php
                        get_allPropuestas( array( array( 'taxonomy' => 'comunas', 'field' => 'slug', 'terms' => $comuna->slug ) ) );
                    ?>
                </section>
                <?php get_sidebar('propuestas'); ?>                
            </section>
        </section>
    </div>

<?php get_footer();?>