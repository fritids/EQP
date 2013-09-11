<?php get_header();?>
        
        <section class="topEntry entryDestacada">
            <div class="destacadaWrap row">
                <ul  id="slideHomeControl"  class="sliderPagination aRight">
                    <li><a class="active evt" data-func="mainSliderControls" href="#" data-index="0" rel="nofollow">1</a></li>
                    <li><a class="evt" data-func="mainSliderControls" href="#" data-index="1"  rel="nofollow">2</a></li>
                    <li><a class="evt" data-func="mainSliderControls" href="#" data-index="2"  rel="nofollow">3</a></li>
                    <li><a class="evt" data-func="mainSliderControls" href="#" data-index="3"  rel="nofollow">4</a></li>
                    <li><a class="evt" data-func="mainSliderControls" href="#" data-index="4"  rel="nofollow">5</a></li>
                    <li><a class="evt" data-func="mainSliderControls" href="#" data-index="5"  rel="nofollow">6</a></li>
                </ul>
                <h2 class="destEntry">ENTRADAS DESTACADAS</h2>
                
                <div id="slideHome" class="sliderContainer column12 slide" data-control="slideHomeControl">
                    <ul class="destaSlides">
                       <?php echo SlideHomeMobile();?>
                    </ul>
                </div> 
            </div>
        </section>
        <section class="recentNews row">
            <ul id="slideEntradasControl" class="sliderPagination aRight hide-on-phones">
                <li><a class="active" href="#" title="Primer slide" rel="nofollow">1</a></li>
                <li><a href="#" title="Segundo slide" rel="nofollow">2</a></li>
                <li><a href="#" title="Tercer slide" rel="nofollow">3</a></li>
            </ul>
            <h2 class="recentEntryTitle">ENTRADAS RECIENTES</h2>
            <div id="slideEntradas" class="recientesWrap row slide" data-control="slideEntradasControl">
                <ul class="clearfix">
                    <li class="clearfix">
                        <?php
                            $params = array('items'=>6);
                            $estructura = array('bajada' => 160,'class'=>'unidad-discreta recentNew column4' );
                            echo bloques($params, $estructura);
                        ?>
                    </li>
                    <li class="clearfix hide-on-phones">
                       <?php
                            $params = array('items'=>6, "offset"=>6);
                            $estructura = array('bajada' => 160,'class'=>'unidad-discreta recentNew column4' );
                            echo bloques($params, $estructura);
                        ?>
                    </li>
                    <li class="clearfix hide-on-phones">
                       <?php
                            $params = array('items'=>6, "offset"=>12);
                            $estructura = array('bajada' => 160,'class'=>'unidad-discreta recentNew column4' );
                            echo bloques($params, $estructura);
                        ?>
                    </li>
                </ul>
            </div> 
            <a class="verMas" href="/entradas/" title="Ver todas las entradas" rel="section">Ver todas las entradas</a>
        </section>
        <section class="accionesDestacadas">
            <div class="row acciones">
                <h2 class="accDestacadas">ACCIONES DESTACADAS</h2>
                    <?php echo mobile_get_actions( array( 'type' => 'featured' ) ); ?>
            </div>
            <a class="verMas" href="/acciones-home" title="Ver todas las acciones" rel="section">Ver todas las acciones</a>
        </section>
     <?php get_footer();?>	



