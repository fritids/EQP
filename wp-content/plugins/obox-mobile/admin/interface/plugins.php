<?php function mobile_plugins(){
	$plugins = new mobile_plugin_class();
	$plugins->plugin_list();	
};
add_action("mobile_plugins", "mobile_plugins"); ?>