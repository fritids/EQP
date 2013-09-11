<?php
function general_page_help($contextual_help, $screen_id, $screen) {

	global $add_general_page;
	if ($screen_id == $add_general_page) {

		$contextual_help = '<p>Thank you for purchasing Obox Mobile. Our goal is to have Obox Mobile be very intuitive and easy to use so we have setup these help menus to answer some of the most common questions. If you have any other questions please post them in the support <a href="http://www.obox-design.com/forum_list.cfm">forum</a> to ensure the fastest response.</p><h3>General</h3><p>When first setting up Obox Mobile it is handy to force the plugin to display the mobile view on all devices and browsers. To do this you simply need to select yes in the <strong>"Force Mobile Site"</strong> setting. While in Force Mobile Site mode you can go back and forth between the Force Mobile Site mode and regular mode by pressing the <strong>"Turn off Mobile"</strong> located at the bottom of the page. You can also activate normal mode by appending <strong>/?site_switch=normal</strong> to the end of your url and you can go back to mobile mode by appending <strong>/?site_switch=mobile</strong> to to the end of your url. Just remember to turn off this feature when you are done testing.</p><h3>Logo & Images</h3><p>The Logo & Images page allows you to specify which logo/background image you want to use for your mobile site. You have a few options when selecting a custom logo or background image. If the image is already on your server you can simply enter the full url or folder path to that image in the input field and press the <strong>"Save Changes"</strong> button at the top of the screen. If you are using an image that still needs to be uploaded you can use the built in image uploader. To use the image uploader press the <strong>"Browse"</strong> button under the section that you want to add the image to. This will bring up a dialog box which will allow you to select which image you want to upload. Once you have selected the image you want to upload simply press the <strong>"Open"</strong> button the the image will be uploaded. If you ever want to remove an image you can press the <strong>"Clear"</strong> button and that image will be removed.<p> Please remember that after you make any changes on this screen you need to press the <strong>"Save Changes"</strong> button or your changes will not be saved. Also the images uploaded on this screen will not be automatically resized. Because of this you want to pay special attention to the recommended image sizes.</p><ul><li><strong>Custom Logo:</strong> 32px wide x 32px high</li><li><strong>Custom Background:</strong> 460px wide</li><li><strong>App Mode Icon:</strong> 55px wide x 55px high</li></ul><h3>Thumbnails</h3><p>For the thumbnails to work correctly you must know how your post images are stored. If you are using a theme by Woothemes you will likely want to use the "Woothemes Custom Image" option. If you are using an Obox theme you will likely want to use the "Obox Main Image" option. If you use the built in WordPress featured image you will likely want to choose the "WordPress thumbnail feature" option.</p><h3>Post & Discussion</h3><p>As a general matter if you want a particular element to show up in the mobile theme the checkbox needs to be checked. An unchecked box means that the particular element will not be displayed.</p><h3>Frequently Asked Questions</h3><p>To change the number of post shown on the home page you need to go to <a href="options-reading.php">Settings ---> Reading</a> and change the value of the option labeled <strong>"Blog pages show at most"</strong></p>';
	}
	return $contextual_help;
}

add_filter('contextual_help', 'general_page_help', 10, 3);

function themes_page_help($contextual_help, $screen_id, $screen) {

	global $add_themes_page;
	if ($screen_id == $add_themes_page) {

		$contextual_help = '<h3>Themes</h3><p>It is a common myth that you have to sacrifice creativity and originality in order to provide your viewers an optimal mobile experience. Here at Obox HQ we don\'t believe that and we have dedicated a substantial amount of time ensuring that the Obox Mobile plugin not only works great but also looks pretty darn good too.</p><p>The Theme Viewer allows you to preview what your site will look like using various Obox Mobile themes. To preview a theme you need to press the <strong>"Preview"</strong> button under the theme you want to try out. If you want to use this feature it is recommended that you use a webkit browser such as Google Chrome or Safari.</p><p>A theme uploader is also included for the easy addition of new themes that you develop yourself or are developed by a third party. To upload a new theme you need to click the <strong>"Add a New Theme"</strong> button which will open a dialog box which will allow you to select your zipped theme folder. </p><p>If you want to remove a theme you need to first press the <strong>"Edit List"</strong> button. This will change the buttons from under the themes from <strong>"Preview" & "Activate"</strong> to <strong>"Delete Theme"</strong>. Once this happens simply press the <strong>Delete Theme"</strong> button and the theme will be removed.</p><p>Once you have decided which theme you want to use changing themes is easy. All you need to do it press the <strong>"Activate"</strong> button under the theme you want to use. If you are unsure which theme you are currently using you can identify your currently active theme by looking for the theme surrounded by a blue border.</p>';
	}
	return $contextual_help;
}

add_filter('contextual_help', 'themes_page_help', 10, 3);

function adverts_page_help($contextual_help, $screen_id, $screen) {

	global $add_adverts_page;
	if ($screen_id == $add_adverts_page) {

		$contextual_help = '<h3>Adverts</h3><p>Obox Mobile contains three different ad areas, header, below post, and footer. Keep in mind that where it asked for a url please input the entire url including the http://. </p> <p>Keep in mind that the recommended advert size is 320px wide by 50px high.</p>';
	}
	return $contextual_help;
}

add_filter('contextual_help', 'adverts_page_help', 10, 3);

function update_page_help($contextual_help, $screen_id, $screen) {

	global $add_update_page;
	if ($screen_id == $add_update_page) {

		$contextual_help = '<h3>Updater</h3><p>Obox Mobile comes with a built in updater to allow you to stay current quickly and easily. To use the updater you need to first enter your license key into the input field below. Your key is located on your <a href="http://www.obox-design.com/please_login.cfm">Obox Profile</a>. Next press the <strong>"Validate Key and Update"</strong> button and the updater will retrieve the latest files from the Obox server and update your plugin.</p><p><strong>NOTE: This screen is completely optional and you are only required to input your license key if you want to take advantage of the updater, it is not required to use Obox Mobile.</strong></p>';
	}
	return $contextual_help;
}

add_filter('contextual_help', 'update_page_help', 10, 3);


?>