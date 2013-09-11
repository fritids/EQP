<?php include("meta-header.php"); ?>
<body <?php body_class($class); ?>>
    
    <div class="siteWrapper">
        <img src="<?php bloginfo('template_directory') ?>/css/ui/logo.png" alt="El Quinto Poder" />
        <header>
            <img src="<?php bloginfo('template_directory') ?>/css/ui/404.png" alt="Error 404" />
        </header>
        <section id="404Content" >
            <div>
                <h1>¡Página no encontrada!</h1>
                <p>Disculpa los inconvenientes, pero parece que la página que estás buscando ha <strong>cambiado</strong>, ha sido <strong>borrada</strong> o <strong>no existe</strong>. Como nuestro objetivo es entregarte el mejor servicio posible, acá encontrarás el acceso a nuestros principales ítems de navegación:</p>
                <ul>
                    <li><a href="/entradas" title="Entradas" >Entradas</a></li>
                    <li><a href="/fotos" title="Fotos" >Fotos</a></li>
                    <li><a href="/videos" title="Videos" >Videos</a></li>
                    <li><a href="/acciones-home" title="Acciones" >Acciones</a></li>
                    <li><a href="/especiales-home" title="Especiales" >Especiales</a></li>
                </ul>
                <p>O si prefieres puedes partir desde el inicio</p>
                <a class="goHome" href="<?php echo home_url(); ?>" title="Inicio" >Inicio</a>
            </div>
        </section>
    </div>
    
</body>
<?php wp_footer();?>
</html>