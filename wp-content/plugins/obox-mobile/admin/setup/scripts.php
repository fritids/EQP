<?php
function mobile_add_scripts()
	{
		global $themeid;
		
		//Add support for 2.9 and 3.0 functions and setup jQuery for theme
		if(is_admin()) :
			wp_enqueue_script("jquery");
			wp_enqueue_script( 'jquery-ui-draggable' );
			wp_enqueue_script( 'jquery-ui-droppable' );
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'jquery-ui-tabs' );
			
			wp_enqueue_script( "ajaxupload", OCMXMOBILEURL."/admin/scripts/ajaxupload.js", array( "jquery" ) );
			
			if(strpos( $_SERVER['REQUEST_URI'], 'mobile' ) !== false) :
				wp_enqueue_script( "ocmx-jquery", OCMXMOBILEURL."/admin/scripts/ocmx_jquery.js", array( "jquery" ) );
				wp_localize_script( "ocmx-jquery", "ThemeAjax", array( "ajaxurl" => admin_url( "admin-ajax.php" ) ) );
				wp_enqueue_script( "mobile-upgrade", OCMXMOBILEURL."/admin/scripts/upgrade.js", array( "jquery" ) );
			endif;
		endif;
		add_action("wp_ajax_validate_key", "mobile_validate_key");
		add_action("wp_ajax_do_mobile_upgrade", "do_mobile_upgrade");
		add_action( 'wp_ajax_mobile_save-options', 'mobile_update_options');
		add_action( 'wp_ajax_mobile_reset-options', 'reset_mobile_options');
		add_action( 'wp_ajax_nopriv_mobile_ads-refresh', 'mobile_ads_refresh' );
		add_action( 'wp_ajax_mobile_ads-refresh', 'mobile_ads_refresh' );
		add_action( 'wp_ajax_mobile_ads-remove', 'mobile_ads_remove' );
		add_action( 'wp_ajax_mobile_ajax-upload', 'mobile_ajax_upload' );
		add_action( 'wp_ajax_mobile_theme-upload', 'mobile_theme_upload' );
		add_action( 'wp_ajax_mobile_theme-remove', 'mobile_theme_remove' );
		add_action( 'wp_ajax_mobile_remove-image', 'mobile_ajax_remove_image' );
	}
?>