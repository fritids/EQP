<?php
/*
Template Name: Publicacion de cosas
*/

// se libera el cache porsiaca
if( function_exists('w3tc_pgcache_flush') ) { w3tc_pgcache_flush(); }

// si no esta logueado o si su usuario no existe, pa la home.
    if( !is_user_logged_in() ){ wp_redirect( home_url() ); exit; }
    else {
        $current_user = wp_get_current_user();
        if ( !($current_user instanceof WP_User) ){ wp_redirect( home_url() ); exit; }
    }
    
// seteos para el formulario
    $sended = false;
    
    switch ( $_GET['tipo'] ) {
        case 'foto':
            $content_post_type = 'post_fotos';
            $content_post_type_title = 'foto';
            $content_post_type_desc = 'A través de este formulario podrás subir una foto a nuestra comunidad. Si no es una foto original, te recomendamos cites al autor y la licencia que te permite compartirla.';
            $pStatus = 'publish';
            $optLabel = 'Fotos';
            break;
        case 'video':
            $content_post_type = 'post_videos';
            $content_post_type_title = 'video';
            $content_post_type_desc = 'A través de este formulario podrás compartir un video en nuestra comunidad, ingresando la URL (dirección de Internet) donde se encuentra alojado.';
            $pStatus = 'publish';
            $optLabel = 'Videos';
            break;
        case 'accion':
            $content_post_type = 'post_acciones';
            $content_post_type_title = 'acción';
            $content_post_type_desc = 'Una acción es una carta pública, dirigida a una persona u organización, a través de la cual podrás solicitar firmas de apoyo para tu causa. Te recomendamos plantear con claridad el objetivo que buscas con la carta y la meta en números de firmas que quieres reunir.';
            $pStatus = 'publish';
            $optLabel = 'Acciones';
            break;
        default:
            $content_post_type = 'post';
            $content_post_type_title = 'entrada';
            $content_post_type_desc = 'A través de este formulario podrás subir tu entrada a nuestra comunidad. Sólo publicaremos entradas que sean de tu autoría.';
            $pStatus = 'draft';
            $optLabel = 'Entradas';
    }
    
    if( $_POST['publish_form'] === 'publicar' && $_POST['isitme'] === ''  ){
        $response = new_publish_contents( $current_user, $_POST, $content_post_type_title );
        if( $response ){ $sended = true; }
    }

get_header(); the_post(); ?>
    <section id="content" class="inside" >
            <section id="inside-showcased-items">
                <div class="section-header">
                    <h1 class="pseudo-breadcrumb eqp">
                        <?php echo breadcrumb(); ?>
                    </h1>
                </div>
                <article id="the-publish-forms" class="inside-content" >
                    <?php if( !$sended ) : ?>
                    <header class="inner-header">
                        <h2 class="publisher-greet" >Hola <?php nombre_y_apellido($current_user->ID, true); ?></h2>
                        <p class="nice-paragraph" ><?php echo $content_post_type_desc; ?></p>
                    </header>
                    <div class="inner-content-box" >
                        <form class="the-publish-form-object new-nice-form" data-delegation="autovalidate" method="post" action="" enctype="multipart/form-data">
                            <?php new_publish_content_form_fields( array( 'post_type' => $content_post_type, 'post_type_name' => $content_post_type_title ) ); ?>
                            
                            <div class="form-actions-box clearfix" >
                                <div class="fake-checkbox-holder">
                                    <input class="pretty-checkbox" type="checkbox" name="licence_agreement" id="licence_agreement" value="iagree" data-customValidation="checkCheckBox" required checked >
                                    <label class="pretty-checkbox-label big-checkbox-label" for="licence_agreement" >
                                        Acepto los <a href="/condiciones-de-uso/" title="Ver Términos y Condiciones" rel="help" target="_blank">Términos y Condiciones</a>
                                    </label>
                                </div>
                                
                                <input type="submit" class="button-submit" data-ga-category="Participacion" data-ga_action="Publicaciones" data-ga_opt_label="BtnEnviar_<?php echo $optLabel; ?>" value="Enviar a Edición" >
                                <input type="hidden" name="publish_form" value="publicar" >
                                <input type="hidden" name="content_post_type" value="<?php echo $content_post_type; ?>" >
                                <input type="hidden" name="content_author_id" value="<?php echo $current_user->ID; ?>" >
                                <input type="hidden" name="content_post_status" value="<?php echo $pStatus; ?>" >
                                <input type="hidden" name="original_referer" value="<?php echo $_SERVER['HTTP_REFERER']; ?>" >
                                <input type="hidden" name="isitme" value="" >
                            </div>
                        </form>
                    </div>
                    
                    <?php else :
                        echo $response;
                    endif; ?>
                </article>
            </section>
    </section>
</div> <!-- cierra div class="siteWrapper" -->



<?php get_footer()?>