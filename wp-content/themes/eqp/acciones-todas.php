<?php
/*
Template Name: Acciones Todas
*/
?>
<?php get_header(); the_post(); if (is_user_logged_in() ) { $current_user = wp_get_current_user(); } ?>


    <section id="content" class="inside">

        <section id="inside-showcased-items">
            <div class="section-header">
                <h1 class="pseudo-breadcrumb actions">
                    <?php echo breadcrumb(); ?>
                </h1>
                <?php waysToConnect(); ?>
            </div>
            <div class="showcased-actions-header">
                <p class="actions-definition col five">El Quinto Poder es una comunidad de conversaciones para la acción ciudadana. En esta sección podrás encontrar las propuestas de cambio que quienes integran nuestra comunidad. Te invitamos a adherir y difundir entre tus redes aquellas con las que te sientas identificado/a. ¡El cambio parte en ti!</p>
                <div id="actions-mainca-holder" class="four fRight">
                    <div class="call-to-action">
                        <a href="#" class="actions evt" data-func="showPublishForm" data-posttype="post_acciones" data-autor="<?php if($current_user) { echo $current_user->ID; } ?>">Crea una acci&oacute;n</a>
                    </div>
                    <ul id="corp-rules-holder">
                        <li><a href="/que-es-el-quinto-poder">¿Qué es el Quinto Poder?</a></li>
                        <li><a href="/reglas-de-la-comunidad">Reglas de la Comunidad</a></li>
                    </ul>                        
                </div>
            </div>
        </section>

        <section id="secondary-content">
            <section class="col eight inside">
                  <div id="tabs-holder">  
                    <?php get_allActions(); ?>
                  </div>
<!--                <a href="#" id="verMasBtn" class="see-more evt" data-func="verMasPostAcciones" data-orden="masAdhesiones" title="Ver más Acciones">Ver más Acciones</a>-->
            </section>

            <?php get_sidebar('acciones'); ?>

        </section>
    </section>
</div>
<?php get_footer();?>