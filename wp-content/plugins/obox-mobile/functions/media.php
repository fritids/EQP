<?php function get_mobile_image($width = 460, $height = '', $href_class = 'thumbnail', $wrap = '', $wrap_class = '', $hide_href = false){
	global $post;
	//Set iamge HTML to nothing
	$img_html = "";
	
	
	if(get_option("mobile_image_usage") == "off") :
		return false;
	elseif(!is_archive() && is_single() && get_option("mobile_image_usage") == "lists") :
		return false;
	elseif(!is_page() && !is_single() && get_option("mobile_image_usage") == "posts") :
		return false;
	endif;
	
	//Set up which meta value we're using for the post	
	if(get_option("mobile_thumbnail_usage") == "none") :
		return false;
	elseif(get_option("mobile_thumbnail_usage") != "0") :
		$meta = get_option("mobile_thumbnail_usage");
	elseif(!get_option("mobile_thumbnail_custom") !== "") :
		$meta = get_option("mobile_thumbnail_custom");
	else :
		$meta = "other_media";
	endif;	//Check for a thumbnail using the meta
	
	if(get_option("mobile_thumbnail_quality")) :
		$q = get_option("mobile_thumbnail_quality");
	else :
		$q = 70;
	endif;
	
	$get_thumbnail = get_post_meta($post->ID, $meta, true);
	$get_post_video = get_post_meta($post->ID, "main_video", true);
	
	if ($get_post_video !== "") :
		$post_image = preg_replace("/(width\s*=\s*[\"\'])[0-9]+([\"\'])/i", "$1 100% \" wmode=\"transparent\"", $get_post_video);
		$post_image = preg_replace("/(height\s*=\s*[\"\'])[0-9]+([\"\'])/i", "$1 250$2", $post_image);
	//Begin the thumbnail check
	elseif ( function_exists("has_post_thumbnail") && ( ( $meta == "wordpress" && has_post_thumbnail() ) || ( $get_thumbnail == "" && has_post_thumbnail() ) )) :

		if(has_post_thumbnail($post->ID)) :
			// Set the height to a huge number so that WP only sizes to the width
			if($height == "") : $height = 2000; endif;
			//Set the post Image Path
			$post_image = get_the_post_thumbnail($post->ID, array($width, $height));
		endif;
	elseif (get_option("mobile_use_timthumb") != "false" && $get_thumbnail !== "") :
		$post_image = "<img src=\"".get_bloginfo('template_directory')."/functions/timthumb.php?q=$q&amp;src=$get_thumbnail&amp;w=$width&amp;h=$height&amp;zc=1&amp;a=".get_option("mobile_thumbnail_alignment")."\" alt=\"$post->post_title\" />";	
	elseif (get_option("mobile_use_timthumb") != "false" && $get_thumbnail !== "") :
		$post_image = "<img src=\"$get_thumbnail\" alt=\"$post->post_title\" />";	
	else :
		//There is no image, lets quit
		return false;
	endif;
	
	//Create the image HTML with the link around it	
	$link = get_permalink($post->ID);
	if($hide_href == false) :
		$img_html = "<a href=\"$link\" class=\"$href_class\">$post_image</a>";
	else :
		$img_html = $post_image;
	endif;
	
	//Class for the surrounding divs
	if($wrap_class != "") :    
    	$class = " class=\"$wrap_class\"";
    endif;
    
	if($wrap != "") :
    	$img_html = "<$wrap".$class.">".$img_html."</$wrap>";
	else :
		$img_html;
	endif;
	return $img_html;
} ?>