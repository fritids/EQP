<?php
global $mobile_theme_options, $advert_areas;

$mobile_theme_options = array();

$mobile_theme_options["general_site_options"] =
	array(
		array(
			"main_section" => "Home Page",
			"main_description" => "These settings control how your content is displayed on the home page.",
			"sub_elements" => 
				array(	
					array("label" => "Display", "description" => "Select a page to display on your home page or display your latest posts.", "name" => "mobile_home_page", "default" => "", "id" => "mobile_home_page", "zero_wording" => "Latest Posts", "input_type" => "select", "options" => "loop_pages"),
					array("label" => "Exclude Category", "description" => "Select a category to exclude from the home page (this is especially useful when using the slider).", "name" => "mobile_category_exclude", "default" => "", "id" => "", "zero_wording" => "Display All Categories", "input_type" => "select", "options" => "loop_categories"),
				)
			),
		array(
			"main_section" => "Menu Settings",
			"main_description" => "",
			"sub_elements" => array(
				array("label" => "Dropdown Arrow/Label", "description" => "Select whether to display the menu label or arrow icon.", "name" => "mobile_menu_label", "default" => "arrow", "id" => "mobile_menu_label", "input_type" => "select", "options" => array("Arrow" => "arrow", "Label" => "label"))
			)
		),
		array(
			"main_section" => "Featured Slider",
			"main_description" => "These settings control what content is displayed in the featured slider.",
			"sub_elements" => 
				array(
					array("label" => "Enable", "description" => "Enable the slider (the slider will only display on iPhones and iPod Touch).", "name" => "mobile_slider", "default" => "false", "id" => "mobile_slider", "input_type" => "checkbox"),
					array("label" => "Category", "description" => "What category do you want your slider to display posts from?", "name" => "mobile_slider_category", "default" => "", "id" => "", "zero_wording" => "All", "input_type" => "select", "options" => "loop_categories", "linked" => "mobile_slider"),
					array("label" => "Post Count", "description" => "How many posts do you want the slider to display?", "name" => "mobile_slider_count", "default" => "", "id" => "", "input_type" => "select", "options" => array("3" => "3", "6" => "6", "9" => "9", "12" => "12", "linked" => "mobile_slider"))
				)
			),
		array("label" => "Custom Footer Text", "description" => "HTML is allowed.", "name" => "mobile_custom_footer", "default" => "<a href=\"http://www.obox-design.com/\">Obox Mobile Framework</a> created by Obox Design", "id" => "mobile_custom_footer", "input_type" => "memo"),
		array("label" => "Site Analytics", "description" => "Enter in the Google Analytics Script here.","name" => "mobile_googleAnalytics", "default" => "", "id" => "mobile_googleAnalytics","input_type" => "memo"),
		array(
			"main_section" => "Force Mobile Site",
			"main_description" => "(Recommended for testing)",
			"sub_elements" => array(
				array("label" => "", "description" => "Do you want to allow the mobile site to be accessible regardless what browser or device is being used?", "name" => "mobile_force", "default" => "no", "id" => "mobile_force", "input_type" => "select", "options" => array("Yes" => "yes", "No" => "no"))
			)
		)
	);
$mobile_theme_options["thumbnail_options"] =
	array(
		array(
			"main_section" => "Thumbnail Settings",
			"main_description" => "These settings control how your thumbnails are displayed.",
			"sub_elements" => 
				array(
					array("label" => "Display on", "description" => "Where do you want your thumbnails to be displayed?", "name" => "mobile_image_usage", "default" => "image_posts_lists", "id" => "mobile_thumbnail_usage", "input_type" => "select", "options" => array("Single Posts and Lists" => "image_posts_lists", "Single Posts Only" => "posts", "Lists Only" => "lists", "Neither" => "off")),
					array("label" => "Post Thumbnail", "description" => "Which image do you want used as your post thumbnail?", "name" => "mobile_thumbnail_usage", "default" => "other_media", "id" => "mobile_thumbnail_usage", "zero_wording" => "Use custom meta.", "input_type" => "select", "options" => array("WordPress Thumbnail Feature" => "wordpress", "Obox Main Image" => "other_media", "WooThemes Custom Image" => "image", "Custom Image Meta" => "0")),
					array("label" => "Custom Meta (advanced)", "description" => "This setting is only used if you selected \"Custom Image Meta\" in the previous option.", "name" => "mobile_thumbnail_custom", "default" => "", "id" => "mobile_thumbnail_custom", "input_type" => "text", "linked" => "mobile_thumbnail_usage"),
					array("label" => "Use Timthumb", "description" => "Use timthumb to automatically resize your site's images?", "name" => "mobile_use_timthumb", "default" => "true", "id" => "mobile_use_timthumb",  "input_type" => "checkbox"),
					array("label" => "Cropping", "description" => "What area would you like to be in the cropping region?", "name" => "mobile_thumbnail_alignment", "default" => "c", "id" => "mobile_thumbnail_usage",  "input_type" => "select", "options" => array("Center" => "c", "Top" => "t", "Bottom" => "b", "Right" => "r", "Left" => "l"), "linked" => "mobile_use_timthumb"),
					array("label" => "Image Quality", "description" => "Select your desired image quality. <br /> <strong>WARNING: The higher the quality the longer the load time.</strong>", "name" => "mobile_thumbnail_quality", "default" => "70", "id" => "mobile_thumbnail_quality",  "input_type" => "select", "options" => array("50 (Lowest)" => 50, "60" => 60, "70 (Recommended)" => 70, "80" => 80, "90" => 90, "100 (Highest)" => 100), "linked" => "mobile_use_timthumb")
				)
			)
		);
