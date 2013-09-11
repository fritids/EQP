<?php
function mobile_default_add_scripts()
	{
		global $themeid;
		if(!strpos($_SERVER["REQUEST_URI"], "wp-login") && (!is_admin() || (is_admin() && is_user_logged_in()))):
			//Add support for 2.9 and 3.0 functions and setup jQuery for theme
			wp_enqueue_script("jquery");
			if(function_exists('WooTumblogInit')) :
				wp_enqueue_script( "jquery-player", get_bloginfo("template_directory")."/scripts/jquery.jplayer.min.js", array( "jquery" ) );
			endif;
			wp_enqueue_script( "jquery-touch", get_bloginfo("template_directory")."/scripts/jquerymobile.min.js", array( "jquery" ) );
			
			$script = array();
			if(get_option("mobile_no_ajax") == "yes") : $script["mobile_no_ajax"] = "true"; endif;
			if(get_option("mobile_page_transition") != "default") : $script["mobile_page_transition"] = get_option("mobile_page_transition"); endif;
			if(get_option("mobile_loading_message") != "Loading" && get_option("mobile_loading_message") != "") : $script["mobile_loading_message"] = get_option("mobile_loading_message"); endif;
			if(get_option("mobile_loading_error_message") != "Error Loading Page" && get_option("mobile_loading_error_message") != "") : $script["mobile_loading_error_message"] = get_option("mobile_loading_error_message"); endif;
			
			wp_localize_script( 'jquery-touch', 'mobi_settings', $script);

			wp_enqueue_script( "jquery-fitvid", get_bloginfo("template_directory")."/scripts/jquery.fitvid.js", array( "jquery" ) );
			wp_enqueue_script( $themeid."-jquery", get_bloginfo("template_directory")."/scripts/mobile_jquery.min.js", array( "jquery" ) );

			//Theme AJAX
			wp_localize_script( $themeid."-jquery", "ThemeAjax", array( "ajaxurl" => WP_PLUGIN_URL."/obox-mobile/admin-ajax.php" ) );
		endif;
	
		//AJAX Functions
		add_action( 'wp_ajax_nopriv_ocmx_comment-post', 'ocmx_comment_post'  );
		add_action( 'wp_ajax_ocmx_comment-post', 'ocmx_comment_post' );
		
		add_action( 'wp_ajax_nopriv_ocmx_load-posts', 'ocmx_load_more_posts');
		add_action( 'wp_ajax_ocmx_load-posts', 'ocmx_load_more_posts');
	}
?>