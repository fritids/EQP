<?php
header("Vary: User-Agent,Accept-Encoding"); // vary para htaccess

if($_GET['userCache']){
    if(is_user_logged_in()){ wp_logout(); }
    if( function_exists('w3tc_pgcache_flush') ) { w3tc_pgcache_flush(); }
    wp_redirect(home_url()); exit; 
}

$bloqueos = array(
    (get_field('bloquear_publicaciones', 'options') ? 'publicaciones' : ''),
    (get_field('bloquear_firmas', 'options') ? 'firmas' : ''),
    (get_field('bloquear_comentarios', 'options') ? 'comentarios' : ''),
    (get_field('bloquear_registro', 'options') ? 'registros' : ''),
    (get_field('bloquear_usuarios', 'options') ? 'usuarios' : '')
);
$bloqueos = array_filter( $bloqueos );
$bloqueo_info = !empty($bloqueos) ? 'data-bloqueos="'. implode(' ', $bloqueos) .'"' : '';

?><!DOCTYPE html>
<!--[if lt IE 7]><html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]><html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> 
<html class="no-js" data-relUrl="<?php bloginfo('stylesheet_directory'); ?>" <?php echo $bloqueo_info; ?>> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <title><?php
                if (is_archive()) { wp_title(''); echo ' | '; }
                elseif (is_search()) { echo 'B&uacutesqueda por &quot;'.wp_specialchars($s).'&quot; | '; }
                elseif (is_singular() && !is_front_page()) { wp_title(''); echo ' | '; }
                elseif (is_404()) { echo 'P&aacutegina no encontrada | '; }
                if ( is_front_page() ) {
                    bloginfo('name'); 
                    if ( get_bloginfo('description') ) { echo ' | '. get_bloginfo('description'); }
                }
                else { bloginfo('name'); }
                if ( $paged > 1 ) { echo ' - p&aacutegina '. $paged; }
            ?></title>
        <meta name="description" content="">
        <meta name="HandheldFriendly" content="True">
        <meta name="MobileOptimized" content="320">
        <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no" /> 
        <meta http-equiv="cleartype" content="on">
        
        <?php mobile_getOpenGraphMetas(); ?>
        
        <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php bloginfo('stylesheet_directory'); ?>/img/icons/touch-icon-114.png">
        <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php bloginfo('stylesheet_directory'); ?>/img/icons/touch-icon-72.png">
        <link rel="apple-touch-icon-precomposed" href="<?php bloginfo('stylesheet_directory'); ?>/img/icons/touch-icon-57.png">
        <link rel="shortcut icon" href="<?php bloginfo('stylesheet_directory'); ?>/img/icons/favicon.ico">
        
        <link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo('stylesheet_directory'); ?>/css/grid.css" >    
        <link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_directory'); ?>/css/estilo.css" >
        
        
        <!-- empieza wp_head -->
        
        
        <?php wp_head(); ?>

    </head>
