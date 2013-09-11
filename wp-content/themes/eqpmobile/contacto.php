<?php
/*
* Template Name: Contacto
*/

get_header(); the_post(); ?>
        <article>
            <header class="topEntry">
                <div class="row topEntryWrap">
                    <?php echo mobile_breadcrumbs(); ?>
                    <h1 class="perfilTitle column10">
                        <?php the_title(); ?>
                    </h1>
                </div>
            </header>
            <div class="row entryWrap backAll">
                <aside class="column4">
                    <p class="infoContact">
                    <strong>Fundación Democracia y Desarrollo</strong>
                    Roberto del Río 1151, Providencia<br />
                    Santiago - Chile<br />
                    Teléfono: (56-2) 2 335 91 78<br />
                    Fax: (56-2) 2 334 23 61</p>
                    <div class="map hide-on-phones">
                        <iframe width="100%" height="400" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.cl/maps?f=q&amp;source=s_q&amp;hl=es-419&amp;geocode=&amp;q=Roberto+del+R%C3%ADo+1151,+Providencia&amp;aq=&amp;sll=-33.668298,-70.363372&amp;sspn=1.241248,2.705383&amp;ie=UTF8&amp;hq=&amp;hnear=Doctor+Roberto+del+R%C3%ADo+1151,+Providencia,+Santiago,+Regi%C3%B3n+Metropolitana&amp;t=m&amp;z=14&amp;ll=-33.428952,-70.600015&amp;output=embed"></iframe>
                        <br />
                        <small>
                            <a class="lookMaps" href="https://maps.google.cl/maps?f=q&amp;source=embed&amp;hl=es-419&amp;geocode=&amp;q=Roberto+del+R%C3%ADo+1151,+Providencia&amp;aq=&amp;sll=-33.668298,-70.363372&amp;sspn=1.241248,2.705383&amp;ie=UTF8&amp;hq=&amp;hnear=Doctor+Roberto+del+R%C3%ADo+1151,+Providencia,+Santiago,+Regi%C3%B3n+Metropolitana&amp;t=m&amp;z=14&amp;ll=-33.428952,-70.600015" style="color:#0000FF;text-align:left">Ver mapa más grande</a>
                        </small>
                    </div>
                </aside>
                <section class="contactForm column8 last">
                    <form id="main-contact-form" class="contacto" action="/" method="post">
                        <label for="contacto_nombre">Nombre</label>
                        <input type="text" required placeholder="Ej: Fernando Silva" value="" name="contacto_nombre" id="contacto_nombre">
                        <label for="contacto_email">E-mail</label>                   
                        <input type="email" required placeholder="Ej: ejemplo@email.com" value="" name="contacto_email" id="contacto_email">
                        <label for="contacto_asunto">Asunto</label>
                        <input type="text" required placeholder="Me gusta su sitio!" value="" name="contacto_asunto" id="contacto_asunto">
                        <label for="contacto_mensaje">Mensaje</label>
                        <textarea placeholder="Escribe el mensaje que nos quieres enviar..." required name="contacto_mensaje" id="contacto_mensaje"></textarea>
                        <input class="sendForm aRight disabled" disabled type="submit" value="Enviar">
                    </form>
                </section>
            </div>
        </article>
     <?php get_footer(); ?> 