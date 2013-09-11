<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="apple-mobile-web-app-capable" content="yes" />
<link rel="apple-touch-icon" href="<?php echo get_option("mobile_app_icon"); ?>" />
<link rel="apple-touch-startup-image" href="<?php echo get_option("mobile_app_splash"); ?>">  
<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<?php mobile_site_title();
    mobile_meta_keywords();
    mobile_meta_description(); ?>
  
<?php if(get_option("ocmx_rss_url")) : ?>
    <link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php echo get_option("ocmx_rss_url"); ?>" />
<?php else : ?>
    <link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<?php endif; ?>
<?php wp_head() ?>    
<?php mobile_custom_bg(); ?>
</head>
<body class="show_normal">
    <div data-role="page">
        
       	<?php mobile_advert("header"); ?>
        <div id="header-container">
            <div id="header">
                <div class="logo">
                    <?php if(get_option("mobile_custom_logo") != "") : ?>
                        <a href="<?php bloginfo('url'); ?>" class="logo-mark">
                            <img src="<?php echo get_option("mobile_custom_logo"); ?>" alt="<?php bloginfo('name'); ?>" />
                        </a>
                    <?php endif; ?>
                    <h1><a href="<?php bloginfo('url'); ?>"><?php bloginfo('name'); ?></a></h1>
                </div>
               
            </div>            
        </div>
       
        <div id="menu-container" class="menu-container">
            <div class="search <?php if(isset($_GET['menu']) && $_GET['menu'] == "search") : ?>active<?php endif; ?>" id="search">
                <form action="<?php bloginfo('url'); ?>" method="get">
                    <input type="text" name="s" id="s" class="search-input" maxlength="50" value="<?php if(is_search()):  the_search_query(); endif; ?>" />
                    <input value="<?php _e("Search", "obox-mobile"); ?>" type="submit">
                </form>
            </div>
       		<?php $locations = get_nav_menu_locations(); ?>
            <?php $mobile_menus = mobile_set_menus(); ?>
             <?php foreach($mobile_menus as $menu => $menuoption) :
				if (isset( $locations[$menu] ) && wp_get_nav_menu_object( $locations[$menu] ) ) :
						$getmenu = wp_get_nav_menu_object( $locations[ $menu ] );  ?>
                    <ul class="navigation menu">
                    	<li class="header"><a href="#"><?php echo $getmenu->name; ?></a> <a href="#" rel=".search" class="search-button"><?php _e("Search", "obox-mobile"); ?></a></li>
                    	<?php wp_nav_menu(array('menu' => $getmenu->name, 'sort_column' => 'menu_order', 'theme_location' => $menu, 'container' => false, 'depth' => '3')); ?>
                    </ul>     
            <?php elseif($menuoption["allowfallback"] == true) :
					mobile_menu_falback();
				endif;
			endforeach; ?>  
            </ul>  
        </div>