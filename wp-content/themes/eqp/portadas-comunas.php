<?php
/*
Template Name: Portadilla Comunas
*/
get_header(); the_post(); ?>

    <section id="content" class="inside">

        <section id="inside-showcased-items">
            <div class="section-header entries">
                <h1 class="pseudo-breadcrumb propuestas">
                    <?php echo breadcrumb(); ?>
                </h1>
                <?php waysToConnect(); ?>
            </div>
            <?php
            $comunaObj = get_term_by('slug', $post->post_name, 'comunas');
            if( $comunaObj->description ){ echo '<p class="comunaDescription">'. $comunaObj->description .'</p>'; }
            ?>
            <ul class="article-list vertical candidatos-portada">
                <?php echo getCandidatosToPortada($post->post_name); ?>
            </ul>

        </section>
        <section id="secondary-content">
            <section id="the-most" class="col eight inside">
                <h2 class="label muniTitle">Propuestas Ciudadanas</h2>
                <div id="tabs-holder">
                    <ul class="menu">
                        <li id="more-active" class="current"><a href="#" title="Ver lo más popular" class="evt" data-func="getPropuestasTab" data-orden="masPopular" data-cat="<?php echo $post->post_name; ?>" data-postType="propuestas">Lo m&aacute;s popular</a></li>  
                        <li id="more-recent"><a href="#" title="Ver lo más nuevo" class="evt" data-func="getPropuestasTab" data-orden="masNuevas" data-cat="<?php echo $post->post_name; ?>" data-postType="propuestas">Lo m&aacute;s nuevo</a></li>
                        <li id="all-the-posts"><a href="<?php echo get_term_link( $post->post_name, 'comunas' ) ?>" title="Ver todas las propuestas">Todas las propuestas</a></li>  
                    </ul>
                    <span id="preTabs" class="clear"></span>
                    <?php echo get_PropuestasTabs('masPopular', 0, array( array( 'taxonomy' => 'comunas', 'field' => 'slug', 'terms' => $post->post_name ) )); ?>
                </div>
            </section>
            <?php get_sidebar('propuestas'); ?>
        </section>
    </section>

    </div>


<?php get_footer();?>