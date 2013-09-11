<?php
/*
Template Name: Contacto
*/

get_header(); the_post();?>

<section id="content" class="inside gen-singles">

    <section id="inside-showcased-items">
        <div class="section-header">
            <h1 class="pseudo-breadcrumb eqp">
                <?php echo breadcrumb(); ?>
            </h1>
            <?php waysToConnect(); ?>
        </div>   
    </section>
    <section class="single-content-holder">
        <p style="margin: 20px 0; text-align: center;">¿Tienes alguna pregunta o sugerencia? Envía un mensaje al equipo de <a href="<?php echo home_url(); ?>" title="<?php bloginfo('name'); ?>">elquintopoder.cl</a></p>
        
        <form id="contactForm" action="" method="post">
            <label for="nombre" >Nombre</label>
            <input type="text" name="nombre" value="" placeholder="José Manuel Riquelme" required />
            
            <label for="email" >Email</label>
            <input type="email" name="email" value="" placeholder="jose.manuel.riquelme@gmail.com" required />
            
            <label for="asunto" >Asunto</label>
            <input type="text" name="asunto" value="" placeholder="Me gusta su sitio!" required />
            
            <label for="mensaje" >Mensaje</label>
            <textarea name="mensaje" required placeholder="Escribe aquí el mensaje que nos quieres enviar" ></textarea>
            
            <?php wp_nonce_field('enviar_contacto','contacto_eqp_nonce'); ?>
            <input type="submit" value="Enviar" class="goHome gac" data-goal="enviar-contacto" />
        </form>
    </section>

</section>
</div>

<?php get_footer(); ?>