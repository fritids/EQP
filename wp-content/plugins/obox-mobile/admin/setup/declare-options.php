<?php
function mobile_general_options (){	
	$ocmx_tabs = array(
					array(
						  "option_header" => "General",
						  "use_function" => "fetch_mobile_options",
						  "function_args" => "general_site_options",
						  "ul_class" => "admin-block-list clearfix"
					  ),
					array(
						"option_header" => "Logo &amp; Images",
						"use_function" => "fetch_mobile_options",						
						"function_args" => "image_options",
						  "ul_class" => "admin-block-list clearfix"
					  ),
					array(
						  "option_header" => "Thumbnails",
						  "use_function" => "fetch_mobile_options",
						  "function_args" => "thumbnail_options",
						  "ul_class" => "admin-block-list clearfix"
					  ),
					array(
						  "option_header" => "Post &amp; Discussion",
						  "use_function" => "fetch_mobile_options",
						  "function_args" => "post_options",
						  "ul_class" => "admin-block-list clearfix"
					  ),
					  array(
						  "option_header" => "jQuery Mobile",
						  "use_function" => "fetch_mobile_options",
						  "function_args" => "jquerymobile",
						  "ul_class" => "admin-block-list clearfix"
					  ),
					array(
						  "option_header" => "Plugin Compatibility",
						  "use_function" => "fetch_mobile_options",
						  "function_args" => "plugins",
						  "ul_class" => "admin-block-list clearfix"
					  )
				);
	$ocmx_container = new OCMX_mobile_Container();
	$ocmx_container->load_container("General Options", $ocmx_tabs, "Save Changes");
};

function mobile_image_options (){	
	$ocmx_tabs = array(
					array(
						"option_header" => "Logo &amp; Images",
						"use_function" => "fetch_mobile_options",						
						"function_args" => "image_options",
						  "ul_class" => "admin-block-list clearfix"
					  )
				);
	$ocmx_container = new OCMX_mobile_Container();
	$ocmx_container->load_container("Logo &amp; Images", $ocmx_tabs, "Save Changes", $note);
};
function mobile_theme_options (){	
	$ocmx_tabs = array(
					array(
						"option_header" => "Themes",
						"use_function" => "mobile_theme_list",						
						"function_args" => "",
						"ul_class" => "clearfix",
					  	"base_button" => array("id" => "theme-list-edit-1", "rel" => "", "href" => "#", "html" => "Edit List"),
					  	"top_button" => array("id" => "theme-list-edit", "rel" => "", "href" => "#", "html" => "Edit List")
					  )
				);
	$note = "We recommend that you use a WebKit browser, such as Google Chrome or Safari to preview themes.";
	$ocmx_container = new OCMX_mobile_Container();
	$ocmx_container->load_container("Themes", $ocmx_tabs, "", $note);
};
function mobile_advert_options(){	
	global $advert_areas;
	$ocmx_tabs = array();
	foreach($advert_areas as $ad_area => $option) :
		array_unshift($ocmx_tabs,
					array(
						  "option_header" => $ad_area,
						  "use_function" => "fetch_mobile_options",
						  "function_args" => $option."_adverts",
						  "ul_class" => "admin-block-list advert clearfix"
					  )
				);
	endforeach;
	
	$ocmx_container = new OCMX_mobile_Container();
	$ocmx_container->load_container("Adverts", $ocmx_tabs);
};
function mobile_plugin_options (){	
	$ocmx_tabs = array(
					array(
						"option_header" => "Plugin Compatibility",
						"use_function" => "fetch_mobile_options",
						"function_args" => "plugins",
						"ul_class" => "admin-block-list clearfix"
					  )
				);
	$ocmx_container = new OCMX_mobile_Container();
	$ocmx_container->load_container("Plugin Compatibility", $ocmx_tabs, "Save Changes");
};

function mobile_upgrade_options (){	
	$ocmx_tabs = array(
					array(
						 "option_header" => "Update Plugin",
						  "use_function" => "upgrade_license_options",
						  "function_args" => "",
						  "ul_class" => "admin-block-list clearfix"
					  )
				);
	$ocmx_container = new OCMX_mobile_Container();
	$ocmx_container->load_container("Update Plugin", $ocmx_tabs, "");
};

?>