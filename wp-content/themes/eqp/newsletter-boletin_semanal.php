<?php 
$utms = '?utm_source='.$theLayout['acf_fc_layout'].'&amp;utm_medium=Email&amp;utm_campaign='.$post->post_name.'_'.  get_the_date("Ymd").'&amp;utm_content=';
?>
<table align="center" style="
             width: 700px;
             margin: 0 auto;
             margin-top: 30px;
             margin-bottom: 30px;
             background: #ffffff;
             border-collapse: collapse;
             font-family: sans-serif;
         " >
    <tr>
        <td>
            <table style=" border-collapse: collapse; width: 100%; font-family: sans-serif;" >
                <tr>
                    <td style=" width: 50%; padding-bottom: 20px; border-bottom: 1px dotted #999999;">
                        <a href="http://elquintopoder.cl/<?php echo $utms; ?>linkLogoHeader" title="El Quinto Poder" style="
                           text-decoration: none;
                           border: 0px;
                       " >
                            <img style="outline: none; border: 0px;" src="http://mailings.ida.cl/eqp/logo-header.png" alt="El Quinto Poder" >
                        </a>
                    </td>
                    <td style="font-family: sans-serif; text-align: right; width: 50%; padding-bottom: 20px; border-bottom: 1px dotted #999999;" >
                        <span style="font-family: sans-serif; display: inline-block; width: 168px; font-weight: bold; text-align: right; padding-bottom: 10px; border-bottom: 1px dotted #999999;" >
                            S&iacute;guenos en
                        </span>
                        <div style="padding-top: 10px;" >
                            <a style="text-decoration: none;" href="https://twitter.com/elquintopoder" title="Twitter" ><img style="outline: none; border: 0px;"  src="http://mailings.ida.cl/eqp/ico-twitter.png" alt="El Quinto Poder" ></a>
                            <a style="text-decoration: none;" href="https://www.facebook.com/elquintopoder" title="Facebook" ><img style="outline: none; border: 0px;"  src="http://mailings.ida.cl/eqp/ico-facebook.png" alt="El Quinto Poder" ></a>
                            <a style="text-decoration: none;" href="http://elquintopoder.tumblr.com/" title="Tumblr" ><img style="outline: none; border: 0px;"  src="http://mailings.ida.cl/eqp/ico-tumblr.png" alt="El Quinto Poder" ></a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style=" width: 50%; padding-top: 20px;">
                        <h1 style="
                            color: #183248;
                            font-size: 28px;
                            font-weight: bold;
                            text-transform: uppercase;
                            font-family: sans-serif;
                        " >
                            <?php echo $theLayout['encabezado_boletin_semanal']; ?>
                        </h1>
                    </td>
                    <td style=" width: 50%; text-align: right;  padding-top: 20px; font-family: sans-serif;">
                        <p>
                            <?php the_field('texto_descriptivo', $post->ID); ?>
                        </p>
                    </td>
                </tr>
            </table>
            <!-- end content header -->

            <?php if( $theLayout['imagen_destacada_boletin_semanal']  ) :
                regenerate_image_data( $theLayout['imagen_destacada_boletin_semanal'], 'news_gigante' );
                $imagenDest = wp_get_attachment_image_src($theLayout['imagen_destacada_boletin_semanal'], 'news_gigante');
            ?>
            <table height="340" style="font-family: sans-serif; border-collapse: collapse;">
                <tr>
                    <td width="700" height="340">
                        <img src="<?php echo $imagenDest[0]; ?>"/>
                    </td>
                </tr>
                <tr>
                    <td style="font-family: sans-serif; vertical-align: middle; text-align: right;" >
                        <?php if( $theLayout['frase_destacada_boletin_semanal'] ) : ?>
                        <span style="
                              text-align: left;
                              padding: 15px;
                              background: #E6E6E6;
                              font-size: 18px;
                              line-height: 140%;
                              color: #336C86;
                              font-weight: bold;
                              display: inline-block;
                              font-family: sans-serif;
                        " >
                            <?php
                                echo $theLayout['frase_destacada_boletin_semanal'];

                                if( $theLayout['id_frase_destacada_boletin_semanal'] != 0 && $theLayout['id_frase_destacada_boletin_semanal'] != '0' ){
                                    $featuredPost = get_post( $theLayout['id_frase_destacada_boletin_semanal'] );
                                    echo ', Por <a style="color: #CA4141;" href="'. home_url() .'/perfil-de-usuario/?user='. $featuredPost->post_author .'&amp;utm_source='.$theLayout['acf_fc_layout'].'&amp;utm_medium=Email&amp;utm_campaign='.$post->post_name.'_'.  get_the_date("Ymd").'&amp;utm_content=fraseDestacadaAuthor" title="Ver Perfil de '. nombre_y_apellido( $featuredPost->post_author ) .'" >'. nombre_y_apellido( $featuredPost->post_author ) .'</a>';
                                    echo ' en <a style="color: #CA4141;" href="'. get_permalink( $featuredPost->ID ) . $utms.'fraseDestacadaPost" title="'. $featuredPost->post_title .'">'. $featuredPost->post_title .'</a>';
                                }
                            ?>
                        </span>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
            <?php endif; ?>

            <?php if( !empty( $theLayout['destacados_primer_nivel_boletin_semanal'] ) ) :  ?>
            <table style=" border-collapse: collapse; margin-bottom: 20px; font-family: sans-serif;" >
                <caption style="
                         text-align: left;
                         padding: 20px 0 20px 20px;
                         outline: none;
                         width: 100%;
                         text-decoration: none;
                         font-weight: bold;
                         font-family: sans-serif;
                         color: #595959;
                         font-size: 18px;
                         text-transform: uppercase;
                 " >
                    <?php echo $theLayout['encabezado_primer_nivel_boletin_semanal']; ?>
                </caption>

                <?php $posicion_destacada=1; foreach( $theLayout['destacados_primer_nivel_boletin_semanal'] as $dest ) :  $thePostDest = get_post( $dest['id_primer_destacado_boletin_semanal'] );  ?>
                <tr>
                    <?php if( has_post_thumbnail( $thePostDest->ID ) ) :
                        $destThumb = get_the_post_thumbnail( $thePostDest->ID , 'news_destacado', array('style' => 'width: 320px; height: 200px;'));
                    ?>
                    <td style="font-family: sans-serif; padding-left: 20px; padding-right: 10px; padding-bottom: 30px; padding-top: 0px; vertical-align: top;" >
                        <?php echo $destThumb; ?>
                    </td>
                    <?php else : $destColspan = 'colspan="2"'; endif; ?>
                    <td <?php echo $destColspan; ?> style="padding-left: 10px; padding-right: 20px; padding-bottom: 30px; vertical-align: top;" >
                        <a style="font-family: sans-serif; font-size: 18px; font-weight: bold; color: #336C87; display: block; margin-bottom: 10px; text-decoration: none;" href="<?php echo get_permalink( $thePostDest->ID ).$utms."destacado_principal_".$posicion_destacada; ?>" title="Ir a <?php echo $thePostDest->post_title; ?>" >
                            <?php echo $thePostDest->post_title; ?>
                        </a>
                        <p style="font-family: sans-serif; line-height: 160%;" >
                            <?php
                                $bajada = get_field('texto_destacado', $thePostDest->ID) ? get_field('texto_destacado', $thePostDest->ID) : $thePostDest->post_content;
                                echo cortar( $bajada , 300); 
                            ?>
                        </p>
                    </td>
                </tr>
                <?php $posicion_destacada++; endforeach; ?>

            </table>
            <?php endif; ?>

            <?php if( !empty($theLayout['destacados_segundo_nivel_boletin_semanal']) ) : ?>
            <table style="font-family: sans-serif; border-collapse: collapse; margin-bottom: 20px;" >
                <?php if( $theLayout['encabezado_segundo_nivel_boletin_semanal'] ) : ?>
                <caption style="
                         text-align: left;
                         padding: 20px 0 20px 20px;
                         outline: none;
                         width: 100%;
                         text-decoration: none;
                         font-weight: bold;
                         font-family: sans-serif;
                         color: #595959;
                         font-size: 18px;
                         text-transform: uppercase;
                 " >
                    <?php echo $theLayout['encabezado_segundo_nivel_boletin_semanal']; ?>
                </caption>
                <?php endif; ?>
                <?php get_destacados_newsletter( $theLayout['destacados_segundo_nivel_boletin_semanal'], 'boletin_semanal', true, $utms ); ?>
            </table>
            <?php endif; ?>

            <?php if( !empty($theLayout['destacados_tercer_nivel_boletin_semanal']) ) : ?>
            <table style="font-family: sans-serif; border-collapse: collapse; margin-bottom: 20px;" >
                <?php if( $theLayout['encabezado_destacados_tercer_nivel_boletin_semanal'] ) : ?>
                <caption style="
                         text-align: left;
                         padding: 20px 0 20px 20px;
                         outline: none;
                         width: 100%;
                         text-decoration: none;
                         font-weight: bold;
                         font-family: sans-serif;
                         color: #595959;
                         font-size: 18px;
                         text-transform: uppercase;
                 " >
                    <?php echo $theLayout['encabezado_destacados_tercer_nivel_boletin_semanal']; ?>
                </caption>
                <?php endif; ?>
                <?php get_dest_tercer_nivel( $theLayout['destacados_tercer_nivel_boletin_semanal'], true, $utms); ?>
            </table>
            <?php endif; ?>
        </td>
    </tr>
</table>