<?php 
$utms = '?utm_source='.$theLayout['acf_fc_layout'].'&amp;utm_medium=Email&amp;utm_campaign='.$post->post_name.'_'.  get_the_date("Ymd").'&amp;utm_content=';
?>
<table  align="center"  cellpadding="0" cellpadding="0"  style="
             width: 700px;
             margin: 0 auto;
             margin-top: 30px;
             margin-bottom: 30px;
             background: #ffffff;
             border-collapse: collapse;
         " >
    <tr>
        <td>
            <table style=" border-collapse: collapse;  width: 100%;" >
                <tr>
                    <td style=" width: 50%; padding-bottom: 20px; border-bottom: 1px dotted #999999;">
                        <a href="http://elquintopoder.cl/" title="El Quinto Poder" style="
                           text-decoration: none;
                           border: 0px;
                       " >
                            <img style="outline: none; border: 0px;" src="http://mailings.ida.cl/eqp/logo-header.png" alt="El Quinto Poder" >
                        </a>
                    </td>
                    <td style="text-align: right; width: 50%; padding-bottom: 20px; border-bottom: 1px dotted #999999;" >
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
                            <?php echo $theLayout['encabezado_especial_newsletter']; ?>
                        </h1>
                    </td>
                    <td style=" width: 50%; text-align: right;  padding-top: 20px;">
                        <p style="font-family: sans-serif;">
                            <?php the_field('texto_descriptivo', $post->ID); ?>
                        </p>
                    </td>
                </tr>
            </table>
            <!-- end content header -->

            <?php echo get_superDest_news( $theLayout['destacado_principal_especial_newsletter'] ); ?>

            <?php if( !empty($theLayout['destacados_secundarios_especial_newsletter']) ) : ?>
            <table style=" border-collapse: collapse; margin-bottom: 20px; font-family: sans-serif;" >
                <?php get_destacados_newsletter( $theLayout['destacados_secundarios_especial_newsletter'], 'especial_newsletter', true, $utms ); ?>
            </table>
            <?php endif; ?>
        </td>
    </tr>
</table>