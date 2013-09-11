<?php 
/*****************/
/* Add Nav Menus */
if (function_exists('register_nav_menus')) :
	register_nav_menus( array(
		'mobile' => __('Mobile Navigation', 'mobile')
	) );
endif; 

function mobile_set_menus(){
	$menus = array(
		"mobile" => array("label" => "Pages", "class" => "pages", "rel" => "pages", "allowfallback" => true)
	);
	return $menus;
}
function mobile_menu_falback(){
	$pages = get_pages(); ?>
	<ul class="navigation menu-pages" id="pages">
    	<li class="header">
        	<a href="#"><?php _e("Pages", "obox-mobile"); ?></a>
        	<a href="#" rel=".search" class="search-button"><?php _e("Search", "obox-mobile"); ?></a>
        </li>
		<?php if (function_exists("wp_nav_menu")) :	wp_nav_menu(array('menu' => 'Mobile Nav', 'sort_column' => 'menu_order', 'theme_location' => 'mobile', 'container' => '', 'depth' => '1')); endif; ?>
    </ul>        
<?php } ?>