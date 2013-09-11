<?php
/*
   	Plugin Name: Obox Mobile Framework
   	Plugin URI: http://obox-design.com
   	Description: A framework which formats your site with a mobile theme for mobile devices.
	Author: Obox Design
	Version: 1.5.3
	Author URI: http://www.obox-design.com
*/

/****************************/
/* Set Directories and Files*/
$wp_plugin_dir = ABSPATH."wp-content/plugins/";

$plugin_dir = ABSPATH."wp-content/plugins/obox-mobile/";
$plugin_url = get_bloginfo('wpurl')."/wp-content/plugins/obox-mobile/";
if(!is_dir($plugin_dir)) :
	$wp_plugin_dir = ABSPATH."wp-content/mu-plugins/";
	$plugin_dir = ABSPATH."wp-content/mu-plugins/obox-mobile/";
	$plugin_url = get_bloginfo('wpurl')."/wp-content/mu-plugins/obox-mobile/";
endif;

define('OCMXMOBILEID', '1330');
define('OCMXMOBILE_VER', '1.5.3' );
define('TEMPLATEURI', get_bloginfo("template_directory"));
define('WPPLUGINDIR', $wp_plugin_dir);
define('OCMXMOBILEDIR', $plugin_dir);
define('OCMXMOBILEURL', $plugin_url);

/***********************************/
/* Run when we activate the plugin */
function mobile_setup(){
	include(OCMXMOBILEDIR."admin/setup/theme-options.php");
	include(OCMXMOBILEDIR."admin/includes/functions.php");
	
	global $mobile_theme_options;
	foreach($mobile_theme_options as $theme_option => $value)
		{
			if(function_exists("mobile_reset_option")):
				mobile_reset_option($theme_option);
			endif;
		}
}

register_activation_hook( __FILE__, 'mobile_setup' );

/***********************/
/* Include admin files */
function mobile_includes(){
	$include_folders = array("functions/", "admin/interface/", "admin/includes/", "admin/setup/");
	include_once ("admin/folder-class.php");
	include_once ("admin/load-includes.php");
}
add_action("plugins_loaded", "mobile_includes");

/***********************/
/* Add OCMX Menu Items */
function mobile_add_admin() {

 	global $add_general_page,$add_themes_page, $add_adverts_page,$add_update_page ;
 	add_object_page("Obox Mobile", "Obox Mobile", 'edit_themes', basename(__FILE__), '', 'http://obox-design.com/images/ocmx-favicon.png');
	
	$add_general_page = add_submenu_page(basename(__FILE__), "General Options", "General", "administrator",  basename(__FILE__), 'mobile_general_options');
	$add_themes_page = add_submenu_page(basename(__FILE__), "Themes", "Themes", "administrator",  "mobile-themes", 'mobile_theme_options');
	$add_adverts_page = add_submenu_page(basename(__FILE__), "Adverts", "Adverts", "administrator",  "mobile-adverts", 'mobile_advert_options');
	$add_update_page = add_submenu_page(basename(__FILE__), "Update", "Update", "administrator",  "mobile-upgrade", 'mobile_upgrade_options');
}
add_action('admin_menu', 'mobile_add_admin');

function my_admin_bar_menu() {
	global $wp_admin_bar;
	if ( !is_super_admin() || !is_admin_bar_showing() )
		return;
		
	$wp_admin_bar->add_menu(array('id' => 'obox-mobile', 'title' => __( 'Obox Mobile'), 'href' => admin_url( 'admin.php?page=mobile.php')) );
	$wp_admin_bar->add_menu( array('parent' => 'obox-mobile', 'title' => __( 'General'), 'href' => admin_url( 'admin.php?page=mobile.php')) );
	$wp_admin_bar->add_menu( array('parent' => 'obox-mobile', 'title' => __( 'Themes'), 'href' => admin_url( 'admin.php?page=mobile-themes')) );
	$wp_admin_bar->add_menu( array('parent' => 'obox-mobile', 'title' => __( 'Adverts'), 'href' => admin_url( 'admin.php?page=mobile-adverts')) );
	$wp_admin_bar->add_menu( array('parent' => 'obox-mobile', 'title' => __( 'Update'), 'href' => admin_url( 'admin.php?page=mobile-upgrade')) );
}
add_action('admin_bar_menu', 'my_admin_bar_menu', 70);

/****************************/
/* Add Localization Support */
load_plugin_textdomain( 'obox-mobile', false, dirname( plugin_basename( __FILE__ ) ) . '/admin/lang/' );

/****************************************/
/* Begin OCMX Mobile Checks & Implement */
function begin_ocmx_mobile(){
	global $ocmx_mobile_class;
	$ocmx_mobile_class = new OCMX_Mobile();
	$ocmx_mobile_class->initiate();
}
add_action( 'plugins_loaded', 'begin_ocmx_mobile' );

/****************************************/
/* Disable Unwanted Plugins*/
function mobile_check_plugins(){
	global $ocmx_mobile_class; 
	if(!is_admin() && $ocmx_mobile_class->allow_mobile() === true && $ocmx_mobile_class->site_style() == "mobile") :
		$plugins = new mobile_plugin_class();
		$plugins->disable_plugins();
	endif;
}
add_action("plugins_loaded", "mobile_check_plugins");

/*************************/
/* Add Menus and scripts */
add_filter('admin_menu', 'mobile_add_admin');
add_action("init", "mobile_add_scripts"); ?>