$mobile_theme_options["post_options"] =
	array(
		array(
			"main_section" => "General",
			"main_description" => "These settings control how your content is displayed.",
			"sub_elements" => 
				array(
					array("label" => "Post Excerpts", "description" => "Do you want an excerpt automatically generated for your posts on listing pages?", "name" => "mobile_auto_excerpt", "default" => "no", "id" => "mobile_auto_excerpt", "input_type" => "select", "options" => array("No" => "no", "Yes" => "yes")),
					array("label" => "Author's Bio", "description" => "Where do you want your author's avatar &amp; bio displayed?", "name" => "mobile_author_display", "default" => "posts_pages", "id" => "mobile_author_display", "input_type" => "select", "options" => array("Posts and Pages" => "posts_pages", "Posts Only" => "posts", "Pages Only" => "pages", "Off" => "off")),
					array("label" => "Comments", "description" => "Where do you want comments displayed?", "name" => "mobile_comments_usage", "default" => "comments_posts", "id" => "mobile_comments_usage", "input_type" => "select", "options" => array("Posts and Pages" => "comments_posts_pages", "Posts Only" => "comments_posts", "Pages Only" => "comments_pages", "Off" => "comments_off")),
					array("label" => "Enable Woo Shortcodes", "description" => "Enable this option only if your site is making us of <a href='http://www.woothemes.com/woocodex/shortcodes/' target='_blank'>WooThemes Shortcodes</a>", "name" => "mobile_woo_shortcodes", "default" => "false", "id" => "mobile_woo_shortcodes", "input_type" => "checkbox")
				)
			),
		array(
			"main_section" => "Post Meta",
			"main_description" => "These settings control which post meta is displayed.",
			"sub_elements" => 
				array(
					array("label" => "Show Post Meta on", "description" => "Where do you want to display the post meta?", "name" => "mobile_post_meta", "default" => "posts_pages", "id" => "mobile_post_meta", "input_type" => "select", "options" => array("Posts, Lists and Pages" => "posts_pages", "Posts &amp; Lists Only" => "posts", "Pages Only" => "pages", "Off" => "off")),
					array("label" => "Date", "name" => "mobile_post_date", "description" => "The date is located underneath the post title.", "default" => "on", "id" => "mobile_post_date", "input_type" => "checkbox", "linked" => "mobile_post_meta"),
					array("label" => "Author", "description" => "The author is located underneath the post title. 
										<strong>NOTE: This option is different from the author's bio.</strong>", "name" => "mobile_post_author", "default" => "on", "id" => "mobile_post_author", "input_type" => "checkbox", "linked" => "mobile_post_meta"),
					array("label" => "Tags", "description" => "The tags are located underneath the post title.", "name" => "mobile_post_tags", "default" => "off", "id" => "mobile_post_tags", "input_type" => "checkbox", "linked" => "mobile_post_meta"),
					array("label" => "Categories", "description" => "The categories are located underneath the post title.", "name" => "mobile_post_categories", "default" => "off", "id" => "mobile_post_categories", "input_type" => "checkbox", "linked" => "mobile_post_meta")
				)
			),
		array(
			"main_section" => "Social Linking",
			"main_description" => "These settings control how your users share your content.",
			"sub_elements" => 
				array(
						array("label" => "Social linking", "description" => "Do you want to display social links on your posts?", "name" => "mobile_social_link_usage", "default" => "social_link_on", "id" => "mobile_social_link_usage", "input_type" => "select", "options" => array("Yes" => "on", "No" => "off")),
						array("label" => "Twitter", "description" => "", "name" => "mobile_twitter", "default" => "on", "id" => "mobile_twitter", "input_type" => "checkbox", "linked" => "mobile_social_link_usage"),
						array("label" => "Facebook", "description" => "", "name" => "mobile_facebook", "default" => "on", "id" => "mobile_facebook", "input_type" => "checkbox", "linked" => "mobile_social_link_usage"),
						array("label" => "Google Plus", "description" => "", "name" => "mobile_googleplus", "default" => "on", "id" => "mobile_googleplus", "input_type" => "checkbox", "linked" => "mobile_social_link_usage")
					)
			)
	);

$mobile_theme_options["jquerymobile"] =
	array(
		array(
			"main_section" => "jQuery Mobile",
			"main_description" => "",
			"sub_elements" => 
				array(
					array("label" => "Disable Ajax", "description" => "Turn off Ajax page loading.", "name" => "mobile_no_ajax", "default" => "no", "id" => "mobile_no_ajax", "input_type" => "select", "options" => array("No" => "no", "Yes" => "yes")),
					array("label" => "Page Transition Effect", "description" => "Change the page transition animation.", "name" => "mobile_page_transition", "default" => "slide", "id" => "mobile_page_transition", "input_type" => "select", "options" => array("Slide (default)" => "slide", "Slideup" => "slideup", "Slidedown" => "slidedown", "Pop" => "pop", "Fade" => "fade", "Flip" => "flip")),
					array("label" => "\"Loading\" text", "description" => "Set a custom message to replace the \"Loading\" message when users navigate your site.", "name" => "mobile_loading_message", "default" => "Loading", "id" => "mobile_loading_message", "input_type" => "text"),
				)
			)
		);
$mobile_theme_options["image_options"] =
	array(
		array("label" => "Custom Logo", "description" => "<strong>Recommended Size: 32px x 32px</strong><br />If you are not using the image uploader please enter the full URL or folder path to your custom logo.", "name" => "mobile_custom_logo", "default" => "", "id" => "upload_button_logo", "input_type" => "file", "sub_title" => "mobile-logo"),		
		array("label" => "Custom Background", "description" => "<strong>Recommended Size: 460px wide</strong><br />If you are not using the image uploader please enter the full URL or folder path to your custom background.", "name" => "mobile_custom_background", "default" => "", "id" => "upload_button_background", "input_type" => "file", "sub_title" => "background"),	
		array("label" => "App Mode Icon", "description" => "<strong>Recommended Size: 55px x 55px</strong><br />This is the icon used when your site is running in stand alone mode. </br><strong>(For iPhone users only)</strong>", "name" => "mobile_app_icon", "default" => OCMXMOBILEURL."/admin/images/iphone_ico.png", "id" => "upload_button_icon", "input_type" => "file", "sub_title" => "icon"),
		array("label" => "App Mode Splash Screen", "description" => "<strong>Recommended Size: 320px x 480px</strong><br />This is the icon used when your site is running in stand alone mode. </br><strong>(For iPhone users only)</strong>", "name" => "mobile_app_splash", "default" => OCMXMOBILEURL."/admin/images/web-app-splash.png", "id" => "upload_button_splash", "input_type" => "file", "sub_title" => "splash")

);
$mobile_theme_options["upgrade_license_options"] =
	array(
		array("label" => "License Key", "description" => "Enter your license key from Obox", "name" => "mobile_license_key", "default" => "", "id" => "mobile_license_key", "input_type" => "text"),		
		array("label" => "Validate", "description" => "", "name" => "", "default" => "Validate", "id" => "mobile_license_button", "input_type" => "button"),
);

$advert_areas = array("Footer" => "footer", "Below Post" => "post", "Site Header" => "header");
foreach($advert_areas as $ad_area => $option) :
	$mobile_theme_options[$option."_adverts"] = array(	
				array("label" => "Advert Title", "description" => "The title will be displayed in the event the advert image is unavailable.", "name" => $option."_ad_title", "default" => "", "id" =>  $option."_ad_title", "input_type" => "text"),
				array("label" => "Advert Link", "description" => "Include the full url including the http://", "name" => $option."_ad_href", "default" => "", "id" => "", "input_type" => "text"),
				array("label" => "Image Url", "description" => "<strong>Recommended Size: <br>300px wide x 50px high</strong><br><br />Include the full url including the http://", "name" => $option."_ad_image", "default" => "", "id" => "upload_button_".$option."_ad_image", "input_type" => "file", "sub_title" => $option."_ad_image"),
				array("label" => "Advert Script", "description" => "Enter the script for your advert here.", "name" => $option."_ad_buysell_id", "default" => "", "id" => $option."_ad_buysell_id", "input_type" => "memo"),
				array(
					"main_section" => "Restrictions",
					"main_description" => "These settings control when your adverts will be displayed.",
					"sub_elements" => 
						array(
								array("label" => "Only in Posts", "description" => "Only show this advert when a user is viewing a single post or page.", "name" => $option."_ad_postst_only", "default" => "false", "id" => $option."_ad_postst_only", "input_type" => "checkbox"),
								array("label" => "Only non-users", "description" => "Hide the adverts for users who are logged in.", "name" => $option."_ad_non_users", "default" => "false", "id" => $option."_ad_non_users", "input_type" => "checkbox")
							)
					)				
		);
endforeach;

$mobile_theme_options["plugins"] =
	array(
		array(
			"main_section" => "Plugins",
			"note" => "In some cases Obox Mobile may clash with your installed plugins. <br>Please select the plugins you would like to enable when users visit your mobile site.",
			"main_description" => "<strong>NOTE:</strong>The fewer plugins you enable the faster your site will load.",
			"sub_elements" => array()
		)
	);
if(class_exists("mobile_plugin_class")):
	$plugins = new mobile_plugin_class();
	$plugins->plugin_list(); 
endif;?>