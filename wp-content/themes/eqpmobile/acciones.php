<?php
/*
Template Name: Acciones Home
*/

get_header();?>
        <section class="topEntry entryDestacada">
            <div class="row acciones noBorder acciones-destacadas">
                <h2>ACCIONES DESTACADAS</h2>
                <?php echo mobile_get_actions( array( 'type' => 'featured' ) ); ?>
            </div>
        </section>
        <section class="recentNews row">
            <div class="row noBorder">
                <ul id="acciones_adherencia_controls" class="sliderPagination aRight hide-on-phones">
                    <li><a class="active" href="#">1</a></li>
                    <li><a href="#">2</a></li>
                </ul>
                <h2 class="accDestacadas">MAYOR ADHERENCIA</h2>
                <div id="slide_acciones_adherencia" data-control="acciones_adherencia_controls" class="editSlideWrap row">
                    <ul>
                        <?php echo mobile_get_actions( array( 'type' => 'masAdherencia' ) ); ?>
                    </ul>
                </div>
            </div>
        </section>
        <section class="recentNews row noBorder">

            <div class="row">
                <ul id="acciones_recientes_controls" class="sliderPagination aRight hide-on-phones">
                    <li><a class="active" href="#">1</a></li>
                    <!--<li><a href="#">2</a></li>-->
                </ul>
                <h2 class="accDestacadas">MÁS RECIENTES</h2>
                <div id="slide_acciones_recientes" data-control="acciones_recientes_controls" class="editSlideWrap row">
                    <ul>
                        <?php echo mobile_get_actions( array( 'type' => 'masRecientes' ) ); ?>
                    </ul>
                </div>
            </div>


        </section>
     <?php get_footer();?>	
	


