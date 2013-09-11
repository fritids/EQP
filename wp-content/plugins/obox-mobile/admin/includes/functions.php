<?php
global $ocmx_version;

// The OCMX custom options form
function mobile_update_options(){
	global $wpdb, $changes_done, $mobile_theme_options;
	
	//Clear our preset options, because we're gonna add news ones.
	wp_cache_flush(); 

	parse_str($_POST["data"], $data);
	
	$update_options = explode(",", $data["update_ocmx"]);
	
	foreach($data as $key => $value) :
		//echo "$key => $value \n";
		wp_cache_flush(); 			
		$clear_options = $wpdb->query("DELETE FROM $wpdb->options WHERE `option_name` = '".$key."'");
		if(!get_option($key)):					
			add_option($key, stripslashes($value));						
		else :						
			update_option($key, stripslashes($value));
		endif;
	endforeach;
	
	foreach($update_options as $option) :
		if(is_array($mobile_theme_options[$option])):
			foreach($mobile_theme_options[$option] as $option) :
				if(isset($option["main_section"])) :
					foreach($option["sub_elements"] as $suboption) :
						if($suboption["input_type"] == "checkbox") :
							$key = $suboption["name"];
							if($data[$key]) :
								update_option($key, "true");
							else :
								update_option($key, "false");
							endif;
						endif;
					endforeach;
				else :
					if($option["input_type"] == "checkbox") : 
						$key = $option["name"];
						if($data[$key]) :
							update_option($key, "true");
						else :
							update_option($key, "false");
						endif;
					endif;
				endif;
			endforeach;
		endif;
	endforeach;
	
	$changes_done = 1;
	die("");
}
function mobile_reset_options(){
	global $wpdb, $changes_done;
	
	//Clear our preset options, because we're gonna add news ones.
	wp_cache_flush(); 

	parse_str($_POST["data"], $data);
	
	$update_options = explode(",", $data["update_ocmx"]);
	
	foreach($update_options as $option) :
		ocmx_reset_option($option);
	endforeach;
	die("");
}
function mobile_reset_option($option){
	global $mobile_theme_options;
	if(is_array($mobile_theme_options[$option])):
	
		foreach($mobile_theme_options[$option] as $themeoption) :	
			update_option($themeoption["name"], $themeoption["default"]);
		endforeach;
	endif;
}

add_action("mobile_update_options", "mobile_update_options");
add_action("mobile_reset_option", "mobile_reset_option"); ?>