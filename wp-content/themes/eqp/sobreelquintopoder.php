<?php
/*
* Template Name: Sobre el Quinto Poder
*/
get_header(); the_post(); ?>
<section class="topEntry entryDestacada">
    <div class="row acciones noBorder">
        <?php echo mobile_breadcrumbs( 'Sobre el Quinto Poder' ); ?>
        <h1 class="aboutEqp">Sobre el Quinto Poder</h1>
    </div>
</section>
<div id="contAbout" class="clearfix">
    <aside class="aboutNav aLeft hide-on-phones">
        <ul id="sobreeqp-nav">
            <li><a class="activo" href="#que-es-el-quinto-poder">¿Qué es El Quinto Poder?</a></li>
            <li><a class="" href="#reglas-de-la-comunidad">Reglas de la Comunidad</a></li>
            <li><a class="" href="#linea-editorial">Línea Editorial</a></li>
            <li><a class="" href="#condiciones-de-uso">Términos de Uso</a></li>
            <li><a class="" href="#politicas-de-privacidad">Políticas de Privacidad</a></li>
            <li><a class="" href="#preguntas-frecuentes">Preguntas Frecuentes</a></li>
        </ul>
    </aside>
    <section class="contentAbout last">
        <article class="about-page-content" id="que-es-el-quinto-poder">
            <?php mobile_get_page_content( 'que-es-el-quinto-poder' ); ?>
        </article>
        <article class="about-page-content" id="reglas-de-la-comunidad">
            <?php mobile_get_page_content( 'reglas-de-la-comunidad' ); ?>
        </article>
        <article class="about-page-content" id="linea-editorial">
            <?php mobile_get_page_content( 'linea-editorial' ); ?>
        </article>
        <article class="about-page-content" id="condiciones-de-uso">
            <?php mobile_get_page_content( 'terminos-de-uso' ); ?>
        </article>
        <article class="about-page-content" id="politicas-de-privacidad">
            <?php mobile_get_page_content( 'politicas-de-privacidad' ); ?>
        </article>
        <article class="about-page-content" id="preguntas-frecuentes">
            <?php mobile_get_page_content( 'preguntas-frecuentes' ); ?>
        </article>
    </section>
</div>    
<?php get_footer(); ?>	