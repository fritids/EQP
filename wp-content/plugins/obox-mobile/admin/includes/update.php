<?php
/*******************************/
/* The Auto Update Starts here */
function ocmx_update(){
    global $productid, $theme_title, $theme_version;
	
	$themes = get_themes();
	$current_theme = get_current_theme(); ?>
<?php
}

function mobile_validate_key(){
	$validate = "http://www.obox-design.com/validate.cfm?hashKey=".$_GET["hashkey"]."&timestamp=".date("Y-M-d");
	
	$usefeed = $validate;
	$string = file_get_contents($usefeed);
	$xml = @simplexml_load_string($string) or print ("no file loaded");
	
	if($xml->error == 0 && $xml->userok == "true") :
		die("<li>Your License is Valid</li>");
	else :
		update_option("mobile_hashkey", $_GET["hashkey"]);
		die("<li>Your License is not Valid</li>");
	endif;
}

function do_mobile_upgrade($hashkey){
	global $productid;
	$plugin_upgrade = new OCMX_Upgrader();
		
	$package = "http://www.obox-design.com/mobile_update.cfm?hashKey=".$_GET["hashkey"];
	$defaults = array( 	'package' => $package, //Please always pass this.
						'destination' => OCMXMOBILEDIR, //And this
						'is_multi' => false,
						'hook_extra' => array() //Pass any extra $hook_extra args here, this will be passed to any hooked filters.
					);
	
	$show_progress = $plugin_upgrade->run($defaults);

	if ( is_wp_error($show_progress) ) :
   		$error = $show_progress->get_error_message();
		die($error);
	endif;
	die("<li>Well done! The upgrade was successful.</li>");	
}; ?>