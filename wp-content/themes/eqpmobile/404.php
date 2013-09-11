<?php get_header(); ?>
        <section>
            <header class="error-container">
                <div class="error-box">
                    <h1 class="error-title">¡Página no encontrada!</h1>
                    <p class="error-description" >Disculpa los inconvenientes, pero parece que la página que estás buscando ha <strong>cambiado</strong>, ha sido <strong>borrada</strong> o <strong>no existe</strong>. Como nuestro objetivo es entregarte el mejor servicio posible, acá encontrarás el acceso a nuestros principales ítems de navegación:</p>
                    <ul class="error-navigation" >
                        <li><a href="/entradas/" title="Ir a Entradas" rel="section">Entradas</a></li>
                        <li><a href="/fotos/" title="Ir a Fotos" rel="section">Fotos</a></li>
                        <li><a href="/videos/" title="Ir a Videos" rel="section">Videos</a></li>
                        <li><a href="/acciones-home/" title="Ir a Acciones" rel="section">Acciones</a></li>
                    </ul>
                    <p class="error-description" >O si prefieres puedes partir desde el inicio.</p>
                    <a class="regular-button error-exit" href="<?php echo home_url(); ?>" title="Ir al Inicio" rel="index">Ir al Inicio</a>
                </div>
            </header>
        </section>
 <?php get_footer();?>