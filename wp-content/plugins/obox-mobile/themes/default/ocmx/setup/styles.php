<?php
function mobile_default_add_styles()
	{
		wp_register_style("color-styles", get_bloginfo('stylesheet_url'));
		wp_enqueue_style( "color-styles");		
				
		wp_register_style("custom", get_bloginfo("template_directory")."/custom.css");
		wp_enqueue_style( "custom");
	}
?>