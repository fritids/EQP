<?php
/*
Template Name: Especiales home
*/
?>
<?php get_header(); the_post();?>


<section id="content" class="inside">
    <section id="inside-showcased-items">
        <div class="section-header entries">
            <h1 class="pseudo-breadcrumb subjects">
                <?php echo breadcrumb(); ?>
            </h1>
            <?php waysToConnect(); ?>
        </div>

        <div class="showcased-actions-header">
            <p class="actions-definition col five">En esta sección encontrarás una selección de los mejores contenidos publicados por los miembros de nuestra comunidad, agrupados según temas. Relevan las múltiples voces, miradas y propuestas de acción ciudadana que construyen una sociedad más abierta y pluralista.</p>
            <div id="actions-mainca-holder" class="four fRight">
                <div class="call-to-action">
                    <a href="/sugiere-tu-especial/" title="Sugiérenos un especial" rel="help" class="no-iconized ganalytics" data-ga-category="Participacion" data-ga_action="Sugerir Especial" data-ga_opt_label="BtnSugerir">Sugiere un Especial</a>
                </div>                        
            </div>
            <a id="municipalesBannerPortada" href="/especiales-home/municipales-2012/" title="Municipales 2012" rel="section">
                <img src="<?php bloginfo('template_directory'); ?>/images/municipalesBanner-grande.jpg" >
            </a>
        </div>

        <ul id="listaEspeciales" class="article-list vertical multiple">
            <?php
                        
                $args = array(
                    'post_type' => 'especiales',
                    'offset' => 0,
                    'postCount' => 8,
                    'formato' => 'portada'
                );
                echo getPostsBlocks($args);

            ?>
        </ul>
    </section>
    <a href="#" class="see-more evt" data-func="verMasPostEspeciales" title="Ver más Especiales">Ver más Especiales</a>
    </section>
    </div>
            


<?php get_footer();?>