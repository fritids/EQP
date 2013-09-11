<?php get_header(); ?>
     <section id="content" class="inside">
            <section id="inside-showcased-items">
                <div class="section-header">
                    <h1 class="pseudo-breadcrumb propuestas">
                        <?php echo breadcrumb(); ?>
                    </h1>
                    <?php waysToConnect(); ?>
                </div>
            </section>
            <section id="secondary-content">
                <section class="col eight inside channel-entry">
                    <h2 class="label">Propuestas en <?php single_term_title(); ?></h2>
                    <?php
                        $tema = get_term_by('name', single_term_title('', false), 'temas');
                        if( isset($_GET['comuna']) ) { $tQuery = array( 'relation' => 'AND', array( 'taxonomy' => 'temas', 'field' => 'slug', 'terms' => $tema->slug ), array( 'taxonomy' => 'comunas', 'field' => 'slug', 'terms' => $_GET['comuna'], ) ); }
                        else { $tQuery = array( array( 'taxonomy' => 'temas', 'field' => 'slug', 'terms' => $tema->slug ) ); }
                        get_allPropuestas( $tQuery );
                    ?>
                </section>
                <?php get_sidebar('propuestas'); ?>                
            </section>
        </section>
    </div>

<?php get_footer();?>