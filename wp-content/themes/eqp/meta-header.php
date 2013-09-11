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
<html <?php language_attributes(); ?> class="no-js" <?php echo $bloqueo_info; ?>>
    <head>
        <meta charset="UTF-8">
        <title>
            <?php
                if (is_archive()) { wp_title(''); echo ' | '; }
                elseif (is_search()) { echo 'B&uacutesqueda por &quot;'.wp_specialchars($s).'&quot; | '; }
                elseif (is_singular() && !is_front_page()) { wp_title(''); echo ' | '; }
                elseif (is_404()) { echo 'P&aacutegina no encontrada | '; }
                if ( is_front_page() ) {
                    bloginfo('name'); 
                    if ( get_bloginfo('description') ) { echo ' | '. get_bloginfo('description'); }
                }
                else { bloginfo('name'); }
                if ($paged>1) { echo ' - p&aacutegina '. $paged; }
            ?>
        </title>
        
        <meta name="Author" content="<?php echo get_bloginfo('name'); ?>"/>
        <meta name="Description" content="<?php if( is_singular() ) { if($post->post_content) { echo cortar($post->post_content, 150); } else { bloginfo('description'); } } else { bloginfo('description'); } ?>"/>
        <meta name="robots" content="index, follow" /> 
        <meta name="google-site-verification" content="578Z7nYeK_ribcJ_coeLyKac6tys7z4YznZRixspdHo" />
        <?php getOpenGraphMetas(); ?>

        <link rel="shortcut icon" href="<?php bloginfo("template_directory"); ?>/favicon.png" />

        <link rel="stylesheet" media="all" href="<?php bloginfo("template_directory"); ?>/css/reset.css">
        <link rel="stylesheet" media="all" href="<?php bloginfo("template_directory"); ?>/css/structure.css">
        <link rel="stylesheet" media="all" href="<?php bloginfo("template_directory"); ?>/css/typo.css">
        <link rel="stylesheet" media="all" href="<?php bloginfo("template_directory"); ?>/css/style.css">
        <link href='//fonts.googleapis.com/css?family=PT+Sans:400,700,400italic,700italic' rel='stylesheet' type='text/css'>
        
                
        <?php wp_head(); ?>

                                     
        <script src="<?php bloginfo("template_directory"); ?>/js/jquery-ui-1.8.22.custom.min.js" ></script>
        <script src="<?php bloginfo("template_directory"); ?>/js/jquery.placeholder.min.js" ></script>
        <script src="<?php bloginfo("template_directory"); ?>/js/jquery.rut.js" ></script>
        <script src="<?php bloginfo("template_directory"); ?>/js/jquery.form.js" ></script>
        <script src="<?php bloginfo("template_directory") ?>/js/script.js" ></script>
        <script src="<?php bloginfo("template_directory") ?>/js/siteHandler.js" ></script>
        
        <!--[if (gte IE 6)&(lte IE 8)]>
            <script type="text/javascript" src="<?php bloginfo("template_directory"); ?>/js/selectivizr-min.js"></script>
        <![endif]--> 
        
        <!--[if lt IE 9]>
            <link rel="stylesheet" media="all" href="<?php bloginfo("template_directory"); ?>/css/ie.css">
            <script src="<?php bloginfo("template_directory") ?>/js/ie.js" ></script>
        <![endif]-->
        
        <!--[if IE 9]>
            <link rel="stylesheet" media="all" href="<?php bloginfo("template_directory"); ?>/css/ie9.css">
        <![endif]-->
        
        
        <?php if(is_single()) : ?>
        <script type="text/javascript">
          window.___gcfg = {lang: 'es-419'};

          (function() {
            var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
            po.src = 'https://apis.google.com/js/plusone.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
          })();
        </script>
        <?php endif; ?>
        
        <?php  if( ($_GET['var'] && $_GET['userid']) ){ // Google Analytics para el usuario recien registrado ?>
            <script type="text/javascript">
                $(function(){ 
                    _gaq.push(['_setCustomVar', 1, 'Usuarios Logueados', 'Si', 2]);
                    _gaq.push(['_trackPageview']);
                });
            </script>
        <?php } ?>        
</head>