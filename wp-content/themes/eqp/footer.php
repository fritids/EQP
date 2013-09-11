<!-------------------------------------- footer ------------------------------------------------>
    <footer>
        <div class="wrapper">
            <div id="corp-vcard">
                <img src="<?php bloginfo("template_directory"); ?>/css/ui/footer-logo.png" alt="El Quinto Poder" title="El Quinto Poder"/>
                <p>
                    <span class="name">Fundación Democracia y Desarrollo</span>
                    <span class="dir">Roberto del Rio 1151, Providencia,</span>
                    <span class="country">Santiago - Chile</file:///C:/Users/NaNotebook-Ida/Desktop/Demandas_SIC_2013.csvspan>
                    <span class="phone">Teléfono (56-2) 2 335 91 78 </span>
                    <span class="fax">Fax (56-2) 2 334 23 61</span>
                </p>
                <a href="http://www.ida.cl" title="Diseñado y desarrollado por Ideas Digitales Aplicadas"><img class="idaLogo" src="<?php bloginfo("template_directory"); ?>/css/ui/logo_ida.png"/></a>
            </div>
            <div id="corp-extras">
                <ul>
                    <li><a href="/que-es-el-quinto-poder" title="¿Qu&eacute; es el Quinto Poder?">¿Qu&eacute; es El Quinto Poder?</a></li>
                    <li><a href="/reglas-de-la-comunidad" title="Reglas de Comunidad">Reglas de la Comunidad</a></li>
                    <li><a href="/linea-editorial" title="Línea editorial">Línea editorial</a></li>
                    <li><a href="/blog" title="Blog">Blog</a></li>
                    <li><a href="/terminos-de-uso" title="Términos de uso">Términos de uso</a></li>
                    <li><a href="/politicas-de-privacidad" title="Políticas de privacidad">Políticas de privacidad</a></li>
                    <li><a href="/preguntas-frecuentes" title="Preguntas frecuentes">Preguntas frecuentes</a></li>
                    <li><a href="/contacto" title="Contacto">Contacto</a></li>
                    <li><a href="/mapa-de-sitio" title="Mapa del sitio">Mapa del sitio</a></li>
                    
                    
                </ul>
            </div>
        </div>
    </footer>
    <!--            formulario oculto en caso de que se identifique con redes sociales -->
   <?php echo get_perfil_social(); ?>
    <!-------------------------------------------------------------------------- ANALYTICS ------------------------------>
    
</body>
<?php wp_footer();?>
</html>

