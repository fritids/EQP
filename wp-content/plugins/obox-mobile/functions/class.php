<?php class OCMX_Mobile
{
	function mobile_template(){
		//Which theme are we hooking?
		if(isset($_GET["template"]) && $_GET["template"]) :
			$template = $_GET["template"];
		elseif(get_option("ocmx_mobile_theme")) :		
			$template = get_option("ocmx_mobile_theme");
		else :
			$template = "default";
		endif;
		//If theme is invalid, change to default
		if(!file_exists($this->mobile_template_dir()."/".$template)) :
			$template = "default";
		endif; 
		
		return $template;
	}
	function mobile_stylesheet(){
		//Which theme are we hooking?
		if(isset($_GET["stylesheet"]) && $_GET["stylesheet"]) :
			$template = $_GET["stylesheet"];
		elseif(get_option("ocmx_mobile_stylesheet")) :	
			$template = get_option("ocmx_mobile_stylesheet");
		else :
			$template = "default";
		endif;
		//If theme is invalid, change to default
		if(!file_exists($this->mobile_template_dir()."/".$template)) :
			$template = "default";
		endif; 
		return $template;
	}
	
	function mobile_template_dir(){
		$template_path = OCMXMOBILEDIR."themes";
		return $template_path;
	}
	
	function mobile_template_uri(){
		$template_path = OCMXMOBILEURL."themes";
		return $template_path;
	}
	
	function remove_plugins(){
		// Disable  common plugins which can cause unwanted behaviour with Obox Mobile, such as showing a cached version of  the wrong site! */
		
		// Facebook Like button
		remove_filter('the_content', 'Add_Like_Button');
		
		//Sharebar Plugin
		remove_filter('the_content', 'sharebar_auto');
		remove_action('wp_head', 'sharebar_header');
		
		// Hyper Cache
		if ( function_exists( 'hyper_activate' ) ) {
			global $hyper_cache_stop;
			$hyper_cache_stop = true;
		}
	} 

	function allow_mobile(){		
		//Begin without allowing mobile
		$mobile = false;
		
		// Current browser
		$browserAgent = $_SERVER['HTTP_USER_AGENT'];
		
		// Devices we allow
		$touch_devices = array('iphone', 'ipod', 'aspen', 'incognito', 'webmate', 'android', 'dream', 'cupcake', 'froyo', 'blackberry', 'webos','samsung', 'bada', 'IEMobile', 'htc', 'opera mini');

		// Loop through the devices and decide if we're allowed to use Mobile
		foreach($touch_devices as $device) :
			if(preg_match ("/$device/i", $browserAgent)) :
				$mobile = true;
			endif;
		endforeach;
		
		// Force Mobile Site
		if(get_option("mobile_force") == "yes") :
			$mobile = true;
		endif;
		
		return $mobile;
	}
	
	function allow_slider(){
		if(get_option("mobile_slider") == "true" && (strpos($_SERVER['HTTP_USER_AGENT'], "Safari") && strpos($_SERVER['HTTP_USER_AGENT'], "iPhone") || strpos($_SERVER['HTTP_USER_AGENT'], "iPad"))) :
			return true;
		else :
			return false;
		endif;
	}

	function site_style(){		
		if($this->allow_mobile() === true) :
			if(isset($_GET["site_switch"]) && $_GET["site_switch"]) :
				$site_style = $_GET["site_switch"];
			elseif(isset($_COOKIE["ocmx_mobile"])) :
				$site_style = $_COOKIE["ocmx_mobile"];
			else :
				$site_style = "mobile";			
			endif;
		elseif(isset($_GET["preview"]) && $_GET["preview"] && $_GET["site_switch"]) :
			$site_style = $_GET["site_switch"];
		else :
			$site_style = "normal";
		endif;
		
		if($this->allow_mobile() === true)
			$this->remove_plugins();
			
		if(!headers_sent() && !isset($_GET["preview"])) :
			setcookie("ocmx_mobile", "", time() - 3600, COOKIEPATH, COOKIE_DOMAIN);
			setcookie("ocmx_mobile", $site_style, 0, COOKIEPATH, COOKIE_DOMAIN);
		endif;
		
		return $site_style;
	}
	
	function set_home_page(){
		//Make sure we're not using the WordPress static home page
		
		$root = $this->mobile_template_dir() . "/" . $this->mobile_template();

		if(get_option("mobile_home_page") != "0" && (is_home() || is_front_page())) :
			global $post;
			query_posts('page_id='.get_option('mobile_home_page'));
			include($root.'/page.php');
			exit;
		elseif(get_option( 'show_on_front', false ) == 'page' && (is_home() || is_front_page())) :
			$query = query_posts('show_posts='.get_option('posts_per_page')*2);
		endif;
	}
	
	function reset_home_page(){
		$home_page = get_option("orig_page_on_front");
		if($home_page !== "")
			update_option("page_on_front", $home_page);
	}
	
	function initiate(){
		$site_style = $this->site_style();
		if (($site_style == "mobile" && strpos( $_SERVER['REQUEST_URI'], '/wp-admin' ) === false)) :		
			add_filter( 'stylesheet', array( &$this, 'mobile_stylesheet') );
			add_filter( 'template', array( &$this, 'mobile_template') );
			add_filter( 'theme_root', array( &$this, 'mobile_template_dir') );
			add_filter( 'theme_root_uri', array( &$this, 'mobile_template_uri') );
			add_action( 'wp', array( &$this, 'set_home_page') );
		endif;
	}
}
?>