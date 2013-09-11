<?php  global $themename, $input_prefix;

/*****************/
/* Theme Details */

$themename = "OCMX Mobile - Default";
$themeid = "ocmx-mobile";
$productid = "1050";

/**********************/
/* Include OCMX files */

$include_folders = array("/ocmx/front-end/", "/ocmx/includes/", "/ocmx/widgets/", "/ocmx/setup/", "/ocmx/shortcodes/", "/ajax/");

include_once (TEMPLATEPATH."/ocmx/folder-class.php");
include_once (TEMPLATEPATH."/ocmx/load-includes.php");
include_once (TEMPLATEPATH."/ocmx/custom.php");
include_once (TEMPLATEPATH."/ocmx/seo-post-custom.php");

/**********************/
/* "Hook" up the OCMX */
if(function_exists("add_theme_support")):
	add_theme_support( 'post-thumbnails' );
endif;
add_action('init', 'mobile_default_add_scripts');
add_action('init', 'mobile_default_add_styles');
add_filter( 'show_admin_bar', '__return_false' );

/***************************************************************************/
/* Remove image dimensions to allow images to fit inside content container */
add_filter( 'the_content', 'remove_thumbnail_dimensions', 10 );
function remove_thumbnail_dimensions( $html ) {
    $html = preg_replace( '/(width|height)=\"\d*\"\s/', "", $html );
    return $html;
}

/*****************/
/* Add Nav Menus */

if (function_exists('register_nav_menus')) :
	register_nav_menus( array(
		'primary' => __('Primary Navigation', '$themename')
	) );
endif; 

function ocmx_no_posts()
	{
?>
	<li class="clearfix <?php echo $image_class; ?>" <?php echo $hideme; ?>>
    
        <div class="copy">
            <p><?php _e("There are no posts which match your selected criteria."); ?></p>
        </div>
	</li>
<?php 
	}
?>