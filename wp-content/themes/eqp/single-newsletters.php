<?php if( function_exists('w3tc_pgcache_flush') ) { w3tc_pgcache_flush(); } ?><!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    </head>
    <body style="margin: 0px; padding: 0px; font-family: sans-serif; color: #333333; font-size: 12px;" >
        <table style="
               border-collapse: collapse;
               width: 100%;
               font-family: sans-serif;
        " >
            <tr>
                <td style="
                padding-top: 40px;
                padding-bottom: 20px;
                background: #e6e6e6;
                text-align: center;
                font-size: 12px;
                color: #7f7f7f;
                border-bottom: 5px solid #183248;
                ">
                    <span style="display: block; margin-bottom: 10px; font-family: sans-serif;" >Si no puedes ver este mensaje correctamente <a style="color: #005183;" href="<?php the_permalink(); ?>" title="er la versi&oacute;n web">Prueba la versi&oacute;n web</a></span>
                </td>
            </tr>
        </table>
        <!-- End PRE HEADER -->
        <?php 
            $theMetaInfo = get_field('newsletter_type_info', $post->ID);
            $theLayout = $theMetaInfo[0];
            require('newsletter-'. $theLayout['acf_fc_layout'] .'.php');
        ?>
        <!-- End CONTENT -->
        <table style="border-collapse: collapse; width: 100%;" >
            <tr>
                <td style="
                    padding-top: 20px;
                    padding-bottom: 40px;
                    background: #e6e6e6;
                    border-top: 5px solid #183248;
                ">
                    <table style=" border-collapse: collapse; width: 700px; margin: 0 auto;" >
                        <tr>
                            <td>
                                <a href="http://elquintopoder.cl" title="El Quinto Poder" style="
                                   text-decoration: none;
                                   border: 0px;
                               " >
                                    <img  style="outline: none; border: 0px;"  src="http://mailings.ida.cl/eqp/logo-footer.png" alt="El Quinto Poder" >
                                </a>
                            </td>
                            <td style="
                                font-size: 12px;
                                color: #7f7f7f;
                                padding-left: 20px;
                                font-family: sans-serif;
                                " >
                                <strong>Fundación Democracia y Desarrollo</strong><br>
                                <strong>Licencia</strong> CC:BY<br>
                                <strong>Dirección postal:</strong> Roberto del Río,Providencia, Santiago 7510359 | Chile
                            </td>
                            <td style="text-align: right; padding-left: 20px; font-family: sans-serif;" >
                                <span style="display: inline-block; width: 168px; font-weight: bold; text-align: right; padding-bottom: 10px; border-bottom: 1px dotted #999999;" >
                                    S&iacute;guenos en
                                </span>
                                <div style="padding-top: 10px;" >
                                    <a style="text-decoration: none;" href="https://twitter.com/elquintopoder" title="Twitter" ><img style="outline: none; border: 0px;"  src="http://mailings.ida.cl/eqp/ico-twitter.png" alt="El Quinto Poder" ></a>
                                    <a style="text-decoration: none;" href="https://www.facebook.com/elquintopoder" title="Facebook" ><img style="outline: none; border: 0px;"  src="http://mailings.ida.cl/eqp/ico-facebook.png" alt="El Quinto Poder" ></a>
                                    <a style="text-decoration: none;" href="http://elquintopoder.tumblr.com/" title="Tumblr" ><img style="outline: none; border: 0px;"  src="http://mailings.ida.cl/eqp/ico-tumblr.png" alt="El Quinto Poder" ></a>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <?php 
            if( is_user_logged_in() && current_user_can('manage_options') ){
                echo get_html_content_box( get_permalink( $post->ID ) );
            }
        ?>
    </body>
</html>