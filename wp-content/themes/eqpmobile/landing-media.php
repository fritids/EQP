<?php

the_post();

 switch ( $post->post_name ) {
    case 'fotos':
        $pageLabel = 'Foto';
        $activeLabel = 'ACTIVAS';
        $typeClass = 'fotoIcon';
    break;
    default :
        $pageLabel = 'Video';
        $activeLabel = 'ACTIVOS';
        $typeClass = 'videoIcon';
    break;
}

?>
        <section class="topEntry entryDestacada">
            <div class="row fotos">
                <h2><?php echo $pageLabel; ?> DEL DÍA</h2>
               	<div class="sliderContainer column12">
                    <?php echo mobile_get_mediadeldia( 'post_' . $post->post_name ); ?>
                </div> 
            </div>
        </section>
        <section class="row fotos">
            <ul id="sliderPortadaMedia_activos_controls" class="sliderPagination aRight hide-on-phones">
                <li><a class="active" href="#">1</a></li>
                <li><a href="#">2</a></li>
                <!--<li><a href="#">3</a></li>-->
            </ul>
            <h2 class="<?php echo $typeClass; ?>"><?php echo $post->post_name; ?> MÁS <?php echo $activeLabel; ?></h2>

            <div id="sliderPortadaMedia_activos" data-control="sliderPortadaMedia_activos_controls"  class="slideFotoWrap slider-separator" >
                <ul>
                    <li>
                        <?php
                            $settings = array( 'post_type' => 'post_' . $post->post_name, 'posts_per_page' => 3, 'offset' => 0 );
                            echo mobile_get_activemedia( $settings );
                        ?>
                    </li>
                    <li>
                        <?php
                            $settings['offset'] = 3;
                            echo mobile_get_activemedia( $settings );
                        ?>
                </ul>
            </div>
            
            
            <ul id="sliderPortadaMedia_recientes_controls" class="sliderPagination aRight hide-on-phones">
                <li><a class="active" href="#">1</a></li>
                <li><a href="#">2</a></li>
            </ul>
            <h2 class="<?php echo $typeClass; ?>"><?php echo $post->post_name; ?> RECIENTES</h2>

            <div id="sliderPortadaMedia_recientes" data-control="sliderPortadaMedia_recientes_controls"  class="slideFotoWrap" >
                <ul>
                    <li>
                        <?php
                            $settings = array( 'post_type' => 'post_' . $post->post_name, 'posts_per_page' => 3, 'offset' => 0 );
                            echo mobile_get_lastmedia( $settings );
                        ?>
                    </li>
                    <li>
                        <?php
                            $settings['offset'] = 3;
                            echo mobile_get_lastmedia( $settings );
                        ?>
                    </li>
                </ul>
            </div>
        </section>