<?php
/*
Template Name: Sugiere Especial
*/

foreach ($_REQUEST as $key => $value) {
   $_REQUEST[$key] = htmlentities(strip_tags($value));
}

session_start();
if (isset($_REQUEST['sendSugerencia']) && (empty($_REQUEST['sendSugerencia']) || $_REQUEST['sendSugerencia'] != $_SESSION['sendSugerencia'] )) {
    header('x', TRUE, 404);
    die;
}

if (isset($_REQUEST['sendSugerencia']) && ($_REQUEST['sendSugerencia'] == $_SESSION['sendSugerencia'])) {
    sendSugerenciaEspecial();
    $message = true;
}


$token = md5(uniqid(rand(), true));
$_SESSION['sendSugerencia'] = $token;

get_header(); the_post();?>

<section id="content" class="inside gen-singles">

    <section id="inside-showcased-items">'
        <div class="section-header">
            <h1 class="pseudo-breadcrumb eqp">
                <?php echo breadcrumb(); ?>
            </h1>
            <?php waysToConnect(); ?>
        </div>   
    </section>
    <section class="single-content-holder">
        <p style="margin: 20px 0; text-align: center;">Sugiérenos un tema para nuestro próximo especial</p>
        <?php if( $message ) {
            echo '<div class="message success">';
            echo '<p><strong>¡Muchas Gracias! </strong>Tu mensaje ha sido enviado correctamente, nos pondremos en contacto contigo a la brevedad</p>';
            echo '</div>';
        } ?>
        <form id="contactForm" action="" method="post">
            <label for="nombre" >Nombre</label>
            <input type="text" name="nombre" value="" placeholder="José Manuel Riquelme" required />
            
            <label for="email" >Email</label>
            <input type="email" name="email" value="" placeholder="jose.manuel.riquelme@gmail.com" required />
            
            <label for="tema" >Tema</label>
            <input type="text" name="tema" value="" placeholder="Ej: La nueva ley del tránsito" required />
            
            <label for="mensaje" >Argumento</label>
            <textarea name="mensaje" required placeholder="Escribenos tus razones por las cuales deberíamos crear tu especial" ></textarea>
            
            <input type="hidden" name="sendSugerencia" value="<?php echo $token; ?>" />
            <input type="submit" value="Enviar" class="goHome ganalytics" data-ga-category="Participacion" data-ga_action="Sugerir Especial" data-ga_opt_label="BtnSugerir_enviar" />
        </form>
    </section>

</section>
</div>


<?php get_footer()?>