<section class="recentNews row noBorder">
            <div class="row noBorder slider-separator">
                <ul id="sliderPortadaEntradas_activas_controls" class="sliderPagination aRight hide-on-phones">
                    <li><a class="active" href="#">1</a></li>
                    <li><a href="#">2</a></li>
<!--                    <li><a href="#">3</a></li>-->
                </ul>
                <h2 class="recentEntryTitle">ENTRADAS MÁS ACTIVAS</h2>
                <div id="sliderPortadaEntradas_activas" data-control="sliderPortadaEntradas_activas_controls" data-offset-start="0" data-offset-end="12" class="editSlideWrap row noBorder ajax-swiper">
                    <ul>
                        <li>
                            <?php echo mobile_get_entradas_activas(array( 'posts_per_page' => 6, 'offset' => 0 )); ?>
                        </li>
                        <li>
                            <?php echo mobile_get_entradas_activas(array( 'posts_per_page' => 6, 'offset' => 6 )); ?>
                        </li>
<!--                        <li>
                            <?php echo mobile_get_entradas_activas(array( 'posts_per_page' => 6, 'offset' => 12 )); ?>
                        </li>-->
                    </ul>
                    <span class="pull-to-refresh first" >Desliza para ver entradas anteriores</span>
                    <span class="pull-to-refresh last" >Desliza para ver más entradas</span>
                </div>
                <a class="verMas only-on-phones evt" data-func="loadMoreEntradas" data-ordertype="activos" data-offset="12" href="#" title="Ver todas las entradas" rel="section">Cargar más</a>
            </div>
            <div class="row noBorder">
                <ul id="sliderPortadaEntradas_recientes_controls" class="sliderPagination aRight hide-on-phones">
                    <li><a class="active" href="#">1</a></li>
                    <li><a href="#">2</a></li>
                    <li><a href="#">3</a></li>
                </ul>
                <h2 class="recentEntryTitle">ENTRADAS RECIENTES</h2>
                <div id="sliderPortadaEntradas_recientes" data-control="sliderPortadaEntradas_recientes_controls" data-offset-start="0" data-offset-end="18" class="editSlideWrap row noBorder ajax-swiper">
                    <ul>
                        <li>
                            <?php
                            $params = array('items' => 6);
                            $estructura = array('class' => 'column4 recentNew unidad-discreta', 'bajada' => 300);
                            echo bloques($params, $estructura);
                            ?>
                        </li>
                        <li>
                            <?php
                            $params = array('items' => 6, 'offset' => 6);
                            $estructura = array('class' => 'column4 recentNew unidad-discreta', 'bajada' => 300);
                            echo bloques($params, $estructura);
                            ?>
                        </li>
                        <li>
                            <?php
                            $params = array('items' => 6, 'offset' => 12);
                            $estructura = array('class' => 'column4 recentNew unidad-discreta', 'bajada' => 300);
                            echo bloques($params, $estructura);
                            ?>
                        </li>
                    </ul>
                    <span class="pull-to-refresh first" >Desliza para ver entradas anteriores</span>
                    <span class="pull-to-refresh last" >Desliza para ver más entradas</span>
                </div>
                <a class="verMas only-on-phones evt" data-func="loadMoreEntradas" data-ordertype="recientes" data-offset="18" href="#" title="Ver todas las entradas" rel="section">Cargar más</a>
            </div>
        </section>